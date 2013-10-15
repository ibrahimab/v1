<?php
/**
 * File for class IHomeStructMessages
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructMessages originally named Messages
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructMessages extends IHomeWsdlClass
{
	/**
	 * The Information
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Information;
	/**
	 * The Errors
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Errors;
	/**
	 * The TimeTaken
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $TimeTaken;
	/**
	 * The ServerName
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ServerName;
	/**
	 * Constructor method for Messages
	 * @see parent::__construct()
	 * @param string $_information
	 * @param string $_errors
	 * @param string $_timeTaken
	 * @param string $_serverName
	 * @return IHomeStructMessages
	 */
	public function __construct($_information = NULL,$_errors = NULL,$_timeTaken = NULL,$_serverName = NULL)
	{
		parent::__construct(array('Information'=>$_information,'Errors'=>$_errors,'TimeTaken'=>$_timeTaken,'ServerName'=>$_serverName));
	}
	/**
	 * Get Information value
	 * @return string|null
	 */
	public function getInformation()
	{
		return $this->Information;
	}
	/**
	 * Set Information value
	 * @param string the Information
	 * @return string
	 */
	public function setInformation($_information)
	{
		return ($this->Information = $_information);
	}
	/**
	 * Get Errors value
	 * @return string|null
	 */
	public function getErrors()
	{
		return $this->Errors;
	}
	/**
	 * Set Errors value
	 * @param string the Errors
	 * @return string
	 */
	public function setErrors($_errors)
	{
		return ($this->Errors = $_errors);
	}
	/**
	 * Get TimeTaken value
	 * @return string|null
	 */
	public function getTimeTaken()
	{
		return $this->TimeTaken;
	}
	/**
	 * Set TimeTaken value
	 * @param string the TimeTaken
	 * @return string
	 */
	public function setTimeTaken($_timeTaken)
	{
		return ($this->TimeTaken = $_timeTaken);
	}
	/**
	 * Get ServerName value
	 * @return string|null
	 */
	public function getServerName()
	{
		return $this->ServerName;
	}
	/**
	 * Set ServerName value
	 * @param string the ServerName
	 * @return string
	 */
	public function setServerName($_serverName)
	{
		return ($this->ServerName = $_serverName);
	}
	/**
	 * Method returning the class name
	 * @return string __CLASS__
	 */
	public function __toString()
	{
		return __CLASS__;
	}
}
?>