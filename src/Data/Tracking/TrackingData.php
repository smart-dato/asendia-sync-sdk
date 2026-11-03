<?php

namespace SmartDato\AsendiaSync\Data\Tracking;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class TrackingData extends Data
{
    /**
     * @param  list<TrackingEventData>  $trackingEvents
     */
    public function __construct(
        public readonly ?string $trackingNumber = null,
        public readonly ?string $lastMileCarrier = null,
        #[MapInputName('lastMileCarrierURL')]
        public readonly ?string $lastMileCarrierUrl = null,
        public readonly array $trackingEvents = [],
    ) {}
}
