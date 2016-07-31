# Codeception DataProvider

[![Latest Version](https://img.shields.io/packagist/v/edno/codeception-dataprovider.svg?style=flat-square)](https://packagist.org/packages/edno/codeception-dataprovider)
[![Dependency Status](https://www.versioneye.com/user/projects/579e2cc8aa78d50041cb0baf/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/579e2cc8aa78d50041cb0baf)
[![Build Status](https://img.shields.io/travis/edno/codeception-dataprovider.svg?style=flat-square)](https://travis-ci.org/edno/codeception-dataprovider)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/16715897-2e48-48c3-bed0-1c4dc452da1a.svg?style=flat-square)](https://insight.sensiolabs.com/projects/16715897-2e48-48c3-bed0-1c4dc452da1a)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/edno/codeception-dataprovider.svg?style=flat-square)](https://scrutinizer-ci.com/g/edno/codeception-dataprovider/?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/edno/codeception-dataprovider.svg?style=flat-square)](https://coveralls.io/github/edno/codeception-gherkin-param?branch=master)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/edno/codeception-secureshell/master/LICENSE)

The [Codeception](http://codeception.com/) extension for supporting dynamic data driven tests (CEST) using `@dataprovider` annotation.

## Minimum Requirements

- Codeception 2.2
- PHP 5.4

## Installation
The extension can be installed using [Composer](https://getcomposer.org)

```bash
$ composer require edno/codeception-dataprovider
```

Be sure to enable the extension in `codeception.yml` as shown in
[configuration](#configuration) below.
## Configuration
Enabling **DataProvider** annotation in your tests is done in `codeception.yml`.

```yaml
extensions:
    enabled:
        - Codeception\Extension\DataProvider
```

## Usage
Once installed you will be to use the `@dataprovider` annotation for defining the
method to be use for fetching the test data.  
Your data source must be a public static function located within your test class.
The method should return data compliant with the `@example` annotation.

## Example
```php
<?php

class ExampleDataProviderCest
{
     /**
      * @dataprovider __myDataSource
      */
      public function testWithDataProvider(FunctionalTester $I, \Codeception\Example $example)
      {
            $expected = ["", "foo", "bar", "re"];
            $I->assertInternalType('integer', $example[0]);
            $I->assertEquals($expected[$example[0]], $example[1]);
      }

      public static function __myDataSource()
      {
          return [
              [1, "foo"],
              [2, "bar"],
              [3, "re"]
          ];
      }
}
```
