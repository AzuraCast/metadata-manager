<?php

declare(strict_types=1);

namespace Azura\MetadataManager\Reader;

use Azura\MetadataManager\Metadata;
use Azura\MetadataManager\Utilities\Arrays;
use Azura\MetadataManager\Utilities\Time;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\Stream;

use const JSON_THROW_ON_ERROR;

class FfmpegReader extends AbstractReader
{
    public static function read(
        string $path,
        string $jsonOutput,
        ?string $artOutput
    ): void {
        $ffprobe = FFProbe::create();

        $format = $ffprobe->format($path);

        $metadata = new Metadata();

        if (is_numeric($format->get('duration'))) {
            $metadata->setDuration(
                Time::displayTimeToSeconds($format->get('duration')) ?? 0.0
            );
        }

        $metaTags = [];

        $toProcess = [
            $format->get('comments'),
            $format->get('tags'),
        ];

        $metaTags = self::aggregateMetaTags($toProcess);

        $metadata->setTags($metaTags);
        $metadata->setMimeType(mime_content_type($path) ?: '');

        file_put_contents(
            $jsonOutput,
            json_encode($metadata, JSON_THROW_ON_ERROR),
        );

        if (null !== $artOutput) {
            $ffmpeg = FFMpeg::create();

            /** @var Stream[] $videoStreams */
            $videoStreams = $ffprobe->streams($path)->videos()->all();
            foreach ($videoStreams as $videoStream) {
                $codecName = $videoStream->get('codec_name');

                if ($codecName !== 'mjpeg') {
                    continue;
                }

                $ffmpeg->getFFMpegDriver()->command([
                    '-i', $path,
                    '-an', '-vcodec',
                    'copy', $artOutput
                ]);

                break;
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
                if (!empty($tagContents) && !isset($metaTags[$tagName])) {
                    $tagValue = $tagContents;
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
