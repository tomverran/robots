<?php
namespace tomverran\Robot\UserAgent;

use tomverran\Robot\UserAgent;

class UserAgentTest extends \PHPUnit_Framework_TestCase
{
    private function assertDoesNotMatch($userAgent, array $list)
    {
        $this->assertTrue(!count((new UserAgent($list))->getMatches($userAgent)),
            var_export($userAgent, true) . " should not match " . var_export($list, true));

    }

    private function assertMatches($userAgent, $list)
    {
        $this->assertTrue(count((new UserAgent($list))->getMatches($userAgent)) > 0,
            var_export($userAgent, true) ." should match " . var_export($list, true));
    }

    /**
     * @test
     */
    public function givenEmptyList_whenMatchingAnyUserAgent_returnFalse()
    {
        $this->assertDoesNotMatch('anything', []);
    }

    /**
     * @test
     */
    public function givenNullUserAgent_doNotMatch()
    {
        $this->assertDoesNotMatch(null, ['a', 'b', 'c']);
    }

    /**
     * @test
     */
    public function givenEmptyUserAgent_doNotMatch()
    {
        $this->assertDoesNotMatch('', ['a', '', 'c']);
    }

    /**
     * @test
     */
    public function givenNoMatches_doNotMatch()
    {
        $this->assertDoesNotMatch('a', ['b', 'c', 'd', 'e']);
    }

    /**
     * @test
     */
    public function givenExactMatch_returnTrue()
    {
        $this->assertMatches('foo', ['foo']);
    }

    /**
     * @test
     */
    public function givenExactMatchWithOthersNonMatching_returnTrue()
    {
        $this->assertMatches('foo', ['aaa', 'boo', 'cee', 'foo', 'dee']);
    }

    /**
     * @test
     * "The name comparisons are case-insensitive."
     */
    public function givenUserAgent_whenMatching_ignoreCase()
    {
        $this->assertMatches('FoO', ['aaa', 'boo', 'cee', 'foo', 'dee']);
        $this->assertMatches('fOo', ['AA', 'bOo', 'ceE', 'FOO', 'FOB']);
    }

    /**
     * @test
     */
    public function givenSubstringMatch_returnTrue()
    {
        $this->assertMatches('foo', ['Cats', 'Fred*', 'Googlebot', 'BotFoo/1.0', 'Dave/2.0']);
    }
    /**
     * @test
     */
    public function givenWildcardAgent_alwaysMatch()
    {
        $wildcard = new UserAgent(['*']);
        foreach(['FigTree/0.1 Robot libwww-perl/5.04', 'Googlebot', 'Tombot', '1234', 'aaaa'] as $ua) {
            $this->assertTrue($wildcard->getMatches($ua) == ['*'], 'wilcard matches all');
        }
    }

    /**
     * @test
     * https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#order-of-precedence-for-user-agents
     */
    public function givenGoogleExamples_behaveAsExpected()
    {
        $googleUAs = new UserAgent(['googlebot-news', '*', 'googlebot']);
        $this->assertContains('googlebot-news', $googleUAs->getMatches('Googlebot-News'));
        $this->assertContains('googlebot', $googleUAs->getMatches('Googlebot-Images'));
        $this->assertNotContains('googlebot-news', $googleUAs->getMatches('Googlebot-Images'));
        $this->assertEquals(['*'], $googleUAs->getMatches('otherbot'));
    }
}