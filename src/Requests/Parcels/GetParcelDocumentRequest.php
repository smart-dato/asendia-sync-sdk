<?php

namespace SmartDato\AsendiaSync\Requests\Parcels;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\Enums\ParcelDocument;

/**
 * Labels come as PDF, PNG or ZPL (text/plain), as the parcel's labelType says.
 */
class GetParcelDocumentRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $parcelId,
        protected readonly ParcelDocument $document,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/parcels/'.rawurlencode($this->parcelId)."/{$this->document->value}";
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/pdf, image/*, text/plain, application/problem+json',
        ];
    }
}
