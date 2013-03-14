<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Analyzer;

use IC\Gherkinics\Feedback\FileFeedback;
use IC\Gherkinics\Model;

/**
 * Coding Style Checker
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class CodingStyleChecker implements AnalyzerInterface
{
    private $numberOfSpacesPerIndentation = 4;

    public function setNumberOfSpacesPerIndentation($numberOfSpacesPerIndentation)
    {
        $this->numberOfSpacesPerIndentation = $numberOfSpacesPerIndentation;
    }

    public function analyze(array $tokenList, FileFeedback $fileFeedback)
    {
        foreach ($tokenList as $token) {
            $fileFeedback->setToken($token);
            $this->validateIndentation($token, $fileFeedback);
        }

        $fileFeedback->setToken(null);
    }

    private function validateIndentation(Model\Token $token, FileFeedback $fileFeedback)
    {
        $rawContent = $token->getRawContent();
        $matches    = array();

        preg_match('/^\s+/', $rawContent, $matches);

        $numberOfLeadingSpaces = $matches ? strlen($matches[0]) : 0;
        $indentationLevel      = $numberOfLeadingSpaces / $this->numberOfSpacesPerIndentation;

        // Tab characters
        if (preg_match('/\t/', $rawContent)) {
            $fileFeedback->add($token->makeComment('Do not use tab characters'));
        }

        // 4-space Indentations
        if ($numberOfLeadingSpaces % $this->numberOfSpacesPerIndentation > 0) {
            $fileFeedback->add($token->makeComment('Please use ' . $this->numberOfSpacesPerIndentation . '-space indentation'));
        }

        // Trailing spaces
        if (preg_match('/\s+$/', $rawContent)) {
            $fileFeedback->add($token->makeComment('Please remove trailing spaces'));
        }

        switch (true) {
            case $token instanceof Model\Feature && $numberOfLeadingSpaces > 0:
                $fileFeedback->add($token->makeComment(
                    'There must be NO whitespaces before "Feature:"'
                ));

                break;

            case $token instanceof Model\Background:
            case $token instanceof Model\Scenario:
                if ($indentationLevel === 1) {
                    break;
                }

                $fileFeedback->add($token->makeComment(
                    $this->makeCommentOnImproperIndentation(1, $numberOfLeadingSpaces)
                ));

                break;

            case $token instanceof Model\Precondition:
            case $token instanceof Model\Action:
            case $token instanceof Model\Assertion:
            case $token instanceof Model\Continuation:
            case $token instanceof Model\Example:
                if ($indentationLevel === 2) {
                    break;
                }

                $fileFeedback->add($token->makeComment(
                    $this->makeCommentOnImproperIndentation(2, $numberOfLeadingSpaces)
                ));

                break;

            case $fileFeedback instanceof Model\TabularData:
                if ($indentationLevel === 3) {
                    break;
                }

                $fileFeedback->add($token->makeComment(
                    $this->makeCommentOnImproperIndentation(3, $numberOfLeadingSpaces)
                ));

                break;
        }
    }

    private function makeCommentOnImproperIndentation($indentationLevel, $numberOfLeadingSpaces)
    {
        return sprintf(
            'Given %d spaces, it must have %d spaces before the beginning of the line (level %d)',
            $numberOfLeadingSpaces,
            $indentationLevel * $this->numberOfSpacesPerIndentation,
            $indentationLevel
        );
    }
}