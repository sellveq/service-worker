# ScandiPWA ServiceWorker

Fork of [scandipwa/service-worker](https://github.com/scandipwa/service-worker) 1.0.3, maintained by Selveq for Magento 2.4.9 and PHP 8.3. Module name and namespace are unchanged, and the package replaces `scandipwa/service-worker` at every version, so it installs as a drop-in replacement. Selveq is not affiliated with or endorsed by Scandiweb.

## What it does

- Answers `/service-worker.js` with the theme's built worker, read from `pub/static` once it is deployed and from the theme's own `Magento_Theme/web/service-worker.js` otherwise, with `Service-Worker-Allowed: /` so the worker may control the whole origin.
- Keeps the ScandiPWA router off that one path, so the request is answered with the script rather than the app shell.
- Answers 404 when the active theme ships no worker, and 405 to any method but GET and HEAD.

## Install

```sh
composer require selveq/service-worker
bin/magento setup:upgrade
```

The theme must ship `Magento_Theme/web/service-worker.js`, which the ScandiPWA theme does.

## License

[OSL-3.0](LICENSE), the license of the original work. Scandiweb's copyright notices are kept in every file, and each file Selveq changed carries a `Modifications © Selveq` notice.
