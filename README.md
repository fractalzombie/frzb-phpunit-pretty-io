# phpunit-pretty-io
> ✅ Make your PHPUnit output beautiful

[![Build Status](https://travis-ci.com/sempro/phpunit-pretty-print.svg?branch=master)](https://travis-ci.com/sempro/phpunit-pretty-print)
[![Packagist](https://img.shields.io/packagist/dt/sempro/phpunit-pretty-print.svg)](https://packagist.org/packages/sempro/phpunit-pretty-print)
[![Packagist](https://img.shields.io/packagist/v/sempro/phpunit-pretty-print.svg)](https://packagist.org/packages/sempro/phpunit-pretty-print)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](http://makeapullrequest.com)
[![psr-2](https://img.shields.io/badge/code_style-PSR_2-blue.svg)](http://www.php-fig.org/psr/psr-2/)


### Installation
```bash
composer require frzb/phpunit-pretty-io --dev
```

This package supports PHPUnit `9` and `10`.

### Usage
You can specify the printer to use on the phpunit command line:

Use the following:
```bash
php vendor/bin/phpunit --printer 'FRZB\PHPUnit\IO\PrettyInputOutput' tests/
```

Optionally, you can add it to your project's `phpunit.xml` file instead:

```xml
<phpunit
    bootstrap="bootstrap.php"
    colors="true"
    printerClass="FRZB\PHPUnit\IO\PrettyInputOutput" 
/>
```

![Alt Text](Misc/preview.gif)

### Optional

To view progress while tests are running you can set `FRZB_PHPUNIT_PRETTY_IO_PROGRESS=true` as environment variable on your server or within your `phpunit.xml` config file.
```xml
<phpunit>
    <php>
        <env name="FRZB_PHPUNIT_PRETTY_IO_PROGRESS" value="true" />
    </php>
</phpunit>
```
