<?php

declare(strict_types=1);

namespace Azura\MetadataManager;

use JsonSerializable;

interface MetadataInterface extends JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    public function getTags(): array;

    /**
     * @param array<string, mixed> $tags
     */
    public function setTags(array $tags): void;

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addTag(string $key, $value): void;

    /**
     * @return float
     */
    public function getDuration(): float;

    /**
     * @param float $duration
     */
    public function setDuration(float $duration): void;

    /**
     * @return string|null
     */
    public function getArtwork(): ?string;

    public function setArtwork(?string $artwork): void;

    public function getMimeType(): string;

    public function setMimeType(string $mimeType): void;

    public function jsonSerialize(): array;

    public static function fromJson(array $data): self;
}
