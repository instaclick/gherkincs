<?php
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
    public function testPositiveWithLevel2NodeAtStart($type)
    {
        $token = $this->createMock($type, 5, 'I am nobody.');

        $this->analyzer->assertContextFlow($token, $this->feedback);

        $this->assertEquals(0, count($this->feedback->all()));
    }

    public function testNegativeWithNonLevel2NodeAtStart()
    {
        $token = $this->createMock('Token', 5, 'I am nobody.');

        $this->analyzer->assertContextFlow($token, $this->feedback);

        $this->assertEquals(1, count($this->feedback->all()));
    }

    public function testNegativeWithPreviousTokenAsPrecondition()
    {
        $this->analyzer->setPreviousToken($this->createMock('Precondition', 1, 'I am nobody.'));

        $token = $this->createMock('Token', 5, 'I am nobody.');

        $this->analyzer->assertContextFlow($token, $this->feedback);

        $this->assertEquals(1, count($this->feedback->all()));
    }

    public function getCompatibleNodeTypeListForLevel2Nodes()
    {
        return array(
            array('Precondition'),
            array('Action'),
            array('Assertion'),
        );
    }

    private function createMock($type, $id, $context)
    {
        return $this->getMock(
            sprintf('IC\Gherkinics\Model\%s', $type),
            array('getId', 'getContext'),
            array($id, 'And ...', $context)
        );
    }
}