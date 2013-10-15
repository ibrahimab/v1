<?php
/**
 * File for class IHomeStructCheckServerHealthResultV2
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructCheckServerHealthResultV2 originally named CheckServerHealthResultV2
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructCheckServerHealthResultV2 extends IHomeWsdlClass
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
	 * The IRent
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $IRent;
	/**
	 * The ServerDBLoadBalancer
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ServerDBLoadBalancer;
	/**
	 * The Endeca
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Endeca;
	/**
	 * The OverallServerHealth
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $OverallServerHealth;
	/**
	 * Constructor method for CheckServerHealthResultV2
	 * @see parent::__construct()
	 * @param IHomeStructMessages $_messages
	 * @param string $_iRent
	 * @param string $_serverDBLoadBalancer
	 * @param string $_endeca
	 * @param string $_overallServerHealth
	 * @return IHomeStructCheckServerHealthResultV2
	 */
	public function __construct($_messages,$_iRent = NULL,$_serverDBLoadBalancer = NULL,$_endeca = NULL,$_overallServerHealth = NULL)
	{
		parent::__construct(array('Messages'=>$_messages,'IRent'=>$_iRent,'ServerDBLoadBalancer'=>$_serverDBLoadBalancer,'Endeca'=>$_endeca,'OverallServerHealth'=>$_overallServerHealth));
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
	 * Get IRent value
	 * @return string|null
	 */
	public function getIRent()
	{
		return $this->IRent;
	}
	/**
	 * Set IRent value
	 * @param string the IRent
	 * @return string
	 */
	public function setIRent($_iRent)
	{
		return ($this->IRent = $_iRent);
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
	 * Get Endeca value
	 * @return string|null
	 */
	public function getEndeca()
	{
		return $this->Endeca;
	}
	/**
	 * Set Endeca value
	 * @param string the Endeca
	 * @return string
	 */
	public function setEndeca($_endeca)
	{
		return ($this->Endeca = $_endeca);
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