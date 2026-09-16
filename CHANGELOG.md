# Changelog

## 2.0.0

Forked from `scandipwa/service-worker` 1.0.3. Module name and namespace are unchanged, and the package replaces `scandipwa/service-worker` at every version, so it installs as a drop-in replacement.

- POST, PUT, DELETE and PATCH on `/service-worker.js` answer 405 with `Allow: GET, HEAD` instead of the script; the check is in the action because Magento validates only the first action of a forward chain.
- The script is served even when another module declares route717's `ignoredURLs` in frontend scope: the bypass is declared in `etc/frontend/di.xml`, where 1.0.3's global-only declaration was discarded by any area declaration and the app shell was answered instead.
- The bypass is anchored to the worker itself, so `/service-worker.js.map` and any other `/service-worker.js…` path get the normal not-found page.
- A theme with no compiled worker answers 404 in both failure modes; 1.0.3 let the filesystem exception escape as a 500, and the asset resolver's not-found exception is a `LogicException` that is now caught as well.
- When the worker is not in `pub/static` it is resolved in-process through the asset repository with the same area, theme and locale fallback; 1.0.3 fetched the site's own static URL over HTTP, which failed in the PHP container with a name-resolution error and answered 500.
- The app-wide preference for `Magento\Framework\Filesystem\DriverInterface` is gone, and with it `etc/di.xml`; the controller types the driver it needs, so an install that relied on this module to resolve a bare `DriverInterface` elsewhere needs its own.
- `php ^8.3`, `magento/framework ^103.0` and `selveq/route717 ^3.0` are required, which 1.0.3 left undeclared although its virtual type builds on route717's router; `ScandiPWA_Route717` is sequenced in `module.xml` and `archive.exclude` keeps development files out of the package archive.
- The copyright headers name only the notices the upstream files carried.
