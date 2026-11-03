<?php

namespace SmartDato\AsendiaSync\Data\Documents;

use Carbon\CarbonImmutable;
use SmartDato\AsendiaSync\Data\Concerns\HasPayload;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

/**
 * Metadata of a customs document (PDF) uploaded for the customer with the
 * given CRM id. `totalAmount` holds duties or taxes, depending on the type.
 */
class CustomsDocumentData extends Data
{
    use HasPayload;

    public function __construct(
        public readonly string $crmId,
        public readonly string $documentNumber,
        public readonly string $documentType,
        public readonly ?string $documentAccessCode = null,
        public readonly ?string $documentReferenceNumber = null,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public readonly ?CarbonImmutable $documentDate = null,
        public readonly ?float $totalAmount = null,
        public readonly ?string $importerTraderIdNumber = null,
        public readonly ?string $customsAccountNumber = null,
    ) {}
}
