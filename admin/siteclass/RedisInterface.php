<?php
namespace Chalet;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
interface RedisInterface
{
    public function set($key, $value);
    public function get($key);
    public function hset($key, $field, $value);
    public function hget($key, $field);
    public function hgetall($key);
    public function hexists($key, $field);
    public function del($key);
    public function exists($key);
    public function keys($query);
    public function togglePrefix();
}