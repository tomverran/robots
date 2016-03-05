<?php
namespace tomverran\Robot;


use tomverran\Robot\UserAgent\UserAgent;

class Record
{
    /**
     * @var UserAgent
     */
    private $ua;

    /**
     * @var AccessRules
     */
    private $ar;

    public function __construct(UserAgent $ua, AccessRules $ar)
    {
        $this->ua = $ua;
        $this->ar = $ar;
    }

    public function isAllowed($userAgent, $url)
    {
        return !$this->ua->matches($userAgent) || $this->ar->isAllowed($url);
    }
}