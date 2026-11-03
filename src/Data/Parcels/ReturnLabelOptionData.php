<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use SmartDato\AsendiaSync\Enums\ReturnLabelType;
use SmartDato\AsendiaSync\Enums\ReturnPayment;
use Spatie\LaravelData\Data;

class ReturnLabelOptionData extends Data
{
    public function __construct(
        public readonly bool $enabled,
        public readonly ?ReturnLabelType $type = null,
        public readonly ?ReturnPayment $payment = null,
    ) {}
}
