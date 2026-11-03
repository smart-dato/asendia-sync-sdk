<?php

namespace SmartDato\AsendiaSync\Requests\Manifests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class RecreateManifestRequest extends Request
{
    protected Method $method = Method::PUT;

    public function __construct(
        protected readonly string $manifestId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/manifests/'.rawurlencode($this->manifestId);
    }
}
