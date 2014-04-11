<?php
require_once("init.php");

/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/IHomeAutoload.php';
/**
 * IHome Informations
 */
define('IHOME_WSDL_URL','https://webservices.interhome.com/partnerV3/WebService.asmx?WSDL');
define('IHOME_USER_LOGIN','NL1000993');
define('IHOME_USER_PASSWORD','chalet2013');
define('IHOME_FTP_URL', 'ftp.interhome.com');
define('IHOME_FTP_USERNAME', 'ihxmlpartner');
define('IHOME_FTP_PASSWORD', 'S13oPjEu');
define('IHOME_XML_URL', 'ftp://' . IHOME_FTP_USERNAME . ":" . IHOME_FTP_PASSWORD . "@" . IHOME_FTP_URL . "/");

/**
*  Interhome web services class
*/
class InterHome extends SoapClass {

	private $wsdl = array();
	private $salesOffice = '4040'; // Netherlands
	private $currencyCode = 'EUR'; // Euro
	private $languageCode = 'NL'; // Dutch
	private $xmlCustomerFeedback = 'customerfeedback.xml.zip'; // The zip file from FTP	containing customer feedback
	private $xmlCountriesRegions = 'countryregionplace_nl.xml.zip'; // The zip file from FTP containing all countries, regions and places with their codes
	private $xmlWeekPrice = 'price_4040_eur.xml.zip'; // The zip file from FTP	containing week prices
	private $_DISCOUNT = null;
    
