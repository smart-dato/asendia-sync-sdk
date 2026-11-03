<?php

namespace SmartDato\AsendiaSync\Data\Pudo;

use Spatie\LaravelData\Data;

/**
 * The address of a PUDO point, and the location a search was made around.
 */
class PudoLocationData extends Data
{
    public function __construct(
        public readonly ?string $countryCode = null,
        public readonly ?string $state = null,
        public readonly ?string $zipCode = null,
        public readonly ?string $city = null,
        public readonly ?string $address1 = null,
        public readonly ?string $address2 = null,
        public readonly ?string $address3 = null,
        public readonly ?string $suburb = null,
        public readonly ?string $streetNumber = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?int $radius = null,
    ) {}
}
