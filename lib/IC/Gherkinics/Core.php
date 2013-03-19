<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics;

use IC\Gherkinics\Exception\CodingStandardViolation;
use IC\Gherkinics\Exception\SemanticError;
use IC\Gherkinics\Util\Output;

/**
 * Core
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
final class Core
{
    /**
     * @var array
     */
    private $pathToFeedbackMap;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var IC\Gherkinics\AnalyzerManager
     */
    private $analyzerManager;

    public function __construct()
    {
        $this->pathToFeedbackMap = array();
        $this->output            = new Output();
    }

    /**
     * Define the base path
     *
     * @param string $basePath
     */
    final public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    final public function setAnalyzerManager(AnalyzerManager $analyzerManager)
    {
        $this->analyzerManager = $analyzerManager;
    }

    final public function validate($filePath)
    {
        if ( ! file_exists($filePath)) {
            throw new \RuntimeException('File not found: ' . $filePath);
        }

        $content = file_get_contents($filePath);

        return $this->analyzerManager->analyze($content);
    }

    public function scan($path)
    {
        foreach (glob($path) as $subPath) {
            if (is_dir($subPath)) {
                $this->scan($subPath . '/*');

                continue;
            }

            if ( ! preg_match('/\.feature/', $subPath)) {
                continue;
            }

            $feedbackMap = $this->validate($subPath);
            
            $this->output->write(empty($feedbackMap) ? '.' : '!');

            $this->pathToFeedbackMap[$subPath] = $this->validate($subPath);
        }
        
        return $this->pathToFeedbackMap;
    }
}