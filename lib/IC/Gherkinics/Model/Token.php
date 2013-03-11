<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Model;

/**
 * Analyzer Interface
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

    public function __construct($id, $rawContent, $context)
    {
        $this->id         = $id;
        $this->rawContent = $rawContent;
        $this->context    = trim($context);
    }

    final public function getId()
    {
        return $this->id;
    }

    final public function getRawContent()
    {
        return $this->rawContent;
    }

    final public function getContext()
    {
        return $this->context;
    }

    final public function getClassName()
    {
        if ( ! $this->className) {
            $this->className = preg_replace('/.+\\\/', '', get_class($this));
        }

        return $this->className;
    }

    final public function setPrevious(Token $previous=null)
    {
        $this->previous = $previous;
    }

    final public function getPrevious()
    {
        return $this->previous;
    }

    final public function setNext(Token $next=null)
    {
        $this->next = $next;
    }

    final public function getNext()
    {
        return $this->next;
    }

    public function __toString()
    {
        return sprintf("%15s %5d: %s", $this->getClassName(), $this->id, $this->rawContent);
    }

    public function makeComment($message)
    {
        return $message;
    }
}