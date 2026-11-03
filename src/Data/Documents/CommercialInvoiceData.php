<?php

namespace SmartDato\AsendiaSync\Data\Documents;

use SmartDato\AsendiaSync\Data\Concerns\HasPayload;
use SmartDato\AsendiaSync\Enums\Incoterm;
use SmartDato\AsendiaSync\Enums\ReasonOfExport;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

/**
 * The parcels must be manifested before Asendia creates their invoice.
 * Amounts (discount, freight, vat, duty, tobacco tax) are absolute values,
 * not percentages; vat, duty and tobacco tax are not needed for DAP.
 */
class CommercialInvoiceData extends Data
{
    use HasPayload;

    /**
     * @param  list<string>  $parcelIds
     */
    public function __construct(
        public readonly string $invoiceNumber,
        #[MapName('sellerEORI')]
        public readonly string $sellerEori,
        public readonly Incoterm $incoterm,
        public readonly ReasonOfExport $reasonOfExport,
        public readonly array $parcelIds,
        public readonly ?bool $proformaInvoice = null,
        public readonly ?string $poNumber = null,
        public readonly ?float $discountRebate = null,
        public readonly ?float $freightPostage = null,
        public readonly ?float $vat = null,
        public readonly ?float $tobaccoTax = null,
        public readonly ?float $duty = null,
        public readonly ?string $additionalComments = null,
        public readonly ?string $declarationStatement = null,
        public readonly ?bool $bulk = null,
        public readonly ?CommercialInvoiceBulkDetailsData $bulkDetails = null,
    ) {}
}
