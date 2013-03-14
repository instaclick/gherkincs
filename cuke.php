#!/usr/bin/env php
<?php
/**
 * Cuke - Coding Standard Checker and Semantic Analyzer for Gherkin
 *
 * @copyright 2013 Instaclick Inc.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */

$basePath = dirname(__FILE__);

function __autoload($className)
{
    global $basePath;

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
    global $basePath;

    $output   = new Output();
    $manager  = new AnalyzerManager();
    $cuke     = new Core();

    if (count($args) < 2) {
        $output->writeln('USAGE: cuke /path/to/config_file /path/pattern/to/scan');

        exit(1);
    }

    $configPath = $args[0];
    $targetPath = $args[1];

    // Set up the analyzer manager.
    $manager->setLexer(new Lexer());

    $config = simplexml_load_file($configPath);

    if ( ! isset($config->analyzers)) {
        $output->writeln('Notice: the configuration file is invalid.');

        exit(1);
    }

    if ( ! isset($config->analyzers->analyzer)) {
        $output->writeln('Terminated due to that no analyzers are found.');

        exit(1);
    }

    foreach ($config->analyzers->analyzer as $analyzer) {
        $analyzerClass = '\\'.$analyzer['class'];
        $output->write('       Registering analyzer: ' . $analyzerClass);
        $manager->registerAnalyzer(new $analyzerClass());
        $output->writeln("\r[DONE]");
    }

    // Set up the core object.
    $cuke->setBasePath($targetPath);
    $cuke->setAnalyzerManager($manager);

    $output->writeln(PHP_EOL . 'Analyzing feature files...');

    $cuke->scan($targetPath . '/*');

    $output->writeln('');
    $cuke->showFeedback();

    $output->writeln('Analysis complete.');
    $output->writeln(PHP_EOL . 'Please note that this tool only detects classic errors.');
    $output->writeln('Bye bye!');
}

main(array_slice($argv, 1));