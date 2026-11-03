<?php

namespace SmartDato\AsendiaSync\Requests\Documents;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\AsendiaSync\Data\Documents\CommercialInvoiceData;

/**
 * Answers with the invoice PDF.
 */
class CreateCommercialInvoiceRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly CommercialInvoiceData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/commercial-invoice';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/pdf, application/problem+json',
        ];
    }

    protected function defaultBody(): array
    {
        return $this->data->toPayload();
    }
}
