<?php

namespace SmartDato\AsendiaSync\Data\Errors;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class AlreadyManifestedParcelData extends Data
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $trackingNumber = null,
        #[MapName('referencenumber')]
        public readonly ?string $referenceNumber = null,
    ) {}
}
