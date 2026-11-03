<?php

namespace SmartDato\AsendiaSync\Requests\Parcels;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Pages start at 0; the X-Total-Count header holds the number of parcels.
 */
class ListCustomerParcelsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $customerId,
        protected readonly bool $showManifested = false,
        protected readonly int $page = 0,
        protected readonly int $size = 20,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/customers/'.rawurlencode($this->customerId).'/parcels';
    }

    protected function defaultQuery(): array
    {
        return [
            'showManifested' => $this->showManifested ? 'true' : 'false',
            'page' => $this->page,
            'size' => $this->size,
        ];
    }
}
