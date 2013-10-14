<?php
/**
 * File for class IHomeStructAdditionalServiceItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAdditionalServiceItem originally named AdditionalServiceItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAdditionalServiceItem extends IHomeWsdlClass
{
	/**
	 * The AdditionalServiceType
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeEnumAdditionalServiceType
	 */
	public $AdditionalServiceType;
	/**
	 * The Amount
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Amount;
	/**
	 * The Count
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Count;
	/**
	 * The IsDefaultService
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $IsDefaultService;
	/**
	 * The IsIncluded
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $IsIncluded;
	/**
	 * The IsInsurance
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $IsInsurance;
	/**
	 * The IsMandatory
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $IsMandatory;
	/**
	 * The Code
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Code;
	/**
	 * The Currency
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Currency;
	/**
	 * The Description
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Description;
	/**
	 * The EitherOr
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $EitherOr;
	/**
	 * The PaymentInfo
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PaymentInfo;
	/**
	 * The PriceRule
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PriceRule;
	/**
	 * The Text
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Text;
	/**
	 * The Type
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Type;
	/**
	 * The ValidFrom
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ValidFrom;
	/**
	 * The ValidTo
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ValidTo;
	/**
	 * Constructor method for AdditionalServiceItem
	 * @see parent::__construct()
	 * @param IHomeEnumAdditionalServiceType $_additionalServiceType
	 * @param decimal $_amount
	 * @param int $_count
	 * @param boolean $_isDefaultService
	 * @param boolean $_isIncluded
	 * @param boolean $_isInsurance
	 * @param boolean $_isMandatory
	 * @param string $_code
	 * @param string $_currency
	 * @param string $_description
	 * @param string $_eitherOr
	 * @param string $_paymentInfo
	 * @param string $_priceRule
	 * @param string $_text
	 * @param string $_type
	 * @param string $_validFrom
	 * @param string $_validTo
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function __construct($_additionalServiceType,$_amount,$_count,$_isDefaultService,$_isIncluded,$_isInsurance,$_isMandatory,$_code = NULL,$_currency = NULL,$_description = NULL,$_eitherOr = NULL,$_paymentInfo = NULL,$_priceRule = NULL,$_text = NULL,$_type = NULL,$_validFrom = NULL,$_validTo = NULL)
	{
		parent::__construct(array('AdditionalServiceType'=>$_additionalServiceType,'Amount'=>$_amount,'Count'=>$_count,'IsDefaultService'=>$_isDefaultService,'IsIncluded'=>$_isIncluded,'IsInsurance'=>$_isInsurance,'IsMandatory'=>$_isMandatory,'Code'=>$_code,'Currency'=>$_currency,'Description'=>$_description,'EitherOr'=>$_eitherOr,'PaymentInfo'=>$_paymentInfo,'PriceRule'=>$_priceRule,'Text'=>$_text,'Type'=>$_type,'ValidFrom'=>$_validFrom,'ValidTo'=>$_validTo));
	}
	/**
	 * Get AdditionalServiceType value
	 * @return IHomeEnumAdditionalServiceType
	 */
	public function getAdditionalServiceType()
	{
		return $this->AdditionalServiceType;
	}
	/**
	 * Set AdditionalServiceType value
	 * @uses IHomeEnumAdditionalServiceType::valueIsValid()
	 * @param IHomeEnumAdditionalServiceType the AdditionalServiceType
	 * @return IHomeEnumAdditionalServiceType
	 */
	public function setAdditionalServiceType($_additionalServiceType)
	{
		if(!IHomeEnumAdditionalServiceType::valueIsValid($_additionalServiceType))
		{
			return false;
		}
		return ($this->AdditionalServiceType = $_additionalServiceType);
	}
	/**
	 * Get Amount value
	 * @return decimal
	 */
	public function getAmount()
	{
		return $this->Amount;
	}
	/**
	 * Set Amount value
	 * @param decimal the Amount
	 * @return decimal
	 */
	public function setAmount($_amount)
	{
		return ($this->Amount = $_amount);
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
	 * Get IsDefaultService value
	 * @return boolean
	 */
	public function getIsDefaultService()
	{
		return $this->IsDefaultService;
	}
	/**
	 * Set IsDefaultService value
	 * @param boolean the IsDefaultService
	 * @return boolean
	 */
	public function setIsDefaultService($_isDefaultService)
	{
		return ($this->IsDefaultService = $_isDefaultService);
	}
	/**
	 * Get IsIncluded value
	 * @return boolean
	 */
	public function getIsIncluded()
	{
		return $this->IsIncluded;
	}
	/**
	 * Set IsIncluded value
	 * @param boolean the IsIncluded
	 * @return boolean
	 */
	public function setIsIncluded($_isIncluded)
	{
		return ($this->IsIncluded = $_isIncluded);
	}
	/**
	 * Get IsInsurance value
	 * @return boolean
	 */
	public function getIsInsurance()
	{
		return $this->IsInsurance;
	}
	/**
	 * Set IsInsurance value
	 * @param boolean the IsInsurance
	 * @return boolean
	 */
	public function setIsInsurance($_isInsurance)
	{
		return ($this->IsInsurance = $_isInsurance);
	}
	/**
	 * Get IsMandatory value
	 * @return boolean
	 */
	public function getIsMandatory()
	{
		return $this->IsMandatory;
	}
	/**
	 * Set IsMandatory value
	 * @param boolean the IsMandatory
	 * @return boolean
	 */
	public function setIsMandatory($_isMandatory)
	{
		return ($this->IsMandatory = $_isMandatory);
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
	 * Get Currency value
	 * @return string|null
	 */
	public function getCurrency()
	{
		return $this->Currency;
	}
	/**
	 * Set Currency value
	 * @param string the Currency
	 * @return string
	 */
	public function setCurrency($_currency)
	{
		return ($this->Currency = $_currency);
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
	 * Get EitherOr value
	 * @return string|null
	 */
	public function getEitherOr()
	{
		return $this->EitherOr;
	}
	/**
	 * Set EitherOr value
	 * @param string the EitherOr
	 * @return string
	 */
	public function setEitherOr($_eitherOr)
	{
		return ($this->EitherOr = $_eitherOr);
	}
	/**
	 * Get PaymentInfo value
	 * @return string|null
	 */
	public function getPaymentInfo()
	{
		return $this->PaymentInfo;
	}
	/**
	 * Set PaymentInfo value
	 * @param string the PaymentInfo
	 * @return string
	 */
	public function setPaymentInfo($_paymentInfo)
	{
		return ($this->PaymentInfo = $_paymentInfo);
	}
	/**
	 * Get PriceRule value
	 * @return string|null
	 */
	public function getPriceRule()
	{
		return $this->PriceRule;
	}
	/**
	 * Set PriceRule value
	 * @param string the PriceRule
	 * @return string
	 */
	public function setPriceRule($_priceRule)
	{
		return ($this->PriceRule = $_priceRule);
	}
	/**
	 * Get Text value
	 * @return string|null
	 */
	public function getText()
	{
		return $this->Text;
	}
	/**
	 * Set Text value
	 * @param string the Text
	 * @return string
	 */
	public function setText($_text)
	{
		return ($this->Text = $_text);
	}
	/**
	 * Get Type value
	 * @return string|null
	 */
	public function getType()
	{
		return $this->Type;
	}
	/**
	 * Set Type value
	 * @param string the Type
	 * @return string
	 */
	public function setType($_type)
	{
		return ($this->Type = $_type);
	}
	/**
	 * Get ValidFrom value
	 * @return string|null
	 */
	public function getValidFrom()
	{
		return $this->ValidFrom;
	}
	/**
	 * Set ValidFrom value
	 * @param string the ValidFrom
	 * @return string
	 */
	public function setValidFrom($_validFrom)
	{
		return ($this->ValidFrom = $_validFrom);
	}
	/**
	 * Get ValidTo value
	 * @return string|null
	 */
	public function getValidTo()
	{
		return $this->ValidTo;
	}
	/**
	 * Set ValidTo value
	 * @param string the ValidTo
	 * @return string
	 */
	public function setValidTo($_validTo)
	{
		return ($this->ValidTo = $_validTo);
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