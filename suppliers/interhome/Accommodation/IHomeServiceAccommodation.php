<?php
/**
 * File for class IHomeServiceAccommodation
 * @package IHome
 * @subpackage Services
 *
 */
/**
 * This class stands for IHomeServiceAccommodation originally named Accommodation
 * @package IHome
 * @subpackage Services
 *
 */
class IHomeServiceAccommodation extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named AccommodationDetail
	 * Documentation : Accommodation details Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructAccommodationDetail::getInputValue()
	 * @param IHomeStructAccommodationDetail $_ihomeStructAccommodationDetail
	 * @return IHomeStructAccommodationDetailResponse
	 */
	public function AccommodationDetail(IHomeStructAccommodationDetail $_ihomeStructAccommodationDetail)
	{

		try
		{
			
			$this->setResult(new IHomeStructAccommodationDetailResponse(self::getSoapClient()->AccommodationDetail(array('inputValue'=>$_ihomeStructAccommodationDetail->getInputValue()))));
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
	 * @return IHomeStructAccommodationDetailResponse
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