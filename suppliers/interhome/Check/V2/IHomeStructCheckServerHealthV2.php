<?php
/**
 * File for class IHomeStructCheckServerHealthV2
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructCheckServerHealthV2 originally named CheckServerHealthV2
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructCheckServerHealthV2 extends IHomeWsdlClass
{
	/**
	 * The type
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $type;
	/**
	 * Constructor method for CheckServerHealthV2
	 * @see parent::__construct()
	 * @param string $_type
	 * @return IHomeStructCheckServerHealthV2
	 */
	public function __construct($_type = NULL)
	{
		parent::__construct(array('type'=>$_type));
	}
	/**
	 * Get type value
	 * @return string|null
	 */
	public function getType()
	{
		return $this->type;
	}
	/**
	 * Set type value
	 * @param string the type
	 * @return string
	 */
	public function setType($_type)
	{
		return ($this->type = $_type);
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