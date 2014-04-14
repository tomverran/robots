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
        $this->parseFile($contents);
    }

    /**
     * Parse a robot file
     * @param $robotFile
     * @throws \LogicException
     */
    private function parseFile( $robotFile )
    {
        $currentUserAgent = null;
        foreach (explode( "\n", strtolower($robotFile)) as $line) {
            list($directive, $argument) = array_map('trim', explode(':', $line));

            //handle setting our user agent
            if ($directive == 'user-agent') {
                $currentUserAgent = $argument;
                continue;
            } else if (!$currentUserAgent) {
                throw new \LogicException('No user agent specified');
            }

            if ($directive == 'sitemap') {
                continue; //not yet supported
            }

            //the last case is allow / deny. Add to the tree
            $urlParts = array_filter(explode('/', $argument));
            $this->tree->getNode($urlParts)->addRule($currentUserAgent, $directive != 'disallow');
        }
    }

    /**
     * Is the given user agent allowed access to the given resource
     * @param $userAgent
     * @param $path
     * @return bool|null
     */
    public function isAllowed( $userAgent, $path )
    {
        $urlParts = array_filter(explode('/', $path));
        $ret = $this->tree->allowed($userAgent, $urlParts);

        if ($ret === null) {
            $ret = $this->tree->allowed('*', $urlParts);
        }
        if ( $ret === null) {
            $ret = true;
        }
        return $ret;
    }
}