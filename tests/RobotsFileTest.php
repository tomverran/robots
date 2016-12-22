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
}
