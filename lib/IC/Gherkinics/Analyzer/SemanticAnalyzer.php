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
    public function analyze(array $tokenList, FileFeedback $fileFeedback)
    {
        $previousToken = null;

        foreach ($tokenList as $token) {
            $fileFeedback->setToken($token);

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
                $previousToken = null;

                continue;
            }

            if (
                ! $previousToken
                && ! (
                    $token instanceof Model\Precondition
                    || $token instanceof Model\Action
                    || $token instanceof Model\Assertion
                )
            ) {
                $fileFeedback->add($token->makeComment('The continuation must not be at the beginning of the block'));
            }

            if (
                $previousToken instanceof Model\Precondition
                && ! (
                    $token instanceof Model\Continuation
                    || $token instanceof Model\Action
                    || $token instanceof Model\Assertion
                )
            ) {
                $fileFeedback->add($token->makeComment('The precondition should be followed by an action, assertion or continuation'));
            }

            if (
                $previousToken instanceof Model\Action
                && ! (
                    $token instanceof Model\Continuation
                    || $token instanceof Model\Assertion
                )
            ) {
                $fileFeedback->add($token->makeComment('The action should be followed by an assertion or continuation'));
            }

            if (
                $previousToken instanceof Assertion
                && ! (
                    $token instanceof Model\Continuation
                    || $token instanceof Model\Precondition
                    || $token instanceof Model\Action
                )
            ) {
                $fileFeedback->add($token->makeComment('The assertion should be followed by an action or continuation'));
            }

            if (
                preg_match('/(type|click|select|follow)/', $token->getContext())
                && ! (
                    $token instanceof Model\Action
                    || ($previousToken instanceof Model\Action && $token instanceof Model\Continuation)
                    || $token instanceof Model\Precondition
                    || ($previousToken instanceof Model\Precondition && $token instanceof Model\Continuation)
                )
            ) {
                $fileFeedback->add($token->makeComment('The context suggests an precondition/action but the prefix does not'));
            }

            if (
                preg_match('/(must|should)/', $token->getContext())
                && ! (
                    $token instanceof Model\Assertion
                    || ($previousToken instanceof Model\Assertion && $token instanceof Model\Continuation)
                )
            ) {
                $fileFeedback->add($token->makeComment('The context suggests an assertion but the prefix does not'));
            }

            // Disregard this token as the previous token.
            if ($token instanceof Model\Continuation) {
                continue;
            }

            $previousToken = $token;
        }
    }
}