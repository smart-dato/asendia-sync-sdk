<?php

use Carbon\CarbonImmutable;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\Data\Documents\CommercialInvoiceData;
use SmartDato\AsendiaSync\Data\Documents\CustomsDocumentData;
use SmartDato\AsendiaSync\Enums\Incoterm;
use SmartDato\AsendiaSync\Enums\ReasonOfExport;
use SmartDato\AsendiaSync\Requests\Documents\CreateCommercialInvoiceRequest;
use SmartDato\AsendiaSync\Requests\Documents\UploadCustomsDocumentRequest;

it('creates a commercial invoice pdf', function () {
    $client = asendiaClient([
        CreateCommercialInvoiceRequest::class => MockResponse::make('%PDF-1.4 invoice', 200, ['Content-Type' => 'application/pdf']),
    ]);

    $invoice = $client->documents()->createCommercialInvoice(new CommercialInvoiceData(
        invoiceNumber: 'A-1234',
        sellerEori: 'IT12345678901',
        incoterm: Incoterm::Ddp,
        reasonOfExport: ReasonOfExport::SaleOfGoods,
        parcelIds: ['3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13'],
        vat: 8.1,
    ));

    expect($invoice->contents())->toBe('%PDF-1.4 invoice');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof CreateCommercialInvoiceRequest
        && $request->body()->all() === [
            'invoiceNumber' => 'A-1234',
            'sellerEORI' => 'IT12345678901',
            'incoterm' => 'DDP',
            'reasonOfExport' => 'SALE_OF_GOODS_PERMANENT',
            'parcelIds' => ['3f1c2a9e-8d4b-4c6e-9a51-2b7d0e6f4a13'],
            'vat' => 8.1,
        ]);
});

it('uploads a customs document with its metadata in the query', function () {
    $client = asendiaClient([
        UploadCustomsDocumentRequest::class => MockResponse::make('', 201),
    ]);

    $client->documents()->uploadCustomsDocument(new CustomsDocumentData(
        crmId: 'IT19050010',
        documentNumber: '26CH0001234',
        documentType: 'IMPORT_DECLARATION',
        documentDate: CarbonImmutable::parse('2026-11-03'),
        totalAmount: 12.5,
    ), '%PDF-1.4 customs');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request instanceof UploadCustomsDocumentRequest
        && $request->query()->all() === [
            'crmId' => 'IT19050010',
            'documentNumber' => '26CH0001234',
            'documentType' => 'IMPORT_DECLARATION',
            'documentDate' => '2026-11-03',
            'totalAmount' => 12.5,
        ]
        && $request->body()->get('file')->value === '%PDF-1.4 customs');
});
