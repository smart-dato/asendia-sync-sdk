<?php

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use SmartDato\AsendiaSync\AsendiaSync;
use SmartDato\AsendiaSync\Requests\Auth\AuthenticateRequest;
use SmartDato\AsendiaSync\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * @return array<array-key, mixed>
 */
function fixtureJson(string $name): array
{
    return json_decode((string) file_get_contents(__DIR__."/Fixtures/{$name}.json"), true, flags: JSON_THROW_ON_ERROR);
}

function asendiaJwt(int $expiresAt, string $subject = 'omestsrl'): string
{
    $encode = fn (array $part): string => rtrim(strtr(base64_encode((string) json_encode($part)), '+/', '-_'), '=');

    return "{$encode(['alg' => 'HS512'])}.{$encode(['sub' => $subject, 'auth' => 'ROLE_API,ROLE_USER', 'exp' => $expiresAt])}.signature";
}

function asendiaLoginResponse(?string $token = null): MockResponse
{
    return MockResponse::make(['id_token' => $token ?? asendiaJwt(now()->addDay()->getTimestamp())]);
}

/**
 * @param  array<string, MockResponse|callable>  $responses
 */
function asendiaClient(array $responses, ?string $customerId = 'IT19050010'): AsendiaSync
{
    $client = AsendiaSync::make([
        'username' => 'omestsrl',
        'password' => 'secret',
        'customer_id' => $customerId,
        'base_url' => 'https://asendia.test',
        'cache' => Cache::store('array'),
    ]);

    $client->connector()->withMockClient(new MockClient([
        AuthenticateRequest::class => asendiaLoginResponse(),
        ...$responses,
    ]));

    return $client;
}
