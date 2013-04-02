<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Util;

/**
 * Output Handler
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class Output
{
    /**
     * Write content to STDOUT.
     *
     * @param string $content
     */
    public function write($content)
    {
        print $content;
    }

    /**
     * Write content to STDOUT with a line break.
     *
     * @param string $content
     */
    public function writeln($content)
    {
        print $content . PHP_EOL;
    }
}
