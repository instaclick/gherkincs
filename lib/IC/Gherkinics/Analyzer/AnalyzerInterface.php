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
    public function analyze(array $tokenList, FileFeedback $fileFeedback);
}