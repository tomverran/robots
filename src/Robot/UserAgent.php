<?php
namespace tomverran\Robot;

/**
 * A user agent matcher that matches against a list of user agent strings
 */
class UserAgent
{
    private $userAgents;

    public function __construct(array $userAgents)
    {
        $this->userAgents = array_map('strtolower', $userAgents);
    }

    public function getMatches($userAgent)
    {
        $ua = strtolower($userAgent);
        if (!$ua) {
            return [];
        }

        $matches = array_filter($this->userAgents, function($elem) use($ua) {
            return strpos($elem, $ua) !== false || strpos($ua, $elem) !== false || $elem == '*';
        });
        return array_values($matches);
    }
}