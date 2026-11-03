<?php

namespace SmartDato\AsendiaSync\Connectors;

use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use SmartDato\AsendiaSync\Auth\AsendiaSyncAuthenticator;
use SmartDato\AsendiaSync\Exceptions\AsendiaSyncApiException;
use SmartDato\AsendiaSync\Exceptions\NotFoundException;
use SmartDato\AsendiaSync\Exceptions\RateLimitExceededException;
use SmartDato\AsendiaSync\Exceptions\ValidationException;
use SmartDato\AsendiaSync\Requests\Auth\AuthenticateRequest;
use Throwable;

class AsendiaSyncConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public const string PRODUCTION_URL = 'https://www.asendia-sync.com';

    /**
     * A second try lets a request recover from a cached token the API no
     * longer accepts, see handleRetry().
     */
    public ?int $tries = 2;

    public function __construct(
        protected AsendiaSyncAuthenticator $asendiaSyncAuthenticator,
        protected string $baseUrl = self::PRODUCTION_URL,
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function defaultAuth(): AsendiaSyncAuthenticator
    {
        return $this->asendiaSyncAuthenticator;
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return match ($response->status()) {
            400 => ValidationException::fromResponse($response, $senderException),
            404 => NotFoundException::fromResponse($response, $senderException),
            429 => RateLimitExceededException::fromResponse($response, $senderException),
            default => AsendiaSyncApiException::fromResponse($response, $senderException),
        };
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if (! $exception instanceof RequestException) {
            return false;
        }

        if ($exception->getResponse()->status() !== 401) {
            return false;
        }

        if ($exception->getPendingRequest()->getRequest() instanceof AuthenticateRequest) {
            return false;
        }

        $this->asendiaSyncAuthenticator->forgetToken();

        return true;
    }
}
