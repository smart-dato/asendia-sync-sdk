<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use Spatie\LaravelData\Data;

/**
 * Asendia accepts a parcel with 202 and reports the checks it failed in
 * `errorMessage` / `errorMessages`; check hasErrors() before using it. The
 * `*Location` fields point at the documents, fetch them through the parcel id.
 */
class ParcelData extends Data
{
    /**
     * @param  list<ParcelErrorData>  $errorMessages
     */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $trackingNumber = null,
        public readonly ?string $returnTrackingNumber = null,
        public readonly ?string $errorMessage = null,
        public readonly array $errorMessages = [],
        public readonly ?string $labelLocation = null,
        public readonly ?string $returnLabelLocation = null,
        public readonly ?string $customsDocumentLocation = null,
        public readonly ?string $manifestLocation = null,
        public readonly ?string $commercialInvoiceLocation = null,
    ) {}

    public function hasErrors(): bool
    {
        return filled($this->errorMessage) || $this->errorMessages !== [];
    }
}
