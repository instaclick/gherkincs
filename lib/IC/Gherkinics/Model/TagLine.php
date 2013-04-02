<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Model;

/**
 * Token for Tag Line
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class TagLine extends Token
{
    /**
     * @var array
     */
    private $tagList = array();

    /**
     * {@inheritdoc}
     */
    public function __construct($id, $rawContent, $context)
    {
        parent::__construct($id, $rawContent, $context);

        foreach (preg_split('/\s+/', trim($rawContent)) as $tag) {
            $tag = trim($tag);

            if ( ! $tag) {
                continue;
            }

            $this->tagList[] = new Tag($tag);
        }
    }

    /**
     * Retrieve the list of tags
     *
     * @return array
     */
    public function getTagList()
    {
        return $this->tagList;
    }
}
