<?php

namespace SmartDato\AsendiaSync\Resources;

use SmartDato\AsendiaSync\Data\Documents\DocumentData;
use SmartDato\AsendiaSync\Data\Parcels\ParcelData;
use SmartDato\AsendiaSync\Data\Parcels\ParcelRequestData;
use SmartDato\AsendiaSync\Enums\ParcelDocument;
use SmartDato\AsendiaSync\Requests\Parcels\CreateParcelRequest;
use SmartDato\AsendiaSync\Requests\Parcels\DeleteParcelRequest;
use SmartDato\AsendiaSync\Requests\Parcels\GetParcelDocumentRequest;
use SmartDato\AsendiaSync\Requests\Parcels\GetParcelRequest;
use SmartDato\AsendiaSync\Requests\Parcels\ListCustomerParcelsRequest;
use SmartDato\AsendiaSync\Requests\Parcels\UpdateParcelRequest;

class ParcelsResource extends BaseResource
{
    public function create(ParcelRequestData $parcel): ParcelData
    {
        return ParcelData::from($this->send(new CreateParcelRequest($parcel))->json());
    }

    public function get(string $parcelId): ParcelData
    {
        return ParcelData::from($this->send(new GetParcelRequest($parcelId))->json());
    }

    public function update(string $parcelId, ParcelRequestData $parcel): ParcelData
    {
        return ParcelData::from($this->send(new UpdateParcelRequest($parcelId, $parcel))->json());
    }

    public function delete(string $parcelId): void
    {
        $this->send(new DeleteParcelRequest($parcelId));
    }

    public function label(string $parcelId): DocumentData
    {
        return $this->download(new GetParcelDocumentRequest($parcelId, ParcelDocument::Label));
    }

    public function returnLabel(string $parcelId): DocumentData
    {
        return $this->download(new GetParcelDocumentRequest($parcelId, ParcelDocument::ReturnLabel));
    }

    public function customsDocument(string $parcelId): DocumentData
    {
        return $this->download(new GetParcelDocumentRequest($parcelId, ParcelDocument::CustomsDocument));
    }

    public function commercialInvoice(string $parcelId): DocumentData
    {
        return $this->download(new GetParcelDocumentRequest($parcelId, ParcelDocument::CommercialInvoice));
    }

    /**
     * @return list<ParcelData>
     */
    public function list(bool $showManifested = false, int $page = 0, int $size = 20, ?string $customerId = null): array
    {
        $response = $this->send(new ListCustomerParcelsRequest(
            customerId: $this->customerId($customerId, 'listing parcels'),
            showManifested: $showManifested,
            page: $page,
            size: $size,
        ));

        return array_values(ParcelData::collect($response->json()));
    }
}
