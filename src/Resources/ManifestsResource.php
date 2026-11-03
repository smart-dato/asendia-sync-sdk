<?php

namespace SmartDato\AsendiaSync\Resources;

use SmartDato\AsendiaSync\Data\Documents\DocumentData;
use SmartDato\AsendiaSync\Data\Manifests\ManifestData;
use SmartDato\AsendiaSync\Data\Parcels\ParcelData;
use SmartDato\AsendiaSync\Requests\Manifests\CreateManifestByTrackingNumbersRequest;
use SmartDato\AsendiaSync\Requests\Manifests\CreateManifestRequest;
use SmartDato\AsendiaSync\Requests\Manifests\GetManifestDocumentRequest;
use SmartDato\AsendiaSync\Requests\Manifests\GetManifestParcelsRequest;
use SmartDato\AsendiaSync\Requests\Manifests\GetManifestRequest;
use SmartDato\AsendiaSync\Requests\Manifests\ListCustomerManifestsRequest;
use SmartDato\AsendiaSync\Requests\Manifests\RecreateManifestRequest;

/**
 * Manifesting at Asendia is optional for e-PAQ to Switzerland: Asendia
 * manifests to Swiss Post itself once it scanned the parcels in Tribiano.
 */
class ManifestsResource extends BaseResource
{
    /**
     * @param  list<string>  $parcelIds
     */
    public function create(array $parcelIds): ManifestData
    {
        return ManifestData::from($this->send(new CreateManifestRequest($parcelIds))->json());
    }

    /**
     * @param  list<string>  $trackingNumbers
     */
    public function createForTrackingNumbers(array $trackingNumbers): ManifestData
    {
        return ManifestData::from($this->send(new CreateManifestByTrackingNumbersRequest($trackingNumbers))->json());
    }

    public function get(string $manifestId): ManifestData
    {
        return ManifestData::from($this->send(new GetManifestRequest($manifestId))->json());
    }

    public function recreate(string $manifestId): ManifestData
    {
        return ManifestData::from($this->send(new RecreateManifestRequest($manifestId))->json());
    }

    /**
     * @return list<ParcelData>
     */
    public function parcels(string $manifestId, int $page = 0, int $size = 20): array
    {
        return array_values(ParcelData::collect($this->send(new GetManifestParcelsRequest($manifestId, $page, $size))->json()));
    }

    public function document(string $manifestId): DocumentData
    {
        return $this->download(new GetManifestDocumentRequest($manifestId));
    }

    /**
     * @return list<ManifestData>
     */
    public function list(int $page = 0, int $size = 20, ?string $customerId = null): array
    {
        $response = $this->send(new ListCustomerManifestsRequest(
            customerId: $this->customerId($customerId, 'listing manifests'),
            page: $page,
            size: $size,
        ));

        return array_values(ManifestData::collect($response->json()));
    }
}
