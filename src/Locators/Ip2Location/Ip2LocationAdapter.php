<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators\Ip2Location;

use IP2Location\Database;
use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locator;
use Webmozart\Assert\Assert;

class Ip2LocationAdapter implements Locator
{
    protected const IMPORTANT_FIELDS = [
        Database::COUNTRY_CODE,
        Database::COUNTRY_NAME,
        Database::CITY_NAME,
        Database::REGION_NAME,
        Database::LATITUDE,
        Database::LONGITUDE,
    ];
    protected Database $database;
    /** @var int[] */
    protected array $fields;
    private array $requiredFields;

    /**
     * Ip2LocationAdapter constructor.
     *
     * @param Database $database
     * @param int[] $fields
     * @param int[]|null $requiredFields
     */
    public function __construct(
        Database $database,
        array $fields = [
            Database::COUNTRY,
            Database::REGION_NAME,
            Database::CITY_NAME,
            Database::COORDINATES,
        ],
        ?array $requiredFields = null
    ) {
        $this->database = $database;
        $this->fields = static::filterFields($fields);

        if ($requiredFields === null) {
            $this->requiredFields = $this->fields;
        } else {
            $this->requiredFields = static::filterFields($requiredFields);
        }
    }

    /**
     * @inheritDoc
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public function locate(Ip $ip): ?Location
    {
        /**
         * @psalm-var mixed|array<int, mixed> $data
         */
        $data = $this->database->lookup((string)$ip, $this->fields, false);

        if (is_array($data)) {
            $this->checkRequiredFields($data);
            $data = $this->filter($data);
            if (
                isset($data[Database::COUNTRY_CODE])
                || isset($data[Database::COUNTRY_NAME])
            ) {
                $region = $city = $coordinates = null;
                if (
                    !empty($data[Database::LATITUDE])
                    && !empty($data[Database::LONGITUDE])
                ) {
                    $coordinates = new Coordinates(
                        (float)$data[Database::LATITUDE],
                        (float)$data[Database::LONGITUDE]
                    );
                    unset($data[Database::LATITUDE], $data[Database::LONGITUDE]);
                }
                if (!empty($data[Database::CITY_NAME])) {
                    $city = new City($data[Database::CITY_NAME], $coordinates);
                    unset($data[Database::CITY_NAME]);
                    $coordinates = null;
                }
                if (!empty($data[Database::REGION_NAME])) {
                    $region = new Region(
                        $data[Database::REGION_NAME],
                        null,
                        $coordinates
                    );
                    unset($data[Database::REGION_NAME]);
                    $coordinates = null;
                }
                $country = new Country(
                    $data[Database::COUNTRY_CODE] ?? null,
                    null,
                    $data[Database::COUNTRY_NAME] ?? null,
                    $coordinates
                );
                unset($data[Database::COUNTRY_CODE], $data[Database::COUNTRY_NAME]);

                return new Location($country, $region, $city, $data);
            }
        }

