<?php

namespace SmartDato\AsendiaSync\Requests\Pudo;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\AsendiaSync\Data\Pudo\PudoServiceData;

/**
 * Pages start at 1 here, unlike the parcel and manifest lists.
 */
class FindPudoPointsByCountryRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<PudoServiceData>  $services
     */
    public function __construct(
        protected readonly string $countryCode,
        protected readonly array $services = [],
        protected readonly int $pageNumber = 1,
        protected readonly int $pageSize = 5,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/pudo/points-by-country/'.rawurlencode(strtoupper($this->countryCode));
    }

    protected function defaultQuery(): array
    {
        return [
            'pageNumber' => $this->pageNumber,
            'pageSize' => $this->pageSize,
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'servicesList' => array_map(fn (PudoServiceData $service): array => $service->toPayload(), $this->services),
        ];
    }
}
