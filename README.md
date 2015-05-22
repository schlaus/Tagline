# Tagline

[![Build Status](https://img.shields.io/travis/schlaus/Tagline.svg?style=flat-square)](https://travis-ci.org/schlaus/Tagline)
[![Coverage Status](https://img.shields.io/coveralls/schlaus/Tagline/master.svg?style=flat-square)](https://coveralls.io/r/schlaus/Tagline?branch=master)
[![Latest Version](https://img.shields.io/github/release/schlaus/Tagline.svg?style=flat-square)](https://packagist.org/packages/schlaus/tagline)
[![Total Downloads](https://img.shields.io/packagist/dt/schlaus/Tagline.svg?style=flat-square)](https://packagist.org/packages/schlaus/tagline)
[![Issues open](https://img.shields.io/github/issues/schlaus/Tagline.svg?style=flat-square)](https://github.com/schlaus/Tagline/issues)
[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](http://schlaus.mit-license.org)

Tagline takes a tag and a file, and tells you if the tag is found in the file, and on which line. Basically it's a vertical `strpos()` that works on files.

## Install

Via Composer

```bash
$ composer require schlaus/tagline
```

## Usage

### The basics
```php
$lineNr   = Tagline::findTag('FindMe!', 'somefile.php');
$nextLine = Tagline::findNext();
```

### API

`int Tagline::findTag(string $tag [, mixed $file = null [, int $offset = 0 [, string $interpretAs = null]]])`

The main function this class provides. Takes in a tag, a target file, and optionally an offset, and looks for the first occurrence of that tag in the file. Returns false is the tag was not found, and throws an InvalidArgumentException if the file could not be read. Otherwise returns the line number the tag was first encountered on, as an integer.

Subsequent calls can be made without specifying a file, as the contents are cached on the first search. To bypass the cache simply specify the file again. This function always returns the first occurrence of the given tag. Use the `$offset` argument or the `Tagline::findNext()` method to find other occurrences. 

#### Arguments
* `$tag` - A string to search for
* `$file` - The file to search in. Can be provided as one of the following:
⋅⋅* A filename as a string
⋅⋅* File contents as a string
⋅⋅* File contents as an array
⋅⋅* A stream resource
⋅⋅* An instance of `SplFileObject`

A string is interpreted as a filename if it has no line breaks, even if such a file doesn't exist. This is to help you catch typos and logic errors. If you really want to look for a tag in a single line string, you can force the type interpretation with the fourth argument.  

* `$offset` - Number of lines to skip from the top of the file

* `$interpretAs` - Forces input to be interpreted as a specific type. Can be one of the following:
⋅⋅* 'string'
⋅⋅* 'array'
⋅⋅* 'filename'
⋅⋅* 'stream'
⋅⋅* 'fileobject'


`int Tagline::findNext()`

Returns the line number of the next occurrence of the tag that was last searched for, or false if no more occurrences are found.


`array Tagline::anyToArray(mixed $source [, string $type = null])`

A helper function for converting any supported source into a **zero-based** line-per-key array. The arguments are the as the second and the fourth argument for `Tagline::findTag()`.


There's comments aplenty in the source, and a rather exhaustive test suite which should help in figuring out how something works if it's not clear from this documentation.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [http://schlaus.mit-license.org](http://schlaus.mit-license.org) for more information.
