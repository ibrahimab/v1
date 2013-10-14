<?php
/**
 * File for class IHomeStructCheckServerHealthResult
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructCheckServerHealthResult originally named CheckServerHealthResult
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructCheckServerHealthResult extends IHomeWsdlClass
{
	/**
	 * The Messages
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeStructMessages
	 */
	public $Messages;
	/**
	 * The SearchObjects
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $SearchObjects;
	/**
	 * The Availability
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Availability;
	/**
	 * The PriceCheck
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PriceCheck;
	/**
	 * The ServerDBLoadBalancer
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ServerDBLoadBalancer;
	/**
	 * The OverallServerHealth
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $OverallServerHealth;
	/**
	 * Constructor method for CheckServerHealthResult
	 * @see parent::__construct()
	 * @param IHomeStructMessages $_messages
	 * @param string $_searchObjects
	 * @param string $_availability
	 * @param string $_priceCheck
	 * @param string $_serverDBLoadBalancer
	 * @param string $_overallServerHealth
	 * @return IHomeStructCheckServerHealthResult
	 */
	public function __construct($_messages,$_searchObjects = NULL,$_availability = NULL,$_priceCheck = NULL,$_serverDBLoadBalancer = NULL,$_overallServerHealth = NULL)
	{
		parent::__construct(array('Messages'=>$_messages,'SearchObjects'=>$_searchObjects,'Availability'=>$_availability,'PriceCheck'=>$_priceCheck,'ServerDBLoadBalancer'=>$_serverDBLoadBalancer,'OverallServerHealth'=>$_overallServerHealth));
	}
	/**
	 * Get Messages value
	 * @return IHomeStructMessages
	 */
	public function getMessages()
	{
		return $this->Messages;
	}
	/**
	 * Set Messages value
	 * @param IHomeStructMessages the Messages
	 * @return IHomeStructMessages
	 */
	public function setMessages($_messages)
	{
		return ($this->Messages = $_messages);
	}
	/**
	 * Get SearchObjects value
	 * @return string|null
	 */
	public function getSearchObjects()
	{
		return $this->SearchObjects;
	}
	/**
	 * Set SearchObjects value
	 * @param string the SearchObjects
	 * @return string
	 */
	public function setSearchObjects($_searchObjects)
	{
		return ($this->SearchObjects = $_searchObjects);
	}
	/**
	 * Get Availability value
	 * @return string|null
	 */
	public function getAvailability()
	{
		return $this->Availability;
	}
	/**
	 * Set Availability value
	 * @param string the Availability
	 * @return string
	 */
	public function setAvailability($_availability)
	{
		return ($this->Availability = $_availability);
	}
	/**
	 * Get PriceCheck value
	 * @return string|null
	 */
	public function getPriceCheck()
	{
		return $this->PriceCheck;
	}
	/**
	 * Set PriceCheck value
	 * @param string the PriceCheck
	 * @return string
	 */
	public function setPriceCheck($_priceCheck)
	{
		return ($this->PriceCheck = $_priceCheck);
	}
	/**
	 * Get ServerDBLoadBalancer value
	 * @return string|null
	 */
	public function getServerDBLoadBalancer()
	{
		return $this->ServerDBLoadBalancer;
	}
	/**
	 * Set ServerDBLoadBalancer value
	 * @param string the ServerDBLoadBalancer
	 * @return string
	 */
	public function setServerDBLoadBalancer($_serverDBLoadBalancer)
	{
		return ($this->ServerDBLoadBalancer = $_serverDBLoadBalancer);
	}
	/**
	 * Get OverallServerHealth value
	 * @return string|null
	 */
	public function getOverallServerHealth()
	{
		return $this->OverallServerHealth;
	}
	/**
	 * Set OverallServerHealth value
	 * @param string the OverallServerHealth
	 * @return string
	 */
	public function setOverallServerHealth($_overallServerHealth)
	{
		return ($this->OverallServerHealth = $_overallServerHealth);
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