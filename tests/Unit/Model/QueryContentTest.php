<?php

namespace OCA\FullTextSearch_OpenSearch\Model;

use OCA\FullTextSearch_OpenSearch\Model\QueryContent;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the QueryContent class jsonSerialize() method.
 */
class QueryContentTest extends TestCase
{
    /**
     * Test that jsonSerialize correctly serializes default values.
     */
    public function testJsonSerializeWithDefaultValues(): void
    {
        $queryContent = new QueryContent('testWord');
        $expected = [
            'word' => 'testWord',
            'should' => 'should',
            'match' => 'match_phrase_prefix',
            'option' => 0
        ];

        $this->assertSame($expected, $queryContent->jsonSerialize());
    }

    /**
     * Test that setWord updates the word value.
     */
    public function testSetWordUpdatesWordValue(): void
    {
        $queryContent = new QueryContent('original');
        $queryContent->setWord('updated');
        $this->assertSame('updated', $queryContent->getWord());
    }

    /**
     * Test that setWord allows empty strings as valid input.
     */
    public function testSetWordAllowsEmptyString(): void
    {
        $queryContent = new QueryContent('initial');
        $queryContent->setWord('');
        $this->assertSame('', $queryContent->getWord());
    }

    /**
     * Test that setWord handles special characters correctly.
     */
    public function testSetWordHandlesSpecialCharacters(): void
    {
        $queryContent = new QueryContent('start');
        $queryContent->setWord('!@#$%^&*');
        $this->assertSame('!@#$%^&*', $queryContent->getWord());
    }

    /**
     * Test getWord() with a basic word that requires no modification.
     */
    public function testGetWordWithNoModification(): void
    {
        $queryContent = new QueryContent('exampleWord');
        $this->assertSame('exampleWord', $queryContent->getWord());
    }

    /**
     * Test getWord() when the word starts with "+".
     */
    public function testGetWordWithPlusPrefix(): void
    {
        $queryContent = new QueryContent('+important');
        $this->assertSame('important', $queryContent->getWord());
    }

    /**
     * Test getWord() when the word starts with "-".
     */
    public function testGetWordWithMinusPrefix(): void
    {
        $queryContent = new QueryContent('-excluded');
        $this->assertSame('excluded', $queryContent->getWord());
    }

    /**
     * Test getWord() when the word is a quoted string.
     */
    public function testGetWordWithQuotedWord(): void
    {
        $queryContent = new QueryContent('"exactMatch"');
        $this->assertSame('exactMatch', $queryContent->getWord());
    }

    /**
     * Test the initialization with a simple word without any prefix or quotes.
     */
    public function testInitWithSimpleWord(): void
    {
        $queryContent = new QueryContent('basic');
        $this->assertSame('basic', $queryContent->getWord());
        $this->assertSame('should', $queryContent->getShould());
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
        $this->assertSame(0, $queryContent->getOption());
    }

    /**
     * Test the initialization with a word starting with "+" (OPTION_MUST condition).
     */
    public function testInitWithPlusPrefix(): void
    {
        $queryContent = new QueryContent('+important');
        $this->assertSame('important', $queryContent->getWord());
        $this->assertSame('must', $queryContent->getShould());
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
        $this->assertSame(QueryContent::OPTION_MUST, $queryContent->getOption());
    }

    /**
     * Test that getOption returns the default value when no prefix is provided.
     */
    public function testGetOptionWithDefaultValue(): void
    {
        $queryContent = new QueryContent('basic');
        $this->assertSame(0, $queryContent->getOption());
    }

    /**
     * Test that getOption returns OPTION_MUST when a word with "+" prefix is provided.
     */
    public function testGetOptionWithPlusPrefix(): void
    {
        $queryContent = new QueryContent('+required');
        $this->assertSame(QueryContent::OPTION_MUST, $queryContent->getOption());
    }

    /**
     * Test that getOption returns OPTION_MUST_NOT when a word with "-" prefix is provided.
     */
    public function testGetOptionWithMinusPrefix(): void
    {
        $queryContent = new QueryContent('-notNeeded');
        $this->assertSame(QueryContent::OPTION_MUST_NOT, $queryContent->getOption());
    }

    /**
     * Test the initialization with a word starting with "-" (OPTION_MUST_NOT condition).
     */
    public function testInitWithMinusPrefix(): void
    {
        $queryContent = new QueryContent('-unwanted');
        $this->assertSame('unwanted', $queryContent->getWord());
        $this->assertSame('must_not', $queryContent->getShould());
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
        $this->assertSame(QueryContent::OPTION_MUST_NOT, $queryContent->getOption());
    }

    /**
     * Test the initialization with a quoted word (match condition should be set to "match").
     */
    public function testInitWithQuotedWord(): void
    {
        $queryContent = new QueryContent('"specific"');
        $this->assertSame('specific', $queryContent->getWord());
        $this->assertSame('should', $queryContent->getShould());
        $this->assertSame('match', $queryContent->getMatch());
        $this->assertSame(0, $queryContent->getOption());
    }

    /**
     * Test the initialization with a quoted word containing spaces.
     */
    public function testInitWithQuotedWordAndSpaces(): void
    {
        $queryContent = new QueryContent('"multi word phrase"');
        $this->assertSame('multi word phrase', $queryContent->getWord());
        $this->assertSame('should', $queryContent->getShould());
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
        $this->assertSame(0, $queryContent->getOption());
    }

