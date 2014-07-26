<?php

namespace Zoya;

use Zoya\Coin\CoinInterface;

/**
 * Class InfiniteList
 * @package Zoya
 */
class InfiniteList implements \Iterator
{
    /**
     * @var CoinInterface
     */
    private $coin;
    /**
     * @var array
     */
    private $items;
    /**
     * @var \InfiniteIterator
     */
    private $list;

    /**
     * @param array $items
     * @param CoinInterface $coin
     */
    public function __construct(array $items, CoinInterface $coin)
    {
        $this->items = $items;
        $this->coin = $coin;
        $this->init();
    }

    /**
     * @return \Zoya\Coin\CoinInterface
     */
    public function getCoin()
    {
        return $this->coin;
    }

    /**
     * @param $item
     * @return $this
     */
    public function prepend($item)
    {
        array_unshift($this->items, $item);
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function append($item)
    {
        array_push($this->items, $item);
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function setItems()
    {
        return $this->items;
    }

    /**
     *
     */
    public function init()
    {
        $this->list = new \InfiniteIterator(new \ArrayIterator($this->items));
        $this->list->rewind();
    }

    /**
     *
     */
    public function next()
    {
        if ($this->list->valid()) {
            $this->getCoin()->flip();
            if ($this->getCoin()->isLucky()) {
                $this->list->next();
            }
        } else {
            throw new \RuntimeException('No more items');
        }
    }

    /**
     * Remove current item from iterator
     */
    public function removeCurrentItem()
    {
        $this->list->offsetUnset($this->list->key());
        $this->list->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->list->current();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->list->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->list->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->list->rewind();
    }
}
