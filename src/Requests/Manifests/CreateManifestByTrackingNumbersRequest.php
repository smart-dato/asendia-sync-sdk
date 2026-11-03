<?php

namespace SmartDato\AsendiaSync\Requests\Manifests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * All tracking numbers must belong to existing parcels of one customer; a
 * single unknown number rejects the whole request.
 */
class CreateManifestByTrackingNumbersRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<string>  $trackingNumbers
     */
    public function __construct(
        protected readonly array $trackingNumbers,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/manifests/tracking-numbers';
    }

    protected function defaultBody(): array
    {
        return $this->trackingNumbers;
    }
}
