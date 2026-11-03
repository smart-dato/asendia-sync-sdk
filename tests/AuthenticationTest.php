<?php

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\AsendiaSync;
use SmartDato\AsendiaSync\Exceptions\AsendiaSyncApiException;
use SmartDato\AsendiaSync\Requests\Auth\AuthenticateRequest;
use SmartDato\AsendiaSync\Requests\Customers\ListCustomersRequest;

afterEach(function () {
    MockClient::destroyGlobal();
});

it('logs in once and sends the token as bearer', function () {
    $token = asendiaJwt(now()->addDay()->getTimestamp());

    $client = asendiaClient([
        AuthenticateRequest::class => asendiaLoginResponse($token),
        ListCustomersRequest::class => MockResponse::make([['id' => 'IT19050010', 'name' => 'Omest']]),
    ]);

    $client->customers()->all();
    $client->customers()->all();

    $mockClient = $client->connector()->getMockClient();

    $mockClient->assertSentCount(1, AuthenticateRequest::class);
    $mockClient->assertSent(fn (Request $request): bool => $request instanceof AuthenticateRequest
        && $request->body()->all() === ['username' => 'omestsrl', 'password' => 'secret']);

    expect($mockClient->getLastPendingRequest()->headers()->get('Authorization'))->toBe("Bearer {$token}");
});

it('shares the token through the cache', function () {
    asendiaClient([ListCustomersRequest::class => MockResponse::make([])])->customers()->all();

    $secondClient = asendiaClient([ListCustomersRequest::class => MockResponse::make([])]);
    $secondClient->customers()->all();

    $secondClient->connector()->getMockClient()->assertNotSent(AuthenticateRequest::class);
});

it('logs in again shortly before the token expires', function () {
    $client = asendiaClient([ListCustomersRequest::class => MockResponse::make([])]);

    $client->customers()->all();

    $this->travel(23)->hours();
    $client->customers()->all();
    $client->connector()->getMockClient()->assertSentCount(1, AuthenticateRequest::class);

    $this->travel(56)->minutes();
    $client->customers()->all();
    $client->connector()->getMockClient()->assertSentCount(2, AuthenticateRequest::class);
});

it('retries once with a new token when the api rejects the cached one', function () {
    $rejectedFirstCall = false;

    $client = asendiaClient([
        ListCustomersRequest::class => function (PendingRequest $pendingRequest) use (&$rejectedFirstCall): MockResponse {
            if (! $rejectedFirstCall) {
                $rejectedFirstCall = true;

                return MockResponse::make(['title' => 'Unauthorized', 'status' => 'UNAUTHORIZED'], 401);
            }

            return MockResponse::make([['id' => 'IT19050010', 'name' => 'Omest']]);
        },
    ]);

    expect($client->customers()->all()[0]->id)->toBe('IT19050010');

    $client->connector()->getMockClient()->assertSentCount(2, AuthenticateRequest::class);
});

it('does not retry a failed login', function () {
    $client = asendiaClient([
        AuthenticateRequest::class => MockResponse::make(['title' => 'Unauthorized', 'status' => 'UNAUTHORIZED', 'detail' => 'Bad credentials'], 401),
    ]);

    expect(fn () => $client->customers()->all())
        ->toThrow(AsendiaSyncApiException::class, 'Unauthorized: Bad credentials');

    $client->connector()->getMockClient()->assertSentCount(1, AuthenticateRequest::class);
});

it('builds the client from the package config', function () {
    config()->set('asendia-sync-sdk.username', 'config-user');
    config()->set('asendia-sync-sdk.password', 'config-password');
    config()->set('asendia-sync-sdk.customer_id', 'IT19050010');
    config()->set('asendia-sync-sdk.base_url', 'https://asendia.config.test');

    $mockClient = MockClient::global([
        AuthenticateRequest::class => asendiaLoginResponse(),
        '*' => MockResponse::make([]),
    ]);

    app(AsendiaSync::class)->tracking()->get('99CH123456789012');

    expect($mockClient->getLastPendingRequest()->getUrl())
        ->toBe('https://asendia.config.test/api/customers/IT19050010/tracking/99CH123456789012');

    $mockClient->assertSent(fn (Request $request): bool => $request instanceof AuthenticateRequest
        && $request->body()->get('username') === 'config-user');
});

it('defaults to the production url and the default cache store', function () {
    $mockClient = MockClient::global([
        AuthenticateRequest::class => asendiaLoginResponse(),
        '*' => MockResponse::make([]),
    ]);

    AsendiaSync::make(['username' => 'omestsrl', 'password' => 'secret'])->customers()->all();

    expect($mockClient->getLastPendingRequest()->getUrl())->toBe('https://www.asendia-sync.com/api/customers')
        ->and(Cache::store()->get('asendia-sync:token:omestsrl:'.substr(hash('sha256', 'secret'), 0, 16)))->toBeArray();
});
