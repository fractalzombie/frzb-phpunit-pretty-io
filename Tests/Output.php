<?php

declare(strict_types=1);

namespace FRZB\PHPUnit\IO\Tests;

use PHPUnit\Framework\TestCase;

class Output extends TestCase
{
    public function testSuccess(): void
    {
        $this->assertTrue(true);
    }

    public function testFail(): void
    {
        $this->fail();
    }

    public function testError(): void
    {
        throw new \Exception('error');
    }

    public function testRisky(): void
    {
    }

    public function testSkip(): void
    {
        $this->markTestSkipped('skipped');
    }

    public function testIncomplete(): void
    {
        $this->markTestIncomplete('incomplete');
    }

    public function testShouldConvertTitleCaseToLowercasedWords(): void
    {
        $this->assertTrue(true);
    }

    public function test_should_convert_snake_case_to_lowercased_words(): void
    {
        $this->assertTrue(true);
    }

    public function test should convert non breaking spaces to lowercased words(): void
    {
        $this->assertTrue(true);
    }

    public function testCanContain1Or99Numbers(): void
    {
        $this->assertTrue(true);
    }

    public function test123CanStartOrEndWithNumbers456(): void
    {
        $this->assertTrue(true);
    }

    public function test_should_preserve_CAPITALIZED_and_paRTiaLLY_CAPitaLIZed_words(): void
    {
        $this->assertTrue(true);
    }

    public function dataProvider(): iterable
    {
        yield 'dataset1' => ['test'];
        yield 'DataSet2' => ['test'];
        yield 'data set 3' => ['test'];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testWithNamedDatasets(string $value): void
    {
        $this->assertEquals('test', $value);
    }
}
