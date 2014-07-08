<?php
/**
 * File to load generated classes once at once time
 * @package NewyseService
 */
/**
 * Includes for all generated classes files
 */
require_once dirname(__FILE__) . '/NewyseServiceWsdlClass.php';
require_once dirname(__FILE__) . '/Resource/Additions/NewyseServiceStructResourceAdditions.php';
require_once dirname(__FILE__) . '/Resource/Addition/NewyseServiceStructResourceAddition.php';
require_once dirname(__FILE__) . '/Tip/Criteria/NewyseServiceStructTipTripCriteria.php';
require_once dirname(__FILE__) . '/Resource/Container/NewyseServiceStructResourceAdditionContainer.php';
require_once dirname(__FILE__) . '/Resource/Criteria/NewyseServiceStructResourceAdditionCriteria.php';
require_once dirname(__FILE__) . '/Price/NewyseServiceStructPrice.php';
require_once dirname(__FILE__) . '/Special/NewyseServiceStructSpecial.php';
require_once dirname(__FILE__) . '/Tip/Container/NewyseServiceStructTipTripContainer.php';
require_once dirname(__FILE__) . '/Tip/Trips/NewyseServiceStructTipTrips.php';
require_once dirname(__FILE__) . '/WSS/Ource/NewyseServiceStructWSSource.php';
require_once dirname(__FILE__) . '/Image/Criteria/NewyseServiceStructImageCriteria.php';
require_once dirname(__FILE__) . '/Sources/NewyseServiceStructSources.php';
require_once dirname(__FILE__) . '/Source/Container/NewyseServiceStructSourceContainer.php';
require_once dirname(__FILE__) . '/Tip/Trip/NewyseServiceStructTipTrip.php';
require_once dirname(__FILE__) . '/Source/Criteria/NewyseServiceStructSourceCriteria.php';
require_once dirname(__FILE__) . '/Prices/NewyseServiceStructPrices.php';
require_once dirname(__FILE__) . '/Availability/NewyseServiceStructAvailability.php';
require_once dirname(__FILE__) . '/Voucher/Container/NewyseServiceStructVoucherContainer.php';
require_once dirname(__FILE__) . '/Vouchers/NewyseServiceStructVouchers.php';
require_once dirname(__FILE__) . '/Voucher/Item/NewyseServiceStructVoucherItem.php';
require_once dirname(__FILE__) . '/Voucher/Criteria/NewyseServiceStructVoucherCriteria.php';
require_once dirname(__FILE__) . '/Debit/Item/NewyseServiceStructDebitCardItem.php';
require_once dirname(__FILE__) . '/Debit/Container/NewyseServiceStructDebitCardItemContainer.php';
require_once dirname(__FILE__) . '/Debit/Items/NewyseServiceStructDebitCardItems.php';
require_once dirname(__FILE__) . '/Codes/NewyseServiceStructCodes.php';
require_once dirname(__FILE__) . '/Customer/Criteria/NewyseServiceStructCustomerTitleCriteria.php';
require_once dirname(__FILE__) . '/Availability/Container/NewyseServiceStructAvailabilityContainer.php';
require_once dirname(__FILE__) . '/Availabilities/NewyseServiceStructAvailabilities.php';
require_once dirname(__FILE__) . '/Availability/Criteria/NewyseServiceStructAvailabilityCriteria.php';
require_once dirname(__FILE__) . '/WSC/Title/NewyseServiceStructWSCustomerTitle.php';
require_once dirname(__FILE__) . '/Customer/Container/NewyseServiceStructCustomerTitleContainer.php';
require_once dirname(__FILE__) . '/Customer/Titles/NewyseServiceStructCustomerTitles.php';
require_once dirname(__FILE__) . '/Image/Container/NewyseServiceStructImageContainer.php';
require_once dirname(__FILE__) . '/Images/NewyseServiceStructImages.php';
require_once dirname(__FILE__) . '/Activity/Categories/NewyseServiceStructActivityCategories.php';
require_once dirname(__FILE__) . '/Opening/Times/NewyseServiceStructOpeningTimes.php';
require_once dirname(__FILE__) . '/Activity/Category/NewyseServiceStructActivityCategory.php';
require_once dirname(__FILE__) . '/Resort/Activity/NewyseServiceStructResortActivity.php';
require_once dirname(__FILE__) . '/Resort/Activities/NewyseServiceStructResortActivities.php';
require_once dirname(__FILE__) . '/Resort/Criteria/NewyseServiceStructResortActivityCriteria.php';
require_once dirname(__FILE__) . '/Resort/Container/NewyseServiceStructResortActivityContainer.php';
require_once dirname(__FILE__) . '/Opening/Time/NewyseServiceStructOpeningTime.php';
require_once dirname(__FILE__) . '/Facility/Criteria/NewyseServiceStructFacilityCriteria.php';
require_once dirname(__FILE__) . '/Object/Status/NewyseServiceEnumObjectCleaningStatus.php';
require_once dirname(__FILE__) . '/Newyse/Error/NewyseServiceStructNewyseWebserviceError.php';
require_once dirname(__FILE__) . '/Session/Criteria/NewyseServiceStructSessionCriteria.php';
require_once dirname(__FILE__) . '/Facility/NewyseServiceStructFacility.php';
require_once dirname(__FILE__) . '/Facility/Container/NewyseServiceStructFacilityContainer.php';
require_once dirname(__FILE__) . '/Facilities/NewyseServiceStructFacilities.php';
require_once dirname(__FILE__) . '/Accommodation/Kind/NewyseServiceStructAccommodationKind.php';
require_once dirname(__FILE__) . '/Accommodation/Kinds/NewyseServiceStructAccommodationKinds.php';
require_once dirname(__FILE__) . '/Tip/Container/NewyseServiceStructTipTripCategoryContainer.php';
require_once dirname(__FILE__) . '/Tip/Categories/NewyseServiceStructTipTripCategories.php';
require_once dirname(__FILE__) . '/Tip/Category/NewyseServiceStructTipTripCategory.php';
require_once dirname(__FILE__) . '/Pincode/Registration/NewyseServiceStructPincodeRegistration.php';
require_once dirname(__FILE__) . '/Brochure/Codes/NewyseServiceStructBrochureCodes.php';
require_once dirname(__FILE__) . '/Image/NewyseServiceStructImage.php';
require_once dirname(__FILE__) . '/Brochure/Request/NewyseServiceStructBrochureRequest.php';
require_once dirname(__FILE__) . '/Subject/Criteria/NewyseServiceStructSubjectCriteria.php';
require_once dirname(__FILE__) . '/Subject/Container/NewyseServiceStructSubjectContainer.php';
require_once dirname(__FILE__) . '/Accommodation/Criteria/NewyseServiceStructAccommodationKindCriteria.php';
require_once dirname(__FILE__) . '/Accommodation/Container/NewyseServiceStructAccommodationKindContainer.php';
require_once dirname(__FILE__) . '/Address/NewyseServiceStructAddress.php';
require_once dirname(__FILE__) . '/Address/Criteria/NewyseServiceStructAddressCriteria.php';
require_once dirname(__FILE__) . '/Subjects/NewyseServiceStructSubjects.php';
require_once dirname(__FILE__) . '/Subject/NewyseServiceStructSubject.php';
require_once dirname(__FILE__) . '/Debit/Criteria/NewyseServiceStructDebitCardItemCriteria.php';
require_once dirname(__FILE__) . '/Object/NewyseServiceStructObject.php';
require_once dirname(__FILE__) . '/Paying/Lines/NewyseServiceStructPayingCustomerBillLines.php';
require_once dirname(__FILE__) . '/Infotexts/NewyseServiceStructInfotexts.php';
require_once dirname(__FILE__) . '/Reserved/Resources/NewyseServiceStructReservedResources.php';
require_once dirname(__FILE__) . '/Agent/Lines/NewyseServiceStructAgentBillLines.php';
require_once dirname(__FILE__) . '/Bill/Lines/NewyseServiceStructBillLines.php';
require_once dirname(__FILE__) . '/Addition/NewyseServiceStructAddition.php';
require_once dirname(__FILE__) . '/Reservation/NewyseServiceStructReservation.php';
require_once dirname(__FILE__) . '/Reservation/Line/NewyseServiceStructReservationBillLine.php';
require_once dirname(__FILE__) . '/Reserved/Resource/NewyseServiceStructReservedResource.php';
require_once dirname(__FILE__) . '/Payment/Criteria/NewyseServiceStructPaymentCriteria.php';
require_once dirname(__FILE__) . '/Payment/NewyseServiceStructPayment.php';
require_once dirname(__FILE__) . '/Debit/Consumption/NewyseServiceStructDebitCardConsumption.php';
require_once dirname(__FILE__) . '/Debit/Consumptions/NewyseServiceStructDebitCardConsumptions.php';
require_once dirname(__FILE__) . '/Customer/NewyseServiceStructCustomer.php';
require_once dirname(__FILE__) . '/Debit/Container/NewyseServiceStructDebitCardTransactionContainer.php';
require_once dirname(__FILE__) . '/Subject/Quantity/NewyseServiceStructSubjectQuantity.php';
require_once dirname(__FILE__) . '/Preference/NewyseServiceStructPreference.php';
require_once dirname(__FILE__) . '/Brochures/NewyseServiceStructBrochures.php';
require_once dirname(__FILE__) . '/Brochure/NewyseServiceStructBrochure.php';
require_once dirname(__FILE__) . '/Object/Criteria/NewyseServiceStructObjectAvailabilityCriteria.php';
require_once dirname(__FILE__) . '/Brochure/Container/NewyseServiceStructBrochureContainer.php';
require_once dirname(__FILE__) . '/Brochure/Criteria/NewyseServiceStructBrochureCriteria.php';
require_once dirname(__FILE__) . '/Countries/NewyseServiceStructCountries.php';
require_once dirname(__FILE__) . '/Country/NewyseServiceStructCountry.php';
require_once dirname(__FILE__) . '/Object/Container/NewyseServiceStructObjectAvailabilityContainer.php';
require_once dirname(__FILE__) . '/Object/Availabilities/NewyseServiceStructObjectAvailabilities.php';
require_once dirname(__FILE__) . '/Additions/NewyseServiceStructAdditions.php';
require_once dirname(__FILE__) . '/Accommodation/NewyseServiceStructAccommodation.php';
require_once dirname(__FILE__) . '/Subject/Quantities/NewyseServiceStructSubjectQuantities.php';
require_once dirname(__FILE__) . '/Preferences/NewyseServiceStructPreferences.php';
require_once dirname(__FILE__) . '/Object/Availability/NewyseServiceStructObjectAvailability.php';
require_once dirname(__FILE__) . '/Reservation/Criteria/NewyseServiceStructReservationCriteria.php';
require_once dirname(__FILE__) . '/Debit/Container/NewyseServiceStructDebitCardCustomerContainer.php';
require_once dirname(__FILE__) . '/Debit/Customers/NewyseServiceStructDebitCardCustomers.php';
require_once dirname(__FILE__) . '/KCP/Incodes/NewyseServiceStructKCPincodes.php';
require_once dirname(__FILE__) . '/Pincode/Items/NewyseServiceStructPincodeItems.php';
require_once dirname(__FILE__) . '/Pincode/Item/NewyseServiceStructPincodeItem.php';
require_once dirname(__FILE__) . '/Accommodation/Object/NewyseServiceStructAccommodationTypeSearchObject.php';
require_once dirname(__FILE__) . '/Accommodation/Objects/NewyseServiceStructAccommodationTypeSearchObjects.php';
require_once dirname(__FILE__) . '/Accommodation/Container/NewyseServiceStructAccommodationTypeSearchContainer.php';
require_once dirname(__FILE__) . '/Accommodation/Search/NewyseServiceStructAccommodationTypeSearch.php';
require_once dirname(__FILE__) . '/Pincodes/NewyseServiceStructPincodes.php';
require_once dirname(__FILE__) . '/Resort/Criteria/NewyseServiceStructResortCriteria.php';
require_once dirname(__FILE__) . '/Object/Container/NewyseServiceStructObjectContainer.php';
require_once dirname(__FILE__) . '/Objects/NewyseServiceStructObjects.php';
require_once dirname(__FILE__) . '/Object/Criteria/NewyseServiceStructObjectCriteria.php';
require_once dirname(__FILE__) . '/Resort/NewyseServiceStructResort.php';
require_once dirname(__FILE__) . '/Resort/Container/NewyseServiceStructResortContainer.php';
require_once dirname(__FILE__) . '/Resorts/NewyseServiceStructResorts.php';
require_once dirname(__FILE__) . '/Special/Codes/NewyseServiceStructSpecialCodes.php';
require_once dirname(__FILE__) . '/Accommodation/Criteria/NewyseServiceStructAccommodationTypeSearchCriteria.php';
require_once dirname(__FILE__) . '/Property/Container/NewyseServiceStructPropertyContainer.php';
require_once dirname(__FILE__) . '/Properties/NewyseServiceStructProperties.php';
require_once dirname(__FILE__) . '/Property/Criteria/NewyseServiceStructPropertyCriteria.php';
require_once dirname(__FILE__) . '/Customer/Info/NewyseServiceStructCustomerReservationInfo.php';
require_once dirname(__FILE__) . '/Debit/Customer/NewyseServiceStructDebitCardCustomer.php';
require_once dirname(__FILE__) . '/Customer/Info/NewyseServiceStructCustomerInfo.php';
require_once dirname(__FILE__) . '/Property/NewyseServiceStructProperty.php';
require_once dirname(__FILE__) . '/Get/Reservation/NewyseServiceStructGetReservation.php';
require_once dirname(__FILE__) . '/Accommodation/Types/NewyseServiceStructAccommodationTypes.php';
require_once dirname(__FILE__) . '/Accommodation/Type/NewyseServiceStructAccommodationType.php';
require_once dirname(__FILE__) . '/Accommodation/Container/NewyseServiceStructAccommodationTypeContainer.php';
require_once dirname(__FILE__) . '/Accommodation/Criteria/NewyseServiceStructAccommodationTypeCriteria.php';
require_once dirname(__FILE__) . '/Reservation/Container/NewyseServiceStructReservationContainer.php';
require_once dirname(__FILE__) . '/Reservations/NewyseServiceStructReservations.php';
require_once dirname(__FILE__) . '/Country/Container/NewyseServiceStructCountryContainer.php';
require_once dirname(__FILE__) . '/Get/NewyseServiceServiceGet.php';
require_once dirname(__FILE__) . '/Update/NewyseServiceServiceUpdate.php';
require_once dirname(__FILE__) . '/Create/NewyseServiceServiceCreate.php';
require_once dirname(__FILE__) . '/Register/NewyseServiceServiceRegister.php';
require_once dirname(__FILE__) . '/Decline/NewyseServiceServiceDecline.php';
require_once dirname(__FILE__) . '/Find/NewyseServiceServiceFind.php';
require_once dirname(__FILE__) . '/Confirm/NewyseServiceServiceConfirm.php';
require_once dirname(__FILE__) . '/Generate/NewyseServiceServiceGenerate.php';
require_once dirname(__FILE__) . '/Destroy/NewyseServiceServiceDestroy.php';
require_once dirname(__FILE__) . '/Request/NewyseServiceServiceRequest.php';
require_once dirname(__FILE__) . '/Cancel/NewyseServiceServiceCancel.php';
require_once dirname(__FILE__) . '/Checkin/NewyseServiceServiceCheckin.php';
require_once dirname(__FILE__) . '/NewyseServiceClassMap.php';
