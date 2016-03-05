<?php
namespace tomverran\Robot\UserAgent;

class UserAgentListTest extends \PHPUnit_Framework_TestCase
{
    private function assertDoesNotMatch($userAgent, array $list)
    {
        $this->assertFalse((new UserAgentList($list))->matches($userAgent),
            var_export($userAgent, true) . " should not match " . var_export($list, true));

    }

    private function assertMatches($userAgent, $list)
    {
        $this->assertTrue((new UserAgentList($list))->matches($userAgent),
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
}