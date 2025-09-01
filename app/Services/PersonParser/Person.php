<?php

namespace App\Services\PersonParser;

use App\Services\PersonParser\Fields\FirstName;
use App\Services\PersonParser\Fields\Initial;
use App\Services\PersonParser\Fields\LastName;
use App\Services\PersonParser\Fields\Title;

readonly class Person
{
    public function __construct(
        private Title $title,
        private ?FirstName $firstName,
        private ?Initial $initial,
        private LastName $lastName
    ) {
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getFirstName(): FirstName
    {
        return $this->firstName;
    }

    public function getInitial(): Initial
    {
        return $this->initial;
    }

    public function getLastName(): LastName
    {
        return $this->lastName;
    }

    /**
     * We will ise this to make sure required fields are present
     * @return string[]
     */
    public static function requiredFields(): array
    {
        return [
            Title::class,
            LastName::class
        ];
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title->getValue(),
            'first_name' => $this->firstName?->getValue() ?? '',
            'initial' => $this->initial?->getValue() ?? '',
            'last_name' => $this->lastName->getValue(),
        ];
    }

}

