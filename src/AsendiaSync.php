<?php

namespace SmartDato\AsendiaSync;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use SmartDato\AsendiaSync\Auth\AsendiaSyncAuthenticator;
use SmartDato\AsendiaSync\Connectors\AsendiaSyncConnector;
use SmartDato\AsendiaSync\Resources\CustomersResource;
use SmartDato\AsendiaSync\Resources\DocumentsResource;
use SmartDato\AsendiaSync\Resources\ManifestsResource;
use SmartDato\AsendiaSync\Resources\ParcelsResource;
use SmartDato\AsendiaSync\Resources\PudoResource;
use SmartDato\AsendiaSync\Resources\TrackingResource;

class AsendiaSync
{
    private ?ParcelsResource $parcelsResource = null;

    private ?ManifestsResource $manifestsResource = null;

    private ?TrackingResource $trackingResource = null;

    private ?CustomersResource $customersResource = null;

    private ?PudoResource $pudoResource = null;

    private ?DocumentsResource $documentsResource = null;

    /**
     * @param  ?string  $customerId  the default for tracking and the parcel/manifest lists
     */
    public function __construct(
        protected AsendiaSyncConnector $connector,
        protected ?string $customerId = null,
    ) {}

    public function parcels(): ParcelsResource
    {
        return $this->parcelsResource ??= new ParcelsResource($this->connector, $this->customerId);
    }

    public function manifests(): ManifestsResource
    {
        return $this->manifestsResource ??= new ManifestsResource($this->connector, $this->customerId);
    }

    public function tracking(): TrackingResource
    {
        return $this->trackingResource ??= new TrackingResource($this->connector, $this->customerId);
    }

    public function customers(): CustomersResource
    {
        return $this->customersResource ??= new CustomersResource($this->connector, $this->customerId);
    }

    public function pudo(): PudoResource
    {
        return $this->pudoResource ??= new PudoResource($this->connector, $this->customerId);
    }

    public function documents(): DocumentsResource
    {
        return $this->documentsResource ??= new DocumentsResource($this->connector, $this->customerId);
    }

    public function connector(): AsendiaSyncConnector
    {
        return $this->connector;
    }

    /**
     * @param array{
     *     username: string,
     *     password: string,
     *     customer_id?: string|null,
     *     base_url?: string|null,
     *     cache?: Repository|null,
     * } $config
     */
    public static function make(array $config): self
    {
        $authenticator = new AsendiaSyncAuthenticator(
            username: $config['username'],
            password: $config['password'],
            cache: $config['cache'] ?? Cache::store(),
        );

        return new self(
            connector: new AsendiaSyncConnector(
                asendiaSyncAuthenticator: $authenticator,
                baseUrl: $config['base_url'] ?? AsendiaSyncConnector::PRODUCTION_URL,
            ),
            customerId: $config['customer_id'] ?? null,
        );
    }
}
