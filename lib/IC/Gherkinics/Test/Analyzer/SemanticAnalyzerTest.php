<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Test\Analyzer;

use IC\Gherkinics\Analyzer\SemanticAnalyzer;
use IC\Gherkinics\Feedback\FileFeedback;

/**
 * Test for Semantic Analyzer
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class SemanticAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Setup
     */
    protected function setUp()
    {
        $this->analyzer = new SemanticAnalyzer();
        $this->feedback = new FileFeedback();
    }

    /**
     * Test Case for Context Flow
     *
     * @param integer $expectedMessageCount
     * @param string  $previousType
     * @param string  $currentType
     *
     * @dataProvider getNodeForContextFlowTest
     */
    public function testPositiveForContextFlow($expectedMessageCount, $previousType, $currentType)
    {
        if ($previousType) {
            $this->analyzer->setPreviousToken($this->createMock($previousType, 1, 'I am nobody.'));
        }

        $token = $this->createMock($currentType, 5, 'I am nobody.');

        $this->analyzer->assertContextFlow($token, $this->feedback);

        $this->assertEquals(
            $expectedMessageCount,
            count($this->feedback->all())
        );
    }

    /**
     * Test Case for Semantic Quality
     *
     * @param integer $expectedMessageCount
     * @param string  $previousType
     * @param string  $currentType
     * @param string  $context
     *
     * @dataProvider getNodeForQualityTest
     */
    public function testSemanticQuality($expectedMessageCount, $previousType, $currentType, $context)
    {
        if ($previousType) {
            $this->analyzer->setPreviousToken($this->createMock($previousType, 1, null));
        }

        $token = $this->createMock($currentType, 5, $context);

        $this->analyzer->assertSemanticQuality($token, $this->feedback);

        $this->assertEquals(
            $expectedMessageCount,
            count($this->feedback->all())
        );
    }

    /**
     * Data Provider for Context Flow Tests
     *
     * @return array
     */
    public function getNodeForContextFlowTest()
    {
        return array(
            array(0, null, 'Precondition'),
            array(0, null, 'Action'),
            array(0, null, 'Assertion'),
            array(0, 'Precondition', 'Continuation'),
            array(0, 'Precondition', 'Action'),
            array(0, 'Precondition', 'Assertion'),
            array(0, 'Action', 'Continuation'),
            array(0, 'Action', 'Assertion'),
            array(0, 'Assertion', 'Continuation'),
            array(0, 'Assertion', 'Precondition'),
            array(0, 'Assertion', 'Action'),
            array(1, null, 'Token'),
            array(1, 'Precondition', 'Token'),
            array(1, 'Precondition', 'Precondition'),
            array(1, 'Action', 'Token'),
            array(1, 'Action', 'Precondition'),
            array(1, 'Action', 'Action'),
            array(1, 'Assertion', 'Token'),
            array(1, 'Assertion', 'Assertion'),
        );
    }

    /**
     * Data Provider for Semantic Quality Tests
     *
     * @return array
     */
    public function getNodeForQualityTest()
    {
        return array(
            array(0, null, 'Action', 'I follow pandas'),
            array(0, null, 'Action', 'I fill in the blank'),
            array(0, null, 'Action', 'I select anime'),
            array(0, null, 'Action', 'I click the signup link'),
            array(0, null, 'Action', 'I eat mochi'),
            array(0, 'Precondition', 'Action', 'I eat mochi'),
            array(0, 'Action', 'Continuation', 'I follow pandas'),
            array(0, 'Action', 'Continuation', 'I fill in the blank'),
            array(0, 'Action', 'Continuation', 'I select anime'),
            array(0, 'Action', 'Continuation', 'I click the signup link'),
            array(0, 'Action', 'Continuation', 'I eat mochi'),
            array(0, null, 'Assertion', 'I should see ninjas'),
            array(0, null, 'Assertion', 'I must have apples'),
            array(0, null, 'Assertion', 'I dance in the office'),
            array(0, 'Action', 'Assertion', 'I should see ninjas'),
            array(0, 'Precondition', 'Assertion', 'I must have apples'),
            array(0, 'Assertion', 'Continuation', 'I should see ninjas'),
            array(0, 'Assertion', 'Continuation', 'I must have apples'),
            array(1, null, 'Precondition', 'I should see elephants'),
            array(1, null, 'Action', 'I should see elephants'),
            array(1, null, 'Assertion', 'I fill in the blank'),
        );
    }

    /**
     * Create a mock token
     *
     * @param string  $type
     * @param integer $id
     * @param string  $context
     *
     * @return \IC\Gherkinics\Model\Token
     */
    private function createMock($type, $id, $context)
    {
        if ($type !== 'Token') {
            $class = sprintf('IC\Gherkinics\Model\%s', $type);

            return new $class($id, null, $context);
        }

        return $this->getMock(
            sprintf('IC\Gherkinics\Model\%s', $type),
            array('getId', 'getContext'),
            array($id, 'And ...', $context)
        );
    }
}
