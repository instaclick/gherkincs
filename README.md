# Gherkin Code Sniffer

By Juti Noppornpitak

## How to Use

To use this tool, please run `php cuke.php <configuration_file_path> <directory_path_to_scan>`.

## Available Analyzers

### Semantic Analyzer

This analyzer first checks for the given-when-then context flow (precondition-action-assertion) in the following patterns:

* Precondition
** Continuation (as precondition)
** Action
** Assertion
* Action
** Continuation (as action)
** Assertion
* Assertion
** Continuation (as assertion)
** Precondition
** Action

It also see if:

* any contexts containing "click", "fill", "follow", or "select" is an action,
* any contexts containing "should" or "must" is an assertion.

### Coding Style Checker

This analyzer checks if:

* **tab characters** are not used,
* the width of indentation is inconsistent where the default width is **4 spaces**,
* there exists **trailing spaces**,
* features will have **no leading spaces** (no indentation),
* backgrounds, scenarios, and scenario outlines have exactly **one** level of indentation,
preconditions, actions, assertions and continuations have exactly **two** levels of indentation,
* tabular data has exactly **three** levels of indentation,
* tag lines has at most **one** level of indentation.

### Instaclick's Coding Style Checker

This analyzer checks if any tags' name must:

* start with **@**,
* start and end with alphanumeric characters,
* only use hyphens ("-"),
* be in lowercase.

## Write a Custom Analyzer

All analyzers must implement `IC\Gherkinics\Analyzer\AnalyzerInterface`.