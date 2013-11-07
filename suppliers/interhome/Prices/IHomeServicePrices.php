<?php
/**
 * File for class IHomeServicePrices
 * @package IHome
 * @subpackage Services
 */
/**
 * This class stands for IHomeServicePrices originally named Prices
 * @package IHome
 * @subpackage Services
 */
class IHomeServicePrices extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named Prices
	 * Documentation : Prices Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructPrices::getInputValue()
	 * @param IHomeStructPrices $_ihomeStructPrices
	 * @return IHomeStructPricesResponse
	 */
	public function Prices(IHomeStructPrices $_ihomeStructPrices)
	{
		try
		{
			$this->setResult(new IHomeStructPricesResponse(self::getSoapClient()->Prices(array('inputValue'=>$_ihomeStructPrices->getInputValue()))));
		}
		catch(SoapFault $soapFault)
		{
			return !$this->saveLastError(__METHOD__,$soapFault);
		}
		return $this->getResult();
	}
	/**
	 * Returns the result
	 * @see IHomeWsdlClass::getResult()
	 * @return IHomeStructPricesResponse
	 */
	public function getResult()
	{
		return parent::getResult();
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