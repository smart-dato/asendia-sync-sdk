<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use SmartDato\AsendiaSync\Data\Concerns\HasPayload;
use SmartDato\AsendiaSync\Enums\LabelRotation;
use SmartDato\AsendiaSync\Enums\LabelType;
use SmartDato\AsendiaSync\Enums\ReasonForExport;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

/**
 * The body of POST /api/parcels and PUT /api/parcels/{id}. `weight` is the
 * total in kg including packaging. `sequenceNumber` is only for customers
 * with their own tracking number range.
 */
class ParcelRequestData extends Data
{
    use HasPayload;

    public function __construct(
        #[MapName('referencenumber')]
        public readonly string $referenceNumber,
        public readonly float $weight,
        public readonly LabelType $labelType,
        public readonly AsendiaServiceData $asendiaService,
        public readonly AddressesData $addresses,
        public readonly CustomsInfoData $customsInfo,
        public readonly ?string $customerId = null,
        public readonly ?LabelRotation $labelRotation = null,
        #[MapName('sequencenumber')]
        public readonly ?string $sequenceNumber = null,
        #[MapName('senderEORI')]
        public readonly ?string $senderEori = null,
        #[MapName('sellerEORI')]
        public readonly ?string $sellerEori = null,
        #[MapName('euDDSReferenceNumber')]
        public readonly ?string $euDdsReferenceNumber = null,
        public readonly ?string $senderTaxId = null,
        public readonly ?string $receiverTaxId = null,
        public readonly ?float $shippingCost = null,
        public readonly ?ReasonForExport $reasonForExport = null,
    ) {}
}
