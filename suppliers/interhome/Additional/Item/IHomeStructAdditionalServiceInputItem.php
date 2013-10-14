<?php
/**
 * File for class IHomeStructAdditionalServiceInputItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAdditionalServiceInputItem originally named AdditionalServiceInputItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAdditionalServiceInputItem extends IHomeWsdlClass
{
	/**
	 * The Count
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Count;
	/**
	 * The Code
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Code;
	/**
	 * Constructor method for AdditionalServiceInputItem
	 * @see parent::__construct()
	 * @param int $_count
	 * @param string $_code
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function __construct($_count,$_code = NULL)
	{
		parent::__construct(array('Count'=>$_count,'Code'=>$_code));
	}
	/**
	 * Get Count value
	 * @return int
	 */
	public function getCount()
	{
		return $this->Count;
	}
	/**
	 * Set Count value
	 * @param int the Count
	 * @return int
	 */
	public function setCount($_count)
	{
		return ($this->Count = $_count);
	}
	/**
	 * Get Code value
	 * @return string|null
	 */
	public function getCode()
	{
		return $this->Code;
	}
	/**
	 * Set Code value
	 * @param string the Code
	 * @return string
	 */
	public function setCode($_code)
	{
		return ($this->Code = $_code);
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