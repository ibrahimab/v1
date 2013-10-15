<?php
/**
 * File for class IHomeServiceAvailability
 * @package IHome
 * @subpackage Services
 *
 */
/**
 * This class stands for IHomeServiceAvailability originally named Availability
 * @package IHome
 * @subpackage Services
 *
 */
class IHomeServiceAvailability extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named Availability
	 * Documentation : Availability Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructAvailability::getInputValue()
	 * @param IHomeStructAvailability $_ihomeStructAvailability
	 * @return IHomeStructAvailabilityResponse
	 */
	public function Availability(IHomeStructAvailability $_ihomeStructAvailability)
	{
		try
		{
			$this->setResult(new IHomeStructAvailabilityResponse(self::getSoapClient()->Availability(array('inputValue'=>$_ihomeStructAvailability->getInputValue()))));
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
	 * @return IHomeStructAvailabilityResponse
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