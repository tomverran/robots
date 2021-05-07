<?php
namespace tomverran\Robot;
use tomverran\Robot\UserAgent;

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

    public function matches($userAgent)
    {
        $matches = $this->ua->getMatches($userAgent);
        return !empty($matches);
    }

    public function getMatchStrength($userAgent)
    {
        $matches = $this->ua->getMatches($userAgent);

        if (empty($matches)) {
            return 0;
        }
        return max(array_map('strlen', $matches));
    }

    public function isAllowed($userAgent, $url)
    {
        return !$this->ua->getMatches($userAgent) || $this->ar->isAllowed($url);
    }

    public function matchesExactly($userAgent)
    {
        return in_array(strtolower($userAgent), $this->ua->getMatches($userAgent));
    }
}