<?php
namespace IC\Gherkinics\Printer;

use IC\Gherkinics\Util\Output;

/**
 * Terminal Printer
 *
 * This printer uses the standard output.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class TerminalPrinter
{
    /**
     * @var \IC\Gherkinics\Util\Output
     */
    private $output;

    /**
     * @var string
     */
    private $basePath;

    public function __construct(Output $output, $basePath)
    {
        $this->output   = $output;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function doPrint(array $pathToFeedbackMap)
    {
        $pathOffset = strlen($this->basePath) + 1;

        foreach ($pathToFeedbackMap as $path => $lineToFeedbackListMap) {
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