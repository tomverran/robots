Robots.txt checker
==================

Given a robots.txt file this library will give you a straight forward yes/no as to whether you're allowed to access
a given resource with a given user agent. Internally it organises the file into a tree.

Wildcards are supported in a basic way:

`Disallow: /hello/*/world` will disallow `/hello/whatever/world` but won't disallow `/hello/what/who/world`

If you find any bugs with this library please don't hesitate to let me know, either create an issue on GitHub or submit a pull request.

Example Usage
-------------

I personally use this library alongside an http client library such that all requests go through a class that checks the site's robots.txt first. Basic usage is as follows:

    <?php
    use \tomverran\Robots\RobotsTxt;
    $robotsTxt = new RobotsTxt(file_get_contents('http://your-site-here/robots.txt'));
    $canViewPage = $robotsTxt->isAllowed('my-user-agent', '/some/path/');

License
-------

tl;dr MIT license

Copyright (c) 2014 Tom Verran

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

