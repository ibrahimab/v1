<?php
/**
 * File for the class which returns the class map definition
 * @package NewyseService
 */
/**
 * Class which returns the class map definition by the static method NewyseServiceClassMap::classMap()
 * @package NewyseService
 */
class NewyseServiceClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'Accommodation' => 'NewyseServiceStructAccommodation',
  'AccommodationKind' => 'NewyseServiceStructAccommodationKind',
  'AccommodationKindContainer' => 'NewyseServiceStructAccommodationKindContainer',
  'AccommodationKindCriteria' => 'NewyseServiceStructAccommodationKindCriteria',
  'AccommodationKinds' => 'NewyseServiceStructAccommodationKinds',
  'AccommodationType' => 'NewyseServiceStructAccommodationType',
  'AccommodationTypeContainer' => 'NewyseServiceStructAccommodationTypeContainer',
  'AccommodationTypeCriteria' => 'NewyseServiceStructAccommodationTypeCriteria',
  'AccommodationTypeSearch' => 'NewyseServiceStructAccommodationTypeSearch',
  'AccommodationTypeSearchContainer' => 'NewyseServiceStructAccommodationTypeSearchContainer',
  'AccommodationTypeSearchCriteria' => 'NewyseServiceStructAccommodationTypeSearchCriteria',
  'AccommodationTypeSearchObject' => 'NewyseServiceStructAccommodationTypeSearchObject',
  'AccommodationTypeSearchObjects' => 'NewyseServiceStructAccommodationTypeSearchObjects',
  'AccommodationTypes' => 'NewyseServiceStructAccommodationTypes',
  'ActivityCategories' => 'NewyseServiceStructActivityCategories',
  'ActivityCategory' => 'NewyseServiceStructActivityCategory',
  'Addition' => 'NewyseServiceStructAddition',
  'Additions' => 'NewyseServiceStructAdditions',
  'Address' => 'NewyseServiceStructAddress',
  'AddressCriteria' => 'NewyseServiceStructAddressCriteria',
  'AgentBillLines' => 'NewyseServiceStructAgentBillLines',
  'Availabilities' => 'NewyseServiceStructAvailabilities',
  'Availability' => 'NewyseServiceStructAvailability',
  'AvailabilityContainer' => 'NewyseServiceStructAvailabilityContainer',
  'AvailabilityCriteria' => 'NewyseServiceStructAvailabilityCriteria',
  'BillLines' => 'NewyseServiceStructBillLines',
  'Brochure' => 'NewyseServiceStructBrochure',
  'BrochureCodes' => 'NewyseServiceStructBrochureCodes',
  'BrochureContainer' => 'NewyseServiceStructBrochureContainer',
  'BrochureCriteria' => 'NewyseServiceStructBrochureCriteria',
  'BrochureRequest' => 'NewyseServiceStructBrochureRequest',
  'Brochures' => 'NewyseServiceStructBrochures',
  'Codes' => 'NewyseServiceStructCodes',
  'Countries' => 'NewyseServiceStructCountries',
  'Country' => 'NewyseServiceStructCountry',
  'CountryContainer' => 'NewyseServiceStructCountryContainer',
  'Customer' => 'NewyseServiceStructCustomer',
  'CustomerInfo' => 'NewyseServiceStructCustomerInfo',
  'CustomerReservationInfo' => 'NewyseServiceStructCustomerReservationInfo',
  'CustomerTitleContainer' => 'NewyseServiceStructCustomerTitleContainer',
  'CustomerTitleCriteria' => 'NewyseServiceStructCustomerTitleCriteria',
  'CustomerTitles' => 'NewyseServiceStructCustomerTitles',
  'DebitCardConsumption' => 'NewyseServiceStructDebitCardConsumption',
  'DebitCardConsumptions' => 'NewyseServiceStructDebitCardConsumptions',
  'DebitCardCustomer' => 'NewyseServiceStructDebitCardCustomer',
  'DebitCardCustomerContainer' => 'NewyseServiceStructDebitCardCustomerContainer',
  'DebitCardCustomers' => 'NewyseServiceStructDebitCardCustomers',
  'DebitCardItem' => 'NewyseServiceStructDebitCardItem',
  'DebitCardItemContainer' => 'NewyseServiceStructDebitCardItemContainer',
  'DebitCardItemCriteria' => 'NewyseServiceStructDebitCardItemCriteria',
  'DebitCardItems' => 'NewyseServiceStructDebitCardItems',
  'DebitCardTransactionContainer' => 'NewyseServiceStructDebitCardTransactionContainer',
  'Facilities' => 'NewyseServiceStructFacilities',
  'Facility' => 'NewyseServiceStructFacility',
  'FacilityContainer' => 'NewyseServiceStructFacilityContainer',
  'FacilityCriteria' => 'NewyseServiceStructFacilityCriteria',
  'GetReservation' => 'NewyseServiceStructGetReservation',
  'Image' => 'NewyseServiceStructImage',
  'ImageContainer' => 'NewyseServiceStructImageContainer',
  'ImageCriteria' => 'NewyseServiceStructImageCriteria',
  'Images' => 'NewyseServiceStructImages',
  'Infotexts' => 'NewyseServiceStructInfotexts',
  'KCPincodes' => 'NewyseServiceStructKCPincodes',
  'NewyseWebserviceError' => 'NewyseServiceStructNewyseWebserviceError',
  'Object' => 'NewyseServiceStructObject',
  'ObjectAvailabilities' => 'NewyseServiceStructObjectAvailabilities',
  'ObjectAvailability' => 'NewyseServiceStructObjectAvailability',
  'ObjectAvailabilityContainer' => 'NewyseServiceStructObjectAvailabilityContainer',
  'ObjectAvailabilityCriteria' => 'NewyseServiceStructObjectAvailabilityCriteria',
  'ObjectContainer' => 'NewyseServiceStructObjectContainer',
  'ObjectCriteria' => 'NewyseServiceStructObjectCriteria',
  'Objects' => 'NewyseServiceStructObjects',
  'OpeningTime' => 'NewyseServiceStructOpeningTime',
  'OpeningTimes' => 'NewyseServiceStructOpeningTimes',
  'PayingCustomerBillLines' => 'NewyseServiceStructPayingCustomerBillLines',
  'Payment' => 'NewyseServiceStructPayment',
  'PaymentCriteria' => 'NewyseServiceStructPaymentCriteria',
  'PincodeItem' => 'NewyseServiceStructPincodeItem',
  'PincodeItems' => 'NewyseServiceStructPincodeItems',
  'PincodeRegistration' => 'NewyseServiceStructPincodeRegistration',
  'Pincodes' => 'NewyseServiceStructPincodes',
  'Preference' => 'NewyseServiceStructPreference',
  'Preferences' => 'NewyseServiceStructPreferences',
  'Price' => 'NewyseServiceStructPrice',
  'Prices' => 'NewyseServiceStructPrices',
  'Properties' => 'NewyseServiceStructProperties',
  'Property' => 'NewyseServiceStructProperty',
  'PropertyContainer' => 'NewyseServiceStructPropertyContainer',
  'PropertyCriteria' => 'NewyseServiceStructPropertyCriteria',
  'Reservation' => 'NewyseServiceStructReservation',
  'ReservationBillLine' => 'NewyseServiceStructReservationBillLine',
  'ReservationContainer' => 'NewyseServiceStructReservationContainer',
  'ReservationCriteria' => 'NewyseServiceStructReservationCriteria',
  'Reservations' => 'NewyseServiceStructReservations',
  'ReservedResource' => 'NewyseServiceStructReservedResource',
  'ReservedResources' => 'NewyseServiceStructReservedResources',
  'Resort' => 'NewyseServiceStructResort',
  'ResortActivities' => 'NewyseServiceStructResortActivities',
  'ResortActivity' => 'NewyseServiceStructResortActivity',
  'ResortActivityContainer' => 'NewyseServiceStructResortActivityContainer',
  'ResortActivityCriteria' => 'NewyseServiceStructResortActivityCriteria',
  'ResortContainer' => 'NewyseServiceStructResortContainer',
  'ResortCriteria' => 'NewyseServiceStructResortCriteria',
  'Resorts' => 'NewyseServiceStructResorts',
  'ResourceAddition' => 'NewyseServiceStructResourceAddition',
  'ResourceAdditionContainer' => 'NewyseServiceStructResourceAdditionContainer',
  'ResourceAdditionCriteria' => 'NewyseServiceStructResourceAdditionCriteria',
  'ResourceAdditions' => 'NewyseServiceStructResourceAdditions',
  'SessionCriteria' => 'NewyseServiceStructSessionCriteria',
  'SourceContainer' => 'NewyseServiceStructSourceContainer',
  'SourceCriteria' => 'NewyseServiceStructSourceCriteria',
  'Sources' => 'NewyseServiceStructSources',
  'Special' => 'NewyseServiceStructSpecial',
  'SpecialCodes' => 'NewyseServiceStructSpecialCodes',
  'Subject' => 'NewyseServiceStructSubject',
  'SubjectContainer' => 'NewyseServiceStructSubjectContainer',
  'SubjectCriteria' => 'NewyseServiceStructSubjectCriteria',
  'SubjectQuantities' => 'NewyseServiceStructSubjectQuantities',
  'SubjectQuantity' => 'NewyseServiceStructSubjectQuantity',
  'Subjects' => 'NewyseServiceStructSubjects',
  'TipTrip' => 'NewyseServiceStructTipTrip',
  'TipTripCategories' => 'NewyseServiceStructTipTripCategories',
  'TipTripCategory' => 'NewyseServiceStructTipTripCategory',
  'TipTripCategoryContainer' => 'NewyseServiceStructTipTripCategoryContainer',
  'TipTripContainer' => 'NewyseServiceStructTipTripContainer',
  'TipTripCriteria' => 'NewyseServiceStructTipTripCriteria',
  'TipTrips' => 'NewyseServiceStructTipTrips',
  'VoucherContainer' => 'NewyseServiceStructVoucherContainer',
  'VoucherCriteria' => 'NewyseServiceStructVoucherCriteria',
  'VoucherItem' => 'NewyseServiceStructVoucherItem',
  'Vouchers' => 'NewyseServiceStructVouchers',
  'WSCustomerTitle' => 'NewyseServiceStructWSCustomerTitle',
  'WSSource' => 'NewyseServiceStructWSSource',
  'objectCleaningStatus' => 'NewyseServiceEnumObjectCleaningStatus',
);
    }
}
