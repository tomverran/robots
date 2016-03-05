<?php
namespace tomverran\Robot\UserAgent;

class UserAgentWildcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function givenAnyUserAgent_returnTrue()
    {
        $wildcard = new UserAgentWildcard();
        foreach(['FigTree/0.1 Robot libwww-perl/5.04', 'Googlebot', 'Tombot', '1234', 'aaaa'] as $ua) {
            $this->assertTrue($wildcard->matches($ua), 'wilcard matches all');
        }
    }
}