<?php

namespace SmartDato\AsendiaSync\Exceptions;

use LogicException;

class MissingCustomerIdException extends LogicException
{
    public static function forEndpoint(string $endpoint): self
    {
        return new self("Asendia Sync needs a customer id for {$endpoint}: pass one or set `customer_id`.");
    }
}
