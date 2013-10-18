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
	private $xmlCustomerFeedback = 'customerfeedback.xml.zip'; // The zip file from FTP	

	function __construct() {		
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
	public function getPrices($accCode = null) {

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
	 * @param string $checkOut Check out date
	 * @return array|null
	 */
	public function getAvailability($accCode = null, $checkOut = "") {

		// Check if the accommodation code is set
		if(empty($accCode) || empty($this->wsdl)) return null;

		/**************************************
		 * IHomeServiceAvailability
		 */
		$ihomeServiceAvailability = new IHomeServiceAvailability($this->wsdl);
		$params = array(
			'AccommodationCode' => $accCode,
			'CheckIn' 			=> date("Y-m-d"),
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
						return $error->getDescription();
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

					if(date("w",$day)==5 && (substr($change,$i-6,1)=="C" || substr($change,$i-6,1)=="I") && substr($min_stay,$i-6,1)!="0") {
						# on Friday we check to see if the previous week was free, and 6 days ago (Saturday) was a changeover was possible
						if($temp_available[$week]==7) {
							if(!isset($soap_available[$week])) $soap_available[$week] = 0;

							$soap_available[$week]+=1;
						}
					}
				}

				return $soap_available;				
			}	
		} else {
			return $ihomeServiceAvailability->getLastError();
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
	 * @param null $accCode The accommodation code from Interhome
	 * @return array|null
	 */
	public function getAdditionalServices($accCode = null) {

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
			'Babies' 			=> ''
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
		}
		else {
			return $ihomeServiceAdditional->getLastError();
		}
	}

	/**
	 * The function is used to search for new accommodations from a specific country, region or place
	 * The returned accommodations can be imported (or not) into the Chalet database
	 *
	 * @param string $countryCode The two letters ISO country code
	 * @param integer $regionCode The id of the region provided by Interhome; the parameter can be omitted
	 * @param integer $placeCode The id of the place provided by Interhome; the parameter can be omitted
	 * @return array\
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
						$count=1;

						for ($i=1; $i<=$pagesCount; $i++) { 
							
							$params["Page"] = $i;

							if($ihomeServiceSearch->Search(new IHomeStructSearch($params))) {
								$result = current($ihomeServiceSearch->getResult());
								$itemSearchResult = $result->getSearchResult();
								$arrSearchResultItems = $itemSearchResult->getItems()->getSearchResultItem();
								
								foreach ($arrSearchResultItems as $key => $value) {									
									$arrSearch[$count] = $value->getAccommodationCode();
									$count++;
								}

							}
							else {
								return $ihomeServiceSearch->getLastError();
							}

						}
					}
				} else {

					$arrSearchResultItems = $itemSearchResult->getItems()->getSearchResultItem();
					$count=1;

					if($RowsCount == 1) {
						$arrSearch[$count] = $arrSearchResultItems;
					} else {
						foreach ($arrSearchResultItems as $value) {
							$arrSearch[$count] = $value;
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
			$xml=simplexml_load_file($output);	
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

} #end class


// Class call examples
#$interHome = new InterHome();

#print_r($interHome->getPrices($accCode = "de2981.100.1"));
#print_r($interHome->getAvailability($accCode = "de2981.100.1"));
#print_r($interHome->getAccommodation($accCode = "at6574.410.1"));
#print_r($interHome->getAdditionalServices($accCode = "at6574.410.1"));
#print_r($interHome->getAccommodations($contryCode="AT", $regionCode="40", $placeCode = "6600"));
#print_r($interHome->getStatus());
#print_r($interHome->getCustomerFeedback());
