<?php

namespace SmartDato\AsendiaSync\Requests\Parcels;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\AsendiaSync\Data\Parcels\ParcelRequestData;

class CreateParcelRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly ParcelRequestData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/parcels';
    }

    protected function defaultBody(): array
    {
        return $this->data->toPayload();
    }
}
