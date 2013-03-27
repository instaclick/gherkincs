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
    /**
     * @var integer
     */
    private $numberOfSpacesPerIndentation = 4;

    /**
     * Define the number of spaces per indentation level
     *
     * @param integer $numberOfSpacesPerIndentation the number of spaces per indentation
     */
    public function setNumberOfSpacesPerIndentation($numberOfSpacesPerIndentation)
    {
        $this->numberOfSpacesPerIndentation = $numberOfSpacesPerIndentation;
    }

    /**
     * {@inheritdoc}
     */
    public function analyze(array $tokenList, FileFeedback $fileFeedback)
    {
        foreach ($tokenList as $token) {
            $fileFeedback->setCurrentToken($token);
            $this->validateIndentation($token, $fileFeedback);
            $this->assertExtraWhitespaces($token, $fileFeedback);
        }
    }

    /**
     * Validate indentation
     *
     * @param \IC\Gherkinics\Model\Token           $token        token
     * @param \IC\Gherkinics\Feedback\FileFeedback $fileFeedback file feedback
     */
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

            case $token instanceof Model\TabularData:
                if ($indentationLevel === 3) {
                    break;
                }

                $fileFeedback->add($token->makeComment(
                    $this->makeCommentOnImproperIndentation(3, $numberOfLeadingSpaces)
                ));

                break;
            case $token instanceof Model\TagLine:
                if ($indentationLevel === 0 || $indentationLevel === 1) {
                    break;
                }

                $fileFeedback->add($token->makeComment(
                    'There exists upto ' . $this->numberOfSpacesPerIndentation . ' spaces for a tag line'
                ));

                break;
        }
    }

    /**
     * Produce a comment about improper indentation.
     *
     * @param integer $identationLevel       the indentation level
     * @param integer $numberOfLeadingSpaces the number of leading spaces
     *
     * @return string
     */
    private function makeCommentOnImproperIndentation($indentationLevel, $numberOfLeadingSpaces)
    {
        return sprintf(
            'Given %d spaces, it must have %d spaces before the beginning of the line (level %d)',
            $numberOfLeadingSpaces,
            $indentationLevel * $this->numberOfSpacesPerIndentation,
            $indentationLevel
        );
    }

    /**
     * Validate indentation
     *
     * @param \IC\Gherkinics\Model\Token           $token        token
     * @param \IC\Gherkinics\Feedback\FileFeedback $fileFeedback file feedback
     */
    private function assertExtraWhitespaces(Model\Token $token, FileFeedback $fileFeedback)
    {
        if ($token instanceof Model\TabularData) {
            return;
        }

        $actualContext = trim($token->getRawContent());

        if (preg_match('/\s{2,}/', $actualContext)) {
            $fileFeedback->add($token->makeComment('Extra whitespaces'));
        }
    }
}