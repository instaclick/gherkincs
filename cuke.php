#!/usr/bin/env php
<?php
/**
 * Cuke - Coding Standard Checker and Semantic Analyzer for Gherkin
 *
 * @copyright 2013 Instaclick Inc.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */

function __autoload($className) {
    $basePath  = dirname(__FILE__);
    $className = preg_replace('@\\\\@', '/', $className);

    require_once "$basePath/lib/$className.php";
}

use IC\Gherkinics\AnalyzerManager;
use IC\Gherkinics\Analyzer;
use IC\Gherkinics\Core;
use IC\Gherkinics\Lexer;
use IC\Gherkinics\Util\Output;

function main($args)
{
    $output   = new Output();
    $manager  = new AnalyzerManager();
    $cuke     = new Core();

    if (count($args) === 0) {
        $output->writeln('USAGE: cuke /path/pattern/to/scan');

        exit(1);
    }
    // Set up the analyzer manager.
    $manager->setLexer(new Lexer());
    $manager->registerAnalyzer(new Analyzer\SemanticAnalyzer());
    $manager->registerAnalyzer(new Analyzer\CodingStyleChecker());
    $manager->registerAnalyzer(new Analyzer\ICCodingStyleChecker());

    // Set up the core object.
    $cuke->setBasePath($args[0]);
    $cuke->setAnalyzerManager($manager);

    $output->writeln(PHP_EOL . 'Analyzing feature files...');

    $cuke->scan($args[0] . '/*');

    $output->writeln('');
    $cuke->showFeedback();

    $output->writeln('Analysis complete.');
    $output->writeln(PHP_EOL . 'Please note that this tool only detects classic errors.');
    $output->writeln('Bye bye!');
}

main(array_slice($argv, 1));