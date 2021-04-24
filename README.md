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

Also, you can use `\NuthouseCIS\IPLocation\Handlers\PsrLogErrorHandler`

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

### SypexGeo
[SypexGeo Documentation]
#### Database file
```php
/**
 * @var $filePath string path to SypexGeo database file
 * @see https://sypexgeo.net/ru/download/
 */
$sxGeo = new \NuthouseCIS\SxGeo\SxGeo(
    $filePath,
    \NuthouseCIS\SxGeo\SxGeo::SXGEO_BATCH | \NuthouseCIS\SxGeo\SxGeo::SXGEO_MEMORY
);
$locator = new \NuthouseCIS\IPLocation\Locators\SypexGeo\SypexGeoAdapter($sxGeo);
$location = $locator->locate(new \NuthouseCIS\IPLocation\Ip('8.8.8.8'));
```
#### REST API
[SypexGeo REST API Documentation]
```php
/** 
 * @var $client \Psr\Http\Client\ClientInterface
 * @var $requestFactory \Psr\Http\Message\RequestFactoryInterface
 * @var $apiKey null|string API Key [Default: null - free plan]
 * @var $server string endpoint domain [Default: api.sypexgeo.net]
 * @see \NuthouseCIS\IPLocation\Locators\SypexGeo\ApiServer
 * @link https://ipgeolocation.io/documentation/ip-geolocation-api.html
 */
$locator = new \NuthouseCIS\IPLocation\Locators\SypexGeo\SypexGeoApiAdapter(
    $client,
    $requestFactory,
    $server,
    $apiKey
);
$location = $locator->locate(new \NuthouseCIS\IPLocation\Ip('8.8.8.8'));
```
### IP2Location
#### Database file
[IP2Location Documentation]
```php
/**
 * @var $filePath string path to IP2Location database file
 * @link https://www.ip2location.com/database/ip2location
 * @link https://lite.ip2location.com/database/ip-country
 */
$db = new \IP2Location\Database($filePath);
/**
 * @var $fields int[] Fields to return
 * @var $requiredFields int[] Required fields. Throws exception, if field empty or not supported
 * If null, copy values from $fields param
 */
$locator = new \NuthouseCIS\IPLocation\Locators\Ip2Location\Ip2LocationAdapter(
    $db,
    $fields,
    $requiredFields
);
$location = $locator->locate(new \NuthouseCIS\IPLocation\Ip('8.8.8.8'));
```
#### API
[IP2Location API Documentation]
```php
/** 
 * @var $client \Psr\Http\Client\ClientInterface
 * @var $requestFactory \Psr\Http\Message\RequestFactoryInterface
 * @var $apiKey string API Key [Default: null - free plan]
 * @var $lang string 
 * @var $package string Web service package of different granularity of return information.
 * Available values: [WS1, ..., WS24]
 * @var $addons string[]  Extra information in addition to the above selected package.
 * Valid value: continent,country,region,city,geotargeting,country_groupings,time_zone_info
 * @var $baseUrl string Endpoint for API [Default: https://api.ip2location.com/v2/]
 * @link https://ipgeolocation.io/documentation/ip-geolocation-api.html
 */
$locator = new \NuthouseCIS\IPLocation\Locators\Ip2Location\Ip2LocationApiAdapter(
    $client,
    $requestFactory,
    $apiKey,
    $package,
    $lang,
    $addons,
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
[IP2Location Documentation]: https://github.com/chrislim2888/IP2Location-PHP-Module
[IP2Location API Documentation]: https://www.ip2location.com/docs/ws1-user-manual.pdf
[SypexGeo Documentation]: https://sypexgeo.net/ru/docs/
[SypexGeo REST API Documentation]: https://sypexgeo.net/ru/api/