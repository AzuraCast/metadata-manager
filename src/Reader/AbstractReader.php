<?php

declare(strict_types=1);

namespace Azura\MetadataManager\Reader;

use voku\helper\UTF8;

abstract class AbstractReader implements ReaderInterface
{
    abstract public static function read(
        string $path,
        string $jsonOutput,
        ?string $artOutput
    ): void;

    protected static function cleanUpString(?string $original): string
    {
        $original ??= '';

        $string = UTF8::encode('UTF-8', $original);
        $string = UTF8::fix_simple_utf8($string);
        return UTF8::clean(
            $string,
            true,
            true,
            true,
            true,
            true
        );
    }
}
