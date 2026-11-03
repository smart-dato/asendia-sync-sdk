<?php

namespace SmartDato\AsendiaSync\Requests\Parcels;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Cancels the labels at the carrier and deletes the parcel, so it is not
 * billed. Deleting a deleted parcel answers 204 as well.
 */
class DeleteParcelRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected readonly string $parcelId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/parcels/'.rawurlencode($this->parcelId);
    }
}
