<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 11/10/2015
 * Time: 9:15 PM
 */

namespace tomverran\Robot;


class RobotsFile
{
    /**
     * @var String[]
     */
    private $lines;

    const USER_AGENT = 'user-agent';

    const DISALLOW = 'disallow';

    const ALLOW = 'allow';

    /**
     * Construct this Robots file
     * @param String $content
     */
    public function __construct($content)
    {
        $withoutComments = preg_replace( '/#.*/', '', strtolower($content));

        foreach(explode("\n", $withoutComments) as $line) {
            $lineParts = array_filter(array_map('trim', explode(':', $line)));
            if ($this->lineIsValid($lineParts)) {
                $this->lines[] = $lineParts;
            }
        }
    }

    private function lineIsValid($line) {
        $validDirectives = [self::USER_AGENT, self::DISALLOW, self::ALLOW];
        return count($line) == 2 && in_array($line[0], $validDirectives);
    }

    /**
     * Get the first directive in the file
     */
    public function firstDirective()
    {
        return $this->lines[0][0];
    }

    public function firstDirectiveIs($args)
    {
        if (!$this->hasLines()) {
            return false;
        }
        return in_array($this->firstDirective(), func_get_args());
    }

    /**
     * Get the argument of the first directive,
     * and shift the file to remove it
     */
    public function shiftArgument()
    {
        return array_shift($this->lines)[1];
    }

    /**
     * Does this file have any remaining lines
     * @return bool
     */
    public function hasLines()
    {
        return !empty($this->lines);
    }
}