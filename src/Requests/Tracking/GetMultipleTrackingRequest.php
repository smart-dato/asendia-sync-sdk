<?php

namespace SmartDato\AsendiaSync\Requests\Tracking;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * At most 100 tracking numbers per call; one unknown number answers 404.
 */
class GetMultipleTrackingRequest extends Request implements HasBody
{
    use HasJsonBody;

    public const int MAX_TRACKING_NUMBERS = 100;

    protected Method $method = Method::POST;

    /**
     * @param  list<string>  $trackingNumbers
     */
    public function __construct(
        protected readonly string $customerId,
        protected readonly array $trackingNumbers,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/customers/'.rawurlencode($this->customerId).'/tracking';
    }

    protected function defaultBody(): array
    {
        return $this->trackingNumbers;
    }
}
