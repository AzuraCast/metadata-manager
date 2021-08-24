<?php

declare(strict_types=1);

namespace Azura\MetadataManager;

use JsonSerializable;

interface MetadataInterface extends JsonSerializable
{
    public function getTags(): array;

    public function setTags(array $tags): void;

    public function addTag(string $key, mixed $value): void;

    public function getDuration(): float;

    public function setDuration(float $duration): void;

    public function getArtwork(): ?string;

    public function setArtwork(?string $artwork): void;

    public function getMimeType(): string;

    public function setMimeType(string $mimeType): void;

    public function jsonSerialize(): array;

    public static function fromJson(array $data): self;
}
