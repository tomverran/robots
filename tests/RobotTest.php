<?php
namespace tomverran\Robot;

/**
 * FieldsetTest.php
 * @author Tom
 * @since 14/03/14
 */
class RobotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get a RobotsTxt class loaded up with google's robots.txt
     * which is like, the most complex one I've seen.
     * @param string $file The filename
     * @return RobotsTxt
     */
    private static function getRobotsTxt($file)
    {
        $d = DIRECTORY_SEPARATOR;
        return new RobotsTxt(file_get_contents(dirname(__FILE__) . $d . 'files' . $d . $file . '.txt'));
    }

    private function assertRfcExample($url, $allowed, $disallowed) {
        $robots = self::getRobotsTxt('rfc');
        foreach($allowed as $allowedUa) {
            $this->assertTrue($robots->isAllowed($allowedUa, $url), "$allowedUa allowed $url");
        }
        foreach($disallowed as $disallowedUa) {
            $this->assertFalse($robots->isAllowed($disallowedUa, $url), "$disallowedUa disallowed $url");
        }
    }

    /**
     * Test that we're not allowed to access a resource which is forbidden.
     */
    public function testBasicDisallow()
    {
        $this->assertFalse(self::getRobotsTxt('google')->isAllowed('robot', '/print'), 'robot cannot access /print');
        $this->assertTrue(self::getRobotsTxt('google')->isDisallowed('robot','/search'),'robot should not access /search');
    }

    /**
     * Test we are allowed to access a resource with Allow:
     */
    public function testBasicAllow()
    {
        $this->assertTrue(self::getRobotsTxt('google')->isAllowed('robot', '/m/finance'), 'robot can access /m/finance');
        $this->assertFalse(self::getRobotsTxt('google')->isDisallowed('robot','/m/finance'),'robot should be able to access /m/finance');
    }

    /**
     * /safebrowsing is disallowed but has children which override it, being allowed
     * test that first of all we honour the disallow of /safebrowsing
     */
    public function testNonLeafDisallow()
    {
        $this->assertFalse(self::getRobotsTxt('google')->isAllowed('robot', '/safebrowsing'), 'robot cannot access /safebrowsing');
    }

    /**
     * Now test that we can access /safebrowsing/diagnostic even though we can't access /safebrowsing
     */
    public function testNonLeafAllow()
    {
        $this->assertTrue(self::getRobotsTxt('google')->isAllowed('robot', '/safebrowsing/diagnostic'), 'robot can access /safebrowsing/diagnostic');
    }

    /**
     * Apparently  *s as wildcards aren't particularly standardised so I support them somewhat
     * so foo/* should match foo/whatever. What won't work is if you have thing/<asterisk>/stuff and you want
     * thing/whatever/really/huh/stuff as I only support wildcards on a URL part by URL part basis
     */
    public function testMyDodgyWildcardSupport()
    {
        $this->assertFalse(self::getRobotsTxt('google')->isAllowed('robot', '/compare/something/applyToThis'), 'wildcards kinda work');
    }

    /**
     * Quite a lot of sites include a really itty bitty robots.txt
     * which just has a wildcard user agent and a blank disallow to indicate they're pretty laid back
     */
    public function testBlankDisallowsMeanAllowed()
    {
        $this->assertTrue(self::getRobotsTxt('minimal')->isAllowed('robot', '/'), 'blank disallows = allow');
    }

    /**
     * Some robots.txt files include inline comments.
     */
    public function testInlineComments()
    {
        $this->assertFalse(self::getRobotsTxt('comment')->isAllowed('robot', '/comment'), 'lines with inline comments');
        $this->assertFalse(self::getRobotsTxt('comment')->isAllowed('robot', '/test'), 'lines following those with inline comments');
    }

    /**
     * This entire library should be case insensitive
     */
    public function testCapitalisedUserAgent()
    {
        $this->assertFalse(self::getRobotsTxt('multiUserAgent')->isAllowed('Googlebot', '/private/page.html'), 'Capitalised UA');
    }

    /**
     *
     */
    public function testMultipleConsecutiveUserAgents()
    {
        foreach(['UA', 'Googlebot', '*'] as $agent) {
            $this->assertFalse(self::getRobotsTxt('multiUserAgent')->isAllowed($agent, '/private/page.html'), $agent);
            $this->assertTrue(self::getRobotsTxt('multiUserAgent')->isAllowed($agent, '/'), $agent);
        }
    }

    public function testMultipleNonConsecutiveUserAgents()
    {
        $this->assertFalse(self::getRobotsTxt('multiUserAgent')->isAllowed('robot2', '/some/other'));
        $this->assertTrue(self::getRobotsTxt('multiUserAgent')->isAllowed('Googlebot', '/some/other'));
    }

    public function testFileWithInvalidLines()
    {
        $this->assertTrue(self::getRobotsTxt('corrupted')->isAllowed('Googlebot', '/some/other'));
    }

    public function testRfcExample()
    {
        $unhipBot = 'unhipbot';
        $webcrawler = 'webcrawler';
        $excite = 'excite';
        $other = 'googlebot';

        $this->assertRfcExample('/', [$webcrawler, $excite], [$other, $unhipBot]);
        $this->assertRfcExample('/index.html', [$webcrawler, $excite], [$other, $unhipBot]);
        $this->assertRfcExample('/robots.txt', [$webcrawler, $excite, $other, $unhipBot], []);
        $this->assertRfcExample('/server.html', [$webcrawler, $excite, $other], [$unhipBot]);
        $this->assertRfcExample('/services/fast.html', [$webcrawler, $excite, $other], [$unhipBot]);
        $this->assertRfcExample('/services/slow.html', [$webcrawler, $excite, $other], [$unhipBot]);
        $this->assertRfcExample('/orgo.gif', [$webcrawler, $excite], [$unhipBot, $other]);
        $this->assertRfcExample('/org/about.html', [$webcrawler, $excite, $other], [$unhipBot]);
        $this->assertRfcExample('/org/plans.html', [$webcrawler, $excite], [$unhipBot, $other]);
        $this->assertRfcExample('/%7Ejim/jim.html', [$webcrawler, $excite], [$unhipBot, $other]);
        $this->assertRfcExample('/%7Emak/mak.html', [$webcrawler, $excite, $other], [$unhipBot]);
    }

    public function testExactMatchesBeatPartial()
    {
        $this->assertTrue(self::getRobotsTxt('precedence')->isAllowed('Googlebot-News', '/news'));
        $this->assertTrue(self::getRobotsTxt('precedence')->isDisallowed('Googlebot', '/news'));
    }

    public function testGooglePrecedenceSpec()
    {
        $g = self::getRobotsTxt('precedence');
        $this->assertTrue($g->isAllowed('Googlebot-News', '/news'));
        $this->assertTrue($g->isAllowed('Googlebot', '/bot'));

        $this->assertTrue($g->isAllowed('Googlebot-Images', '/bot'));
        $this->assertTrue($g->isDisallowed('Googlebot-Images', '/news'));
    }

    public function testCaseSensitivePaths()
    {
        $g = self::getRobotsTxt('caseSensitive');
        $this->assertFalse($g->isAllowed('Googlebot', '/lowercase.html'));
        $this->assertTrue($g->isAllowed('Googlebot', '/LOWERCASE.HTML'));

        $this->assertTrue($g->isAllowed('Googlebot', '/camelcase.html'));
        $this->assertFalse($g->isAllowed('Googlebot', '/CamelCase.html'));

        $this->assertTrue($g->isAllowed('Googlebot', '/uppercase.html'));
        $this->assertFalse($g->isAllowed('Googlebot', '/UPPERCASE.HTML'));
    }

    /**
     * Do not emit an E_NOTICE with an empty needle from strpos when a user-agent line is invalid (empty).
     * This effectively ignores the line.
     */
    public function testHandlesEmptyUserAgentLine()
    {
        $invalid_text = <<<EOF
User-agent: foo
User-agent:
Disallow: /
EOF;

        $g = new RobotsTxt($invalid_text);

        $this->assertFalse($g->isAllowed('foo', '/something'));
        $this->assertTrue($g->isAllowed('Googlebot', '/something'));
    }
}
