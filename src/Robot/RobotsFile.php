<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 11/10/2015
 * Time: 9:15 PM
 */

namespace tomverran\Robot;


class RobotsFile implements \Iterator
{
    /**
     * @var \ArrayIterator
     */
    private $lineIterator;

    /**
     * Construct this Robots file
     * @param String $content
     */
    public function __construct($content)
    {
        $withoutComments = preg_replace( '/#.*/', '', $content);
        $lines = [];

        foreach(preg_split("/[\r\n]+/", $withoutComments) as $line) {
            $lineParts = array_filter(array_map('trim', explode(':', $line)));
            if (!empty($lineParts)) {
                $lines[] = $lineParts;
            }
        }

        $this->lineIterator = new \ArrayIterator($lines);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $cur = $this->lineIterator->current();
        return count($cur) > 1 ? $cur[1] : '';
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->lineIterator->next();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        $cur = $this->lineIterator->current();
        return $cur[0];
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->lineIterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->lineIterator->rewind();
    }
}
