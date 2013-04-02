<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Test;

use IC\Gherkinics\Lexer;

/**
 * Test for Gherkin Lexer
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Setup
     */
    protected function setUp()
    {
        $this->lexer = new Lexer();
    }

    /**
     * Test Case
     *
     * @param integer $content
     * @param string  $typeSequence
     *
     * @dataProvider getSample
     */
    public function testStructure($content, $typeSequence)
    {
        $tokenList = $this->lexer->analyze($content);

        for ($i = 0, $l = count($tokenList); $i < $l; $i++) {
            $token        = $tokenList[$i];
            $tokenType    = get_class($token);
            $expectedType = $typeSequence[$i];

            $this->assertTrue(
                (bool) preg_match('/' . $expectedType . '$/', $tokenType),
                sprintf('Line %d: expecting %s, had %s instead', $i, $expectedType, $tokenType)
            );
        }
    }

    /**
     * Data Provider
     *
     * @return array
     */
    private function getSample()
    {
        $content = <<<ENDING
Feature: Some terse yet descriptive text of what is desired
    In order to realize a named business value
    As an explicit system actor
    I want to gain some beneficial outcome which furthers the goal

    @sample
    Scenario: Some determinable business situation
        Given some precondition
        And some other precondition
        When some action by the actor
        And some other action
        And yet another action
        Then some testable outcome is achieved
        And I click something
        But something else we can check happens too

    @panda
    Scenario Outline: Some determinable business situation
            |Search Engine|Keyword|
            |Google       |Sushi  |
            |Bing         |Pasta  |
ENDING;
        $tokenTypeSequence = array(
            'Feature',
            'Node',
            'Node',
            'Node',
            'Blank',
            'TagLine',
            'Scenario',
            'Precondition',
            'Continuation',
            'Action',
            'Continuation',
            'Continuation',
            'Assertion',
            'Continuation',
            'Continuation',
            'Blank',
            'TagLine',
            'Scenario',
            'TabularData',
            'TabularData',
            'TabularData',
        );

        return array(
            array($content, $tokenTypeSequence),
        );
    }
}
