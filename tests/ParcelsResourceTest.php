<?php

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\Data\Parcels\AddressesData;
use SmartDato\AsendiaSync\Data\Parcels\AsendiaServiceData;
use SmartDato\AsendiaSync\Data\Parcels\CustomsInfoData;
use SmartDato\AsendiaSync\Data\Parcels\CustomsItemData;
use SmartDato\AsendiaSync\Data\Parcels\ParcelRequestData;
use SmartDato\AsendiaSync\Data\Shared\AddressData;
use SmartDato\AsendiaSync\Enums\LabelType;
use SmartDato\AsendiaSync\Enums\Product;
use SmartDato\AsendiaSync\Enums\ReasonForExport;
use SmartDato\AsendiaSync\Enums\Service;
use SmartDato\AsendiaSync\Enums\ServiceOption;
use SmartDato\AsendiaSync\Exceptions\MissingCustomerIdException;
use SmartDato\AsendiaSync\Exceptions\ValidationException;
use SmartDato\AsendiaSync\Requests\Parcels\CreateParcelRequest;
use SmartDato\AsendiaSync\Requests\Parcels\DeleteParcelRequest;
use SmartDato\AsendiaSync\Requests\Parcels\GetParcelDocumentRequest;
use SmartDato\AsendiaSync\Requests\Parcels\ListCustomerParcelsRequest;
use SmartDato\AsendiaSync\Requests\Parcels\UpdateParcelRequest;

function swissParcel(): ParcelRequestData
{
    return new ParcelRequestData(
        referenceNumber: 'OLC-1001',
        weight: 1.25,
        labelType: LabelType::Pdf,
        asendiaService: new AsendiaServiceData(
            product: Product::EpaqSelect,
            service: Service::CustomsPrepaidByRetailer,
            options: [ServiceOption::SmartgateDirect],
        ),
        addresses: new AddressesData(
            receiver: new AddressData(
                name: 'Peter Baer',
                address1: 'Bahnhofstrasse 1',
                postalCode: '3011',
                city: 'Bern',
                country: 'CH',
                email: 'pbaer@example.ch',
            ),
        ),
        customsInfo: new CustomsInfoData(
            currency: 'EUR',
            items: [new CustomsItemData(
                articleDescription: 'Cotton T-shirt',
                quantity: 2,
                unitValue: 19.9,
                unitWeight: 0.2,
                harmonizationCode: '610910',
                originCountry: 'IT',
            )],
        ),
        reasonForExport: ReasonForExport::Merchandise,
    );
}

it('creates a parcel and leaves unset fields out of the payload', function () {
    $client = asendiaClient([
        CreateParcelRequest::class => MockResponse::make(fixtureJson('parcel'), 202),
    ]);

    $parcel = $client->parcels()->create(swissParcel());

    expect($parcel->id)->toBe('3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13')
        ->and($parcel->trackingNumber)->toBe('99CH123456789012')
        ->and($parcel->hasErrors())->toBeFalse();

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof CreateParcelRequest
        && json_encode($request->body()->all()) === json_encode([
            'referencenumber' => 'OLC-1001',
            'weight' => 1.25,
            'labelType' => 'PDF',
            'asendiaService' => [
                'product' => 'EPAQSCT',
                'service' => 'CPPR',
                'options' => ['SGD'],
            ],
            'addresses' => [
                'receiver' => [
                    'name' => 'Peter Baer',
                    'address1' => 'Bahnhofstrasse 1',
                    'postalCode' => '3011',
                    'city' => 'Bern',
                    'country' => 'CH',
                    'email' => 'pbaer@example.ch',
                ],
            ],
            'customsInfo' => [
                'currency' => 'EUR',
                'items' => [[
                    'articleDescription' => 'Cotton T-shirt',
                    'quantity' => 2,
                    'unitValue' => 19.9,
                    'unitWeight' => 0.2,
                    'harmonizationCode' => '610910',
                    'originCountry' => 'IT',
                ]],
            ],
            'reasonForExport' => 'M',
        ]));
});

it('reports the checks an accepted parcel failed', function () {
    $client = asendiaClient([
        CreateParcelRequest::class => MockResponse::make(fixtureJson('parcel-with-errors'), 202),
    ]);

    $parcel = $client->parcels()->create(swissParcel());

    expect($parcel->hasErrors())->toBeTrue()
        ->and($parcel->errorMessage)->toBe('Parcel could not be labelled')
        ->and($parcel->errorMessages[0]->field)->toBe('addresses.receiver.postalCode');
});

