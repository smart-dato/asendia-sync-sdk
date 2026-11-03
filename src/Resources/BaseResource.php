<?php

namespace SmartDato\AsendiaSync\Resources;

use Saloon\Http\Request;
use Saloon\Http\Response;
use SmartDato\AsendiaSync\Connectors\AsendiaSyncConnector;
use SmartDato\AsendiaSync\Data\Documents\DocumentData;
use SmartDato\AsendiaSync\Exceptions\MissingCustomerIdException;

abstract class BaseResource
{
    public ?Request $lastRequest = null;

    public ?Response $lastResponse = null;

    public function __construct(
        protected AsendiaSyncConnector $connector,
        protected ?string $customerId = null,
    ) {}

    protected function send(Request $request): Response
    {
        $this->lastRequest = $request;
        $this->lastResponse = $this->connector->send($request);

        return $this->lastResponse;
    }

    protected function download(Request $request): DocumentData
    {
        $response = $this->send($request);

        return DocumentData::fromBinary($response->body(), (string) $response->header('Content-Type'));
    }

    protected function customerId(?string $customerId, string $endpoint): string
    {
        return $customerId ?? $this->customerId ?? throw MissingCustomerIdException::forEndpoint($endpoint);
    }
}
