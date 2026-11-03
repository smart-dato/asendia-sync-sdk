<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use Spatie\LaravelData\Data;

class PudoAddressData extends Data
{
    public function __construct(
        public readonly ?string $pudoId = null,
        public readonly ?string $address1 = null,
        public readonly ?string $address2 = null,
        public readonly ?string $address3 = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zipCode = null,
        public readonly ?string $countryCode = null,
    ) {}
}
