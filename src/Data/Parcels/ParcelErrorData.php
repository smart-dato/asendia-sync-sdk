<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use Spatie\LaravelData\Data;

class ParcelErrorData extends Data
{
    public function __construct(
        public readonly ?string $field = null,
        public readonly ?string $message = null,
    ) {}
}
