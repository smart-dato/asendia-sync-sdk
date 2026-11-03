<?php

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use SmartDato\AsendiaSync\Enums\TrackingEventCode;
use SmartDato\AsendiaSync\Exceptions\RateLimitExceededException;
use SmartDato\AsendiaSync\Requests\Tracking\GetMultipleTrackingRequest;
use SmartDato\AsendiaSync\Requests\Tracking\GetTrackingRequest;

it('gets the events of a tracking number', function () {
    $client = asendiaClient([
        GetTrackingRequest::class => MockResponse::make(fixtureJson('tracking-events')),
    ]);

    $events = $client->tracking()->get('99CH123456789012');

    expect($events)->toHaveCount(3)
        ->and($events[0]->milestone())->toBe(TrackingEventCode::ElectronicNotification)
        ->and($events[0]->time->toIso8601ZuluString())->toBe('2026-11-02T08:15:00Z')
        ->and($events[1]->code)->toBe('CUSTOMSCLEARED')
        ->and($events[1]->milestone())->toBeNull()
        ->and($events[2]->milestone())->toBe(TrackingEventCode::Delivered)
        ->and($events[2]->locationName)->toBe('Bern');

    $client->connector()->getMockClient()->assertSent(fn (Request $request): bool => $request->resolveEndpoint() === '/api/customers/IT19050010/tracking/99CH123456789012');
});

it('tracks many numbers in batches of 100', function () {
    $client = asendiaClient([
        GetMultipleTrackingRequest::class => fn (PendingRequest $pendingRequest): MockResponse => MockResponse::make(array_map(
            fn (string $trackingNumber): array => ['trackingNumber' => $trackingNumber, 'lastMileCarrier' => 'Swiss Post', 'lastMileCarrierURL' => 'https://service.post.ch', 'trackingEvents' => fixtureJson('tracking-events')],
            $pendingRequest->body()->all(),
        )),
    ]);

    $trackingNumbers = array_map(fn (int $number): string => "99CH{$number}", range(1, 150));

    $trackings = $client->tracking()->many($trackingNumbers);

    expect($trackings)->toHaveCount(150)
        ->and($trackings[149]->trackingNumber)->toBe('99CH150')
        ->and($trackings[0]->lastMileCarrierUrl)->toBe('https://service.post.ch')
        ->and($trackings[0]->trackingEvents)->toHaveCount(3);

    $client->connector()->getMockClient()->assertSentCount(2, GetMultipleTrackingRequest::class);
});

it('throws a rate limit exception on 429', function () {
    $client = asendiaClient([
        GetTrackingRequest::class => MockResponse::make(['title' => 'Too Many Requests', 'status' => 'TOO_MANY_REQUESTS'], 429),
    ]);

    expect(fn () => $client->tracking()->get('99CH123456789012'))->toThrow(RateLimitExceededException::class, 'Too Many Requests');
});
