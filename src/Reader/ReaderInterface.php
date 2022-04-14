<?php

declare(strict_types=1);

namespace Azura\MetadataManager\Reader;

interface ReaderInterface
{
    public static function read(
        string $path,
        string $jsonOutput,
        ?string $artOutput
    ): void;
}
