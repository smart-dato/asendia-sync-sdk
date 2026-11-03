<?php

namespace SmartDato\AsendiaSync\Requests\Customers;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListCustomersRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/customers';
    }
}
