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

    /**
     * @test
     */
    public function givenMultiplePossibleMatches_returnFirstInList()
    {
        $this->assertAllowed('/tmp', ['/tmp' => true, '/tm' => false]);
        $this->assertDisallowed('/tmp', ['/tm' => false, '/tmp' => true]);
    }
}