# Gherkin Code Sniffer

By Juti Noppornpitak

## Dependencies

* twig (dev-master, required for HTML report)

## Installation

To use normally, run `composer.phar install`.

If you plan on developing or extending functionality, run `composer.phar install --dev`.

## Usage

To use this tool, please run `php cuke.php [--html <directory_to_put_reports>] <configuration_file_path> <directory_path_to_scan>`.

## Available Analyzers

### Semantic Analyzer

#### Context Flow

This analyzer first checks for the given-when-then context flow (precondition-action-assertion) in the following patterns:

    Precondition
      -> Continuation (as precondition)
      -> Action
      -> Assertion

    Action
      -> Continuation (as action)
      -> Assertion

    Assertion
      -> Continuation (as assertion)
      -> Precondition
      -> Action

#### Semantic Quality

It also asserts one of the conditions:

* any contexts containing "click", "fill", "follow", or "select" is an action,
* any contexts containing "should" or "must" is an assertion.

When a context does not satisfy two or more conditions, the analyzer will consider it an exception (and emit no warnings).

### Coding Style Checker

This analyzer checks if:

* **tab characters** are not used,
* the width of indentation is consistent where the default width is **4 spaces**,
* there exists no **trailing spaces**,
* features have **no leading spaces** (no indentation),
* backgrounds, scenarios, and scenario outlines have exactly **one** level of indentation,
* preconditions, actions, assertions and continuations have exactly **two** levels of indentation,
* tabular data has exactly **three** levels of indentation,
* tag lines have at most **one** level of indentation,
* extra spaces (two or more whitespaces) occur in non-tabular-data contexts.

### Instaclick's Coding Style Checker

This analyzer checks if any tags' name must:

* start with **@**,
* start and end with alphanumeric characters,
* only use hyphens ("-"),
* be in lowercase.

## Write a Custom Analyzer

All analyzers must implement `IC\Gherkinics\Analyzer\AnalyzerInterface`.