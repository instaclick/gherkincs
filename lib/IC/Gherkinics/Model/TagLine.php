<?php
namespace IC\Gherkinics\Model;

class TagLine extends Token
{
    /**
     * @var array
     */
    private $tagList = array();

    public function __construct($id, $rawContent, $context)
    {
        parent::__construct($id, $rawContent, $context);

        foreach (preg_split('/\s+/', $rawContent) as $tag) {
            $tag = trim($tag);

            if ( ! $tag) {
                continue;
            }

            $this->tagList[] = new Tag($tag);
        }
    }

    public function getTagList()
    {
        return $this->tagList;
    }
}