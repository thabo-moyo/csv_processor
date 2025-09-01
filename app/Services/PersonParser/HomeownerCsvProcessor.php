<?php

namespace App\Services\PersonParser;

use Exception;
use Illuminate\Support\Collection;


readonly class HomeownerCsvProcessor
{

    public function __construct(private PersonParser $parser) {}

    public function processContent(string $content): array
    {
        $lines = $this->parseLines($content);

        $processedLines = [];


        return $processedLines;
    }

    /**
     * @throws Exception
     */
    public function processLine(string $line): array
    {
        $line = trim($line);

        return $this->parser->parse($line);
    }

    private function parseLines(string $content): Collection
    {
        return collect(explode("\n", $content))
            ->map(fn($line) => str_replace(',', '', trim($line)))
            ->filter(fn($line) => !empty($line));
    }

    public function toArray(array $persons): array
    {
        return array_map(fn($person) => $person->toArray(), $persons);
    }
}
