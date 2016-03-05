<?php
namespace tomverran\Robot;
use tomverran\Robot\UserAgent\UserAgentList;
use tomverran\Robot\UserAgent\UserAgentWildcard;
use tomverran\Robot\UserAgent\UserAgentWildcardTest;

/**
 * RobotsTxt.php
 * @author Tom
 * @since 14/04/14
 */
class RobotsTxt
{
    /**
     * @var Record[]
     */
    private $records;

    /**
     * Construct this Robots.txt model
     * @param $contents
     */
    public function __construct($contents)
    {
        $this->parseFile(new RobotsFile($contents));
    }

    /**
     * Parse a robot file
     * @param $robotFile
     * @throws \LogicException
     */
    private function parseFile(RobotsFile $robotFile)
    {
        while($robotFile->hasLines()) {
            $currentUserAgents = [];

            while ($robotFile->firstDirectiveIs(RobotsFile::USER_AGENT)) {
                $currentUserAgents[] = $robotFile->shiftArgument();
            }

            $accessRules = [];
            while ($robotFile->firstDirectiveIs(RobotsFile::ALLOW, RobotsFile::DISALLOW)) {
                $isAllowed = $robotFile->firstDirective() == RobotsFile::ALLOW;
                $accessRules[$robotFile->shiftArgument()] = $isAllowed;
            }

            $ua = in_array('*', $currentUserAgents) ? new UserAgentWildcard : new UserAgentList($currentUserAgents);
            $this->records[] = new Record($ua, new AccessRules($accessRules));
        }
    }

    /**
     * Is the given user agent allowed access to the given resource
     * @param string $userAgent The user agent to check
     * @param string $path The path of the URL
     * @return bool|null
     */
    public function isAllowed($userAgent, $path)
    {
        foreach($this->records as $record) {
            if (!$record->isAllowed($userAgent, $path)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Is the given user agent disallowed access to the given resource?
     * @param  string  $userAgent The user agent to check
     * @param  string  $path The path of the url
     * @return bool|null
     */
    public function isDisallowed($userAgent, $path) 
    {
        return !$this->isAllowed($userAgent,$path);
    }
}