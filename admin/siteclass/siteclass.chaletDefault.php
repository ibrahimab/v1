<?php

/**
* default class as a parent for other classes
*
* @author: Jeroen Boschman (jeroen@webtastic.nl)
* @since: 2015-05-29 10:08
*/

class chaletDefault
{

	/**  Location for overloaded data  */
	protected $data = array();

	/**  site-configuration  */
	protected $config;

	/**
	 * setup Configuration
	 *
	 * @return void
	 */
	function __construct()
	{
		$this->setConfiguration(new Configuration());
	}

	/**
	 * Inject Configuration class
	 *
	 * @param Configuration $configuration
	 */
	public function setConfiguration($configuration)
	{
		$this->config = $configuration;
	}

	/**
	 * overloading set
	 *
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * overloading get
	 *
	 * @return mixed
	 */
	public function __get($name)
	{

		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);
		return null;
	}

	/**
	 * overloading isset
	 *
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	/**
	 * overloading unset
	 *
	 * @return void
	 */
	public function __unset($name)
	{
		unset($this->data[$name]);
	}
}
