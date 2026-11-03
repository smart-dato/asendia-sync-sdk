<?php

namespace SmartDato\AsendiaSync\Requests\Manifests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetManifestDocumentRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $manifestId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/manifests/'.rawurlencode($this->manifestId).'/document';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/pdf, application/problem+json',
        ];
    }
}
