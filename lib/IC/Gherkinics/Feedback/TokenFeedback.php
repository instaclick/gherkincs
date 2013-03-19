<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Feedback;

use IC\Gherkinics\Model\Token;

/**
 * File Feedback Backet
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
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

    /**
     * Retrieve token
     *
     * @return IC\Gherkinics\Model\Token
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Define token
     *
     * @param IC\Gherkinics\Model\Token $token
     */
    public function setToken(Token $token = null)
    {
        $this->token = $token;
    }
    
    public function add($message)
    {
        $this->messageList[] = $message;
    }
    
    public function all()
    {
        return $this->messageList;
    }
}