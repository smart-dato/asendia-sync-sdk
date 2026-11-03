<?php

namespace SmartDato\AsendiaSync\Data\Manifests;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class ManifestData extends Data
{
    /**
     * @param  list<string>  $errorParcelIds
     */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $status = null,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d\TH:i:s.uP', 'Y-m-d\TH:i:sP'])]
        public readonly ?CarbonImmutable $createdAt = null,
        public readonly ?string $errorMessage = null,
        public readonly array $errorParcelIds = [],
        public readonly ?string $manifestDocumentLocation = null,
        public readonly ?string $parcelsLocation = null,
        public readonly ?string $manifestLocation = null,
    ) {}
}
