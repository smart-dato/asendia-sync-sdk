<?php

namespace SmartDato\AsendiaSync\Resources;

use SmartDato\AsendiaSync\Data\Tracking\TrackingData;
use SmartDato\AsendiaSync\Data\Tracking\TrackingEventData;
use SmartDato\AsendiaSync\Requests\Tracking\GetMultipleTrackingRequest;
use SmartDato\AsendiaSync\Requests\Tracking\GetTrackingRequest;

/**
 * Events come in chronological order. Asendia can push the same events to a
 * webhook instead; they enable that per customer on request.
 */
class TrackingResource extends BaseResource
{
    /**
     * @return list<TrackingEventData>
     */
    public function get(string $trackingNumber, ?string $customerId = null): array
    {
        $response = $this->send(new GetTrackingRequest(
            customerId: $this->customerId($customerId, 'tracking'),
            trackingNumber: $trackingNumber,
        ));

        return array_values(TrackingEventData::collect($response->json()));
    }

    /**
     * Sends one request per 100 tracking numbers, the most the API takes.
     *
     * @param  list<string>  $trackingNumbers
     * @return list<TrackingData>
     */
    public function many(array $trackingNumbers, ?string $customerId = null): array
    {
        $customerId = $this->customerId($customerId, 'tracking');
        $trackings = [];

        foreach (array_chunk(array_values(array_unique($trackingNumbers)), GetMultipleTrackingRequest::MAX_TRACKING_NUMBERS) as $chunk) {
            $response = $this->send(new GetMultipleTrackingRequest($customerId, $chunk));

            array_push($trackings, ...TrackingData::collect($response->json()));
        }

        return $trackings;
    }
}
