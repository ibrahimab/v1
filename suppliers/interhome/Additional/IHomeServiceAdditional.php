<?php
/**
 * File for class IHomeServiceAdditional
 * @package IHome
 * @subpackage Services
 *
 */
/**
 * This class stands for IHomeServiceAdditional originally named Additional
 * @package IHome
 * @subpackage Services
 *
 */
class IHomeServiceAdditional extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named AdditionalServices
	 * Documentation : AdditionalServices Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructAdditionalServices::getInputValue()
	 * @param IHomeStructAdditionalServices $_ihomeStructAdditionalServices
	 * @return IHomeStructAdditionalServicesResponse
	 */
	public function AdditionalServices(IHomeStructAdditionalServices $_ihomeStructAdditionalServices)
	{
		try
		{
			$this->setResult(new IHomeStructAdditionalServicesResponse(self::getSoapClient()->AdditionalServices(array('inputValue'=>$_ihomeStructAdditionalServices->getInputValue()))));
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
	 * @return IHomeStructAdditionalServicesResponse
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