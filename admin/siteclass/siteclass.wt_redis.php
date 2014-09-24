<?php

/**
*  talk to redis-server
*/


// IMHO you should catch the RedisException and reconnect to redis server yourself. and also you should check your redis.conf.


class wt_redis {

	private $redis;

	public $retry = 0;
	public $expire_time = 604800; // expire every key after 7 days

	function __construct() {



		global $vars;

		if(!$this->connect()) {
			$this->connect();
		}

		if($vars["acceptatie_testserver"] or $vars["lokale_testserver"] or (defined("wt_test") and wt_test===true)) {
			$this->prefix = "chalettest_";
		}

	}

	private function connect() {

		$return = false;

		try {
			$this->redis = new Redis();
			$this->redis->connect(wt_redis_host, 6379);
			$return = true;
		}
		catch (Exception $e) {
			$this->error("Couldn't connect to Redis:". $e->getMessage());
		}
		return $return;
	}

	private function error($message) {

		global $vars;

		trigger_error($message,E_USER_NOTICE);

		if($vars["lokale_testserver"]) {
			exit;
		}

	}

	public function store_array($group, $key, $data) {

		try {
			$this->redis->hSet($this->prefix.$group, $key, serialize($data));

			if($this->expire_time) {
				$this->redis->expire($this->prefix.$group, $this->expire_time);
			}
		}
		catch (Exception $e) {
			$this->error("error redis store_array:". $e->getMessage());
		}
	}

	public function get_array($group, $key) {

		try {
			$data = $this->redis->hGet($this->prefix.$group, $key);
		}
		catch (Exception $e) {
			$this->error("error redis get_array:". $e->getMessage());
		}

		if($data) {
			$return = unserialize($data);
		}

		return $return;

	}

	public function array_group_exists($group) {

		try {
			$return = $this->redis->exists($this->prefix.$group);
		}
		catch (Exception $e) {
			$this->error("error redis array_group_exists:". $e->getMessage());
		}
		return $return;
	}

	public function array_group_delete($group) {

		try {
			$return = $this->redis->del($this->prefix.$group);
		}
		catch (Exception $e) {
			$this->error("error redis array_group_delete:". $e->getMessage());
		}
		return $return;
	}

	public function get($key) {

		try {
			$return = $this->redis->get($this->prefix.$key);
		}
		catch (Exception $e) {
			$this->error("error redis get:". $e->getMessage());
		}
		return $return;
	}

	public function hset($key, $field, $value) {

		try {
			$return = $this->redis->hset($this->prefix.$key, $field, $value);

			if($this->expire_time) {
				$this->redis->expire($this->prefix.$key, $this->expire_time);
			}

		}
		catch (Exception $e) {
			$this->error("error redis hset:". $e->getMessage());
		}
		return $return;
	}

	public function hget($key, $field) {

		try {
			$return = $this->redis->hget($this->prefix.$key, $field);
		}
		catch (Exception $e) {
			$this->error("error redis hget:". $e->getMessage());
		}
		return $return;
	}

	public function hgetall($key, $use_prefix=true) {

		try {
			if($use_prefix) {
				$return = $this->redis->hgetall($this->prefix.$key);
			} else {
				$return = $this->redis->hgetall($key);
			}
		}
		catch (Exception $e) {
			$this->error("error redis hgetall:". $e->getMessage());
		}
		return $return;
	}

	public function hexists($key, $field) {

		try {
			$return = $this->redis->hexists($this->prefix.$key, $field);
		}
		catch (Exception $e) {
			$this->error("error redis hexists:". $e->getMessage());
		}
		return $return;
	}

	public function del($key) {

		try {
			$return = $this->redis->del($this->prefix.$key);
		}
		catch (Exception $e) {
			$this->error("error redis del:". $e->getMessage());
		}
		return $return;
	}

	public function exists($key) {

		try {
			$return = $this->redis->exists($this->prefix.$key);
		}
		catch (Exception $e) {
			$this->error("error redis exists:". $e->getMessage());
		}
		return $return;
	}

	public function keys($query) {

		try {
			$return = $this->redis->keys($this->prefix.$query);
		}
		catch (Exception $e) {
			$this->error("error redis exists:". $e->getMessage());
		}
		return $return;
	}

}

?>