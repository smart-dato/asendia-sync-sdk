<?php

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\Data\Pudo\PudoSearchAddressData;
use SmartDato\AsendiaSync\Data\Pudo\PudoServiceData;
use SmartDato\AsendiaSync\Requests\Pudo\FindPudoPointsByAddressRequest;
use SmartDato\AsendiaSync\Requests\Pudo\FindPudoPointsByCountryRequest;

it('finds pudo points around an address', function () {
    $client = asendiaClient([
        FindPudoPointsByAddressRequest::class => MockResponse::make(fixtureJson('pudo-points')),
    ]);

    $result = $client->pudo()->byAddress(
        new PudoSearchAddressData(countryCode: 'FR', zipCode: '75007', city: 'Paris', street: 'Av. de la Bourdonnais', streetNumber: '14', radius: 5),
        [new PudoServiceData('SUP00138')],
    );

    $point = $result->pudoPoints[0];

    expect($point->pudoId)->toBe('FR075007A')
        ->and($point->addressDetail->city)->toBe('Paris')
        ->and($point->workHours->mo)->toBe(['09:00-12:30', '14:00-19:00'])
        ->and($point->distance)->toBe(0.21)
        ->and($result->requestedLocation->radius)->toBe(5);

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof FindPudoPointsByAddressRequest
        && $request->body()->all() === [
            'address' => ['countryCode' => 'FR', 'zipCode' => '75007', 'city' => 'Paris', 'street' => 'Av. de la Bourdonnais', 'streetNumber' => '14', 'radius' => 5],
            'servicesList' => [['supplierId' => 'SUP00138']],
        ]);
});

it('pages pudo points of a country', function () {
    $client = asendiaClient([
        FindPudoPointsByCountryRequest::class => MockResponse::make(fixtureJson('pudo-points')),
    ]);

    $client->pudo()->byCountry('fr', [new PudoServiceData('SUP00138', 'SS0000944')], pageNumber: 2, pageSize: 50);

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof FindPudoPointsByCountryRequest
        && $request->resolveEndpoint() === '/api/pudo/points-by-country/FR'
        && $request->query()->all() === ['pageNumber' => 2, 'pageSize' => 50]
        && $request->body()->all() === ['servicesList' => [['supplierId' => 'SUP00138', 'serviceCode' => 'SS0000944']]]);
});
