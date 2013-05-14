<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Printer;

/**
 * Report Printer for Jenkins CI in Checkstyle format
 *
 * This printer produces reports in XML (checkstyle format) and export them to a specified location.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class JenkinsReportPrinter
{
    /**
     * @var string
     */
    private $scannedPath;

    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * Constructor
     *
     * @param string $loaderPath
     * @param string $outputPath
     * @param string $scannedPath
     * @param string $cachePath
     */
    public function __construct($loaderPath, $outputPath, $scannedPath, $cachePath = null)
    {
        $environmentOption = $cachePath === null
            ? array()
            : array('cache' => $cachePath);

        $this->scannedPath  = $scannedPath;
        $this->outputPath   = $outputPath;
        $this->environment  = new \Twig_Environment(
            new \Twig_Loader_Filesystem($loaderPath),
            $environmentOption
        );

        if (file_exists($outputPath) || is_dir($outputPath)) {
            throw new \RuntimeException('The path (' . $outputPath . ') exists but is not a directory.');
        }
    }

    /**
     * Display feedback
     *
     * @param array $pathToFeedbackMap
     */
    public function doPrint(array $pathToFeedbackMap)
    {
        $pathToDataMap    = array();
        $prefixLength     = strlen($this->scannedPath) + 1;
        $fileScanningMode = count($pathToFeedbackMap) === 1;

        foreach ($pathToFeedbackMap as $filePath => $lineToViolationsMap) {
            $relativePath      = $fileScanningMode ? $filePath : substr($filePath, $prefixLength);
            $violatedLineCount = count($lineToViolationsMap->all());

            $segmentList = array();

            preg_match(
                '/(?P<directory>.+)\/(?P<name>[^\.]+)\.(?P<extension>.+)$/',
                $relativePath,
                $segmentList
            );

            if ( ! $segmentList) {
                preg_match(
                    '/(?P<name>[^\.]+)\.(?P<extension>.+)$/',
                    $relativePath,
                    $segmentList
                );

                $segmentList['directory'] = '';
            }

            $pathToDataMap[realpath($filePath)] = array(
                'path'              => $relativePath,
                'directory'         => $segmentList['directory'],
                'name'              => $segmentList['name'],
                'extension'         => $segmentList['extension'],
                'hash'              => sha1($relativePath),
                'violatedLineCount' => $violatedLineCount,
                'feedbackMap'       => $lineToViolationsMap,
            );
        }

        $this->printCheckStyle($pathToFeedbackMap, $pathToDataMap);
    }

    /**
     * Render template
     *
     * @param string $templatePath
     * @param array  $contextVariableMap
     *
     * @return string
     */
    private function render($templatePath, $contextVariableMap = array())
    {
        $output = $this->environment->render($templatePath, $contextVariableMap);

        // Pretty print
        $output = preg_replace('/(>)(<file)/', "\${1}\n    \${2}", $output);
        $output = preg_replace('/(<error[^>]+>)/', "\n        \${1}", $output);
        $output = preg_replace('/(<\/file>)/', "\n    \${1}", $output);
        $output = preg_replace('/(<\/checkstyle>)/', "\n\${1}", $output);

        return $output;
    }

    /**
     * Export the rendered report to file
     *
     * @param string $templatePath
     * @param array  $contextVariableMap
     */
    private function export($templatePath, $contextVariableMap = array())
    {
        $output = $this->render($templatePath, $contextVariableMap);

        if (file_exists($this->outputPath)) {
            unlink($this->outputPath);
        }

        file_put_contents($this->outputPath, $output);
    }

    /**
     * Print summary in checkstyle format
     *
     * @param array $pathToFeedbackMap
     * @param array $pathToDataMap
     */
    private function printCheckStyle(array $pathToFeedbackMap, array $pathToDataMap)
    {
        $relativePathToDataMap = array();

        foreach ($pathToDataMap as $filePath => $dataMap) {
            $relativePathToDataMap[$filePath] = $dataMap;
        }

        $this->export(
            'checkstyle.xml.twig',
            array(
                'relativePathToDataMap' => $relativePathToDataMap,
            )
        );
    }
}
