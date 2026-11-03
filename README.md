# Asendia Sync SDK for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smart-dato/asendia-sync-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/asendia-sync-sdk)
[![Tests](https://github.com/smart-dato/asendia-sync-sdk/actions/workflows/run-tests.yml/badge.svg)](https://github.com/smart-dato/asendia-sync-sdk/actions/workflows/run-tests.yml)

A [Saloon](https://docs.saloon.dev) SDK for the [Asendia Sync API](https://www.asendia-sync.com/swagger-ui/index.html):
parcels and their labels, manifests, tracking, PUDO points, commercial invoices and customs documents.
Payloads and responses are [spatie/laravel-data](https://spatie.be/docs/laravel-data) DTOs.

## Installation

```bash
composer require smart-dato/asendia-sync-sdk
php artisan vendor:publish --tag="asendia-sync-sdk-config"
```

```dotenv
ASENDIA_SYNC_USERNAME=
ASENDIA_SYNC_PASSWORD=
ASENDIA_SYNC_CUSTOMER_ID=
```

The API login is the Asendia Sync portal login. `customer_id` is the default for tracking and the
parcel/manifest lists; `AsendiaSync::customers()->all()` lists the ids a login may use.

## Authentication

The SDK logs in with `POST /api/authenticate` and sends the JWT as bearer token. Asendia issues it
for 24 hours; it is cached per login (`cache_store`, default store) until five minutes before its
`exp` claim. A request answered with 401 drops the cached token, logs in again and is retried once.

## Usage

Through the facade (config credentials) or per account:

```php
use SmartDato\AsendiaSync\AsendiaSync;

$asendia = AsendiaSync::make([
    'username' => 'user',
    'password' => 'secret',
    'customer_id' => 'IT19050010',
    // 'base_url' => 'https://www.asendia-sync.com',
    // 'cache' => Cache::store('redis'),
]);
```

### Parcels

```php
use SmartDato\AsendiaSync\Data\Parcels\{AddressesData, AsendiaServiceData, CustomsInfoData, CustomsItemData, ParcelRequestData};
use SmartDato\AsendiaSync\Data\Shared\AddressData;
use SmartDato\AsendiaSync\Enums\{LabelType, Product, ReasonForExport, Service, ServiceOption};

$parcel = $asendia->parcels()->create(new ParcelRequestData(
    referenceNumber: 'OLC-1001',
    weight: 1.25, // kg, including packaging
    labelType: LabelType::Pdf,
    asendiaService: new AsendiaServiceData(
        product: Product::EpaqSelect,
        service: Service::CustomsPrepaidByRetailer,
        options: [ServiceOption::SmartgateDirect],
    ),
    addresses: new AddressesData(
        receiver: new AddressData(name: 'Peter Baer', address1: 'Bahnhofstrasse 1', postalCode: '3011', city: 'Bern', country: 'CH'),
    ),
    customsInfo: new CustomsInfoData(currency: 'EUR', items: [
        new CustomsItemData(articleDescription: 'Cotton T-shirt', quantity: 2, unitValue: 19.9, unitWeight: 0.2, harmonizationCode: '610910', originCountry: 'IT'),
    ]),
    reasonForExport: ReasonForExport::Merchandise,
));

if ($parcel->hasErrors()) {
    // Asendia accepted the parcel (202) but reports failed checks in errorMessage / errorMessages
}

$label = $asendia->parcels()->label($parcel->id); // DocumentData: contents(), contentType
$asendia->parcels()->delete($parcel->id);         // cancels the label, the parcel is not billed
```

`update()`, `get()`, `returnLabel()`, `customsDocument()`, `commercialInvoice()` and `list()` work alike.
Unset optional fields are left out of the payload. `ParcelFormat` (B/N/L/XL) is chosen by the shipper;
e-PAQ Select and Elite may omit it.

### Manifests, tracking, PUDO, documents

```php
$manifest = $asendia->manifests()->create([$parcel->id]);
$asendia->manifests()->createForTrackingNumbers(['99CH123456789012']);
$asendia->manifests()->document($manifest->id);

$events = $asendia->tracking()->get('99CH123456789012');           // list<TrackingEventData>
$trackings = $asendia->tracking()->many($trackingNumbers);          // batches of 100
$events[0]->milestone();                                            // ?TrackingEventCode

$asendia->pudo()->byAddress(new PudoSearchAddressData(...), [new PudoServiceData('SUP00138')]);
$asendia->pudo()->byCountry('FR', pageNumber: 1, pageSize: 50);

$asendia->documents()->createCommercialInvoice(new CommercialInvoiceData(...)); // PDF
$asendia->documents()->uploadCustomsDocument(new CustomsDocumentData(...), $pdf);
```

Lists page from 0 (`page`, `size`); the total is in `lastResponse->header('X-Total-Count')`.
PUDO searches page from 1.

### Errors

Failed requests throw `AsendiaSyncApiException` (a Saloon `RequestException`), or one of
`ValidationException` (400, with `fieldErrors` and, for manifests, `alreadyManifestedParcels`),
`NotFoundException` (404) and `RateLimitExceededException` (429). The message lists the field errors.

Not covered: `POST /api/logtrade/transport-instructions`, which is meant for LogTrade.

## Testing

```bash
composer test
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
