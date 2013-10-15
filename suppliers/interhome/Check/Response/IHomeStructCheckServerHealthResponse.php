<?php
/**
 * File for class IHomeStructCheckServerHealthResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructCheckServerHealthResponse originally named CheckServerHealthResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructCheckServerHealthResponse extends IHomeWsdlClass
{
	/**
	 * The CheckServerHealthResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeStructCheckServerHealthResult
	 */
	public $CheckServerHealthResult;
	/**
	 * Constructor method for CheckServerHealthResponse
	 * @see parent::__construct()
	 * @param IHomeStructCheckServerHealthResult $_checkServerHealthResult
	 * @return IHomeStructCheckServerHealthResponse
	 */
	public function __construct($_checkServerHealthResult)
	{
		parent::__construct(array('CheckServerHealthResult'=>$_checkServerHealthResult));
	}
	/**
	 * Get CheckServerHealthResult value
	 * @return IHomeStructCheckServerHealthResult
	 */
	public function getCheckServerHealthResult()
	{
		return $this->CheckServerHealthResult;
	}
	/**
	 * Set CheckServerHealthResult value
	 * @param IHomeStructCheckServerHealthResult the CheckServerHealthResult
	 * @return IHomeStructCheckServerHealthResult
	 */
	public function setCheckServerHealthResult($_checkServerHealthResult)
	{
		return ($this->CheckServerHealthResult = $_checkServerHealthResult);
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