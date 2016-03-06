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
        uksort($rules, function($r1, $r2) {
            return strlen($r2) - strlen($r1);
        });
        $this->rules = $rules;
    }

    private function convertPathToRegex($path) {
        $pathWithoutTrailingDollar = rtrim($path, '$');

        $quotedWithWildcards = implode(array_map(function($input) {
            return preg_quote($input, '#');
        }, explode('*', $pathWithoutTrailingDollar)),'.*?');

        $trailingDollar = $path == $pathWithoutTrailingDollar ? '' : '$';
        return "#^${quotedWithWildcards}{$trailingDollar}#";
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
            if ($ruleUrl && preg_match($this->convertPathToRegex($this->urlDecodeNonSlashes($ruleUrl)), $this->urlDecodeNonSlashes($url))) {
                $matches[$ruleUrl] = $allowed;
            }
        }
        return empty($matches) || array_shift($matches);
    }
}