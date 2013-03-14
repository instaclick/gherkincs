<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Test\Analyzer;

use IC\Gherkinics\Analyzer\CodingStyleChecker;
use IC\Gherkinics\Feedback\FileFeedback;

/**
 * Test for Coding Style Checker
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class CodingStyleCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->analyzer = new CodingStyleChecker();
        $this->feedback = new FileFeedback();
    }

    /**
     * @dataProvider getSampleForIndentation
     */
    public function testIndentation($expectedFeedbackCount, $type, $rawContext, $expectedPatternMap = array())
    {
        $token = $this->createMock($type, $rawContext);

        $this->analyzer->analyze(array($token), $this->feedback);

        $messageMap = $this->feedback->all();

        if ( ! $expectedFeedbackCount) {
            $this->assertEquals(0, count($messageMap));

            return;
        }

        $messageList = $messageMap[$token->getId()];

        $this->assertEquals(1, count($messageMap));
        $this->assertEquals($expectedFeedbackCount, count($messageList));

        foreach ($expectedPatternMap as $index => $expectedPattern) {
            $this->assertTrue(
                (bool) preg_match($expectedPattern, $messageList[$index]),
                '[' . $index . '] ' . $messageList[$index]
            );
        }
    }

    public function getSampleForIndentation()
    {
        return array(
            array(
                0,
                'Feature',
                'Feature: rock and roll!',
            ),
            array(
                0,
                'Scenario',
                '    Scenario Outline: rock and roll!',
            ),
            array(
                0,
                'Action',
                '        Ikayaki is delicious',
            ),
            array(
                0,
                'TabularData',
                '            |ABC|DEF|GHI|',
            ),
            array(
                0,
                'TagLine',
                '@abc @def',
            ),
            array(
                0,
                'TagLine',
                '    @abc @def',
            ),
            array(
                3,
                'Action',
                "\t Ikayaki is delicious",
                array(
                    0 => '/tab/',
                    1 => '/4-space/',
                    2 => '/ 8 /',
                ),
            ),
            array(
                2,
                'Action',
                "   Ikayaki is delicious",
                array(
                    0 => '/4-space/',
                    1 => '/ 8 /',
                ),
            ),
            array(
                2,
                'TagLine',
                '     @abc @def',
                array(
                    0 => '/4-space/',
                    1 => '/ upto 4 /',
                ),
            ),
            array(
                1,
                'TagLine',
                '        @abc @def',
                array(
                    0 => '/ upto 4 /',
                ),
            ),
            array(
                2,
                'Feature',
                "Feature: rock and roll!  \t",
                array(
                    0 => '/tab/',
                    1 => '/trailing spaces/',
                ),
            ),
        );
    }

    private function createMock($type, $rawContext)
    {
        if ($type !== 'Token') {
            $class = sprintf('IC\Gherkinics\Model\%s', $type);

            return new $class(1, $rawContext, null);
        }

        $token = $this->getMock(
            sprintf('IC\Gherkinics\Model\%s', $type),
            array('getId', 'getRawContext', 'getContext'),
            array(1, $rawContext, null)
        );

        return $token;
    }
}