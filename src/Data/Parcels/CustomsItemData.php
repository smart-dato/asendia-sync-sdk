<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use Spatie\LaravelData\Data;

/**
 * One article of the customs declaration. Shipments from outside the EU into
 * the EU worth EUR 150 or less need `mPid` and `nsPid`.
 */
class CustomsItemData extends Data
{
    public function __construct(
        public readonly ?string $articleDescription = null,
        public readonly ?int $quantity = null,
        public readonly ?float $unitValue = null,
        public readonly ?float $unitWeight = null,
        public readonly ?string $currency = null,
        public readonly ?string $harmonizationCode = null,
        public readonly ?string $originCountry = null,
        public readonly ?string $articleNumber = null,
        public readonly ?string $articleUrl = null,
        public readonly ?string $tradeItemNumber = null,
        public readonly ?string $articleComposition = null,
        public readonly ?string $mPid = null,
        public readonly ?string $nsPid = null,
        public readonly ?string $sPid = null,
        public readonly ?int $volatileOrganicCompoundInGrams = null,
        public readonly ?string $destinationTariffCode = null,
        public readonly ?string $certifierId = null,
        public readonly ?string $productId = null,
        public readonly ?string $certificateVersionId = null,
    ) {}
}
