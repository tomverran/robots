<?php
namespace tomverran\Robot\UserAgent;

use tomverran\Robot\AccessRules;

class AccessRulesTest extends \PHPUnit_Framework_TestCase
{
    private function assertAllowed($url, array $rules)
    {
        $this->assertTrue((new AccessRules($rules))->isAllowed($url),
            var_export($url, true) . " should be allowed by " . var_export($rules, true));
    }

    private function assertDisallowed($url, array $rules)
    {
        $this->assertFalse((new AccessRules($rules))->isAllowed($url),
            var_export($url, true) . " should not be allowed by " . var_export($rules, true));
    }

    private function assertUrlMatches($recordPath, $urlPath)
    {
        $this->assertAllowed($urlPath, [$recordPath => true]);
        $this->assertDisallowed($urlPath, [$recordPath => false]);
    }

    private function assertUrlDoesNotMatch($recordPath, $urlPath)
    {
        $this->assertAllowed($urlPath, [$recordPath => false]);
    }

    /*
     * These tests are implemented with reference to http://www.robotstxt.org/norobots-rfc.txt
     * With the exception of the first match being used, which is contradicted by Google
     */

    /**
     * @test
     */
    public function givenNoMatches_isAllowed()
    {
        $this->assertAllowed('/', []);
        $this->assertAllowed('/foo/bar/index.html', []);
        $this->assertAllowed('', []);
    }

    /**
     * @test
     */
    public function givenExactMatch_returnWhateverTheRuleSpecifies()
    {
        $this->assertUrlMatches('/tmp', '/tmp');
        $this->assertUrlMatches('/tmp/', '/tmp/');
    }

    /**
     * @test
     */
    public function givenUrlPathBeginsWithRecordPath_returnWhateverTheRuleSpecifies()
    {
        $this->assertUrlMatches('/tmp', '/tmp.html');
        $this->assertUrlMatches('/tmp', '/tmp/a.html');
        $this->assertUrlMatches('/tmp/', '/tmp/a.html');
    }

    /**
     * @test
     */
    public function givenRecordPathLongerThanUrlPath_doNotmatch()
    {
        $this->assertUrlDoesNotMatch('/tmp/', '/tmp');
    }

    /**
     * @test
     */
    public function givenUrlEncodedCharacters_decodeBeforeMatching()
    {
        $this->assertUrlMatches('/a%3cd.html', '/a%3cd.html');
        $this->assertUrlMatches('/a%3Cd.html', '/a%3cd.html');
        $this->assertUrlMatches('/a%3cd.html', '/a%3Cd.html');
        $this->assertUrlMatches('/a%3Cd.html', '/a%3Cd.html');

        $this->assertUrlMatches('/%7ejoe/index.html', '/~joe/index.html');
        $this->assertUrlMatches('/~joe/index.html', '/%7Ejoe/index.html');
    }

    /**
     * @test
     */
    public function givenUrlEncodedSlashes_doNotDecodeBeforeMatching()
    {
        $this->assertUrlMatches('/a%2fb.html', '/a%2fb.html');

        // the spec says don't decode slashes, so they should be treated case-sensitively I assume
        $this->assertUrlDoesNotMatch('/a%2Fb.html', '/a%2fb.html');
        $this->assertUrlDoesNotMatch('/a%2fb.html', '/a%2Fb.html');

        $this->assertUrlDoesNotMatch('/a%2fb.html', '/a/b.html');
        $this->assertUrlDoesNotMatch('/a/b.html', '/a%2fb.html');
        $this->assertUrlMatches('/a/b.html', '/a/b.html');
    }

    /*
     * The below tests implement google's extensions to the robots txt syntax or test against their examples.
     * https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt?hl=en#url-matching-based-on-path-values
     */

    /**
     * @test
     */
    public function givenGoogleFishExamples_workAsExpected()
    {
        foreach(['/fish', '/fish.html', '/fish/salmon.html', '/fishheads', '/fishheads/yummy.html', '/fish.php?id=anything'] as $str) {
            $this->assertAllowed('/fish*', [$str => true]);
            $this->assertAllowed('/fish', [$str => true]);
        }
    }

    /**
     * @test
     * "The trailing slash means this matches anything in this folder."
     */
    public function givenGoogleFishWithTrailingSlash_workAsExpected()
    {
        foreach(['/fish/', '/fish/?id=anything', '/fish/salmon.htm'] as $str) {
            $this->assertUrlMatches('/fish/', $str);
        }
        foreach(['/fish', '/fish.html', '/Fish/Salmon.asp'] as $str) {
            $this->assertUrlDoesNotMatch('/fish/', $str);
        }
    }

    /**
     * @test
     */
    public function givenMidRecordWildcard_expandWhenMatching()
    {
        foreach(['/filename.php', '/folder/filename.php', '/folder/filename.php?parameters', '/folder/any.php.file.html', '/filename.php/'] as $str) {
            $this->assertUrlMatches('/*.php', $str);
        }
        foreach(['/', '/windows.PHP'] as $str) {
            $this->assertUrlDoesNotMatch('/*.php', $str);
        }
    }

    /**
     * @test
     */
    public function givenTrailingDollar_AnchorMatchToEndOfString()
    {
        foreach(['/filename.php', '/folder/filename.php'] as $str) {
            $this->assertUrlMatches('/*.php$', $str);
        }
        foreach(['/filename.php?parameters', '/filename.php/', '/filename.php5', '/windows.PHP'] as $str) {
            $this->assertUrlDoesNotMatch('/*.php$', $str);
        }
    }

    /**
     * @test
     */
    public function givenFishWildcardExamples_workAsExpected()
    {
        foreach(['/fish.php', '/fishheads/catfish.php?parameters'] as $str) {
            $this->assertUrlMatches('/fish*.php', $str);
        }
        $this->assertUrlDoesNotMatch('/fish*.php', '/Fish.PHP');
    }

    /**
     * @test
     */
    public function givenMultipleMatches_pickMostSpecificRuleBasedOnLengthOfPathEntry()
    {
        $this->assertAllowed('/page', ['/p' => true, '/' => false]);
        $this->assertAllowed('/page', ['/' => false, '/p' => true]);
        $this->assertDisallowed('/page', ['/' => false]);
    }
}