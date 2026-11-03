<?php

namespace SmartDato\AsendiaSync\Exceptions;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;
use SmartDato\AsendiaSync\Data\Errors\AlreadyManifestedParcelData;
use SmartDato\AsendiaSync\Data\Errors\FieldErrorData;
use Throwable;

/**
 * Asendia Sync answers errors as problem details. Unlike the OpenAPI spec says,
 * `status` is the name of the status ("BAD_REQUEST"), not its code. The
 * reasons come in one of three shapes:
 *
 * - bean validation: `fieldErrors` [{field, message, errorCode}]
 * - business validation: `validationErrors.fields.<field>.violations` [{message, errorCode}]
 * - carrier errors: `errors` [string] and `errorMessages` [{message}]; these
 *   come with the `id` of a parcel Asendia stored anyway, see $parcelId
 *
 * A manifest request with parcels that were manifested separately carries an
 * object as `detail`.
 */
class AsendiaSyncApiException extends RequestException
{
    /**
     * @param  list<FieldErrorData>  $fieldErrors
     * @param  list<AlreadyManifestedParcelData>  $alreadyManifestedParcels
     * @param  ?string  $parcelId  the parcel a rejected booking left behind; delete it, or it lingers in error state
     */
    final public function __construct(
        Response $response,
        string $message,
        public readonly ?string $title = null,
        public readonly ?string $detail = null,
        public readonly array $fieldErrors = [],
        public readonly array $alreadyManifestedParcels = [],
        public readonly ?string $parcelId = null,
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
            $alreadyManifestedParcels = array_values(AlreadyManifestedParcelData::collect(self::asArray($detail['list'] ?? null)));
            $detail = $detail['message'] ?? null;
        }

        $detail = is_string($detail) ? $detail : null;
        $fieldErrors = self::fieldErrors($data);

        return new static(
            response: $response,
            message: self::message($response, $title, $detail, $fieldErrors),
            title: $title,
            detail: $detail,
            fieldErrors: $fieldErrors,
            alreadyManifestedParcels: $alreadyManifestedParcels,
            parcelId: is_string($data['id'] ?? null) ? $data['id'] : null,
            previous: $previous,
        );
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return list<FieldErrorData>
     */
    private static function fieldErrors(array $data): array
    {
        $errors = [...self::asArray($data['fieldErrors'] ?? null), ...self::asArray($data['violations'] ?? null)];

        foreach (self::asArray($data['validationErrors']['fields'] ?? null) as $field => $fieldErrors) {
            foreach (self::asArray(is_array($fieldErrors) ? ($fieldErrors['violations'] ?? null) : null) as $violation) {
                $errors[] = ['field' => (string) $field, ...(is_array($violation) ? $violation : [])];
            }
        }

        $messages = [
            ...self::asArray($data['errors'] ?? null),
            ...array_map(fn (mixed $error): mixed => is_array($error) ? ($error['message'] ?? null) : null, self::asArray($data['errorMessages'] ?? null)),
        ];

        foreach (array_unique(array_filter($messages, is_string(...))) as $message) {
            $errors[] = ['message' => $message];
        }

        return array_values(FieldErrorData::collect($errors));
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

        $reasons = implode('; ', array_map(
            fn (FieldErrorData $error): string => is_null($error->field) ? (string) $error->message : "{$error->field}: {$error->message}",
            $fieldErrors,
        ));

        return "{$message} ({$reasons})";
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function asArray(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }
}
