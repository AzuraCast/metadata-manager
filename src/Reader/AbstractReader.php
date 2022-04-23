<?php

declare(strict_types=1);

namespace Azura\MetadataManager\Reader;

use Azura\MetadataManager\Utilities\Arrays;
use voku\helper\UTF8;

abstract class AbstractReader implements ReaderInterface
{
    abstract public static function read(
        string $path,
        string $jsonOutput,
        ?string $artOutput
    ): void;

    protected static function aggregateMetaTags(array $toProcess): array
    {
        $metaTags = [];

        foreach ($toProcess as $tagSet) {
            if (empty($tagSet)) {
                continue;
            }

            foreach ($tagSet as $tagName => $tagContents) {
                if (!empty($tagContents[0]) && !isset($metaTags[$tagName])) {
                    $tagValue = $tagContents[0];
                    if (is_array($tagValue)) {
                        // Skip pictures
                        if (isset($tagValue['data'])) {
                            continue;
                        }
                        $flatValue = Arrays::flattenArray($tagValue);
                        $tagValue = implode(', ', $flatValue);
                    }

                    $metaTags[(string)$tagName] = self::cleanUpString((string)$tagValue);
                }
            }
        }

        return $metaTags;
    }

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
