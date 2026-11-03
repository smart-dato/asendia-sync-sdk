<?php

namespace SmartDato\AsendiaSync\Data\Pudo;

use Spatie\LaravelData\Data;

class PudoPointsData extends Data
{
    /**
     * @param  list<PudoPointData>  $pudoPoints
     * @param  array<string, mixed>  $additionalData
     */
    public function __construct(
        public readonly array $pudoPoints = [],
        public readonly ?PudoLocationData $requestedLocation = null,
        public readonly ?string $status = null,
        public readonly ?int $code = null,
        public readonly ?string $transactionKey = null,
        public readonly array $additionalData = [],
    ) {}
}