        return null;
    }

    /**
     * @param int[] $fields
     *
     * @return int[]
     */
    protected static function filterFields(array $fields): array
    {
        Assert::allInteger($fields);
        if (in_array(Database::ALL, $fields, true)) {
            $fields[] = Database::REGION_NAME;
            $fields[] = Database::CITY_NAME;
            $fields[] = Database::ISP;
            $fields[] = Database::DOMAIN_NAME;
            $fields[] = Database::ZIP_CODE;
            $fields[] = Database::TIME_ZONE;
            $fields[] = Database::NET_SPEED;
            $fields[] = Database::ELEVATION;
            $fields[] = Database::USAGE_TYPE;

            $fields[] = Database::COUNTRY;
            $fields[] = Database::COORDINATES;
            $fields[] = Database::IDD_AREA;
            $fields[] = Database::WEATHER_STATION;
            $fields[] = Database::MCC_MNC_MOBILE_CARRIER_NAME;

            $fields[] = Database::IP_ADDRESS;
            $fields[] = Database::IP_VERSION;
            $fields[] = Database::IP_NUMBER;
        }
        $filteredFields = [];
        foreach ($fields as $field) {
            switch ($field) {
                case Database::COUNTRY:
                    if (!in_array(Database::COUNTRY_NAME, $filteredFields, true)) {
                        $filteredFields[] = Database::COUNTRY_NAME;
                    }
                    if (!in_array(Database::COUNTRY_CODE, $filteredFields, true)) {
                        $filteredFields[] = Database::COUNTRY_CODE;
                    }
                    break;
                case Database::COORDINATES:
                    if (!in_array(Database::LATITUDE, $filteredFields, true)) {
                        $filteredFields[] = Database::LATITUDE;
                    }
                    if (!in_array(Database::LONGITUDE, $filteredFields, true)) {
                        $filteredFields[] = Database::LONGITUDE;
                    }
                    break;
                case Database::IDD_AREA:
                    if (!in_array(Database::IDD_CODE, $filteredFields, true)) {
                        $filteredFields[] = Database::IDD_CODE;
                    }
                    if (!in_array(Database::AREA_CODE, $filteredFields, true)) {
                        $filteredFields[] = Database::AREA_CODE;
                    }
                    break;
                case Database::WEATHER_STATION:
                    if (!in_array(Database::WEATHER_STATION_CODE, $filteredFields, true)) {
                        $filteredFields[] = Database::WEATHER_STATION_CODE;
                    }
                    if (!in_array(Database::WEATHER_STATION_NAME, $filteredFields, true)) {
                        $filteredFields[] = Database::WEATHER_STATION_NAME;
                    }
                    break;
                case Database::MCC_MNC_MOBILE_CARRIER_NAME:
                    if (!in_array(Database::MCC, $filteredFields, true)) {
                        $filteredFields[] = Database::MCC;
                    }
                    if (!in_array(Database::MNC, $filteredFields, true)) {
                        $filteredFields[] = Database::MNC;
                    }
                    if (!in_array(Database::MOBILE_CARRIER_NAME, $filteredFields, true)) {
                        $filteredFields[] = Database::MOBILE_CARRIER_NAME;
                    }
                    break;

                case Database::IP_ADDRESS:
                    if (!in_array(Database::IP_ADDRESS, $filteredFields, true)) {
                        $filteredFields[] = Database::IP_ADDRESS;
                    }
                    break;
                case Database::IP_VERSION:
                    if (!in_array(Database::IP_VERSION, $filteredFields, true)) {
                        $filteredFields[] = Database::IP_VERSION;
                    }
                    break;
                case Database::IP_NUMBER:
                    if (!in_array(Database::IP_NUMBER, $filteredFields, true)) {
                        $filteredFields[] = Database::IP_NUMBER;
                    }
                    break;
                default:
                    if (!in_array($field, $filteredFields, true)) {
                        $filteredFields[] = $field;
                    }
                    break;
            }
        }

        return $filteredFields;
    }

    /**
     * @param array $data
     *
     * @psalm-param array<int, mixed> $data
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress MixedArgument
     */
    protected function checkRequiredFields(array $data): void
    {
        foreach ($this->requiredFields as $key) {
            if (!isset($data[$key])) {
                throw new IPLocationException(
                    sprintf('Field \'%s\' is required', $key)
                );
            } elseif ($data[$key] === Database::FIELD_NOT_SUPPORTED || $data[$key] === Database::FIELD_NOT_KNOWN) {
                throw new IPLocationException(
                    sprintf('Required field \'%s\' has invalid value: %s', $key, $data[$key])
                );
            }
        }
    }

    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     */
    protected function filter(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value === null || $value === '-') {
                unset($data[$key]);
            }
            if (in_array($key, static::IMPORTANT_FIELDS, true) && $value === Database::FIELD_NOT_SUPPORTED) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
