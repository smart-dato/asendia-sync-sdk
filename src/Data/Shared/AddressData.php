<?php

namespace SmartDato\AsendiaSync\Data\Shared;

use Spatie\LaravelData\Data;

/**
 * A party of a parcel or of a commercial invoice. Commercial invoices ignore
 * `company` and `mobile`. Asendia caps name, company and the address lines at
 * 50 characters, the city at 30.
 */
class AddressData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $address1,
        public readonly string $postalCode,
        public readonly string $city,
        public readonly string $country,
        public readonly ?string $company = null,
        public readonly ?string $address2 = null,
        public readonly ?string $address3 = null,
        public readonly ?string $province = null,
        public readonly ?string $email = null,
        public readonly ?string $mobile = null,
        public readonly ?string $phone = null,
    ) {}
}
