<?php

declare(strict_types=1);

namespace Azura\MetadataManager\Reader;

use Azura\MetadataManager\Exception\ReadException;
use Azura\MetadataManager\Metadata;
use Azura\MetadataManager\Utilities\Arrays;
use Azura\MetadataManager\Utilities\Time;
use JamesHeinrich\GetID3\GetID3;

use const JSON_THROW_ON_ERROR;

class GetId3Reader extends AbstractReader
{
    public static function read(
        string $path,
        string $jsonOutput,
        ?string $artOutput
    ): void {
        $id3 = new GetID3();

        $id3->option_md5_data = true;
        $id3->option_md5_data_source = true;
        $id3->encoding = 'UTF-8';

        $info = $id3->analyze($path);
        $id3->CopyTagsToComments($info);

        if (!empty($info['error'])) {
            throw new ReadException(
                sprintf(
                    'Cannot process media at path %s: %s',
                    pathinfo($path, PATHINFO_FILENAME),
                    json_encode($info['error'], JSON_THROW_ON_ERROR)
                )
            );
        }

        $metadata = new Metadata();

        if (is_numeric($info['playtime_seconds'])) {
            $metadata->setDuration(
                Time::displayTimeToSeconds($info['playtime_seconds']) ?? 0.0
            );
        }

        $toProcess = [
            $info['comments'] ?? null,
            $info['tags'] ?? null,
        ];

        $metaTags = self::aggregateMetaTags($toProcess);

        $metadata->setTags($metaTags);
        $metadata->setMimeType($info['mime_type']);

        file_put_contents(
            $jsonOutput,
            json_encode($metadata, JSON_THROW_ON_ERROR),
        );

        if (null !== $artOutput) {
            $artwork = null;
            if (!empty($info['attached_picture'][0])) {
                $artwork = $info['attached_picture'][0]['data'];
            } elseif (!empty($info['comments']['picture'][0])) {
                $artwork = $info['comments']['picture'][0]['data'];
            } elseif (!empty($info['id3v2']['APIC'][0]['data'])) {
                $artwork = $info['id3v2']['APIC'][0]['data'];
            } elseif (!empty($info['id3v2']['PIC'][0]['data'])) {
                $artwork = $info['id3v2']['PIC'][0]['data'];
            }

            if (!empty($artwork)) {
                file_put_contents(
                    $artOutput,
                    $artwork
                );
            }
        }
    }

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
}
