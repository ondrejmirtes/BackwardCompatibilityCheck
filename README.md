# Roave Backward Compatibility Check

[![Latest Stable Version](https://poser.pugx.org/ondrejmirtes/backward-compatibility-check/v/stable)](https://packagist.org/packages/ondrejmirtes/backward-compatibility-check)
[![License](https://poser.pugx.org/ondrejmirtes/backward-compatibility-check/license)](https://packagist.org/packages/ondrejmirtes/backward-compatibility-check)

A tool that can be used to verify BC breaks between two versions
of a PHP library.

**This fork changes internal classes and methods interpretation according to [PHPStan backward compatibility promise](https://phpstan.org/developing-extensions/backward-compatibility-promise).**

## Pre-requisites/assumptions

* Your project uses `git`
* Your project uses `composer.json` to define its dependencies
* All source paths are covered by an `"autoload"` section in `composer.json`
* Changes need to be committed to `git` to be covered. You can implement your own logic to extract sources and dependencies from a project though.

## Installation

```bash
composer require --dev ondrejmirtes/backward-compatibility-check
```

## Usage

### Adding to a continuous integration pipeline

The typical intended usage is to just add `roave-backward-compatibility-check`
to your CI build:

```bash
vendor/bin/roave-backward-compatibility-check
```

This will automatically detect the last minor version tagged, and
compare the API against the current `HEAD`. If any BC breaks are found,
the tool returns a non-zero status, which on most CI systems will cause
the build to fail.

*NOTE:* detecting the base version only works if you have git tags in
the SemVer-compliant `x.y.z` format, such as `1.2.3`.

*NOTE:* since this tool relies on tags, you need to make sure tags are fetched
as part of your CI pipeline. For example in a GitHub action, note the use of
[`fetch-depth: 0`](https://github.com/actions/checkout#fetch-all-history-for-all-tags-and-branches):

```yaml
jobs:
  roave-backwards-compatibility-check:
    name: Roave Backwards Compatibility Check
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
        with:
          fetch-depth: 0
      - name: "Install PHP"
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.0"
      - name: "Install dependencies"
        run: "composer install"
      - name: "Check for BC breaks"
        run: "vendor/bin/roave-backward-compatibility-check"
```

### Running manually

To generate additional documentation for changelogs:

```bash
vendor/bin/roave-backward-compatibility-check --format=markdown > results.md
```

### GitHub Actions

When running in GitHub Actions, it is endorsed to use the `--format=github-actions` output format:

```bash
vendor/bin/roave-backward-compatibility-check --format=github-actions
```

### Documentation

If you need further guidance:

```bash
vendor/bin/roave-backward-compatibility-check --help
```

## Configuration

The file `.roave-backward-compatibility-check.xml` is read from the current working directory (when it exists) and sets configuration for the command.

It's expected to be an XML file that follows our [schema](Resources/schema.xsd):

**Example:**

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<roave-bc-check
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/roave/backward-compatibility-check/Resources/schema.xsd">
    <baseline>
        <ignored-regex>#\[BC\] CHANGED: The parameter \$a of TestArtifact\\TheClass\#method\(\)#</ignored-regex>
        <ignored-regex>#\[BC\] CHANGED: The parameter \$b of TestArtifact\\TheClass\#method2\(\)#</ignored-regex>
    </baseline>
</roave-bc-check>
```
