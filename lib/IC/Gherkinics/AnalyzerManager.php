<?php
namespace IC\Gherkinics;

use IC\Gherkinics\Analyzer\AnalyzerInterface;
use IC\Gherkinics\Feedback\FileFeedback;
use IC\Gherkinics\Model\Action;
use IC\Gherkinics\Model\Assertion;
use IC\Gherkinics\Model\Background;
use IC\Gherkinics\Model\Blank as BlankLine;
use IC\Gherkinics\Model\Continuation;
use IC\Gherkinics\Model\Example;
use IC\Gherkinics\Model\Feature;
use IC\Gherkinics\Model\Node;
use IC\Gherkinics\Model\Precondition;
use IC\Gherkinics\Model\Scenario;
use IC\Gherkinics\Model\TabularData;
use IC\Gherkinics\Model\Tag;
use IC\Gherkinics\Model\TagLine;
use IC\Gherkinics\Model\Token;

final class AnalyzerManager
{
    /**
     * @var \Cuke\Lexer
     */
    private $lexer;

    /**
     * @var array
     */
    private $analyzerList = array();

    public function setLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    public function registerAnalyzer(AnalyzerInterface $analyzer)
    {
        $this->analyzerList[] = $analyzer;
    }

    /**
     * Analyze the content
     *
     * @param string $content
     */
    public function analyze($content)
    {
        $fileFeedback = new FileFeedback();
        $tokenList    = $this->lexer->analyze($content);

        if (count($tokenList) > 200) {
            $fileFeedback->add('Warning: Is this all about Star Wars? TL;DR. Please split it up');
        }

        foreach ($this->analyzerList as $analyzer) {
            $fileFeedback->setToken(null);
            $analyzer->analyze($tokenList, $fileFeedback);
        }

        return $fileFeedback;
    }
}