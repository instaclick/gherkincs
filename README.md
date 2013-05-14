# Gherkin Code Sniffer

By Juti Noppornpitak

## Dependencies

* twig (dev-master, required for HTML report)

## Installation

### Standalone

At the root directory where Gherkin CS's `composer.json` is,

**Installation (Normal):** run `composer.phar install`

**Installation (DEV):** run `composer.phar install --dev`

**Executable:** `cuke-standalone`.

### Composer with Packagist

**Installation:** Add `"instaclick/gherkincs": "dev-master"` in the `require` or `require-dev` section of the
project's `composer.json`.

**Executable:** `vendor/bin/cuke`.

## Usage

Define `cuke` as either `cuke-standalone` or `vendor/bin/cuke`.

``` bash
cuke [--jcs <directory_to_put_report>] [--html <directory_to_put_reports>] <configuration_file_path> <directory_path_to_scan>
```

where `--html` is to produce a set of reports in HTML and `--jcs` is to produce a checkstyle-format report for Jenkins/Hudson CI.

Please note that:

* by default, the report will be displayed on the standard output,
* `--html` will be used if both `--jcs` and `--html` are given.

For example,

``` bash
cuke --jcs darkside.xml config.xml jedi/
```

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