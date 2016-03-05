<?php
namespace tomverran\Robot\UserAgent;

/**
 * A user agent matcher that matches against a list of user agent strings
 */
class UserAgentList implements UserAgent
{
    private $userAgents;

    public function __construct(array $userAgents)
    {
        $this->userAgents = array_map('strtolower', $userAgents);
    }

    public function matches($userAgent)
    {
        $ua = strtolower($userAgent);
        return $ua && count(array_filter($this->userAgents, function($elem) use($ua) {
            return strpos($elem, $ua) !== false;
        })) > 0;
    }
}