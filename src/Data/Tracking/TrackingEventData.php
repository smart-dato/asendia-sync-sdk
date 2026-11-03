<?php

namespace SmartDato\AsendiaSync\Data\Tracking;

use Carbon\CarbonImmutable;
use SmartDato\AsendiaSync\Enums\TrackingEventCode;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class TrackingEventData extends Data
{
    public function __construct(
        public readonly ?string $code = null,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d\TH:i:s.uP', 'Y-m-d\TH:i:sP'])]
        public readonly ?CarbonImmutable $time = null,
        public readonly ?string $locationName = null,
        public readonly ?string $locationCountry = null,
        public readonly ?string $carrierEventDescription = null,
        public readonly ?int $id = null,
    ) {}

    public function milestone(): ?TrackingEventCode
    {
        return is_null($this->code) ? null : TrackingEventCode::tryFrom($this->code);
    }
}
