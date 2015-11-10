<?php
namespace tomverran\Robot;
/**
 * Leaf.php
 * @author Tom
 * @since 14/04/14
 */
class Leaf
{
    /**
     * @var Leaf[]
     */
    private $children = [];

    /**
     * User agent -> is allowed
     * @var bool[]
     */
    private $rules = [];

    /**
     * Add children
     * @param $values
     * @return $this
     */
    public function getNode($values)
    {
        if (empty($values)) {
            return $this;
        }

        $childValue = array_shift($values);
        if (!isset($this->children[$childValue])) {
            $this->children[$childValue] = new Leaf();
        }

        return $this->children[$childValue]->getNode($values);
    }

    /**
     * Add a rule determining whether a user agent can access this path
     * @param string $userAgent The user agent to check
     * @param bool $allowed Are they allowed
     */
    public function addRule($userAgents, $allowed)
    {
        foreach ($userAgents as $userAgent) {
            $this->rules[$userAgent] = $allowed;
        }
    }

    /**
     * Is this user agent explicitly allowed to access this path
     * @param string $userAgent The user agent to check our rules for
     * @param array $urlParts An array of URL parts we wish to check access against
     * @return bool|null true for yes, false for no, null for no rule set (so inherit previous)
     */
    public function allowed($userAgent, $urlParts)
    {
        $wildcardRule = isset($this->rules['*']) ? $this->rules['*'] : null;
        $ourRuleForUserAgent = isset($this->rules[$userAgent]) ? $this->rules[$userAgent] : null;
        $ourRule = $ourRuleForUserAgent === null ? $wildcardRule : $ourRuleForUserAgent;

        $currentUrlPart = array_shift($urlParts);
        $theirRule = null;

        foreach ($this->children as $part => $leaf) {

            //convert our leaf into a regular expression, replacing * with regex non greedy wildcards
            $leafRegex = '/^' . str_replace('\\*', '(.*?)', preg_quote($part)) .'$/';

            if (preg_match($leafRegex, $currentUrlPart)) {
                $theirRule = $leaf->allowed($userAgent, $urlParts);
                if ($theirRule === false) { //this is a bit simplistic and wrong, just giving up on the first false
                    return false;
                }
            }
        }

        return $theirRule !== null ? $theirRule : $ourRule;
    }
} 