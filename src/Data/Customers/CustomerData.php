<?php

namespace SmartDato\AsendiaSync\Data\Customers;

use Spatie\LaravelData\Data;

class CustomerData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
    ) {}
}
