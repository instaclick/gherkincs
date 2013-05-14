<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Model;

/**
 * Abstract Token
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
abstract class Token
{
    /**
     * @var \IC\Gherkinics\Model\Token|null
     */
    protected $previous = null;

    /**
     * @var \IC\Gherkinics\Model\Token|null
     */
    protected $next = null;

    /**
     * @var string class name
     */
    protected $className = null;

    /**
     * @var integer line number
     */
    protected $id;

    /**
     * @var string line content
     */
    protected $rawContent;

    /**
     * @var string feature context
     */
    protected $context;

    /**
     * Constructor
     *
     * @param integer $id         token ID (e.g., line number)
     * @param string  $rawContent the original content
     * @param string  $context    the processed context based on the raw content
     */
    public function __construct($id, $rawContent, $context)
    {
        $this->id         = $id;
        $this->rawContent = $rawContent;
        $this->context    = trim($context);
    }

    /**
     * Retrieve the ID
     *
     * @return integer
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieve the raw content
     *
     * @return string
     */
    final public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * Retrieve the context
     *
     * @return string
     */
    final public function getContext()
    {
        return $this->context;
    }

    /**
     * Retrieve the class name
     *
     * @return string
     */
    final public function getClassName()
    {
        if ( ! $this->className) {
            $this->className = preg_replace('/.+\\\/', '', get_class($this));
        }

        return $this->className;
    }

    /**
     * Retrieve the class name (Java style)
     *
     * @return string
     */
    final public function getJavaClassName()
    {
        if ( ! $this->className) {
            $this->className = preg_replace('/\\\/', '.', get_class($this));
        }

        return $this->className;
    }

    /**
     * Define the previous token
     *
     * @param \IC\Gherkinics\Model\Token|null $previous
     */
    final public function setPrevious(Token $previous = null)
    {
        $this->previous = $previous;
    }

    /**
     * Retrieve the previous token
     *
     * @return \IC\Gherkinics\Model\Token|null
     */
    final public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * Define the next token
     *
     * @param \IC\Gherkinics\Model\Token|null $next
     */
    final public function setNext(Token $next = null)
    {
        $this->next = $next;
    }

    /**
     * Retrieve the next token
     *
     * @return \IC\Gherkinics\Model\Token|null
     */
    final public function getNext()
    {
        return $this->next;
    }

    /**
     * Convert this object to string
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("%15s %5d: %s", $this->getClassName(), $this->id, $this->rawContent);
    }

    /**
     * Make a comment from this token
     *
     * @param string $message
     *
     * @return string
     */
    public function makeComment($message)
    {
        return $message;
    }
}
