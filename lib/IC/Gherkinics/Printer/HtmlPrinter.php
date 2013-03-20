<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Printer;

class HtmlPrinter
{
    /**
     * @var string
     */
    private $scannedPath;

    /**
     * @var string
     */
    private $resourcePath;

    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct($loaderPath, $resourcePath, $outputPath, $scannedPath, $cachePath = null)
    {
        $environmentOption = $cachePath === null
            ? array()
            : array('cache' => $cachePath);

        $this->scannedPath  = $scannedPath;
        $this->resourcePath = $resourcePath;
        $this->outputPath   = $outputPath;
        $this->environment  = new \Twig_Environment(
            new \Twig_Loader_Filesystem($loaderPath),
            $environmentOption
        );

        if (file_exists($outputPath) && ! is_dir($outputPath)) {
            throw new \RuntimeException('The path (' . $outputPath . ') exists but is not a directory.');
        }

        if ( ! file_exists($outputPath)) {
            mkdir($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function doPrint(array $pathToFeedbackMap)
    {
        $this->copyStaticResource();
        $this->printSummary($pathToFeedbackMap);
    }

    private function copyStaticResource()
    {
        exec(sprintf('cp -r %s %s', $this->resourcePath, $this->outputPath));
    }

    private function render($templatePath, $contextVariableMap = array())
    {
        return $this->environment->render($templatePath, $contextVariableMap);
    }

    private function printSummary(array $pathToFeedbackMap)
    {
        $relativePathToDataMap    = array();
        $prefixLength             = strlen($this->scannedPath) + 1;
        $maximumViolatedLineCount = 0;

        foreach ($pathToFeedbackMap as $filePath => $lineToViolationsMap) {
            $relativePath      = substr($filePath, $prefixLength);
            $violatedLineCount = count($lineToViolationsMap->all());

            if ($maximumViolatedLineCount < $violatedLineCount) {
                $maximumViolatedLineCount = $violatedLineCount;
            }

            $relativePathToDataMap[$relativePath] = array(
                'hash'              => sha1($relativePath),
                'violatedLineCount' => $violatedLineCount,
            );
        }

        $filePath = $this->outputPath . '/index.html';
        $output   = $this->render(
            'index.html.twig',
            array(
                'relativePathToDataMap'   => $relativePathToDataMap,
                'maximumViolatedLineCount' => $maximumViolatedLineCount,
            )
        );

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        file_put_contents($filePath, $output);
    }
}