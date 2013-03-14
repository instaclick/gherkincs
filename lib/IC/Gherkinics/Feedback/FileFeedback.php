<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Feedback;

use IC\Gherkinics\Model\Token;

final class FileFeedback
{
    /**
     * @var \IC\Gherkincs\Model\Token the current token
     */
    private $token;

    /**
     * @var array
     */
    private $messageList = array();

    public function setToken(Token $token = null)
    {
        $this->token = $token;
    }

    public function add($message)
    {
        $lineNumber = $this->token
            ? $this->token->getId()
            : 0;
        $this->messageList[$lineNumber][] = $message;
    }

    public function all()
    {
        return $this->messageList;
    }
}