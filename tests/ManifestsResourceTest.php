<?php

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\Exceptions\ValidationException;
use SmartDato\AsendiaSync\Requests\Manifests\CreateManifestRequest;
use SmartDato\AsendiaSync\Requests\Manifests\ListCustomerManifestsRequest;

it('creates a manifest for parcel ids', function () {
    $client = asendiaClient([
        CreateManifestRequest::class => MockResponse::make(fixtureJson('manifests')[0], 202),
    ]);

    $manifest = $client->manifests()->create(['3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13']);

    expect($manifest->id)->toBe('435f4f3e-7aaa-48e3-bc94-f91b3d86f8d3')
        ->and($manifest->status)->toBe('CREATING_MANIFEST')
        ->and($manifest->createdAt->toIso8601ZuluString('microsecond'))->toBe('2026-05-22T10:12:23.939705Z');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof CreateManifestRequest
        && $request->body()->all() === ['3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13']);
});

it('names the parcels that were manifested separately', function () {
    $client = asendiaClient([
        CreateManifestRequest::class => MockResponse::make(fixtureJson('already-manifested'), 400),
    ]);

    try {
        $client->manifests()->create(['3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13']);
        $this->fail('Expected a ValidationException.');
    } catch (ValidationException $exception) {
        expect($exception->detail)->toBe('Parcels were already manifested separately')
            ->and($exception->alreadyManifestedParcels[0]->trackingNumber)->toBe('99CH123456789012')
            ->and($exception->alreadyManifestedParcels[0]->referenceNumber)->toBe('OLC-1001');
    }
});

it('lists the manifests of the configured customer', function () {
    $client = asendiaClient([
        ListCustomerManifestsRequest::class => MockResponse::make(fixtureJson('manifests'), 200, ['X-Total-Count' => '7']),
    ]);

    $manifests = $client->manifests()->list(size: 2);

    expect($manifests)->toHaveCount(2)
        ->and($client->manifests()->lastResponse->header('X-Total-Count'))->toBe('7');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof ListCustomerManifestsRequest
        && $request->resolveEndpoint() === '/api/customers/IT19050010/manifests'
        && $request->query()->all() === ['page' => 0, 'size' => 2]);
});
