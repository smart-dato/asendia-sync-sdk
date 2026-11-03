<?php

namespace SmartDato\AsendiaSync\Facades;

use Illuminate\Support\Facades\Facade;
use SmartDato\AsendiaSync\Resources\CustomersResource;
use SmartDato\AsendiaSync\Resources\DocumentsResource;
use SmartDato\AsendiaSync\Resources\ManifestsResource;
use SmartDato\AsendiaSync\Resources\ParcelsResource;
use SmartDato\AsendiaSync\Resources\PudoResource;
use SmartDato\AsendiaSync\Resources\TrackingResource;

/**
 * @method static ParcelsResource parcels()
 * @method static ManifestsResource manifests()
 * @method static TrackingResource tracking()
 * @method static CustomersResource customers()
 * @method static PudoResource pudo()
 * @method static DocumentsResource documents()
 *
 * @see \SmartDato\AsendiaSync\AsendiaSync
 */
class AsendiaSync extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\AsendiaSync\AsendiaSync::class;
    }
}
