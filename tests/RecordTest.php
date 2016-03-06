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
}