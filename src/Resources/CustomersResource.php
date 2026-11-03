<?php

namespace SmartDato\AsendiaSync\Resources;

use SmartDato\AsendiaSync\Data\Customers\CustomerData;
use SmartDato\AsendiaSync\Requests\Customers\ListCustomersRequest;

class CustomersResource extends BaseResource
{
    /**
     * The customers the login may act for, e.g. [IT19050010 => Omest].
     *
     * @return list<CustomerData>
     */
    public function all(): array
    {
        return array_values(CustomerData::collect($this->send(new ListCustomersRequest)->json()));
    }
}
