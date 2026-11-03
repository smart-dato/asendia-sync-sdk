<?php

namespace SmartDato\AsendiaSync\Requests\Auth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Auth\NullAuthenticator;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AuthenticateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $username,
        protected readonly string $password,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/authenticate';
    }

    /**
     * The login itself must not ask the connector's authenticator for a token.
     */
    protected function defaultAuth(): NullAuthenticator
    {
        return new NullAuthenticator;
    }

    protected function defaultBody(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}
