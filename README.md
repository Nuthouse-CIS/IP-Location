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
use NuthouseCIS\IPLocation\Ip;use NuthouseCIS\IPLocation\Locator;require 'vendor/autoload.php';
/** @var $locator Locator */
$ip = new Ip('8.8.8.8');
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
/** @var $location Location */
use NuthouseCIS\IPLocation\Decorators\LocationJsonDecorator;use NuthouseCIS\IPLocation\Location\Location;$decorator = new LocationJsonDecorator($location);
print json_encode($decorator);
```

### Cache Locator

This locator using PSR-16 Simple Cache interface for caching result of another implementation
of `\NuthouseCIS\IPLocation\Locator`

```php
/**
 * @var $simpleCacheIterface CacheInterface
 * @var $adapter Locator
 */
use NuthouseCIS\IPLocation\Locator;use NuthouseCIS\IPLocation\Locators\CacheLocator;use Psr\SimpleCache\CacheInterface;$locator = new CacheLocator(
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
use NuthouseCIS\IPLocation\Handlers\ErrorHandler;use NuthouseCIS\IPLocation\Locator;use NuthouseCIS\IPLocation\Locators\MuteLocator;$errorHandler = new class implements ErrorHandler {
    public function handle(Exception $exception): void
    {
        // Do some stuff with exception
    }
};
/**
 * @var $adapter Locator
 */
$locator = new MuteLocator(
    $adapter,
    $errorHandler
);
```

Also you can use `\NuthouseCIS\IPLocation\Handlers\PsrLogErrorHandler`

```php
/**
 * @var $logger LoggerInterface
 */
use NuthouseCIS\IPLocation\Handlers\PsrLogErrorHandler;use Psr\Log\LoggerInterface;use Psr\Log\LogLevel;$errorHandler = new PsrLogErrorHandler(
    $logger,
    LogLevel::ERROR
);
```

### Chain Locator

Chain locator returns the most complete result from all passed implementations of `\NuthouseCIS\IPLocation\Locator`

```php
/**
 * @var $adapters Locator[]
 */
use NuthouseCIS\IPLocation\Locator;use NuthouseCIS\IPLocation\Locators\ChainLocator;$locator = new ChainLocator(
    ...$adapters
);
```

Services
--------
### IpGeoLocation
[IP Geolocation API Documentation]

```php
/** 
 * @var $client ClientInterface
 * @var $requestFactory RequestFactoryInterface
 * @var $apiKey string API Key from your dashboard https://app.ipgeolocation.io/
 * @var $lang string [en, de, ru, ja, fr, cn, es, cs, it]
 * @var $fields null|string[] required fields. Default: ['geo'] - minimum sufficient set of fields
 * @var $excludes null|string[] excluded fields
 * @var $baseUrl string default: https://api.ipgeolocation.io/ipgeo
 * @link https://ipgeolocation.io/documentation/ip-geolocation-api.html
 */
use NuthouseCIS\IPLocation\Ip;use NuthouseCIS\IPLocation\Locators\IpGeoLocationIo\IpGeoLocationIoAdapter;use Psr\Http\Client\ClientInterface;use Psr\Http\Message\RequestFactoryInterface;$locator = new IpGeoLocationIoAdapter(
    $client,
    $requestFactory,
    $apiKey,
    $lang,
    $fields,
    $excludes,
    $baseUrl
);
$location = $locator->locate(new Ip('8.8.8.8'));
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