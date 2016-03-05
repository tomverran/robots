<?php
namespace tomverran\Robot;


class AccessRules
{
    private $rules;

    /**
     * @param Boolean[] $rules A map of path => allowed
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    private function urlDecodeNonSlashes($str)
    {
        return implode(array_map(function($input) {
            return strtolower($input) == '%2f' ? $input : urldecode($input);
        }, preg_split('/(%2F)/i', $str, -1, PREG_SPLIT_DELIM_CAPTURE)), '');
    }

    public function isAllowed($url)
    {
        $matches = [];
        foreach($this->rules as $ruleUrl => $allowed) {
            if (strpos($this->urlDecodeNonSlashes($url), $this->urlDecodeNonSlashes($ruleUrl)) === 0) {
                $matches[$ruleUrl] = $allowed;
            }
        }
        return empty($matches) || array_shift($matches);
    }
}