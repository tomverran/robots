<?php
namespace tomverran\Robot;
/**
 * RobotsTxt.php
 * @author Tom
 * @since 14/04/14
 */
class RobotsTxt
{
    /**
     * @var Leaf
     */
    private $tree;

    /**
     * Construct this Robots.txt model
     * @param $contents
     */
    public function __construct($contents)
    {
        $this->tree = new Leaf();
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
            while ($robotFile->firstDirectiveIs('user-agent')) {
                $currentUserAgents[] = $robotFile->shiftArgument();
            }
            while ($robotFile->firstDirectiveIs('allow', 'disallow')) {
                $isAllowed = $robotFile->firstDirective() == 'allow';
                $urlParts = array_filter(explode('/', $robotFile->shiftArgument()));
                $this->tree->getNode($urlParts)->addRule($currentUserAgents, $isAllowed);
            }
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
        $urlParts = array_filter(explode('/', $path));
        return $this->tree->allowed(strtolower($userAgent), $urlParts) !== false;
    }
}