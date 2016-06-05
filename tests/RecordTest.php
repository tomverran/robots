<?php
namespace tomverran\Robot;

/**
 * We're just testing wiring here
 * @package tomverran\Robot
 */
class RecordTest extends \PHPUnit_Framework_TestCase
{
    // a stub could be made for these but I figured it isn't worth it really
    // if this test gets broken due to AccessRules being broken it might be worth it

    private $allUrlsAllowed;
    private $fooForbidden;

    public function setUp() {
        $this->fooForbidden = new AccessRules(['/foo' => false]);
        $this->allUrlsAllowed = new AccessRules([]);
    }

    /**
     * @test
     */
    public function givenNoUserAgentMatch_whenDeterminingIfUrlIsAllowed_returnTrue()
    {
        $neverMatchingUserAgent = new UserAgent([]);
        $this->assertTrue((new Record($neverMatchingUserAgent, $this->allUrlsAllowed))->isAllowed('Googlebot', '/foo'));
    }

    /**
     * @test
     */
    public function givenUserAgentMatch_delegateToAccessRules()
    {
        $alwaysMatchingUserAgent = new UserAgent(['*']);
        $this->assertFalse((new Record($alwaysMatchingUserAgent, $this->fooForbidden))->isAllowed('Googlebot', '/foo'));
        $this->assertTrue((new Record($alwaysMatchingUserAgent, $this->allUrlsAllowed))->isAllowed('Googlebot', '/foo'));
    }

    /**
     * @test
     */
    public function givenNoMatches_returnMatchStrengthOfZero()
    {
        $googleOnly = new Record(new UserAgent(['Googlebot']), new AccessRules([]));
        $this->assertTrue($googleOnly->getMatchStrength('Bing') == 0, 'No match at all means a match strength of zero');
    }

    /**
     * @test
     */
    public function givenMatch_returnLengthOfMatchedUAAsMatchStrength()
    {
        $googleOnly = new Record(new UserAgent(['Googlebot']), new AccessRules([]));
        $this->assertTrue($googleOnly->getMatchStrength('G') == 9, 'Length of the matched UA is the strength');
    }

    /**
     * @test
     */
    public function givenExactMatch_flagAsBeingExact()
    {
        $googleOnly = new Record(new UserAgent(['Googlebot']), new AccessRules([]));
        $this->assertTrue($googleOnly->matchesExactly('Googlebot'));
        $this->assertTrue($googleOnly->matchesExactly('googlebot'));

        $this->assertFalse($googleOnly->matchesExactly('google'));
        $this->assertFalse($googleOnly->matchesExactly('googlebot-news'));
    }
}