<?php

/**
 * Default Controller that initializes the Model and the Template
 * It is extended by other application Controllers
 *
 */
class Controller {

	protected $_controller;
	protected $_action;
	protected $_template;

	public $doNotRenderHeader;
	public $render;

	function __construct($controller, $action) {

		$this->_controller = $controller;
		$this->_action = $action;

		$model = ucfirst($controller);
		$this->doNotRenderHeader = 0;
		$this->render = 1;

		$this->$model = new $model;
		$this->_template = new Template($controller,$action);

	}

	function set($name,$value) {
		$this->_template->set($name,$value);
	}

	function __destruct() {
		if ($this->render) {
			$this->_template->render($this->doNotRenderHeader);
		}
	}

	public function getRequest() {
		return null;
	}

}