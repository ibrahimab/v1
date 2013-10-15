<?php
/**
 * File for class IHomeStructCheckServerHealthV2Response
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructCheckServerHealthV2Response originally named CheckServerHealthV2Response
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructCheckServerHealthV2Response extends IHomeWsdlClass
{
	/**
	 * The CheckServerHealthV2Result
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeStructCheckServerHealthResultV2
	 */
	public $CheckServerHealthV2Result;
	/**
	 * Constructor method for CheckServerHealthV2Response
	 * @see parent::__construct()
	 * @param IHomeStructCheckServerHealthResultV2 $_checkServerHealthV2Result
	 * @return IHomeStructCheckServerHealthV2Response
	 */
	public function __construct($_checkServerHealthV2Result)
	{
		parent::__construct(array('CheckServerHealthV2Result'=>$_checkServerHealthV2Result));
	}
	/**
	 * Get CheckServerHealthV2Result value
	 * @return IHomeStructCheckServerHealthResultV2
	 */
	public function getCheckServerHealthV2Result()
	{
		return $this->CheckServerHealthV2Result;
	}
	/**
	 * Set CheckServerHealthV2Result value
	 * @param IHomeStructCheckServerHealthResultV2 the CheckServerHealthV2Result
	 * @return IHomeStructCheckServerHealthResultV2
	 */
	public function setCheckServerHealthV2Result($_checkServerHealthV2Result)
	{
		return ($this->CheckServerHealthV2Result = $_checkServerHealthV2Result);
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