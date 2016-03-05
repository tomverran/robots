<?php
namespace tomverran\Robot\UserAgent;

/**
 * A user agent matcher
 * @package tomverran\Robot\UserAgent
 */
interface UserAgent
{
    public function matches($userAgent);
}