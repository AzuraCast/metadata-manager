<?php

declare(strict_types=1);

namespace Azura\MetadataManager\Command;

use Azura\MetadataManager\Reader\FfmpegReader;
use Azura\MetadataManager\Reader\GetId3Reader;
use Azura\MetadataManager\Reader\ReaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class ReadCommand extends Command
{
    protected function configure(): void
    {
        $this->setDefinition(
            [
                new InputArgument('path', InputArgument::REQUIRED, 'file path'),
                new InputArgument('json-output', InputArgument::REQUIRED, 'json output path'),
                new InputArgument('art-output', InputArgument::OPTIONAL, 'art output path')
            ]
        );
    }

    public function getDescription(): string
    {
        return 'Read metadata and write to a JSON file (and optionally an artwork file).';
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument('path');
        $jsonOutput = $input->getArgument('json-output');
        $artOutput = $input->getArgument('art-output');

        if (!is_file($path)) {
            $io->error(sprintf('File not readable: %s', $path));
            return 1;
        }

        /**
         * @var ReaderInterface[] Readers to try, by priority.
         */
        $metadataReaders = [
            GetId3Reader::class,
            FfmpegReader::class
        ];

        foreach($metadataReaders as $metadataReader) {
            try {
                $metadataReader::read($path, $jsonOutput, $artOutput);
                return 0;
            } catch (Throwable $exception) {
                $io->error($exception->getMessage());
            }
        }

        return 1;
    }
}
