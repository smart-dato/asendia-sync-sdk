<?php

namespace SmartDato\AsendiaSync\Requests\Tracking;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetTrackingRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $customerId,
        protected readonly string $trackingNumber,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/customers/'.rawurlencode($this->customerId).'/tracking/'.rawurlencode($this->trackingNumber);
    }
}
