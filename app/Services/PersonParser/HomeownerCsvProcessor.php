<?php

namespace App\Services\PersonParser;

use Exception;
use Illuminate\Support\Collection;
use App\Models\Person;


readonly class HomeownerCsvProcessor
{

    public function __construct(private PersonParser $parser) {}

    public function processContent(string $content): array
    {
        $lines = $this->parseLines($content);

        $processedLines = [];
        $countBefore = Person::count();

        foreach ($lines as  $line) {
            try {
                $result = $this->processLine($line);
                $personsAsArrays = array_map(fn($person) => $person->toArray(), $result);
                $processedLines = array_merge($processedLines, $personsAsArrays);
            } catch (\Exception $e) {
                continue;
            }
        }

        Person::Upsert($processedLines, ['title', 'first_name', 'initial', 'last_name']);
        
        $countAfter = Person::count();
        $newCount = $countAfter - $countBefore;
        
        return [
            'persons' => $processedLines,
            'total_processed' => count($processedLines),
            'new_added' => $newCount,
            'duplicates' => count($processedLines) - $newCount
        ];
    }

    /**
     * @throws Exception
     * @return Person[]
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
