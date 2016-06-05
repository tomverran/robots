Robots.txt checker
==================

[![Build Status](https://travis-ci.org/tomverran/robots.svg?branch=master)](https://travis-ci.org/tomverran/robots)
[![Packagist](https://img.shields.io/packagist/v/tomverran/robots-txt-checker.svg)](https://packagist.org/packages/tomverran/robots-txt-checker)
[![Test Coverage](https://codeclimate.com/github/tomverran/robots/badges/coverage.svg)](https://codeclimate.com/github/tomverran/robots/coverage)
[![Code Climate](https://codeclimate.com/github/tomverran/robots/badges/gpa.svg)](https://codeclimate.com/github/tomverran/robots)


Given a robots.txt file this library will give you a straight forward yes/no as to whether you're allowed to access
a given resource with a given user agent.

This library has been built with reference to both [Google's robots.txt specification](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt) and the draft [norobots RFC](http://www.robotstxt.org/norobots-rfc.txt) from 1996(!). As such all the following features are supported:

 - Wildcards in paths, including across directory boundaries
 - The '$' suffix to anchor a path match to the end of a URL
 - Sorting of multiple matching user-agent/path blocks by user-agent length
 - Decoding of URL encoded paths, with the exception of forward slashes
 - A trailing slash on a path indicating that all files under that directory should match

If you find any bugs with this library please don't hesitate to let me know, either create an issue on GitHub or submit a pull request.

Installation
------------

Either type the following into your terminal:

```bash
composer require tomverran/robots-txt-checker
```

or add the following to your composer.json:

```json
"require": {
    "tomverran/robots-txt-checker": "^1.12"
}
```

Example Usage
-------------

I personally use this library alongside an http client library such that all requests go through a class that checks the site's robots.txt first. Basic usage is as follows:
```php
<?php
use \tomverran\Robot\RobotsTxt;
$robotsTxt = new RobotsTxt(file_get_contents('http://your-site-here/robots.txt'));
$canViewPage = $robotsTxt->isAllowed('my-user-agent', '/some/path/');
```
License
-------

tl;dr MIT license

Copyright (c) 2014, 2016 Tom Verran

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
