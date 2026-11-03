<?php

namespace SmartDato\AsendiaSync\Requests\Manifests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Pages start at 0; the X-Total-Count header holds the number of manifests.
 */
class ListCustomerManifestsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $customerId,
        protected readonly int $page = 0,
        protected readonly int $size = 20,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/customers/'.rawurlencode($this->customerId).'/manifests';
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => $this->page,
            'size' => $this->size,
        ];
    }
}
