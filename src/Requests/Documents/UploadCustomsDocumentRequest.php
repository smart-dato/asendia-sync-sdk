<?php

namespace SmartDato\AsendiaSync\Requests\Documents;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasMultipartBody;
use SmartDato\AsendiaSync\Data\Documents\CustomsDocumentData;

/**
 * The metadata travels in the query string, the PDF as multipart `file`.
 */
class UploadCustomsDocumentRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly CustomsDocumentData $data,
        protected readonly string $pdf,
        protected readonly string $filename = 'customs-document.pdf',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/customs-documents';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toPayload();
    }

    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'file', value: $this->pdf, filename: $this->filename, headers: ['Content-Type' => 'application/pdf']),
        ];
    }
}
