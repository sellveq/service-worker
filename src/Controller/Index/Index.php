<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ServiceWorker
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ServiceWorker\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\View\Asset\File\NotFoundException as AssetNotFoundException;
use Magento\Framework\View\Asset\Repository;

class Index implements HttpGetActionInterface
{
    /**
     * @param DirectoryList $directoryList
     * @param File $filesystemDriver
     * @param Repository $assetRepo
     * @param ResultFactory $resultFactory
     * @param Http $request
     */
    public function __construct(
        private readonly DirectoryList $directoryList,
        private readonly File $filesystemDriver,
        private readonly Repository $assetRepo,
        private readonly ResultFactory $resultFactory,
        private readonly Http $request
    ) {}

    /**
     * read the theme's compiled worker, falling back to the asset pipeline when pub/static has none
     * @return string
     * @throws FileSystemException
     * @throws AssetNotFoundException
     */
    private function getServiceWorkerContent(): string
    {
        $staticAbsolutePath = $this->directoryList->getPath(DirectoryList::STATIC_VIEW);
        $frontendLocalePath = $this->assetRepo->getStaticViewFileContext()->getPath();
        $serviceWorkerName = 'service-worker.js';

        $bundleFilePath = sprintf(
            '%s/%s/Magento_Theme/%s',
            $staticAbsolutePath,
            $frontendLocalePath,
            $serviceWorkerName
        );

        if (!$this->filesystemDriver->isExists($bundleFilePath)) {
            // resolved in-process because PHP cannot reach the site's own static URL from this container
            $bundleFilePath = $this->assetRepo
                ->createAsset(sprintf('Magento_Theme::%s', $serviceWorkerName))
                ->getSourceFile();
        }

        return $this->filesystemDriver->fileGetContents($bundleFilePath);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        // HttpGetActionInterface rejects nothing here: only the first action of a forward chain is validated
        if (!in_array($this->request->getMethod(), ['GET', 'HEAD'], true)) {
            // a Raw result carries no body until setContents(), so 405 answers empty on its own
            return $this->resultFactory
                ->create(ResultFactory::TYPE_RAW)
                ->setHttpResponseCode(405)
                ->setHeader('Allow', 'GET, HEAD');
        }

        $result = $this->resultFactory
            ->create(ResultFactory::TYPE_RAW)
            ->setHeader('Content-Type', 'text/javascript');

        try {
            $content = $this->getServiceWorkerContent();
        } catch (FileSystemException | AssetNotFoundException) {
            // AssetNotFoundException is a LogicException, not a FileSystemException, so it needs its own name
            return $result->setHttpResponseCode(404);
        }

        return $result
            ->setHeader('Service-Worker-Allowed', '/')
            ->setContents($content);
    }
}
