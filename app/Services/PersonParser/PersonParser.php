<?php

namespace App\Services\PersonParser;

use App\Services\PersonParser\Fields\Conjunction;
use App\Services\PersonParser\Fields\FirstName;
use App\Services\PersonParser\Fields\Initial;
use App\Services\PersonParser\Fields\LastName;
use App\Services\PersonParser\Fields\Title;
use Exception;
use Illuminate\Support\Collection;

class PersonParser
{
    private Collection $fields;

    public function __construct()
    {
        $this->fields = collect([ //Order matters here
            Title::class,
            Initial::class,
            Conjunction::class,
            FirstName::class,
            LastName::class,
        ]);
    }

    /**
     * @param string $input
     * @return Person[]
     * @throws Exception
     */
    public function parse(string $input): array
    {
        $tokens = explode(' ', $input);

        if ($this->hasConjunction($tokens)) {
            return $this->parseCouple($tokens);
        }

        return [$this->parseSingle($tokens)];
    }


    private function parseFields(array $fields): Collection
    {
        $added = collect();

        if (!Title::canAccept($fields[0])) {
            return $added;
        }
        $added->push(new Title($fields[0]));  //we can assume the first index is title

        $lastIndex = count($fields) - 1;
        if ($lastIndex > 0 && isset($fields[$lastIndex]) && LastName::canAccept($fields[$lastIndex])) {
            $added->push(new LastName($fields[$lastIndex]));
        }

        //skip first and last, already handled
        for ($i = 1; $i < $lastIndex; $i++) {
            foreach ($this->fields as $fieldClass) {
                if ($fieldClass === Title::class || $fieldClass === LastName::class) {
                    continue;
                }

                //Avoid duplicates
                if ($added->contains(fn($parsedField) => $parsedField instanceof $fieldClass)) {
                    continue;
                }

                if ($fieldClass::canAccept($fields[$i])) {
                    $added->push(new $fieldClass($fields[$i]));
                    break;
                }
            }
        }

        return $added;
    }

    /**
     * @throws Exception
     */
    private function parseSingle(array $tokens): Person
    {
        $fields = $this->parseFields($tokens);

        $this->verifyRequiredFields($fields->map(fn($field) => $field::class)->toArray());

        return $this->createPersonFromFields($fields);
    }

    /**
     * @throws Exception
     */
    private function verifyRequiredFields(array $fields): void
    {
        $requiredFields = Person::requiredFields();

        foreach ($requiredFields as $fieldClass) {
            if (!in_array($fieldClass, $fields)) {
                throw new Exception("Missing required field: " . $fieldClass);
            }
        }
    }

    /**
     * @throws Exception
     * @return Person[]
     */
    private function parseCouple(array $tokens): array
    {
        $groups = $this->splitByConjunction($tokens);
        $parsedGroups = collect();

        foreach ($groups as $group) {
            $parsedGroups->push($this->parseFields($group));
        }

        $sharedLastName = $parsedGroups
            ->map(fn($fields) => $fields->first(fn($field) => $field instanceof LastName))
            ->filter()
            ->first();

        $persons = [];
        foreach ($parsedGroups as $fields) {
            if (!$fields->contains(fn($field) => $field instanceof LastName) && $sharedLastName) {
                $fields->push($sharedLastName);
            }

            $persons[] = $this->createPersonFromFields($fields);

            $this->verifyRequiredFields($fields->map(fn($field) => $field::class)->toArray());
        }

        return $persons;
    }

    /**
     * @param Collection<FieldInterface> $fields
     * @return Person|null
     */
    private function createPersonFromFields(Collection $fields): Person|null
    {
        $title = $fields->first(fn($field) => $field instanceof Title);
        $firstName = $fields->first(fn($field) => $field instanceof FirstName);
        $initial = $fields->first(fn($field) => $field instanceof Initial);
        $lastName = $fields->first(fn($field) => $field instanceof LastName);

        if ($title && $lastName) {
            return new Person(
                $title,
                $firstName,
                $initial,
                $lastName
            );
        }

        return null;
    }

    /**
     * Split tokens into groups by conjunction
     * exampe: Mr & Mrs Smith => [ [Mr], [Mrs Smith] ]
     */
    private function splitByConjunction(array $tokens): array
    {
        $groups = [];
        $current = [];

        foreach ($tokens as $token) {
            if (Conjunction::canAccept($token)) {
                if (!empty($current)) {
                    $groups[] = $current;
                    $current = [];
                }
            } else {
                $current[] = trim($token);
            }
        }

        if (!empty($current)) {
            $groups[] = $current;
        }

        return $groups;
    }



    private function hasConjunction(array $tokens): bool
    {
        foreach ($tokens as $token) {
            if (Conjunction::canAccept($token)) {
                return true;
            }
        }

        return false;
    }
}
