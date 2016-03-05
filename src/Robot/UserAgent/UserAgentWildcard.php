<?php
namespace tomverran\Robot\UserAgent;

/**
 * A user-agent matcher that matches everything
 */
class UserAgentWildcard implements UserAgent
{
    public function matches($userAgent)
    {
        return true;
    }
}