<?php

/**
 * class to provide general configuration-variables
 *
 * @author Jeroen Boschman (jeroen@webtastic.nl)
 * @since  2015-05-26 14:00
*/

class Configuration
{
	protected $configdata = array();


	/**
	 * fill $this->configdata with all the needed configuration-variables
	 *
	 * @return void
	 */
	function __construct()
	{

		global $vars, $isMobile, $voorkant_cms;

		$this->configdata["path"]                            = $vars["path"];
		$this->configdata["seizoentype"]                     = $vars["seizoentype"];
		$this->configdata["website"]                         = $vars["website"];
		$this->configdata["websitetype"]                     = $vars["websitetype"];
		$this->configdata["websitenaam"]                     = $vars["websitenaam"];
		$this->configdata["wederverkoop"]                    = $vars["wederverkoop"];
		$this->configdata["livechat_code"]                   = $vars["livechat_code"];
		$this->configdata["taal"]                            = $vars["taal"];
		$this->configdata["reserveringskosten"]              = $vars["reserveringskosten"];
		$this->configdata["lokale_testserver"]               = $vars["lokale_testserver"];
		$this->configdata["chalettour_aanpassing_commissie"] = $vars["chalettour_aanpassing_commissie"];
		$this->configdata["ttv"]                             = $vars["ttv"];
		$this->configdata["unixdir"]                         = $vars["unixdir"];

		$this->configdata["isMobile"]                        = $isMobile;
		$this->configdata["voorkant_cms"]                    = $voorkant_cms;
	}


	/**
	 * Use php5-overloading to get the wanted variable
	 *
	 * @param variable name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->configdata)) {
			return $this->configdata[$name];
		}

		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);
		return null;
	}
}

