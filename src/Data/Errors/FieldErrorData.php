<?php

namespace SmartDato\AsendiaSync\Data\Errors;

use Spatie\LaravelData\Data;

class FieldErrorData extends Data
{
    public function __construct(
        public readonly ?string $field = null,
        public readonly ?string $message = null,
        public readonly ?string $objectName = null,
        public readonly ?string $errorCode = null,
    ) {}
}
