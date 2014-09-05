<?php

/**
*  talk to redis-server
*/


// IMHO you should catch the RedisException and reconnect to redis server yourself. and also you should check your redis.conf.


class wt_redis {

	private $redis;

	public $retry = 0;

	function __construct() {
		if(!$this->connect()) {
			$this->connect();
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
			$this->redis->hSet($group, $key, serialize($data));
		}
		catch (Exception $e) {
			$this->error("error redis store_array:". $e->getMessage());
		}
	}

	public function get_array($group, $key) {

		try {
			$data = $this->redis->hGet($group, $key);
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
			$return = $this->redis->exists($group);
		}
		catch (Exception $e) {
			$this->error("error redis array_group_exists:". $e->getMessage());
		}
		return $return;
	}

	public function array_group_delete($group) {
		try {
			$return = $this->redis->del($group);
		}
		catch (Exception $e) {
			$this->error("error redis array_group_delete:". $e->getMessage());
		}
		return $return;
	}
}

?>