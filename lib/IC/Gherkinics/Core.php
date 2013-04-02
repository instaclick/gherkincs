<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics;

use IC\Gherkinics\Exception\CodingStandardViolation;
use IC\Gherkinics\Exception\SemanticError;
use IC\Gherkinics\Util\Output;

/**
 * Gherkinics Core
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

    /**
     * Constructor
     */
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

    /**
     * Define the analyzer manager
     *
     * @param \IC\Gherkinics\AnalyzerManager $analyzerManager
     */
    final public function setAnalyzerManager(AnalyzerManager $analyzerManager)
    {
        $this->analyzerManager = $analyzerManager;
    }

    /**
     * Validate file
     *
     * @param string $filePath
     *
     * @return \IC\Gherkinics\Feedback\FileFeedback
     */
    final public function validate($filePath)
    {
        if ( ! file_exists($filePath)) {
            throw new \RuntimeException('File not found: ' . $filePath);
        }

        $content = file_get_contents($filePath);

        return $this->analyzerManager->analyze($content);
    }

    /**
     * Scan and validate the path
     *
     * @param string $path
     *
     * @return array
     */
    public function scan($path)
    {
        if (is_file($path)) {
            $this->scanFile($path);

            return $this->pathToFeedbackMap;
        }

        foreach (glob($path) as $subPath) {
            if (is_dir($subPath)) {
                $this->scan($subPath . '/*');

                continue;
            }

            $this->scanFile($subPath);
        }

        return $this->pathToFeedbackMap;
    }

    /**
     * Scan an individual file.
     *
     * @param string $path file path
     */
    private function scanFile($path)
    {
        if ( ! preg_match('/\.feature$/', $path)) {
            return;
        }

        $feedbackMap = $this->validate($path);

        $this->output->write($feedbackMap->count() > 0 ? '!' : '.');

        if ($feedbackMap->count() === 0) {
            return;
        }

        $this->pathToFeedbackMap[$path] = $this->validate($path);
    }
}
