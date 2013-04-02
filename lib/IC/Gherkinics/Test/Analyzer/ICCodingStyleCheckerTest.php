<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Test\Analyzer;

use IC\Gherkinics\Analyzer\ICCodingStyleChecker;
use IC\Gherkinics\Feedback\FileFeedback;

/**
 * Test for Coding Style Checker
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class ICCodingStyleCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Setup
     */
    protected function setUp()
    {
        $this->analyzer = new ICCodingStyleChecker();
        $this->feedback = new FileFeedback();
    }

    /**
     * Test Case
     *
     * @param integer $expectedFeedbackCount
     * @param string  $rawContext
     * @param array   $expectedPatternMap
     *
     * @dataProvider getSampleTokenListForTagLines
     */
    public function test($expectedFeedbackCount, $rawContext, $expectedPatternMap = array())
    {
        $token = $this->createMock('TagLine', $rawContext);

        $this->analyzer->analyze(array($token), $this->feedback);

        $messageMap = $this->feedback->all();

        if ( ! $expectedFeedbackCount) {
            $this->assertEquals(0, count($messageMap));

            return;
        }

        $messageList = $messageMap[$token->getId()]->all();

        $this->assertEquals(1, count($messageMap));
        $this->assertEquals($expectedFeedbackCount, count($messageList));

        foreach ($expectedPatternMap as $index => $expectedPattern) {
            $this->assertTrue(
                (bool) preg_match($expectedPattern, $messageList[$index]),
                '[' . $index . '] ' . $messageList[$index]
            );
        }
    }

    /**
     * Data Provider
     *
     * @return array
     */
    public function getSampleTokenListForTagLines()
    {
        return array(
            array(
                0,
                '@abc @def @ghi-jkl',
            ),
            array(
                1,
                '@aBc @def @ghi-jkl',
                array(
                    0 => '/lowercase/',
                )
            ),
            array(
                2,
                '@abc @def @ghi--_jkl',
                array(
                    0 => '/underscore/',
                    1 => '/only one hyphen/',
                )
            ),
            array(
                3,
                '@abc @def @ghi- @-jkl panda',
                array(
                    0 => '/must end/',
                    1 => '/must start/',
                    2 => '/\"@\"/',
                )
            ),
        );
    }

    /**
     * Create a mock token
     *
     * @param string $type
     * @param string $rawContext
     *
     * @return \IC\Gherkinics\Model\Token
     */
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
