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
     * @var Record[]
     */
    private $records;

    const USER_AGENT = 'user-agent';
    const DISALLOW = 'disallow';
    const ALLOW = 'allow';


    /**
     * Construct this Robots.txt model
     * @param $contents
     */
    public function __construct($contents)
    {
        $this->parseFile(new RobotsFile($contents, [self::USER_AGENT, self::ALLOW, self::DISALLOW]));
    }

    /**
     * Parse a robot file
     * @param $robotFile
     */
    private function parseFile(RobotsFile $robotFile)
    {
        $emptyRecord = [
            'rules' => [],
            'ua' => []
        ];

        $fileRecords = [];
        $lastDirective = '';

        foreach($robotFile as $directive => $value) {
            $directive = strtolower($directive);
            
            // add a new record if we have the beginning of a new set of UAs
            if ($directive == self::USER_AGENT && $lastDirective != $directive) {
                $fileRecords[] = $emptyRecord;
            }

            //modify the current record in place
            $currentRecord = &$fileRecords[count($fileRecords) - 1];

            if ($directive == self::USER_AGENT) {
                $currentRecord['ua'][] = $value;
            }

            if ($directive == self::ALLOW || $directive == self::DISALLOW) {
                $currentRecord['rules'][$value] = $directive == self::ALLOW;
            }

            $lastDirective = $directive;
        }

        $this->records = [];
        foreach($fileRecords as $record) {
            if (!empty($record['ua'])) {
                $record['ua'] = array_filter($record['ua']);
            }
            if (!empty($record['ua']) && !empty($record['rules'])) {
                $this->records[] = new Record(new UserAgent($record['ua']), new AccessRules($record['rules']));
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
        if ($path == '/robots.txt') {
            return true;
        }

        /** @var Record[] $exactMatches */
        $exactMatches = array_filter($this->records, function(Record $r) use ($userAgent) {
            return $r->matchesExactly($userAgent);
        });

        if (!empty($exactMatches)) {
            $firstMatch = array_shift($exactMatches);
            return $firstMatch->isAllowed($userAgent, $path);
        }

        $matching = array_filter($this->records, function(Record $r) use ($userAgent) {
            return $r->matches($userAgent);
        });

        uasort($matching, function(Record $r1, Record $r2) use ($userAgent) {
            return $r2->getMatchStrength($userAgent) - $r1->getMatchStrength($userAgent);
        });

        return empty($matching) || reset($matching)->isAllowed($userAgent, $path);
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
