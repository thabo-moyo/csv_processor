<?php

namespace Tests\Unit\App\Services\PersonParser;

use App\Services\PersonParser\PersonParser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PersonParserTest extends TestCase
{
    private PersonParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new PersonParser();
    }

    public function test_parses_single_person()
    {
        $result = $this->parser->parse('Mr John Smith');

        $this->assertCount(1, $result);
        $person = $result[0]->toArray();

        $this->assertEquals('Mr', $person['title']);
        $this->assertEquals('John', $person['first_name']);
        $this->assertEquals('Smith', $person['last_name']);
    }

    public function test_parses_person_with_initial()
    {
        $result = $this->parser->parse('Mr M Mackie');

        $this->assertCount(1, $result);
        $person = $result[0]->toArray();

        $this->assertEquals('Mr', $person['title']);
        $this->assertEquals('M', $person['initial']);
        $this->assertEquals('Mackie', $person['last_name']);
    }

    public function test_parses_couple_with_shared_surname()
    {
        $result = $this->parser->parse('Mr & Mrs Smith');

        $this->assertCount(2, $result);

        $first = $result[0]->toArray();
        $this->assertEquals('Mr', $first['title']);
        $this->assertEquals('Smith', $first['last_name']);

        $second = $result[1]->toArray();
        $this->assertEquals('Mrs', $second['title']);
        $this->assertEquals('Smith', $second['last_name']);
    }

    public function test_parses_couple_with_different_surnames()
    {
        $result = $this->parser->parse('Mr Tom Staff and Mr John Doe');

        $this->assertCount(2, $result);

        $first = $result[0]->toArray();
        $this->assertEquals('Mr', $first['title']);
        $this->assertEquals('Tom', $first['first_name']);
        $this->assertEquals('Staff', $first['last_name']);

        $second = $result[1]->toArray();
        $this->assertEquals('Mr', $second['title']);
        $this->assertEquals('John', $second['first_name']);
        $this->assertEquals('Doe', $second['last_name']);
    }

    public function test_parses_couple_with_ampersand_and_shared_surname()
    {
        $result = $this->parser->parse('Dr & Mrs Joe Bloggs');

        $this->assertCount(2, $result);

        $first = $result[0]->toArray();
        $this->assertEquals('Dr', $first['title']);
        $this->assertEquals('Bloggs', $first['last_name']);

        $second = $result[1]->toArray();
        $this->assertEquals('Mrs', $second['title']);
        $this->assertEquals('Joe', $second['first_name']);
        $this->assertEquals('Bloggs', $second['last_name']);
    }

    public function test_handles_mister_title()
    {
        $result = $this->parser->parse('Mister John Doe');

        $this->assertCount(1, $result);
        $person = $result[0]->toArray();

        $this->assertEquals('Mister', $person['title']);
        $this->assertEquals('John', $person['first_name']);
        $this->assertEquals('Doe', $person['last_name']);
    }

    #[DataProvider('sampleNamesProvider')]
    public function test_all_sample_names($input, $expected)
    {
        $result = $this->parser->parse($input);

        $this->assertCount(1, $result);
        $person = $result[0]->toArray();

        $this->assertEquals($expected['title'] ?? null, $person['title'], "Title mismatch for: $input");
        $this->assertEquals($expected['first_name'] ?? '', $person['first_name'], "First name mismatch for: $input");
        $this->assertEquals($expected['initial'] ?? '', $person['initial'], "Initial mismatch for: $input");
        $this->assertEquals($expected['last_name'] ?? null, $person['last_name'], "Last name mismatch for: $input");
    }

    public static function sampleNamesProvider(): \Generator
    {
        yield 'Mr John Smith' => ['Mr John Smith', ['title' => 'Mr', 'first_name' => 'John', 'last_name' => 'Smith']];
        yield 'Mrs Jane Smith' => ['Mrs Jane Smith', ['title' => 'Mrs', 'first_name' => 'Jane', 'last_name' => 'Smith']];
        yield 'Mister John Doe' => ['Mister John Doe', ['title' => 'Mister', 'first_name' => 'John', 'last_name' => 'Doe']];
        yield 'Mr Bob Lawblaw' => ['Mr Bob Lawblaw', ['title' => 'Mr', 'first_name' => 'Bob', 'last_name' => 'Lawblaw']];
        yield 'Mr Craig Charles' => ['Mr Craig Charles', ['title' => 'Mr', 'first_name' => 'Craig', 'last_name' => 'Charles']];
        yield 'Mr M Mackie' => ['Mr M Mackie', ['title' => 'Mr', 'initial' => 'M', 'last_name' => 'Mackie']];
        yield 'Mrs Jane McMaster' => ['Mrs Jane McMaster', ['title' => 'Mrs', 'first_name' => 'Jane', 'last_name' => 'Mcmaster']];
        yield 'Dr P Gunn' => ['Dr P Gunn', ['title' => 'Dr', 'initial' => 'P', 'last_name' => 'Gunn']];
        yield 'Ms Claire Robbo' => ['Ms Claire Robbo', ['title' => 'Ms', 'first_name' => 'Claire', 'last_name' => 'Robbo']];
        yield 'Prof Alex Brogan' => ['Prof Alex Brogan', ['title' => 'Prof', 'first_name' => 'Alex', 'last_name' => 'Brogan']];
        yield 'Mrs Faye Hughes-Eastwood' => ['Mrs Faye Hughes-Eastwood', ['title' => 'Mrs', 'first_name' => 'Faye', 'last_name' => 'Hughes-Eastwood']];
        yield 'Mr F. Fredrickson' => ['Mr F. Fredrickson', ['title' => 'Mr', 'initial' => 'F.', 'last_name' => 'Fredrickson']];
    }
}