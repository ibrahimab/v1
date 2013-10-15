<?php
/**
 * File for class IHomeStructArrayOfSituations
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfSituations originally named ArrayOfSituations
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfSituations extends IHomeWsdlClass
{
	/**
	 * The Situations
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * @var IHomeEnumSituations
	 */
	public $Situations;
	/**
	 * Constructor method for ArrayOfSituations
	 * @see parent::__construct()
	 * @param IHomeEnumSituations $_situations
	 * @return IHomeStructArrayOfSituations
	 */
	public function __construct($_situations = NULL)
	{
		parent::__construct(array('Situations'=>$_situations));
	}
	/**
	 * Get Situations value
	 * @return IHomeEnumSituations|null
	 */
	public function getSituations()
	{
		return $this->Situations;
	}
	/**
	 * Set Situations value
	 * @param IHomeEnumSituations the Situations
	 * @return IHomeEnumSituations
	 */
	public function setSituations($_situations)
	{
		return ($this->Situations = $_situations);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeEnumSituations
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeEnumSituations
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeEnumSituations
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeEnumSituations
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeEnumSituations
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Add element to array
	 * @see IHomeWsdlClass::add()
	 * @uses IHomeEnumSituations::valueIsValid()
	 * @param IHomeEnumSituations $_item
	 * @return IHomeEnumSituations
	 */
	public function add($_item)
	{
		return IHomeEnumSituations::valueIsValid($_item)?parent::add($_item):false;
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string Situations
	 */
	public function getAttributeName()
	{
		return 'Situations';
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