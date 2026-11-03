<?php

namespace SmartDato\AsendiaSync\Requests\Manifests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * 202 starts a new manifest, 200 means all parcels were manifested together
 * already and returns that manifest.
 */
class CreateManifestRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<string>  $parcelIds
     */
    public function __construct(
        protected readonly array $parcelIds,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/manifests';
    }

    protected function defaultBody(): array
    {
        return $this->parcelIds;
    }
}
