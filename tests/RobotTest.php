<?php
use tomverran\Robot\RobotsTxt;

/**
 * FieldsetTest.php
 * @author Tom
 * @since 14/03/14
 */
class RobotTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get a RobotsTxt class loaded up with google's robots.txt
     * which is like, the most complex one I've seen.
     * @param string $file The filename
     * @return RobotsTxt
     */
    private static function getRobotsTxt( $file )
    {
        $d = DIRECTORY_SEPARATOR;
        return new RobotsTxt(file_get_contents(dirname(__FILE__).$d.'files'.$d.$file.'.txt'));
    }

    /**
     * Test that we're not allowed to access a resource which is forbidden.
     */
    public function testBasicDisallow()
    {
        $this->assertFalse(self::getRobotsTxt('google')->isAllowed('robot', '/print'), 'robot cannot access /print');
    }

    /**
     * Test we are allowed to access a resource with Allow:
     */
    public function testBasicAllow()
    {
        $this->assertTrue(self::getRobotsTxt('google')->isAllowed('robot', '/m/finance'), 'robot can access /m/finance');
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
} 