<?php

namespace SmartDato\AsendiaSync\Resources;

use SmartDato\AsendiaSync\Data\Pudo\PudoPointsData;
use SmartDato\AsendiaSync\Data\Pudo\PudoSearchAddressData;
use SmartDato\AsendiaSync\Data\Pudo\PudoServiceData;
use SmartDato\AsendiaSync\Requests\Pudo\FindPudoPointsByAddressRequest;
use SmartDato\AsendiaSync\Requests\Pudo\FindPudoPointsByCountryRequest;

class PudoResource extends BaseResource
{
    /**
     * @param  list<PudoServiceData>  $services
     */
    public function byCountry(string $countryCode, array $services = [], int $pageNumber = 1, int $pageSize = 5): PudoPointsData
    {
        $response = $this->send(new FindPudoPointsByCountryRequest($countryCode, $services, $pageNumber, $pageSize));

        return PudoPointsData::from($response->json());
    }

    /**
     * @param  list<PudoServiceData>  $services
     */
    public function byAddress(PudoSearchAddressData $address, array $services = []): PudoPointsData
    {
        return PudoPointsData::from($this->send(new FindPudoPointsByAddressRequest($address, $services))->json());
    }
}
