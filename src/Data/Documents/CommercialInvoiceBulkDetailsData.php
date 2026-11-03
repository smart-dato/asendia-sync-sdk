<?php

namespace SmartDato\AsendiaSync\Data\Documents;

use SmartDato\AsendiaSync\Data\Shared\AddressData;
use Spatie\LaravelData\Data;

class CommercialInvoiceBulkDetailsData extends Data
{
    public function __construct(
        public readonly AddressData $sender,
        public readonly AddressData $receiver,
        public readonly AddressData $seller,
        public readonly AddressData $importer,
        public readonly ?string $senderTaxId = null,
        public readonly ?string $zazAccountNumber = null,
        public readonly ?string $uidChTaxNumber = null,
    ) {}
}
