<?php

/**
*  talk to redis-server
*/


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
			$this->expire_time = 6048000;
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
		if($return) {
			wt_debugbar_message("connected to redis-server ".wt_redis_host, "connect", "redis");
		}
		return $return;
	}

	private function error($message) {

		global $vars;

		trigger_error($message,E_USER_NOTICE);

		wt_debugbar_message($message, "error", "redis");

		if($vars["lokale_testserver"]) {
			exit;
		}

	}

	public function store_array($group, $key, $data) {

		wt_debugbar_message("store_array group:".$group." - key:".$key, "query", "redis");

		try {
			$this->redis->hSet($this->prefix.$group, $key, serialize($data));

			if($this->expire_time) {
				$this->redis->expire($this->prefix.$group, $this->expire_time);
			}
		}
		catch (Exception $e) {
			$this->error("error redis store_array:". $e->getMessage());
			return false;
		}
	}

	public function get_array($group, $key) {

		wt_debugbar_message("get_array group:".$group." - key:".$key, "query", "redis");

		try {
			$data = $this->redis->hGet($this->prefix.$group, $key);
		}
		catch (Exception $e) {
			$this->error("error redis get_array:". $e->getMessage());
			return false;
		}

		if($data) {
			$return = unserialize($data);
		}

		return $return;

	}

	public function array_group_exists($group) {

		wt_debugbar_message("array_group_exists group:".$group, "query", "redis");

		try {
			$return = $this->redis->exists($this->prefix.$group);
		}
		catch (Exception $e) {
			$this->error("error redis array_group_exists:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function array_group_delete($group) {

		wt_debugbar_message("array_group_delete group:".$group, "query", "redis");

		try {
			$return = $this->redis->del($this->prefix.$group);
		}
		catch (Exception $e) {
			$this->error("error redis array_group_delete:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function set($key, $value) {

		wt_debugbar_message("set key:".$key." - value:".$value, "query", "redis");

		try {
			$return = $this->redis->set($this->prefix.$key, $value);
		}
		catch (Exception $e) {
			$this->error("error redis set:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function get($key) {

		wt_debugbar_message("get key:".$key, "query", "redis");

		try {
			$return = $this->redis->get($this->prefix.$key);
		}
		catch (Exception $e) {
			$this->error("error redis get:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function hset($key, $field, $value) {

		wt_debugbar_message("hset key:".$key." - field:".$field, "query", "redis");

		try {
			$return = $this->redis->hset($this->prefix.$key, $field, $value);

			if($this->expire_time) {
				$this->redis->expire($this->prefix.$key, $this->expire_time);
			}

		}
		catch (Exception $e) {
			$this->error("error redis hset:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function hget($key, $field) {

		wt_debugbar_message("hget key:".$key." - field:".$field, "query", "redis");

		try {
			$return = $this->redis->hget($this->prefix.$key, $field);
		}
		catch (Exception $e) {
			$this->error("error redis hget:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function hgetall($key, $use_prefix=true) {

		wt_debugbar_message("hgetall key:".$key, "query", "redis");

		try {
			if($use_prefix) {
				$return = $this->redis->hgetall($this->prefix.$key);
			} else {
				$return = $this->redis->hgetall($key);
			}
		}
		catch (Exception $e) {
			$this->error("error redis hgetall:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function hexists($key, $field) {

		wt_debugbar_message("hexists key:".$key." - field:".$field, "query", "redis");

		try {
			$return = $this->redis->hexists($this->prefix.$key, $field);
		}
		catch (Exception $e) {
			$this->error("error redis hexists:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function del($key) {

		wt_debugbar_message("del key:".$key, "query", "redis");

		try {
			$return = $this->redis->del($this->prefix.$key);
		}
		catch (Exception $e) {
			$this->error("error redis del:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function exists($key) {

		wt_debugbar_message("exists key:".$key, "query", "redis");

		try {
			$return = $this->redis->exists($this->prefix.$key);
		}
		catch (Exception $e) {
			$this->error("error redis exists:". $e->getMessage());
			return false;
		}
		return $return;
	}

	public function keys($query) {

		wt_debugbar_message("keys query:".$key, "query", "redis");

		try {
			$return = $this->redis->keys($this->prefix.$query);
		}
		catch (Exception $e) {
			$this->error("error redis exists:". $e->getMessage());
			return false;
		}
		return $return;
	}

}

?>