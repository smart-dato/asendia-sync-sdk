<?php

namespace SmartDato\AsendiaSync\Requests\Parcels;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetParcelRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $parcelId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/parcels/'.rawurlencode($this->parcelId);
    }
}
