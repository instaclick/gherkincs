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
    private $currentToken;

    /**
     * @var array
     */
    private $tokenList;

    /**
     * @var array
     */
    private $messageList = array();

    /**
     * @var boolean
     */
    private $sorted = false;

    /**
     * Constructor
     *
     * @param array $tokenList
     */
    public function __construct(array $tokenList = array())
    {
        $this->tokenList = $tokenList;
    }

    /**
     * Retrieve the list of tokens
     *
     * @return array
     */
    public function getTokenList()
    {
        return $this->tokenList;
    }

    /**
     * Define the current token
     *
     * @param \IC\Gherkinics\Model\Token $token
     */
    public function setCurrentToken(Token $token = null)
    {
        $this->currentToken = $token;
    }

    /**
     * Add the message
     *
     * @param string $message
     */
    public function add($message)
    {
        $lineNumber = $this->currentToken
            ? $this->currentToken->getId()
            : 0;

        if ( ! isset($this->messageList[$lineNumber])) {
            $this->messageList[$lineNumber] = new TokenFeedback($this->currentToken ?: null);
        }

        $this->messageList[$lineNumber]->add($message);
    }

    /**
     * Retrieve all token feedbacks
     *
     * @return array
     */
    public function all()
    {
        if ($this->sorted) {
            return $this->messageList;
        }

        $messageList    = array();
        $lineNumberList = array_keys($this->messageList);

        sort($lineNumberList);

        foreach ($lineNumberList as $lineNumber) {
            $messageList[$lineNumber] = $this->messageList[$lineNumber];
        }

        $this->sorted = true;

        return $this->messageList = $messageList;
    }

    /**
     * Retrieve the number of token feedbacks
     *
     * @return integer
     */
    public function count()
    {
        return count($this->messageList);
    }

    /**
     * Retrieve a token feedback
     *
     * @param integer $id
     *
     * @return \IC\Gherkinics\Feedback\TokenFeedback|null
     */
    public function get($id)
    {
        return isset($this->messageList[$id])
            ? $this->messageList[$id]
            : null;
    }
}
