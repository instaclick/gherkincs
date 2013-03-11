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

            $this->output->write('.');

            $this->pathToFeedbackMap[$subPath] = $this->validate($subPath);
        }
    }

    public function showFeedback()
    {
        $pathOffset = strlen($this->basePath) + 1;

        foreach ($this->pathToFeedbackMap as $path => $lineToFeedbackListMap) {
            if ( ! $lineToFeedbackListMap) {
                continue;
            }

            $previousFeedback            = null;
            $lineNumberListWithSameError = array();

            if ( ! $lineToFeedbackListMap->all()) {
                continue;
            }

            $this->output->writeln(substr($path, $pathOffset));

            foreach ($lineToFeedbackListMap->all() as $lineNo => $feedbackList) {
                if ($previousFeedback == $feedbackList) {
                    $lineNumberListWithSameError[] = $lineNo;

                    continue;
                }

                if ($lineNumberListWithSameError) {
                    $this->output->writeln('');
                    $this->displayLineNumbersHavingThePreviousErrors($lineNumberListWithSameError);
                    $this->output->writeln('');
                }

                $this->output->writeln('  line ' . $lineNo . ':');
                $this->output->writeln('    - ' . implode('.' . PHP_EOL . '    - ', $feedbackList) . '.');

                $previousFeedback            = $feedbackList;
                $lineNumberListWithSameError = array();
            }

            if ($lineNumberListWithSameError) {
                $this->output->writeln('');
                $this->displayLineNumbersHavingThePreviousErrors($lineNumberListWithSameError);
            }

            $this->output->writeln('');
        }
    }

    /**
     * Display line numbers which have the previous errors
     *
     * @param array $lineNumberList
     */
    private function displayLineNumbersHavingThePreviousErrors(array $lineNumberList)
    {
        $this->output->writeln(
            '  ... the previous set of errors also occurs on lines: '
            . implode(', ', $lineNumberList)
        );
    }
}