<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Test\Analyzer;

use IC\Gherkinics\Analyzer\SemanticAnalyzer;
use IC\Gherkinics\Feedback\FileFeedback;

class SemanticAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->analyzer = new SemanticAnalyzer();
        $this->feedback = new FileFeedback();
    }

    /**
     * @dataProvider getCompatibleNodeTypeListForLevel2Nodes
     */
    public function testPositiveForContextFlow($previousType, $currentType)
    {
        if ($previousType) {
            $this->analyzer->setPreviousToken($this->createMock($previousType, 1, 'I am nobody.'));
        }

        $token = $this->createMock($currentType, 5, 'I am nobody.');

        $this->analyzer->assertContextFlow($token, $this->feedback);

        $this->assertEquals(0, count($this->feedback->all()));
    }

    /**
     * @dataProvider getIncompatibleNodeTypeListForLevel2Nodes
     */
    public function testNegativeForContextFlow($previousType, $currentType)
    {
        if ($previousType) {
            $this->analyzer->setPreviousToken($this->createMock($previousType, 1, 'I am nobody.'));
        }

        $token = $this->createMock($currentType, 5, 'I am nobody.');

        $this->analyzer->assertContextFlow($token, $this->feedback);

        $this->assertEquals(1, count($this->feedback->all()));
    }

    /**
     * @dataProvider getNodeForQualityTest
     */
    public function testQuality($expectedMessageCount, $previousType, $currentType, $context)
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

    public function getCompatibleNodeTypeListForLevel2Nodes()
    {
        return array(
            array(null, 'Precondition'),
            array(null, 'Action'),
            array(null, 'Assertion'),
            array('Precondition', 'Continuation'),
            array('Precondition', 'Action'),
            array('Precondition', 'Assertion'),
            array('Action', 'Continuation'),
            array('Action', 'Assertion'),
            array('Assertion', 'Continuation'),
            array('Assertion', 'Precondition'),
            array('Assertion', 'Action'),
        );
    }

    public function getIncompatibleNodeTypeListForLevel2Nodes()
    {
        return array(
            array(null, 'Token'),
            array('Precondition', 'Token'),
            array('Precondition', 'Precondition'),
            array('Action', 'Token'),
            array('Action', 'Precondition'),
            array('Action', 'Action'),
            array('Assertion', 'Token'),
            array('Assertion', 'Assertion'),
        );
    }

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