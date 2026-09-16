<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ServiceWorker
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ServiceWorker\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    /**
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        private readonly ActionFactory $actionFactory
    ) {}

    /**
     * {@inheritdoc}
     */
    public function match(RequestInterface $request)
    {
        if (trim($request->getPathInfo(), '/') !== 'service-worker.js') {
            return null;
        }

        $request
            ->setModuleName('serviceworker')
            ->setControllerName('index')
            ->setActionName('index');

        return $this->actionFactory->create(Forward::class);
    }
}
