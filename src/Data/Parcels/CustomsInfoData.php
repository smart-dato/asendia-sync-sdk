<?php

namespace SmartDato\AsendiaSync\Data\Parcels;

use Spatie\LaravelData\Data;

class CustomsInfoData extends Data
{
    /**
     * @param  list<CustomsItemData>  $items
     */
    public function __construct(
        public readonly ?string $currency = null,
        public readonly array $items = [],
    ) {}
}
