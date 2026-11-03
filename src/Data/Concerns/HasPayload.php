<?php

namespace SmartDato\AsendiaSync\Data\Concerns;

/**
 * Optional fields are null on the DTOs; the API gets them left out instead.
 */
trait HasPayload
{
    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return self::withoutNullValues($this->toArray());
    }

    /**
     * @param  array<array-key, mixed>  $values
     * @return array<array-key, mixed>
     */
    private static function withoutNullValues(array $values): array
    {
        $filtered = [];

        foreach ($values as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            $filtered[$key] = is_array($value) ? self::withoutNullValues($value) : $value;
        }

        return array_is_list($values) ? array_values($filtered) : $filtered;
    }
}
