<?php

namespace SmartDato\AsendiaSync\Exceptions;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;
use SmartDato\AsendiaSync\Data\Errors\AlreadyManifestedParcelData;
use SmartDato\AsendiaSync\Data\Errors\FieldErrorData;
use Throwable;

/**
 * Asendia Sync answers errors as problem details. Unlike the OpenAPI spec says,
 * `status` is the name of the status ("BAD_REQUEST"), not its code, and
 * bean validation failures come as `fieldErrors`. A manifest request with
 * parcels that were manifested separately carries an object as `detail`.
 */
class AsendiaSyncApiException extends RequestException
{
    /**
     * @param  list<FieldErrorData>  $fieldErrors
     * @param  list<AlreadyManifestedParcelData>  $alreadyManifestedParcels
     */
    final public function __construct(
        Response $response,
        string $message,
        public readonly ?string $title = null,
        public readonly ?string $detail = null,
        public readonly array $fieldErrors = [],
        public readonly array $alreadyManifestedParcels = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($response, $message, $response->status(), $previous);
    }

    public static function fromResponse(Response $response, ?Throwable $previous = null): static
    {
        try {
            $data = $response->json();
        } catch (Throwable) {
            $data = [];
        }

        $title = is_string($data['title'] ?? null) ? $data['title'] : null;
        $detail = $data['detail'] ?? null;
        $alreadyManifestedParcels = [];

        if (is_array($detail)) {
            $alreadyManifestedParcels = array_values(AlreadyManifestedParcelData::collect(is_array($detail['list'] ?? null) ? $detail['list'] : []));
            $detail = $detail['message'] ?? null;
        }

        $detail = is_string($detail) ? $detail : null;

        $fieldErrors = array_values(FieldErrorData::collect(array_merge(
            is_array($data['fieldErrors'] ?? null) ? $data['fieldErrors'] : [],
            is_array($data['violations'] ?? null) ? $data['violations'] : [],
        )));

        return new static(
            response: $response,
            message: self::message($response, $title, $detail, $fieldErrors),
            title: $title,
            detail: $detail,
            fieldErrors: $fieldErrors,
            alreadyManifestedParcels: $alreadyManifestedParcels,
            previous: $previous,
        );
    }

    /**
     * @param  list<FieldErrorData>  $fieldErrors
     */
    private static function message(Response $response, ?string $title, ?string $detail, array $fieldErrors): string
    {
        $message = $title ?? "Asendia Sync API error: {$response->status()}";

        if (! is_null($detail)) {
            $message = "{$message}: {$detail}";
        }

        if ($fieldErrors === []) {
            return $message;
        }

        $fields = implode('; ', array_map(
            fn (FieldErrorData $error): string => "{$error->field}: {$error->message}",
            $fieldErrors,
        ));

        return "{$message} ({$fields})";
    }
}
