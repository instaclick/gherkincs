<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Analyzer;

use IC\Gherkinics\Feedback\FileFeedback;

/**
 * Analyzer Interface
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
interface AnalyzerInterface
{
    /**
     * Analyze the token list
     *
     * @param array $tokenList    the list of tokens
     * @param array $fileFeedback the file feedback bucket
     */
    public function analyze(array $tokenList, FileFeedback $fileFeedback);
}