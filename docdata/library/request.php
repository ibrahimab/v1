<?php
/**
 * Class Request
 * Used to manipulate the requests
 */
class Request {

	private $queryString;

	public function setQueryString($queryString) {
		$this->queryString = $queryString;
	}

	public function getParam($param) {

		if($this->queryString || isset($_POST) || isset($_POST)) {

			if($result = $this->_getParam($param, $this->queryString, 1)) {
				return $result;
			}
			if($result = $this->_getParam($param, $_POST, 0)) {
				return $result;
			}
			if($result = $this->_getParam($param, $_GET, 0)) {
				return $result;
			}
			return null;
		}
		return null;
	}

	private function _getParam($param, $source, $next = 0) {

		$key = array_search($param, $source);

		if($key !== false) {
			if(isset($source[$key+$next])) {
				return $source[$key+$next];
			} else {
				return null;
			}
		} elseif(array_key_exists($param, $source)) {
			return $source[$param];
		} else {
			return null;
		}
	}
}