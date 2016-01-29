<?php

/**
*  talk to redis-server
*/


class wt_redis {

	private $redis;
	private $logger;

	public $retry = 0;
	public $expire_time = 604800; // expire every key after 7 days

	function __construct(\LoggerInterface $logger = null) {

		global $vars;
		dump($logger);
		$this->logger = $logger ?: new \Logger('redis');

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
			$this->logger->log('connected to redis-server ' . wt_redis_host, 'connect-redis');
		}
		return $return;
	}

	private function error($message) {

		global $vars;

		trigger_error($message,E_USER_NOTICE);

		$this->logger->error($message);

		if($vars["lokale_testserver"]) {
			exit;
		}

	}

	public function store_array($group, $key, $data) {

		$this->logger->log("store_array group:".$group." - key:".$key, "query-redis");

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

		$this->logger->log("get_array group:".$group." - key:".$key, "query-redis");

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

		$this->logger->log("array_group_exists group:".$group, "query-redis");

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

		$this->logger->log("array_group_delete group:".$group, "query-redis");

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

		$this->logger->log("set key:".$key." - value:".$value, "query-redis");

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

		$this->logger->log("get key:".$key, "query-redis");

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

		$this->logger->log("hset key:".$key." - field:".$field, "query-redis");

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

		$this->logger->log("hget key:".$key." - field:".$field, "query-redis");

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

		$this->logger->log("hgetall key:".$key, "query-redis");

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

		$this->logger->log("hexists key:".$key." - field:".$field, "query-redis");

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

		$this->logger->log("del key:".$key, "query-redis");

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

		$this->logger->log("exists key:".$key, "query-redis");

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

		$this->logger->log("keys query:".$key, "query-redis");

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