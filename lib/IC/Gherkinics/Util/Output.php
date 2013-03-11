<?php
namespace IC\Gherkinics\Util;

class Output
{
    public function write($content)
    {
        print $content;
    }

    public function writeln($content)
    {
        print $content . PHP_EOL;
    }
}