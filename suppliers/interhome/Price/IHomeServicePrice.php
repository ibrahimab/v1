<?php
/**
 * File for class IHomeServicePrice
 * @package IHome
 * @subpackage Services
 *
 */
/**
 * This class stands for IHomeServicePrice originally named Price
 * @package IHome
 * @subpackage Services
 *
 */
class IHomeServicePrice extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named PriceDetail
	 * Documentation : PriceDetail Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructPriceDetail::getInputValue()
	 * @param IHomeStructPriceDetail $_ihomeStructPriceDetail
	 * @return IHomeStructPriceDetailResponse
	 */
	public function PriceDetail(IHomeStructPriceDetail $_ihomeStructPriceDetail)
	{
		try
		{
			$this->setResult(new IHomeStructPriceDetailResponse(self::getSoapClient()->PriceDetail(array('inputValue'=>$_ihomeStructPriceDetail->getInputValue()))));
		}
		catch(SoapFault $soapFault)
		{
			return !$this->saveLastError(__METHOD__,$soapFault);
		}
		return $this->getResult();
	}
	/**
	 * Method to call the operation originally named PriceList
	 * Documentation : PriceList Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructPriceList::getInputValue()
	 * @param IHomeStructPriceList $_ihomeStructPriceList
	 * @return IHomeStructPriceListResponse
	 */
	public function PriceList(IHomeStructPriceList $_ihomeStructPriceList)
	{
		try
		{
			$this->setResult(new IHomeStructPriceListResponse(self::getSoapClient()->PriceList(array('inputValue'=>$_ihomeStructPriceList->getInputValue()))));
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
	 * @return IHomeStructPriceDetailResponse|IHomeStructPriceListResponse
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