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
class ICCodingStyleChecker implements AnalyzerInterface
{
    public function setNumberOfSpacesPerIndentation($numberOfSpacesPerIndentation)
    {
        $this->numberOfSpacesPerIndentation = $numberOfSpacesPerIndentation;
    }

    public function analyze(array $tokenList, FileFeedback $fileFeedback)
    {
        if (count($tokenList) > 350) {
            $fileFeedback->add('Warning: Is this all about Star Wars? TL;DR. Please split it up');
        }

        foreach ($tokenList as $token) {
            $fileFeedback->setToken($token);
            $this->validateIndentation($token, $fileFeedback);
        }

        $fileFeedback->setToken(null);
    }

    private function validateIndentation(Model\Token $token, FileFeedback $fileFeedback)
    {
        if ( ! $token instanceof Model\TagLine) {
            return;
        }

        foreach ($token->getTagList() as $tag) {
            if ( ! preg_match('/^\@/', $tag->getName())) {
                $fileFeedback->add($token->makeComment('Given "' . $tag->getName() . '", please prefix this tag with "@"'));
            }

            if (preg_match('/[A-Z]/', $tag->getName())) {
                $fileFeedback->add($token->makeComment('Given "' . $tag->getName() . '", please only use lowercase'));
            }

            if (preg_match('/_/', $tag->getName())) {
                $fileFeedback->add($token->makeComment('Given "' . $tag->getName() . '", please use hyphen instead of underscore'));
            }

            if (preg_match('/-{2,}/', $tag->getName())) {
                $fileFeedback->add($token->makeComment('Given "' . $tag->getName() . '", please use only one hyphen'));
            }
        }
    }
}