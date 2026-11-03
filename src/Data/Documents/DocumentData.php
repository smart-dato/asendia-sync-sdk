<?php

namespace SmartDato\AsendiaSync\Data\Documents;

use Spatie\LaravelData\Data;

/**
 * A downloaded label or document. The content is base64 encoded so the DTO
 * stays serializable; contents() returns the raw bytes.
 */
class DocumentData extends Data
{
    public function __construct(
        public readonly string $content,
        public readonly string $contentType,
    ) {}

    public static function fromBinary(string $binary, string $contentType): self
    {
        return new self(base64_encode($binary), $contentType);
    }

    public function contents(): string
    {
        return (string) base64_decode($this->content, true);
    }
}
