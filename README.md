IP Location
===========
A simple set of third-party service implementations for determining a geographical location using an IP address.

This library is based on the screencasts of the knowledge base [Deworker.pro]

Installation
------------

The preferred way to install this extension is through [composer]

```shell
composer require nuthouse-cis/ip-location
```

Or add to your `composer.json` file

```json
{
  "require": {
    "nuthouse-cis/ip-location": "*"
  }
}
```

Examples
--------

### Basic usage

```php
require 'vendor/autoload.php';
/** @var $locator \NuthouseCIS\IPLocation\Locator */
$ip = new \NuthouseCIS\IPLocation\Ip('8.8.8.8');
$location = $locator->locate($ip);

if ($location
    && ($location->getCountry()->getIsoAlpha2() === 'US'
        || $location->getCountry()->getIsoAlpha3() === 'USA'
        || $location->getCountry()->getName() === 'United States')
) {
    // Do some stuff
}
```
Also placed JSON decorator:

```php
/** @var $location \NuthouseCIS\IPLocation\Location\Location */
$decorator = new \NuthouseCIS\IPLocation\Decorators\LocationJsonDecorator($location);
print json_encode($decorator);
```

### Cache Locator

This locator using PSR-16 Simple Cache interface for caching result of another implementation
of `\NuthouseCIS\IPLocation\Locator`

```php
/**
 * @var $simpleCacheIterface \Psr\SimpleCache\CacheInterface
 * @var $adapter \NuthouseCIS\IPLocation\Locator
 */
$locator = new \NuthouseCIS\IPLocation\Locators\CacheLocator(
    $adapter,
    $simpleCacheIterface,
    10 * 60,
    'adapter1-'
);
```

### Mute Locator

Mute locator catch all `Exceptions` and use implementation of `\NuthouseCIS\IPLocation\Handlers\ErrorHandler` to handle
they

```php
$errorHandler = new class implements \NuthouseCIS\IPLocation\Handlers\ErrorHandler {
    public function handle(Exception $exception): void
    {
        // Do some stuff with exception
    }
};
/**
 * @var $adapter \NuthouseCIS\IPLocation\Locator
 */
$locator = new \NuthouseCIS\IPLocation\Locators\MuteLocator(
    $adapter,
    $errorHandler
);
```

Also you can use `\NuthouseCIS\IPLocation\Handlers\PsrLogErrorHandler`

```php
/**
 * @var $logger \Psr\Log\LoggerInterface
 */
$errorHandler = new \NuthouseCIS\IPLocation\Handlers\PsrLogErrorHandler(
    $logger,
    \Psr\Log\LogLevel::ERROR
);
```

### Chain Locator

Chain locator returns the most complete result from all passed implementations of `\NuthouseCIS\IPLocation\Locator`

```php
/**
 * @var $adapters \NuthouseCIS\IPLocation\Locator[]
 */
$locator = new \NuthouseCIS\IPLocation\Locators\ChainLocator(
    ...$adapters
);
```

Services
--------
### IpGeoLocation
[IP Geolocation API Documentation]

```php
/** 
 * @var $client \Psr\Http\Client\ClientInterface
 * @var $requestFactory \Psr\Http\Message\RequestFactoryInterface
 * @var $apiKey string API Key from your dashboard https://app.ipgeolocation.io/
 * @var $lang string [en, de, ru, ja, fr, cn, es, cs, it]
 * @var $fields null|string[] required fields. Default: ['geo'] - minimum sufficient set of fields
 * @var $excludes null|string[] excluded fields
 * @var $baseUrl string default: https://api.ipgeolocation.io/ipgeo
 * @link https://ipgeolocation.io/documentation/ip-geolocation-api.html
 */
$locator = new \NuthouseCIS\IPLocation\Locators\IpGeoLocationIo\IpGeoLocationIoAdapter(
    $client,
    $requestFactory,
    $apiKey,
    $lang,
    $fields,
    $excludes,
    $baseUrl
);
$location = $locator->locate(new \NuthouseCIS\IPLocation\Ip('8.8.8.8'));
```
Testing
-------

```shell 
# Unit tests:
composer run-script test
# or
$ vendor/bin/phpunit

# Code style:
composer run-script phpcs
# or
$ vendor/bin/phpcs --standard=phpcs.xml

# Static analysis tool (Psalm):
composer run-script psalm
# or
$ vendor/bin/psalm --config=psalm.xml
```

[composer]: http://getcomposer.org/download/

[Deworker.pro]: https://deworker.pro/edu/series/ip-geolocator
[IP Geolocation API Documentation]: https://ipgeolocation.io/documentation/ip-geolocation-api.html