    /**
     * Test that jsonSerialize handles words with "+" correctly, applying OPTION_MUST.
     */
    public function testJsonSerializeWithOptionMust(): void
    {
        $queryContent = new QueryContent('+testWord');
        $expected = [
            'word' => 'testWord',
            'should' => 'must',
            'match' => 'match_phrase_prefix',
            'option' => QueryContent::OPTION_MUST
        ];

        $this->assertSame($expected, $queryContent->jsonSerialize());
    }

    /**
     * Test that jsonSerialize handles words with "-" correctly, applying OPTION_MUST_NOT.
     */
    public function testJsonSerializeWithOptionMustNot(): void
    {
        $queryContent = new QueryContent('-testWord');
        $expected = [
            'word' => 'testWord',
            'should' => 'must_not',
            'match' => 'match_phrase_prefix',
            'option' => QueryContent::OPTION_MUST_NOT
        ];

        $this->assertSame($expected, $queryContent->jsonSerialize());
    }

    /**
     * Test that jsonSerialize handles quoted words correctly with "match".
     */
    public function testJsonSerializeWithQuotedWord(): void
    {
        $queryContent = new QueryContent('"testWord"');
        $expected = [
            'word' => 'testWord',
            'should' => 'should',
            'match' => 'match',
            'option' => 0
        ];

        $this->assertSame($expected, $queryContent->jsonSerialize());
    }

    /**
     * Test that jsonSerialize handles words with spaces in quotes, applying match_phrase_prefix.
     */
    public function testJsonSerializeWithQuotedWordAndSpaces(): void
    {
        $queryContent = new QueryContent('"test Word"');
        $expected = [
            'word' => 'test Word',
            'should' => 'should',
            'match' => 'match_phrase_prefix',
            'option' => 0
        ];

        $this->assertSame($expected, $queryContent->jsonSerialize());
    }
    /**
     * Test that getShould returns the default value of "should".
     */
    public function testGetShouldWithDefaultValue(): void
    {
        $queryContent = new QueryContent('testWord');
        $this->assertSame('should', $queryContent->getShould());
    }

    /**
     * Test that getMatch returns the default value of "match_phrase_prefix".
     */
    public function testGetMatchWithDefaultValue(): void
    {
        $queryContent = new QueryContent('defaultWord');
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
    }

    /**
     * Test that getMatch returns "match_phrase_prefix" for a specific word without changes.
     */
    public function testGetMatchWithMatchPhrasePrefix(): void
    {
        $queryContent = new QueryContent('sample');
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
    }

    /**
     * Test that getMatch returns "match" when the word is quoted.
     */
    public function testGetMatchWithQuotedWord(): void
    {
        $queryContent = new QueryContent('"exactWord"');
        $this->assertSame('match', $queryContent->getMatch());
    }

    /**
     * Test that getMatch returns "match_phrase_prefix" for a multi-word quoted phrase.
     */
    public function testGetMatchWithQuotedWordWithSpaces(): void
    {
        $queryContent = new QueryContent('"multi word phrase"');
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
    }

    /**
     * Test that getShould returns "must" when a word with "+" prefix is provided.
     */
    public function testGetShouldWithPlusPrefix(): void
    {
        $queryContent = new QueryContent('+important');
        $this->assertSame('must', $queryContent->getShould());
    }

    /**
     * Test that setShould updates the should property with a valid value.
     */
    public function testSetShouldUpdatesValue(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setShould('must');
        $this->assertSame('must', $queryContent->getShould());
    }

    /**
     * Test that setShould can set the value to "must_not".
     */
    public function testSetShouldToMustNot(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setShould('must_not');
        $this->assertSame('must_not', $queryContent->getShould());
    }

    /**
     * Test that setShould accepts "should" as a valid value.
     */
    public function testSetShouldToShould(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setShould('should');
        $this->assertSame('should', $queryContent->getShould());
    }

    /**
     * Test that getShould returns "must_not" when a word with "-" prefix is provided.
     */
    public function testGetShouldWithMinusPrefix(): void
    {
        $queryContent = new QueryContent('-excluded');
        $this->assertSame('must_not', $queryContent->getShould());
    }
    /**
     * Test that setMatch updates the match property with a valid value.
     */
    public function testSetMatchUpdatesValue(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setMatch('custom_match');
        $this->assertSame('custom_match', $queryContent->getMatch());
    }

    /**
     * Test that setMatch accepts "match" as a valid value.
     */
    public function testSetMatchAllowsMatch(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setMatch('match');
        $this->assertSame('match', $queryContent->getMatch());
    }

    /**
     * Test that setMatch accepts "match_phrase_prefix" as a valid value.
     */
    public function testSetMatchAllowsMatchPhrasePrefix(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setMatch('match_phrase_prefix');
        $this->assertSame('match_phrase_prefix', $queryContent->getMatch());
    }
    /**
     * Test that setOption updates the option property with a valid value.
     */
    public function testSetOptionUpdatesValue(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setOption(QueryContent::OPTION_MUST);
        $this->assertSame(QueryContent::OPTION_MUST, $queryContent->getOption());

        $queryContent->setOption(QueryContent::OPTION_MUST_NOT);
        $this->assertSame(QueryContent::OPTION_MUST_NOT, $queryContent->getOption());
    }

    /**
     * Test that setOption can handle the default value of 0.
     */
    public function testSetOptionAllowsDefault(): void
    {
        $queryContent = new QueryContent('testWord');
        $queryContent->setOption(0);
        $this->assertSame(0, $queryContent->getOption());
    }

    /**
     * Test that setOption handles invalid values gracefully (if behavior is defined).
     */
    public function testSetOptionInvalidValue(): void
    {
        $this->expectException(\TypeError::class);
        $queryContent = new QueryContent('testWord');
        $queryContent->setOption('invalidOption'); // Invalid value
    }
}