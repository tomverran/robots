<?php
namespace tomverran\Robot;


class RobotsFileTest extends \PHPUnit_Framework_TestCase
{

    public function getTestFile($separator)
    {
        return new RobotsFile("User-Agent: *${separator}Disallow: /");
    }

    public function newLines()
    {
        return [
            'only cr' => ["\r"],
            'only lf' =>["\n"],
            'cr + lf' =>["\r\n"]
        ];
    }

    /**
     * @test
     * @dataProvider newLines
     * @param $newline string The line separator to use
     */
    public function givenCrOnlyLineBreaks_pickUpTwoLines($newline)
    {
        $test = $this->getTestFile($newline);
        $this->assertEquals(['User-Agent', '*'], [$test->key(), $test->current()]);
        $test->next();
        $this->assertEquals(['Disallow', '/'], [$test->key(), $test->current()]);
    }


    /**
     * Get a RobotsTxt class loaded up with google's robots.txt
     * which is like, the most complex one I've seen.
     * @param string $file The filename
     * @return RobotsTxt
     */
    private static function getRobotsFile($file)
    {
        $d = DIRECTORY_SEPARATOR;
        return new RobotsFile(file_get_contents(dirname(__FILE__) . $d . 'files' . $d . $file . '.txt'));
    }

    public function testInvalidDataIn()
    {
        $robots = self::getRobotsFile('invalid-data-in');
        $lines = implode(',', iterator_to_array($robots));
        $this->assertEquals('SomeUserAgent,x,', $lines);
    }

    public function testEmptyfile()
    {
        $robots = self::getRobotsFile('empty-file');
        $this->assertEmpty(iterator_to_array($robots));
    }

    public function testTestContent() {
        $robots = self::getRobotsFile('test');
        $this->assertEquals(['Test' => ''], iterator_to_array($robots));
    }
}
