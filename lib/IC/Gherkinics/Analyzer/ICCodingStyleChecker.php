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
    /**
     * {@inheritdoc}
     */
    public function analyze(array $tokenList, FileFeedback $fileFeedback)
    {
        if (count($tokenList) > 350) {
            $fileFeedback->add('Warning: Is this all about Star Wars? TL;DR. Please split it up');
        }

        foreach ($tokenList as $token) {
            $fileFeedback->setCurrentToken($token);
            $this->validateTagLine($token, $fileFeedback);
        }
    }

    /**
     * Validate a tag line
     *
     * @param \IC\Gherkinics\Model\Token           $token        token
     * @param \IC\Gherkinics\Feedback\FileFeedback $fileFeedback file feedback
     */
    private function validateTagLine(Model\Token $token, FileFeedback $fileFeedback)
    {
        if ( ! $token instanceof Model\TagLine) {
            return;
        }

        foreach ($token->getTagList() as $tag) {
            $startWithAtSign = preg_match('/^\@/', $tag->getName());

            if ( ! $startWithAtSign) {
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

            if ($startWithAtSign && ! preg_match('/^[a-zA-Z0-9]/', substr($tag->getName(), 1))) {
                $fileFeedback->add($token->makeComment('Given "' . $tag->getName() . '", the tag name must start with an alphanumeric character'));
            }

            if ( ! preg_match('/[a-zA-Z0-9]$/', $tag->getName())) {
                $fileFeedback->add($token->makeComment('Given "' . $tag->getName() . '", the tag name must end with an alphanumeric character'));
            }
        }
    }
}
