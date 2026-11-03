<?php

namespace SmartDato\AsendiaSync\Data\Pudo;

use Spatie\LaravelData\Data;

class PudoPointData extends Data
{
    public function __construct(
        public readonly ?string $pudoId = null,
        public readonly ?string $asendiaPudoPointId = null,
        public readonly ?string $supplierId = null,
        public readonly ?string $title = null,
        public readonly ?string $pudoType = null,
        public readonly ?string $mappedType = null,
        public readonly ?PudoLocationData $addressDetail = null,
        public readonly ?PudoWorkHoursData $workHours = null,
        public readonly ?float $distance = null,
    ) {}
}
