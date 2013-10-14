<?php
/**
 * File for class IHomeStructError
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructError originally named Error
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructError extends IHomeWsdlClass
{
	/**
	 * The Number
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Number;
	/**
	 * The Description
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Description;
	/**
	 * The ExtensionData
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructExtensionDataObject
	 */
	public $ExtensionData;
	/**
	 * Constructor method for Error
	 * @see parent::__construct()
	 * @param int $_number
	 * @param string $_description
	 * @param IHomeStructExtensionDataObject $_extensionData
	 * @return IHomeStructError
	 */
	public function __construct($_number,$_description = NULL,$_extensionData = NULL)
	{
		parent::__construct(array('Number'=>$_number,'Description'=>$_description,'ExtensionData'=>$_extensionData));
	}
	/**
	 * Get Number value
	 * @return int
	 */
	public function getNumber()
	{
		return $this->Number;
	}
	/**
	 * Set Number value
	 * @param int the Number
	 * @return int
	 */
	public function setNumber($_number)
	{
		return ($this->Number = $_number);
	}
	/**
	 * Get Description value
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->Description;
	}
	/**
	 * Set Description value
	 * @param string the Description
	 * @return string
	 */
	public function setDescription($_description)
	{
		return ($this->Description = $_description);
	}
	/**
	 * Get ExtensionData value
	 * @return IHomeStructExtensionDataObject|null
	 */
	public function getExtensionData()
	{
		return $this->ExtensionData;
	}
	/**
	 * Set ExtensionData value
	 * @param IHomeStructExtensionDataObject the ExtensionData
	 * @return IHomeStructExtensionDataObject
	 */
	public function setExtensionData($_extensionData)
	{
		return ($this->ExtensionData = $_extensionData);
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