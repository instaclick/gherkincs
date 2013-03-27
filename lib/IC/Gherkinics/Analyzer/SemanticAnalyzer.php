<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Analyzer;

use IC\Gherkinics\Feedback\FileFeedback;
use IC\Gherkinics\Model;

/**
 * Analyzer for semantic quality
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class SemanticAnalyzer implements AnalyzerInterface
{
    /**
     * @var \IC\Gherkinics\Model\Token
     */
    private $previousToken = null;

    /**
     * Define the previous token
     *
     * @param \IC\Gherkinics\Model\Token $previousToken the previous token
     */
    public function setPreviousToken(Model\Token $previousToken)
    {
        $this->previousToken = $previousToken;
    }

    /**
     * {@inheritdoc}
     */
    public function analyze(array $tokenList, FileFeedback $fileFeedback)
    {
        $this->previousToken = null;

        foreach ($tokenList as $token) {
            $fileFeedback->setCurrentToken($token);

            if (
                $token instanceof Model\Node
                || $token instanceof Model\Blank
                || $token instanceof Model\Feature
                || $token instanceof Model\TagLine
                || $token instanceof Model\Example
                || $token instanceof Model\TabularData
            ) {
                continue;
            }

            if ($token instanceof Model\Background || $token instanceof Model\Scenario) {
                $this->previousToken = null;

                continue;
            }

            $this->assertContextFlow($token, $fileFeedback);
            $this->assertSemanticQuality($token, $fileFeedback);

            // Disregard this token as the previous token.
            if ($token instanceof Model\Continuation) {
                continue;
            }

            $this->previousToken = $token;
        }
    }

    /**
     * Assert the context flow (semantic flow)
     *
     * {@internal This method is made publicly accessible to allow a focus and easy test case. }}
     *
     * @param \IC\Gherkinics\Model\Token           $token        token
     * @param \IC\Gherkinics\Feedback\FileFeedback $fileFeedback file feedback
     */
    public function assertContextFlow(Model\Token $token, FileFeedback $fileFeedback)
    {
        if (
            ! $this->previousToken
            && ! (
                $token instanceof Model\Precondition
                || $token instanceof Model\Action
                || $token instanceof Model\Assertion
            )
        ) {
            $fileFeedback->add($token->makeComment('The continuation must not be at the beginning of the block'));
        }

        if (
            $this->previousToken instanceof Model\Precondition
            && ! (
                $token instanceof Model\Continuation
                || $token instanceof Model\Action
                || $token instanceof Model\Assertion
            )
        ) {
            $fileFeedback->add($token->makeComment('The precondition should be followed by an action, assertion or continuation'));
        }

        if (
            $this->previousToken instanceof Model\Action
            && ! (
                $token instanceof Model\Continuation
                || $token instanceof Model\Assertion
            )
        ) {
            $fileFeedback->add($token->makeComment('The action should be followed by an assertion or continuation'));
        }

        if (
            $this->previousToken instanceof Model\Assertion
            && ! (
                $token instanceof Model\Continuation
                || $token instanceof Model\Precondition
                || $token instanceof Model\Action
            )
        ) {
            $fileFeedback->add($token->makeComment('The assertion should be followed by an action or continuation'));
        }
    }

    /**
     * Assert for the semantic quality
     *
     * {@internal This method is made publicly accessible to allow a focus and easy test case. }}
     *
     * @param \IC\Gherkinics\Model\Token           $token        token
     * @param \IC\Gherkinics\Feedback\FileFeedback $fileFeedback file feedback
     */
    public function assertSemanticQuality(Model\Token $token, FileFeedback $fileFeedback)
    {
        $isPossibleAction    = preg_match('/ (fill|click|select|follow) /', $token->getContext());
        $isPossibleAssertion = preg_match('/ (must|should) /', $token->getContext());

        if (
            $isPossibleAction
            && ! $isPossibleAssertion
            && ! (
                $token instanceof Model\Action
                || ($this->previousToken instanceof Model\Action && $token instanceof Model\Continuation)
                || $token instanceof Model\Precondition
                || ($this->previousToken instanceof Model\Precondition && $token instanceof Model\Continuation)
            )
        ) {
            $fileFeedback->add($token->makeComment('The context suggests an precondition/action but the prefix does not'));
        }

        if (
            $isPossibleAssertion
            && ! $isPossibleAction
            && ! $token instanceof Model\Assertion
            && ! ($this->previousToken instanceof Model\Assertion && $token instanceof Model\Continuation)
        ) {
            $fileFeedback->add($token->makeComment('The context suggests an assertion but the prefix does not'));
        }
    }
}