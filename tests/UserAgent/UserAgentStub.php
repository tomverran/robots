<?php
namespace tomverran\Robot\UserAgent;


class UserAgentStub implements UserAgent
{
    private $matches;

    public function __construct($matches)
    {
        $this->matches = $matches;
    }
    public function matches($userAgent)
    {
        return $this->matches;
    }
}