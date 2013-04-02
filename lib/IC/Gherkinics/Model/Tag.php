<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Model;

/**
 * Sub Token for Tag
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
final class Tag
{
    private $name;

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Retrieve the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