	function __construct() {

		parent::__construct();

		/**
		 * Wsdl instanciation infos
		 */
		$wsdl = array();
		$wsdl[IHomeWsdlClass::WSDL_URL] = IHOME_WSDL_URL;
		$wsdl[IHomeWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
		$wsdl[IHomeWsdlClass::WSDL_TRACE] = true;
		if(IHOME_USER_LOGIN !== '')
			$wsdl[IHomeWsdlClass::WSDL_LOGIN] = IHOME_USER_LOGIN;
		if(IHOME_USER_PASSWORD !== '')
			$wsdl[IHomeWsdlClass::WSDL_PASSWD] = IHOME_USER_PASSWORD;

		$this->wsdl = $wsdl;
		$this->xml_url = IHOME_XML_URL;
	}

	/**
	 * The function can be used to check the server status before executing any other SOAP calls
	 *
	 * @return array|IHomeStructCheckServerHealthResponse|IHomeStructCheckServerHealthV2Response
	 */
	public function getStatus() {
		/*******************************
		 * IHomeServiceCheck
		 */

		// Service check does not require the SoapHeader with username and password
		$this->wsdl["wsdl_header"] = false;

		$ihomeServiceCheck = new IHomeServiceCheck($this->wsdl);

		// Reset the SOAP header
		unset($this->wsdl["wsdl_header"]);

		// Call for IHomeServiceCheck::CheckServerHealth()
		if($ihomeServiceCheck->CheckServerHealth())
			return $ihomeServiceCheck->getResult();
		else
			return $ihomeServiceCheck->getLastError();
	}

	/**
	 * The function retrieves the prices using SOAP for a specific accommodation
	 * It then formats the prices dates and returns the prices for each week, starting with Saturday
	 *
	 * @param string $accCode The accommodation code from Interhome
	 * @return array|null
	 */
	public function getPriceList($accCode = null) {

		// Check if the accommodation code is set
		if(empty($accCode) || empty($this->wsdl)) return null;

		/*******************************
		 * IHomeServicePrice
		 */
		 $ihomeServicePrice = new IHomeServicePrice($this->wsdl);
		
		// Call for IHomeServicePrice::PriceList()
		$params = array(
			'AccommodationCode' => $accCode,
			'CurrencyCode' 		=> $this->currencyCode,
			'SalesOffice' 		=> $this->salesOffice
		);
		
		$soap_price = array();

		if($ihomeServicePrice->PriceList(new IHomeStructPriceList($params))) {
			
			$tmpPriceList = current($ihomeServicePrice->getResult());
			$itemPriceList = $tmpPriceList->getPriceListResult();

			// Check for errors
			if($itemPriceList->getOk() == false) {
				if(count($errors = $itemPriceList->getErrors()) > 0) {
					foreach ($errors as $error) {					
						return $error->getDescription();
					}
				}
			} else {
				// If no errors were found				
				// Get all the price items from the response
				$arrPriceListItems = $itemPriceList->getItems()->getPriceListItem();

				if(count($arrPriceListItems) > 0) {
					foreach ($arrPriceListItems as $key => $item) {
						$date_begin = strtotime($item->getStartDate());
						$date_end = strtotime($item->getEndDate());
						$price = $item->getPrice();

						$day = $date_begin;
						// Create price for each day
						while($day <= $date_end) {
							$day_price = round($price/7, 2);
							$tmp_price[$day] = $day_price;
							$day = mktime(0,0,0,date("m",$day),date("d",$day)+1,date("Y",$day));
						}
					}

					// Create a temp array with costs for each week and the number of days in that week
					foreach ($tmp_price as $tmp_day => $tmp_day_price) {
						if(date("w",$tmp_day)==6) {
							$tmp_week = $tmp_day;
							if(!isset($tmp_soap_price[$tmp_week])) $tmp_soap_price[$tmp_week] = array("price" => 0, "days" => 0);
						}
						if(isset($tmp_week)) {
							$tmp_soap_price[$tmp_week]["price"] += $tmp_day_price;
							$tmp_soap_price[$tmp_week]["days"]++;
						}
					}

					# Loop throught all the weeks and round the price for each week only if the week contains 7 days
					foreach($tmp_soap_price as $week => $value) {
						if($value["days"] == 7) {
							$soap_price[$week] = round($value["price"], 0);
						}
					}
				}
			}

			return $soap_price;
		}
		else {
			return $ihomeServicePrice->getLastError();
		}
	}

	/**
	 * The function retrieves the availability dates using SOAP for a specific accommodation
	 * It then formats the dates and returns the availability for each week, starting with Saturday
	 *
	 * @param string $accCode The accommodation code from Interhome
	 * @param string $checkIn date
	 * @param string $checkOut Check out date
	 * @return array|null
	 */
	public function getAvailability($accCode = null, $checkIn, $checkOut = "") {

		// Check if the accommodation code is set
		if(empty($accCode) || empty($this->wsdl)) return false;

		/**************************************
		 * IHomeServiceAvailability
		 */

		if(!isset($checkIn) || empty($checkIn)) {
			$checkIn = date("Y-m-d");
		}
		$ihomeServiceAvailability = new IHomeServiceAvailability($this->wsdl);
		$params = array(
			'AccommodationCode' => $accCode,
			'CheckIn' 			=> $checkIn,
			'CheckOut' 			=> $checkOut
		);

		// Call for IHomeServiceAvailability::Availability()
		if($ihomeServiceAvailability->Availability(new IHomeStructAvailability($params))) {

			$tmpServiceAvailability = current($ihomeServiceAvailability->getResult());
			$itemServiceAvailability = $tmpServiceAvailability->getAvailabilityResult();

			// Check for errors
			if($itemServiceAvailability->getOk() == false) {
				if(count($errors = $itemServiceAvailability->getErrors()) > 0) {
					foreach ($errors as $error) {						
						//return $error->getDescription();
						return false;
					}
				}
			} else {
				// If no errors were found				
				$date_begin = strtotime($itemServiceAvailability->getStartDate());
				$date_end = strtotime($itemServiceAvailability->getEndDate());
				$state = $itemServiceAvailability->getState();
				$change = $itemServiceAvailability->getChange();
				$min_stay = $itemServiceAvailability->getMinimumStay();
				
				$temp_available = array();
				$soap_available = array();

				for($i=0; $i<=strlen($state); $i++) {

					$day = mktime(0,0,0,date("m",$date_begin),date("d",$date_begin)+$i,date("Y",$date_begin));

					# Convert day to week (Saturdays)
					if(date("w",$day)==6) {
						$week = $day;
					} else {
						$week = mktime(0,0,0,date("m",$day),date("d",$day)-(date("w",$day)+1),date("Y",$day));
					}
					$week = $this->date_to_gmdate($week);
					/*
					State Availability of property
						- Y = available
						- N = occupied
						- Q = on request
					*/
					# Available in this day
					if(substr($state,$i,1)=="Y" || substr($state,$i,1)=="Q") {
						if(!isset($temp_available[$week])) $temp_available[$week] = 0;
						# available
						$temp_available[$week]++;
					} else {
						# not available
						if(!isset($temp_available[$week])) $temp_available[$week] = 0;
						$temp_available[$week]--;
					}

					/*
					Change Indicates, if the house-keys can be handed over (check-in possible?) that day
						- X - no action possible
						- C - check-in and check-out
						- O - check-out only
						- I - check-in only
					
					MinimumStay Minimum-stay for the chosen start-day
						- 0 - not bookable
						- A - min. stay 1 day
						- B - min. stay 2 days
						- C - min. stay 3 days
						- D - min. stay 4 days
						- E - min. stay 5 days
						- F - min. stay 6 days
						- G - min. stay 7 days
						...
						- N - min. stay 14 days
						...
						- Z - min. stay 26 days
					*/

					if(date("w",$day)==5 && substr($min_stay,$i-6,1)!="0") {
						# on Friday we check to see if the previous week was free, and 6 days ago (Saturday) was a changeover was possible
						if(substr($change,$i-6,1)=="C" || substr($change,$i-6,1)=="I") {
							if($temp_available[$week]==7) {
								if(!isset($soap_available[$week])) $soap_available[$week] = 0;

								$soap_available[$week]+=1;
							} else {
								$soap_available[$week]=0;
							}
						} else {
							$soap_available[$week]=0;
						}
					}
				}

				return $soap_available;				
			}	
		} else {
			//return $ihomeServiceAvailability->getLastError();
			return false;
		}
	}

	/**
	 * The function retrieves accommodation details using SOAP for a specific accommodation code
	 * It returns the object with all the details provided by Interhome
	 *
	 * @param null $accCode The accommodation code from Interhome
	 * @return object|null
	 */
	public function getAccommodation($accCode = null) {

		// Check if the accommodation code is set
		if(empty($accCode) || empty($this->wsdl)) return null;

		/***************************************
		 * IHomeServiceAccommodation
		 */
		$ihomeServiceAccommodation = new IHomeServiceAccommodation($this->wsdl);

		$params = array(
			'AccommodationCode' => $accCode,
			'LanguageCode' 		=> $this->languageCode,
		);

		// sample call for IHomeServiceAccommodation::AccommodationDetail()
		if($ihomeServiceAccommodation->AccommodationDetail(new IHomeStructAccommodationDetail($params))) {
			
			$result = current($ihomeServiceAccommodation->getResult());	
			// IHomeStructAccommodationDetailReturnValue
			$itemAccommodationDetail = $result->getAccommodationDetailResult();
		 	
			// Check for errors
		 	if($itemAccommodationDetail->getOk() == false) {
				if(count($errors = $itemAccommodationDetail->getErrors()) > 0) {
					foreach ($errors as $error) {						
						return $error->getDescription();
					}
				}
		 	} else {
				return $itemAccommodationDetail;
			}
		}
		else {
			return $ihomeServiceAccommodation->getLastError();
		}
	}

	/**
	 * The function retrieves all the additional services that can be applied for a specific accommodation
	 *
	 * @param $accCode The accommodation code from Interhome
	 * @param string $seasonStart date
	 * @param string $seasonEnd date
	 * @return array|null
	 */
	public function getAdditionalServices($accCode, $seasonStart = '', $seasonEnd = '') {

		// Check if the accommodation code is set
		if(empty($accCode) || empty($this->wsdl)) return null;

		/************************************
		 * IHomeServiceAdditional
		 */
		$params = array(
			'AccommodationCode' => $accCode,
			'LanguageCode' 		=> $this->languageCode,
			'CurrencyCode' 		=> $this->currencyCode,
			'SalesOfficeCode' 	=> $this->salesOffice,
			'Adults' 			=> '',
			'Children' 			=> '',
			'Babies' 			=> '',
			'CheckIn' 			=> $seasonStart,
			'CheckOut' 			=> $seasonEnd
		);

		$ihomeServiceAdditional = new IHomeServiceAdditional($this->wsdl);
		// Call for IHomeServiceAdditional::AdditionalServices()
		if($ihomeServiceAdditional->AdditionalServices(new IHomeStructAdditionalServices($params))) {
			
			$result = current($ihomeServiceAdditional->getResult());	
			$arrayAdditionalServices = $result->getAdditionalServicesResult();

			// Check for errors
			if($arrayAdditionalServices->getOk() == false) {		
				if(count($errors = $arrayAdditionalServices->getErrors()) > 0) {
					foreach ($errors as $error) {
						return $error->getDescription();
					}
				}
			} else {				
				$arrayOfAdditionalServices = $arrayAdditionalServices->getAdditionalServices()->getAdditionalServiceItem();
				if(count($arrayOfAdditionalServices) > 0) {
					$arrServices = array();
					// IHomeStructAdditionalServiceItem
					foreach ($arrayOfAdditionalServices as $value) {
						$arrServices[] = $value;

					}
				}
				return $arrServices;
			}
		} else {
			return $ihomeServiceAdditional->getLastError();
		}
	}

	/**
	 * The function is used to search for new accommodations from a specific country, region or place
	 * The returned accommodations can be imported (or not) into the Chalet database
	 *
	 * @param string $countryCode The two letters ISO country code
	 * @param string $regionCode The id of the region provided by Interhome; the parameter can be omitted
	 * @param string $placeCode The id of the place provided by Interhome; the parameter can be omitted
	 * @return array
	 */
	public function getAccommodations($countryCode = "", $regionCode = "", $placeCode = "") {

		/********************************
		 * IHomeServiceSearch
		 */
		$ihomeServiceSearch = new IHomeServiceSearch($this->wsdl);

		$Page = 1;
		$MaxPageSize = 100;

		$params = array(
			"SalesOfficeCode" => $this->salesOffice,
			"LanguageCode" => $this->languageCode,
			"CurrencyCode" => $this->currencyCode,
			"CountryCode" => $countryCode,
			"RegionCode" => $regionCode,
			"PlaceCode" => $placeCode,
			"Quicksearch" => "",
			"CheckIn" => "",
			"Duration" => 0,
			"BathroomsMin" => 0,
			"BathroomsMax" => 0,
			"RoomsMin" => 0,
			"RoomsMax" => 0,
			"BedroomsMin" => 0,
			"BedroomsMax" => 0,
			"PaxMin" => 0,
			"PaxMax" => 0,
			"QualityMin" => 0,
			"QualityMax" => 0,
			"Page" => $Page,
			"PageSize" => $MaxPageSize, // max per page is 100
			"OrderDirection" => "Ascending",
			"OrderBy" => "Price",
			"SpecialOffer" => "NotSet",
			"ThemeFilter" => "NotSet",
			"HouseApartmentType" => "NotSet",
			"DistanceToCenter" => 0,
			"DistanceToGolfCourse" => 0,
			"DistanceToLake" => 0,
			"DistanceToSea" => 0,
			"DistanceToSeaOrLake" => 0,
			"DistanceToSkiLifts" => 0,
		);


		// sample call for IHomeServiceSearch::Search()
		if($ihomeServiceSearch->Search(new IHomeStructSearch($params))) {
			$result = current($ihomeServiceSearch->getResult());
			$itemSearchResult = $result->getSearchResult();

			// Check for errors
			if($itemSearchResult->getOk() == false) {

				if(count($errors = $itemSearchResult->getErrors()) > 0) {
					foreach ($errors as $error) {						
						return $error->getDescription();
					}
				}

			} else {

				$RowsCount = $itemSearchResult->getResultCount();
				$arrSearch = array();

				if((int)$RowsCount > $MaxPageSize) {
					$pagesCount = ceil($RowsCount / $MaxPageSize);

					if($pagesCount >= 1) {

						for ($i=1; $i<=$pagesCount; $i++) {

							$params["Page"] = $i;

							if($ihomeServiceSearch->Search(new IHomeStructSearch($params))) {
								$result = current($ihomeServiceSearch->getResult());
								$itemSearchResult = $result->getSearchResult();
								$arrSearchResultItems = $itemSearchResult->getItems()->getSearchResultItem();

								foreach ($arrSearchResultItems as $value) {
									$arrSearch[$value->getAccommodationCode()]["region"] = $value->getRegion();
									$arrSearch[$value->getAccommodationCode()]["place"] = $value->getPlace();
									$arrSearch[$value->getAccommodationCode()]["info"] = $value;
								}

							}
							else {
								return $ihomeServiceSearch->getLastError();
							}

						}
					}
				} else {

					$arrSearchResultItems = $itemSearchResult->getItems()->getSearchResultItem();

					if($RowsCount == 1) {
						$arrSearch[$arrSearchResultItems->getAccommodationCode()]["region"] = $arrSearchResultItems->getRegion();
						$arrSearch[$arrSearchResultItems->getAccommodationCode()]["place"] = $arrSearchResultItems->getPlace();
						$arrSearch[$arrSearchResultItems->getAccommodationCode()]["info"] = $arrSearchResultItems;
					} else {
						foreach ($arrSearchResultItems as $value) {
							$arrSearch[$value->getAccommodationCode()]["region"] = $value->getRegion();
							$arrSearch[$value->getAccommodationCode()]["place"] = $value->getPlace();
							$arrSearch[$value->getAccommodationCode()]["info"] = $value;
							$count++;
						}
					}
				}

				return $arrSearch;
			}
		}
		else {
			return $ihomeServiceSearch->getLastError();
		}
	}

	/**
	 * The function retrieves all the customer feedback information using XML import
	 *
	 * @return array|null It returns an array item for each feedback entry
	 */
	public function getCustomerFeedback() {
		// Called from parent class
		$output = $this->getFileFromZip($this->xml_url, $this->xmlCustomerFeedback);

		if(file_exists($output)) {
			$xml = simplexml_load_file($output);
			$arrFeedback = array();
			foreach ($xml->children() as $item) {
				#e.g. SimpleXMLElement Object ( [code] => AT1010.103.1 [customername] => Chiara G. [customercountry] => IT [periodoftravel] => 12.2010 [totalrating] => 9.0 [localservice] => 8 [furnishings] => 8 [cleanliness] => 10 [valueformoney] => 10 )
				$arrItem = $item;
				$arrFeedback[] = $arrItem;
			}
			return $arrFeedback;
		}

		return null;
	}

	public function getWeekPrice($arrCodes = array()) {

		// Called from parent class
		$output = $this->getFileFromZip($this->xml_url, $this->xmlWeekPrice);

		if(file_exists($output)) {
			$xml = simplexml_load_file($output);

			foreach ($xml->children() as $data) {
				# e.g.: SimpleXMLElement Object ( [code] => AT6574.410.1 [startdate] => 2013-11-02 [enddate] => 2013-11-08 [rentalprice] => 521.00 [minrentalprice] => 349.00 [maxrentalprice] => 1220.00 [specialoffer] => SimpleXMLElement Object ( [code] => LM/00000001 [specialofferprice] => 349.00 ) [services] => SimpleXMLElement Object ( [service] => SimpleXMLElement Object ( [code] => CF [serviceprice] => 2.5 [textcode] => 0000000182 ) ) )

				$accCode = $data->code->__toString();
				if($arrCodes[$accCode] == 1) {

					$startDate = strtotime((string)$data->startdate);
					$endDate = strtotime((string)$data->enddate);

					if(isset($data->specialoffer)) {
						$price = floatval($data->specialoffer->specialofferprice);
					} else {
						$price = floatval($data->rentalprice);
					}

					$arrWeekPrice[$accCode][$startDate] = array("e" => $endDate, "p" => $price);
				}
			}
			return $arrWeekPrice;

		}

		return array();
	}

	public function getPrices($accCode = NULL, $arrPriceListItems = array()) {

		// Check if the accommodation code is set
		if(empty($arrPriceListItems) || empty($accCode)) return null;

		if(!isset($arrPriceListItems[$accCode])) return null;

		if(count($arrPriceListItems[$accCode]) > 0) {
			foreach ($arrPriceListItems[$accCode] as $date_begin => $item) {
				$date_end = $item["e"];
				$xml_price = $item["p"];

				if(date("w",$date_begin) != 6) {
					$week = $this->_nearestSaturday($date_begin);
				} else {
					$week = $date_begin;
				}

				// Create price for each day
				while($week <= $date_end) {
					$price[$week] = $xml_price;
					$week = mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
				}
			}
		}

		return $price;

	}

	/**
	 * The function returns an array with all the countries and the associated regions from Interhome
	 *
	 * @return null|array
	 */
	public function getCountriesRegions() {
		// Called from parent class
		$output = $this->getFileFromZip($this->xml_url, $this->xmlCountriesRegions);

		if(file_exists($output)) {
			$xml = simplexml_load_file($output);
			$arrFeedback = array();
			foreach ($xml->children() as $node => $item) {
				// If the parent node is a country
				if($node == "country") {
					$code = (string)$item->code;
					$arrItem[$code]["code"] = $code;
					$arrItem[$code]["name"] = (string)$item->name;
					// It there are regions
					if(isset($item->regions)) {
						// loop through each region
						foreach((array)$item->regions as $region) {
							// If there are multiple regions
							if(is_array($region)) {
								foreach($region as $reg) {
									// Add the region code and name
									$arrItem[$code]["regions"][] = array("code" => (string)$reg->code, "name" => (string)$reg->name);
									// Check for sub regions
									if(isset($reg->subregions)) {
										foreach((array)$reg->subregions as $subreg) {
											if(is_array($subreg)) {
												foreach($subreg as $sreg) {
													// Add the sub region code and name
													$arrItem[$code]["regions"][] = array("code" => (string)$sreg->code, "name" => (string)$sreg->name);
												}
											} else {
												// Add the sub region code and name
												$arrItem[$code]["regions"][] = array("code" => (string)$subreg->code, "name" => (string)$subreg->name);
											}
										}
									}
								}
							} else { // if there is only one region
								// Add the region code and name
								$arrItem[$code]["regions"][] = array("code" => (string)$region->code, "name" => (string)$region->name);
							}
						}
					}
				}
			}

			return $arrItem;
		}

		return null;
	}

	/**
	 * The function returns the accommodations prices using the SOAP Prices method for a specific week
	 *
	 * @param $accCodes;	Array containing all the accommodations and their weekly availability
	 * @param $checkIn; 	The start of the week
	 * @param null $checkOut; The end of the week
	 * @return array|null
	 */
	private function _getPrices($accCodes, $checkIn, $checkOut = NULL) {

		if(!$checkOut) {
			// By default the checkout date is after 7 days
			$checkOut = date("Y-m-d", strtotime("+ 7 days", $checkIn));
		}

		$checkIn = date("Y-m-d", $checkIn);

		/********************************
		 * IHomeServicePrices
		 */
		$ihomeServicePrices = new IHomeServicePrices($this->wsdl);

		if(count($accCodes) > 0) {
			foreach ($accCodes as $accCode) {
				$stays[] = array(
					'AccommodationCode' => $accCode,
					'CheckIn'			=> $checkIn,
					'CheckOut' 			=> $checkOut
				);
			}
		}

		if(count($stays) == 0) return null;

		$params = array(
			'Stays' => $stays,
			'CurrencyCode' => 'EUR',
			'SalesOfficeCode' => '4040',
			'LanguageCode' => $this->languageCode,
		);

		// call for IHomeServicePrices::Prices()
		if($ihomeServicePrices->Prices(new IHomeStructPrices($params))){

			$result = current($ihomeServicePrices->getResult());
			$itemPricesResult = $result->getPricesResult();

			// Check for errors
			if($itemPricesResult->getOk() == false) {

				if(count($errors = $itemPricesResult->getErrors()) > 0) {
					foreach ($errors as $error) {
						return $error->getDescription();
					}
				}

			} else {
				$arrPrices = array();

				$arrPricesResultItems = $itemPricesResult->getPrices()->getPricesPriceItem();

				if(is_array($arrPricesResultItems) && count($arrPricesResultItems) > 0) {
					foreach ($arrPricesResultItems as $item){
	                        if($item->getPrice2() > 0){
	                            $arrPrices[$item->getAccommodationCode()] = $item->getPrice2();
	                            $this->_DISCOUNT[$item->getAccommodationCode()][strtotime($checkIn)] = $this->calculateDiscount($item->getPrice1(), $item->getPrice2());
	                        }else{
	                            $arrPrices[$item->getAccommodationCode()] = $item->getPrice1();
	                        }
                    	}
				} else {
	                    if($arrPricesResultItems->getPrice2() > 0){
	                        $arrPrices[$arrPricesResultItems->getAccommodationCode()] =  $arrPricesResultItems->getPrice2();
	                        $this->_DISCOUNT[$arrPricesResultItems->getAccommodationCode()][strtotime($checkIn)] = $this->calculateDiscount($arrPricesResultItems->getPrice1(), $arrPricesResultItems->getPrice2());
	                    }else{
	                        $arrPrices[$arrPricesResultItems->getAccommodationCode()] =  $arrPricesResultItems->getPrice1();
	                    }
                	}

				return $arrPrices;
			}

		}
		else {
			return $ihomeServicePrices->getLastError();
		}
	    }
    
	    /**
	     * Calculate the discount.
	     * @param type $finalPrice
	     * @param type $initialPrice
	     * @return $discount|null
	     */
	    private function calculateDiscount($finalPrice, $initialPrice){
	        if($initialPrice > 0){
	            $discount = 100*($initialPrice - $finalPrice)/$initialPrice;
	            return round($discount);
	        }else {
	            return null;
	        }
	    }
    
	    /**
	     * Get all discounts for all accommodations
	     * @return Array
	     */
	    public function getDiscounts() {
	        return $this->_DISCOUNT;
	    }

	/**
	 * The function returns the weekly prices for each accommodation
	 */
	public function processPrices($av) {

		$list = $av;

		// Create a list with the accommodations that are available per week
		foreach($list as $acc_code => $weeks) {
			if(is_array($weeks)) {
				foreach($weeks as $week => $ok) {
					$tmp[$week][] = $acc_code;
				}
			}
		}

		// For each week call the Prices method by sending an array with all the accommodations that are available on that week
		if(is_array($tmp)) {
			foreach($tmp as $week => $accommodations) {
				// Call the Prices SOAP method
				$formated[$week] = $this->_getPrices($accommodations, $week);
			}
		}

		if(is_array($formated)) {
			// Return the weekly prices for each accommodation
			foreach($formated as $week => $accommodations) {
				if(is_array($accommodations)) {
					foreach($accommodations as $code => $price) {
						$final[$code][$week] = $price;
						ksort($final[$code]);
					}
				}
			}
		}
		return $final;
	}

	public function formatServices($services) {

		$included_services = array();
		$exclusive_services = array();
		$extra_services = array();

		while(list($key,$service)=each($services)) {
			if(!($service instanceof SoapFault)) {
				switch($service->getType()) {
					case "Y2":
					case "N2":
					case "N5":
						$included_services_text = iconv("UTF-8", "CP1252", $service->getDescription());

						$included_services[$service->getCode()] = $included_services_text;

						#$txt = $service->getText();
						#if(!empty($txt)) $included_services .= " " . iconv("UTF-8", "CP1252", rtrim($service->getText(), ".")) . ". ";
						break;

					case "Y1":
					case "Y4":
					case "N1":
						$amount = $service->getAmount();
						$currency = $service->getCurrency();

						$exclusive_services_text = iconv("UTF-8", "CP1252", $service->getDescription());
						if($amount > 0) $exclusive_services_text .= " (". $currency . " " .$amount .")";

						$exclusive_services[$service->getCode()] = $exclusive_services_text;

						#$txt = $service->getText();
						#if(!empty($txt)) $exclusive_services .= " " . iconv("UTF-8", "CP1252", rtrim($txt, ".")) . ". ";
						break;

					default:
						#Y5, Y6, N4 codes
						$amount = $service->getAmount();
						$currency = $service->getCurrency();


						$extra_services_text = iconv("UTF-8", "CP1252", $service->getDescription());
						if($amount > 0) $extra_services_text .= " (". $currency . " " .$amount .")";

						$extra_services[$service->getCode()] = $extra_services_text;

						#$txt = $service->getText();
						#if(!empty($txt)) $extra_services_text " " . iconv("UTF-8", "CP1252", rtrim($txt, ".")) . ". ";
						break;
				}
			} else {
				return $service->getMessage();
			}
		}

		$additional_services = array_merge($exclusive_services, $extra_services);

		$txt_included = '';
		$txt_additional = '';
		if(count($included_services) > 0) $txt_included = implode(". ", $included_services) . ".";
		if(count($additional_services) > 0) $txt_additional = implode(". ", $additional_services) . ".";

		return array(
			"included_services" 	=> $txt_included,
			"additional_services" 	=> $txt_additional
		);
	}

	/**
	 * Function gets the first available booking date for an accommodation, starting from a specific date
	 *
	 * @param string $accCode
	 * @param date $checkIn
	 *
	 * @return mixed
	 */
	public function getNearestDate($accCode, $checkIn) {

		$ihomeServiceNearest = new IHomeServiceNearest($this->wsdl);
		// Call for IHomeServiceNearest::NearestBookingDate()
		$params = array(
			"AccomodationCode" => $accCode,
			"CheckIn" => $checkIn,
			"Duration" => 7
		);
		$return = null;

		if($ihomeServiceNearest->NearestBookingDate(new IHomeStructNearestBookingDate($params))) {
			$result = $ihomeServiceNearest->getResult();
			$data = current($result->getNearestBookingDateResult());
			if($data->getOk() == 1) {
				/*
				State Availability of property
					- Y = available
					- N = occupied
					- Q = on request
				*/
				if($data->getState() == "Y" || $data->getState() == "Q") {
					$min_stay = $data->getMinimumStay();
					if($min_stay != "0") {
						$stay = ord($min_stay) - 64;
						$timestamp = strtotime($data->getCheckIn());
						$checkOut = date("Y-m-d", strtotime("+".$stay." days", $timestamp));
						$return = array($data->getCheckIn(), $checkOut);
					}
				}
			}
			return $return;
		} else {
			return $ihomeServiceNearest->getLastError();
		}
	}

} #end class


// Class call examples
/*
#$interHome = new InterHome();
#$accCode = "AT6290.530.2"; //at6370.230.1 fr7351.340.6
#$key = 23;
#print_r($interHome->getPriceList($accCode = "ch6612.200.3"));
#$availability = $interHome->getAvailability($accCode, $start_date = date("Y-m-d"), $end_date = "2014-05-03");
#if($availability) {
#	var_dump($availability);
#	// Get the availability
#	if(isset($xml_beschikbaar[$key][$accCode])) {
#		$xml_beschikbaar[$key][$accCode] = $xml_beschikbaar[$key][$accCode] + $availability;
#	} else {
#		$xml_beschikbaar[$key][$accCode] = $availability;
#	}
#} else {
#	$xml_beschikbaar[$key][$accCode] = array();
#}
#$xml_brutoprijs[$key] = $interHome->processPrices($xml_beschikbaar);
*/
//$interHome = new InterHome();
#print_r($interHome->getAccommodation($accCode = "at6290.530.2"));
//print_r($interHome->getAdditionalServices($accCode = "at6290.530.2", "2013-12-07", "2014-06-26"));
#print_r($interHome->getAccommodations($contryCode="AT", $regionCode="40", $placeCode = "6600"));
#print_r($interHome->getStatus());
#print_r($interHome->getCustomerFeedback());
#print_r($interHome->getCountriesRegions());
#print_r($interHome->getWeekPrice(array("AT6574.220.1" => true, "AT6365.700.1" => true, "AT6290.530.2" => true, "AT6450.510.1" => true, "AT6574.170.1" => true)));
#print_r($interHome->getPrices("CH7050.250.4"));
#print_r($interHome->processPrices(array()));