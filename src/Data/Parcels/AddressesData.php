<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use SmartDato\AsendiaSync\Data\Shared\AddressData;
use Spatie\LaravelData\Data;

class AddressesData extends Data
{
    public function __construct(
        public readonly AddressData $receiver,
        public readonly ?AddressData $sender = null,
        public readonly ?AddressData $importer = null,
        public readonly ?AddressData $seller = null,
        public readonly ?PudoAddressData $pudoAddress = null,
    ) {}
}
