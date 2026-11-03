<?php

namespace SmartDato\AsendiaSync\Data\Pudo;

use SmartDato\AsendiaSync\Data\Concerns\HasPayload;
use Spatie\LaravelData\Data;

class PudoSearchAddressData extends Data
{
    use HasPayload;

    public function __construct(
        public readonly string $countryCode,
        public readonly string $zipCode,
        public readonly string $city,
        public readonly string $street,
        public readonly string $streetNumber,
        public readonly int $radius,
        public readonly ?string $state = null,
        public readonly ?string $address2 = null,
        public readonly ?string $address3 = null,
        public readonly ?string $suburb = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}
}
