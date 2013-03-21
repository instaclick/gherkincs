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
        $pathToDataMap = array();
        $prefixLength  = strlen($this->scannedPath) + 1;

        foreach ($pathToFeedbackMap as $filePath => $lineToViolationsMap) {
            $relativePath      = substr($filePath, $prefixLength);
            $violatedLineCount = count($lineToViolationsMap->all());

            $segmentList = array();

            preg_match(
                '/(?P<directory>.+)\/(?P<name>[^\.]+)\.(?P<extension>.+)$/',
                $relativePath,
                $segmentList
            );

            $pathToDataMap[$filePath] = array(
                'path'              => $relativePath,
                'directory'         => $segmentList['directory'],
                'name'              => $segmentList['name'],
                'extension'         => $segmentList['extension'],
                'hash'              => sha1($relativePath),
                'violatedLineCount' => $violatedLineCount,
            );
        }

        $this->printSummary($pathToFeedbackMap, $pathToDataMap);
        $this->printMultipleReports($pathToFeedbackMap, $pathToDataMap);
        $this->copyStaticResource();
    }

    private function copyStaticResource()
    {
        exec(sprintf('cp -r %s %s', $this->resourcePath, $this->outputPath));
    }

    private function render($templatePath, $contextVariableMap = array())
    {
        return $this->environment->render($templatePath, $contextVariableMap);
    }

    private function export($templatePath, $outputName, $contextVariableMap = array())
    {
        $filePath = $this->outputPath . '/' . $outputName;
        $output   = $this->render($templatePath, $contextVariableMap);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        file_put_contents($filePath, $output);
    }

    private function printSummary(array $pathToFeedbackMap, array $pathToDataMap)
    {
        $relativePathToDataMap    = array();
        $maximumViolatedLineCount = 0;

        foreach ($pathToDataMap as $filePath => $dataMap) {
            if ($maximumViolatedLineCount < $dataMap['violatedLineCount']) {
                $maximumViolatedLineCount = $dataMap['violatedLineCount'];
            }

            $relativePathToDataMap[$dataMap['path']] = $dataMap;
        }

        $this->export(
            'index.html.twig',
            'index.html',
            array(
                'relativePathToDataMap'    => $relativePathToDataMap,
                'maximumViolatedLineCount' => $maximumViolatedLineCount,
            )
        );
    }

    private function printMultipleReports(array $pathToFeedbackMap, array $pathToDataMap)
    {
        foreach ($pathToDataMap as $filePath => $dataMap) {
            $this->export(
                'file_feedback.html.twig',
                $dataMap['hash'] . '.html',
                array(
                    'feedbackMap' => $pathToFeedbackMap[$filePath],
                    'dataMap'     => $dataMap,
                )
            );
        }
    }
}