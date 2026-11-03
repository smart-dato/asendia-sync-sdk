<?php

namespace SmartDato\AsendiaSync\Data\Pudo;

use Spatie\LaravelData\Data;

class PudoWorkHoursData extends Data
{
    /**
     * @param  list<string>  $mo
     * @param  list<string>  $tu
     * @param  list<string>  $we
     * @param  list<string>  $th
     * @param  list<string>  $fr
     * @param  list<string>  $sa
     * @param  list<string>  $su
     */
    public function __construct(
        public readonly array $mo = [],
        public readonly array $tu = [],
        public readonly array $we = [],
        public readonly array $th = [],
        public readonly array $fr = [],
        public readonly array $sa = [],
        public readonly array $su = [],
    ) {}
}
