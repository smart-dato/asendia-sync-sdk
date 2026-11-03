<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use SmartDato\AsendiaSync\Enums\Insurance;
use SmartDato\AsendiaSync\Enums\ParcelFormat;
use SmartDato\AsendiaSync\Enums\Product;
use SmartDato\AsendiaSync\Enums\Service;
use SmartDato\AsendiaSync\Enums\ServiceOption;
use Spatie\LaravelData\Data;

class AsendiaServiceData extends Data
{
    /**
     * @param  list<ServiceOption>  $options
     */
    public function __construct(
        public readonly Product $product,
        public readonly Service $service,
        public readonly ?ParcelFormat $format = null,
        public readonly array $options = [],
        public readonly ?Insurance $insurance = null,
        public readonly ?ReturnLabelOptionData $returnLabelOption = null,
    ) {}
}
