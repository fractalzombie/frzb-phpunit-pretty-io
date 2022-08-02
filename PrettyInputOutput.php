<?php

declare(strict_types=1);

namespace FRZB\PHPUnit\IO;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Runner\BaseTestRunner;
use PHPUnit\TextUI\DefaultResultPrinter;
use PHPUnit\Util\Filter;

class PrettyInputOutput extends DefaultResultPrinter
{
    private const DEFAULT_LONG_PROCESS_EXECUTION = 0.5;

    protected string $className = '';
    protected string $previousClassName = '';

    public function startTest(Test $test): void
    {
        $this->className = $test::class;
    }

    public function endTest(Test $test, float $time): void
    {
        parent::endTest($test, $time);

        $testMethodName = \PHPUnit\Util\Test::describe($test);

        $parts = preg_split('/ with data set /', $testMethodName[1]);
        $methodName = array_shift($parts);
        $dataSet = array_shift($parts);

        // Convert capitalized words to lowercase
        $methodName = preg_replace_callback('/([A-Z]{2,})/', static fn (array $matches) => strtolower($matches[0]), $methodName);

        // Convert non-breaking method name to camelCase
        $methodName = str_replace("\u{a0}", '', ucwords($methodName, "\u{a0}"));

        // Convert snakeCase method name to camelCase
        $methodName = str_replace('_', '', ucwords($methodName, '_'));

        preg_match_all('/((?:^|[A-Z])[a-z\d]+)/', $methodName, $matches);

        // Prepend all numbers with a space
        $replaced = preg_replace('/(\d+)/', ' $1', $matches[0]);

        $testNameArray = array_map(strtolower(...), $replaced);

        $name = implode(' ', $testNameArray);

        // check if prefix is test remove it
        $name = preg_replace('/^test /', '', $name, 1);

        // Get the data set name
        if ($dataSet) {
            // Note: Use preg_replace() instead of trim() because the dataset may end with a quote
            // (double quotes) and trim() would remove both from the end. This matches only a single
            // quote from the beginning and end of the dataset that was added by PHPUnit itself.
            $name .= ' [ '.preg_replace('/^"|"$/', '', $dataSet).' ]';
        }

        $this->write(' ');

        match (method_exists($test, 'getStatus') ? $test->getStatus() : BaseTestRunner::STATUS_UNKNOWN) {
            BaseTestRunner::STATUS_PASSED => $this->writeWithColor('fg-green', $name, false),
            BaseTestRunner::STATUS_SKIPPED => $this->writeWithColor('fg-yellow', $name, false),
            BaseTestRunner::STATUS_INCOMPLETE => $this->writeWithColor('fg-blue', $name, false),
            BaseTestRunner::STATUS_FAILURE => $this->writeWithColor('fg-red', $name, false),
            BaseTestRunner::STATUS_ERROR => $this->writeWithColor('fg-red', $name, false),
            BaseTestRunner::STATUS_RISKY => $this->writeWithColor('fg-magenta', $name, false),
            BaseTestRunner::STATUS_WARNING => $this->writeWithColor('fg-magenta', $name, false),
            BaseTestRunner::STATUS_UNKNOWN => $this->writeWithColor('fg-cyan', $name, false),
            default => $this->writeWithColor('fg-cyan', $name, false),
        };

        $this->write(' ');

        $this->writeWithColor(
            $time > self::DEFAULT_LONG_PROCESS_EXECUTION ? 'fg-yellow' : 'fg-white',
            '['.number_format($time, 3).'s]',
        );
    }

    protected function writeProgress(string $progress): void
    {
        if ($this->previousClassName !== $this->className) {
            $this->write("\n");
            $this->writeWithColor('bold', $this->className, false);
            $this->writeNewLine();
        }

        $this->previousClassName = $this->className;

        $this->printProgress();

        match (strtoupper(preg_replace('#\\x1b[[^A-Za-z]*[A-Za-z]#', '', $progress))) {
            '.' => $this->writeWithColor('fg-green', '  ✓', false),
            'S' => $this->writeWithColor('fg-yellow', '  →', false),
            'I' => $this->writeWithColor('fg-blue', '  ∅', false),
            'F' => $this->writeWithColor('fg-red', '  x', false),
            'E' => $this->writeWithColor('fg-red', '  ⚈', false),
            'R' => $this->writeWithColor('fg-magenta', '  ⌽', false),
            'W' => $this->writeWithColor('fg-yellow', '  ¤', false),
            default => $this->writeWithColor('fg-cyan', '  ≈', false),
        };
    }

    protected function printDefectTrace(TestFailure $defect): void
    {
        $this->write($this->formatExceptionMsg($defect->getExceptionAsString()));
        $trace = Filter::getFilteredStacktrace($defect->thrownException());

        if (!empty($trace)) {
            $this->write(\PHP_EOL.$trace);
        }

        $exception = $defect->thrownException()->getPrevious();

        while ($exception) {
            $this->write(
                \PHP_EOL.'Caused by'.\PHP_EOL
                .TestFailure::exceptionToString($exception).\PHP_EOL
                .Filter::getFilteredStacktrace($exception)
            );

            $exception = $exception->getPrevious();
        }
    }

    protected function formatExceptionMsg($exceptionMessage): string
    {
        $exceptionMessage = str_replace("+++ Actual\n", '', $exceptionMessage);
        $exceptionMessage = str_replace("--- Expected\n", '', $exceptionMessage);
        $exceptionMessage = str_replace('@@ @@', '', $exceptionMessage);

        if ($this->colors) {
            $exceptionMessage = preg_replace('/^(Exception.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage);
            $exceptionMessage = preg_replace('/(Failed.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage);
            $exceptionMessage = preg_replace('/(-+.*)$/m', "\033[01;32m$1\033[0m", $exceptionMessage);
            $exceptionMessage = preg_replace('/(\\++.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage);
        }

        return $exceptionMessage;
    }

    private function printProgress(): void
    {
        if (filter_var(getenv('PHPUNIT_PRETTY_PRINT_PROGRESS'), \FILTER_VALIDATE_BOOLEAN)) {
            ++$this->numTestsRun;

            $total = $this->numTests;
            $current = str_pad((string) $this->numTestsRun, \strlen((string) $total), '0', \STR_PAD_LEFT);

            $this->write("[{$current}/{$total}]");
        }
    }
}