it('throws the field errors of a rejected parcel', function () {
    $client = asendiaClient([
        CreateParcelRequest::class => MockResponse::make(fixtureJson('validation-error'), 400, ['Content-Type' => 'application/problem+json']),
    ]);

    try {
        $client->parcels()->create(swissParcel());
        $this->fail('Expected a ValidationException.');
    } catch (ValidationException $exception) {
        expect($exception->getCode())->toBe(400)
            ->and($exception->fieldErrors)->toHaveCount(6)
            ->and($exception->fieldErrors[0]->field)->toBe('customsInfo')
            ->and($exception->fieldErrors[0]->errorCode)->toBe('VE-004')
            ->and($exception->getMessage())->toStartWith('Method argument not valid (customsInfo: Mandatory field; labelType: Mandatory field;');
    }
});

it('names the parcel a carrier error left behind', function () {
    $client = asendiaClient([
        CreateParcelRequest::class => MockResponse::make(fixtureJson('carrier-error'), 400),
    ]);

    try {
        $client->parcels()->create(swissParcel());
        $this->fail('Expected a ValidationException.');
    } catch (ValidationException $exception) {
        expect($exception->getMessage())->toBe('Bad Request (Unknown Carrier Error)')
            ->and($exception->parcelId)->toBe('69d32c8c-ebb3-465c-b403-0a562076df2b')
            ->and($exception->fieldErrors)->toHaveCount(1);
    }
});

it('reads the business validation errors', function () {
    $client = asendiaClient([
        CreateParcelRequest::class => MockResponse::make(fixtureJson('business-validation-error'), 400),
    ]);

    try {
        $client->parcels()->create(swissParcel());
        $this->fail('Expected a ValidationException.');
    } catch (ValidationException $exception) {
        expect($exception->getMessage())->toBe('Bad Request (options: Invalid options)')
            ->and($exception->fieldErrors[0]->errorCode)->toBe('VE-013')
            ->and($exception->parcelId)->toBeNull();
    }
});

it('reads the customs item violations and fills their message templates', function () {
    $client = asendiaClient([
        CreateParcelRequest::class => MockResponse::make(fixtureJson('order-line-validation-error'), 400),
    ]);

    try {
        $client->parcels()->create(swissParcel());
        $this->fail('Expected a ValidationException.');
    } catch (ValidationException $exception) {
        expect($exception->getMessage())->toBe('Bad Request (orderLines[0].weight: Value must be greater than 0.001 (got 0))')
            ->and($exception->fieldErrors[0]->field)->toBe('orderLines[0].weight')
            ->and($exception->fieldErrors[0]->errorCode)->toBe('VE-001');
    }
});

it('updates a parcel', function () {
    $client = asendiaClient([
        UpdateParcelRequest::class => MockResponse::make(fixtureJson('parcel'), 202),
    ]);

    $client->parcels()->update('3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13', swissParcel());

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof UpdateParcelRequest
        && $request->resolveEndpoint() === '/api/parcels/3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13'
        && $request->body()->get('referencenumber') === 'OLC-1001');
});

it('deletes a parcel', function () {
    $client = asendiaClient([
        DeleteParcelRequest::class => MockResponse::make('', 204),
    ]);

    $client->parcels()->delete('3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof DeleteParcelRequest
        && $request->resolveEndpoint() === '/api/parcels/3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13');
});

it('downloads the label', function () {
    $client = asendiaClient([
        GetParcelDocumentRequest::class => MockResponse::make('%PDF-1.4 label', 200, ['Content-Type' => 'application/pdf']),
    ]);

    $label = $client->parcels()->label('3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13');

    expect($label->contents())->toBe('%PDF-1.4 label')
        ->and($label->contentType)->toBe('application/pdf');

    $pendingRequest = $client->connector()->getMockClient()->getLastPendingRequest();

    expect($pendingRequest->getUrl())->toBe('https://asendia.test/api/parcels/3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13/label')
        ->and($pendingRequest->headers()->get('Accept'))->toStartWith('application/pdf');
});

it('lists the parcels of the configured customer', function () {
    $client = asendiaClient([
        ListCustomerParcelsRequest::class => MockResponse::make([fixtureJson('parcel')]),
    ]);

    $parcels = $client->parcels()->list(showManifested: true, page: 1, size: 50);

    expect($parcels)->toHaveCount(1)
        ->and($parcels[0]->trackingNumber)->toBe('99CH123456789012');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof ListCustomerParcelsRequest
        && $request->resolveEndpoint() === '/api/customers/IT19050010/parcels'
        && $request->query()->all() === ['showManifested' => 'true', 'page' => 1, 'size' => 50]);
});

it('needs a customer id to list parcels', function () {
    $client = asendiaClient([], customerId: null);

    expect(fn () => $client->parcels()->list())->toThrow(MissingCustomerIdException::class);
});
