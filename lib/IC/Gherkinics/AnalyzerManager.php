<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
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

/**
 * Analyzer Manager
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
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

    /**
     * Define the lexer
     *
     * @param \IC\Gherkinics\Lexer $lexer
     */
    public function setLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Register analyzer
     *
     * @param \IC\Gherkinics\Analyzer\AnalyzerInterface $analyzer
     */
    public function registerAnalyzer(AnalyzerInterface $analyzer)
    {
        $this->analyzerList[] = $analyzer;
    }

    /**
     * Analyze the content
     *
     * @param string $content
     *
     * @return \IC\Gherkinics\Feedback\FileFeedback
     */
    public function analyze($content)
    {
        $tokenList    = $this->lexer->analyze($content);
        $fileFeedback = new FileFeedback($tokenList);

        foreach ($this->analyzerList as $analyzer) {
            $fileFeedback->setCurrentToken(null);
            $analyzer->analyze($tokenList, $fileFeedback);
        }

        return $fileFeedback;
    }
}
