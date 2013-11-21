<?php
/**
 * File for the class which returns the class map definition
 * @package IHome
 */
/**
 * Class which returns the class map definition by the static method IHomeClassMap::classMap()
 * @package IHome
 */
class IHomeClassMap
{
	/**
	 * This method returns the array containing the mapping between WSDL structs and generated classes
	 * This array is sent to the SoapClient when calling the WS
	 * @return array
	 */
	final public static function classMap()
	{
		return array (
  'Accessibilities' => 'IHomeEnumAccessibilities',
  'AccommodationDetail' => 'IHomeStructAccommodationDetail',
  'AccommodationDetailInputValue' => 'IHomeStructAccommodationDetailInputValue',
  'AccommodationDetailResponse' => 'IHomeStructAccommodationDetailResponse',
  'AccommodationDetailReturnValue' => 'IHomeStructAccommodationDetailReturnValue',
  'Activities' => 'IHomeEnumActivities',
  'AdditionalServiceInputItem' => 'IHomeStructAdditionalServiceInputItem',
  'AdditionalServiceItem' => 'IHomeStructAdditionalServiceItem',
  'AdditionalServiceType' => 'IHomeEnumAdditionalServiceType',
  'AdditionalServices' => 'IHomeStructAdditionalServices',
  'AdditionalServicesInputValue' => 'IHomeStructAdditionalServicesInputValue',
  'AdditionalServicesResponse' => 'IHomeStructAdditionalServicesResponse',
  'AdditionalServicesReturnValue' => 'IHomeStructAdditionalServicesReturnValue',
  'ArrayOfAccessibilities' => 'IHomeStructArrayOfAccessibilities',
  'ArrayOfActivities' => 'IHomeStructArrayOfActivities',
  'ArrayOfAdditionalServiceInputItem' => 'IHomeStructArrayOfAdditionalServiceInputItem',
  'ArrayOfAdditionalServiceItem' => 'IHomeStructArrayOfAdditionalServiceItem',
  'ArrayOfError' => 'IHomeStructArrayOfError',
  'ArrayOfFacilities' => 'IHomeStructArrayOfFacilities',
  'ArrayOfPriceListItem' => 'IHomeStructArrayOfPriceListItem',
  'ArrayOfPricesPriceItem' => 'IHomeStructArrayOfPricesPriceItem',
  'ArrayOfPropertyTypes' => 'IHomeStructArrayOfPropertyTypes',
  'ArrayOfSearchResultItem' => 'IHomeStructArrayOfSearchResultItem',
  'ArrayOfSituations' => 'IHomeStructArrayOfSituations',
  'ArrayOfString' => 'IHomeStructArrayOfString',
  'Availability' => 'IHomeStructAvailability',
  'AvailabilityInputValue' => 'IHomeStructAvailabilityInputValue',
  'AvailabilityResponse' => 'IHomeStructAvailabilityResponse',
  'AvailabilityRetunValue' => 'IHomeStructAvailabilityRetunValue',
  'CheckServerHealth' => 'IHomeStructCheckServerHealth',
  'CheckServerHealthResponse' => 'IHomeStructCheckServerHealthResponse',
  'CheckServerHealthResult' => 'IHomeStructCheckServerHealthResult',
  // 'CheckServerHealthResultV2' => 'IHomeStructCheckServerHealthResultV2',
  // 'CheckServerHealthV2' => 'IHomeStructCheckServerHealthV2',
  // 'CheckServerHealthV2Response' => 'IHomeStructCheckServerHealthV2Response',
  'Error' => 'IHomeStructError',
  'ExtensionDataObject' => 'IHomeStructExtensionDataObject',
  'Facilities' => 'IHomeEnumFacilities',
  'HouseApartmentTypes' => 'IHomeEnumHouseApartmentTypes',
  'Messages' => 'IHomeStructMessages',
  'NearestBookingDate' => 'IHomeStructNearestBookingDate',
  'NearestBookingDateInputValue' => 'IHomeStructNearestBookingDateInputValue',
  'NearestBookingDateResponse' => 'IHomeStructNearestBookingDateResponse',
  'NearestBookingDateReturnValue' => 'IHomeStructNearestBookingDateReturnValue',
  'OrderBy' => 'IHomeEnumOrderBy',
  'OrderDirection' => 'IHomeEnumOrderDirection',
  'PriceDetail' => 'IHomeStructPriceDetail',
  'PriceDetailInputValue' => 'IHomeStructPriceDetailInputValue',
  'PriceDetailResponse' => 'IHomeStructPriceDetailResponse',
  'PriceDetailRetunValue' => 'IHomeStructPriceDetailRetunValue',
  'PriceList' => 'IHomeStructPriceList',
  'PriceListInputValue' => 'IHomeStructPriceListInputValue',
  'PriceListItem' => 'IHomeStructPriceListItem',
  'PriceListResponse' => 'IHomeStructPriceListResponse',
  'PriceListReturnValue' => 'IHomeStructPriceListReturnValue',
  'Prices' => 'IHomeStructPrices',
  'PricesInputValue' => 'IHomeStructPricesInputValue',
  'PricesPriceItem' => 'IHomeStructPricesPriceItem',
  'PricesResponse' => 'IHomeStructPricesResponse',
  'PricesRetunValue' => 'IHomeStructPricesRetunValue',
  'PropertyTypes' => 'IHomeEnumPropertyTypes',
  'ReturnValue' => 'IHomeStructReturnValue',
  'Search' => 'IHomeStructSearch',
  'SearchInputValue' => 'IHomeStructSearchInputValue',
  'SearchResponse' => 'IHomeStructSearchResponse',
  'SearchResultItem' => 'IHomeStructSearchResultItem',
  'SearchReturnValue' => 'IHomeStructSearchReturnValue',
  'ServiceAuthHeader' => 'IHomeStructServiceAuthHeader',
  'Situations' => 'IHomeEnumSituations',
  'SpecialOffers' => 'IHomeEnumSpecialOffers',
  'ThemeFilterTypes' => 'IHomeEnumThemeFilterTypes',
);
	}
}
?>