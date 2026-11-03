<?php

namespace SmartDato\AsendiaSync\Requests\Pudo;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\AsendiaSync\Data\Pudo\PudoSearchAddressData;
use SmartDato\AsendiaSync\Data\Pudo\PudoServiceData;

class FindPudoPointsByAddressRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<PudoServiceData>  $services
     */
    public function __construct(
        protected readonly PudoSearchAddressData $address,
        protected readonly array $services = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/pudo/points-by-address';
    }

    protected function defaultBody(): array
    {
        return [
            'address' => $this->address->toPayload(),
            'servicesList' => array_map(fn (PudoServiceData $service): array => $service->toPayload(), $this->services),
        ];
    }
}
