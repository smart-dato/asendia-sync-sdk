<?php

namespace SmartDato\AsendiaSync\Requests\Manifests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetManifestParcelsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $manifestId,
        protected readonly int $page = 0,
        protected readonly int $size = 20,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/manifests/'.rawurlencode($this->manifestId).'/parcels';
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => $this->page,
            'size' => $this->size,
        ];
    }
}
