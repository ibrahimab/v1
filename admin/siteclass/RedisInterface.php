<?php
namespace Chalet;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
interface RedisInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param string $field
     * @param mixed  $value
     */
    public function hset($key, $field, $value);

    /**
     * @param string $key
     * @param string $field
     * @return mixed
     */
    public function hget($key, $field);

    /**
     * @param string $key
     * @return mixed
     */
    public function hgetall($key);

    /**
     * @param string $key
     * @param string $field
     * @return bool
     */
    public function hexists($key, $field);

    /**
     * @param string $key
     */
    public function del($key);

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key);

    /**
     * @param string $query
     * @return array
     */
    public function keys($query);
}