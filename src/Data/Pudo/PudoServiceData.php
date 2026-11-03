<?php

namespace SmartDato\AsendiaSync\Data\Pudo;

use SmartDato\AsendiaSync\Data\Concerns\HasPayload;
use Spatie\LaravelData\Data;

/**
 * Narrows a PUDO search to the points of one supplier (e.g. "SUP00138"),
 * optionally of one of its services (e.g. "SS0000944").
 */
class PudoServiceData extends Data
{
    use HasPayload;

    public function __construct(
        public readonly string $supplierId,
        public readonly ?string $serviceCode = null,
    ) {}
}
