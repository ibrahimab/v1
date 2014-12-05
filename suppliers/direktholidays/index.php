<?php
if(!defined("DS")) define("DS", DIRECTORY_SEPARATOR);
if(!defined('SITE_ROOT')) define('SITE_ROOT', dirname(dirname(dirname(__FILE__))));

class DirektHolidays {

	private $_xpath = null;
	private $_session = array();
	private $_links = array();
	private $_codes = array();
	private $_short_descriptions = array();
	private $_de_months = array(1 => "Januar", 
								2 => "Februar",
								3 => "März",
								4 => "April",
								5 => "Mai",
								6 => "Juni",
								7 => "Juli",
								8 => "August",
								9 => "September",
								10 => "Oktober",
								11 => "November",
								12 => "Dezember");
	
	CONST ROOT_PAGE = "https://www.direktholidays.at/WebCenter/listView/sort:VOStamm.objektname/direction:asc/page:";
	CONST CHECK_AVAILABILITY_URL = "https://www.direktholidays.at/Ajax/checkArrivalDepartureOnObject";
	CONST DETAIL_VIEW_URL = "https://www.direktholidays.at/WebCenter/detailView/";
	CONST ZILLERTAL_REGION = "Zillertal"; // We assume that all the accommodations are in the Zillertal region
	CONST ROOT_URL = "https://www.direktholidays.at/";
	CONST AJAX_FILTER_URL = "https://www.direktholidays.at/Ajax/objFilter";
	

	public function __construct($process = false) {	
		$available = true;
		if($process)
			$available = $this->processDirektHolidays();
		if (!$available) throw new Exception();
	}
	
	/**
	 * Function goes parses all the accommodations pages and retrieves their URLs
	 *
	 * @param string $url;	The URL of the first page
	 */
	public function processDirektHolidays() {
		$first_page_url = self::ROOT_PAGE . "1";
		$output = $this->execute($first_page_url);
		if ($output) {
			$xPath = $this->getXPath();
			$page_node = $xPath->query("//div[@class='container']/div[@class='container pagecontainer offset-0']/div[@class='col-md-3 col-md-pull-9 filters offset-0']/div[@class='filtertip']/div[@class='padding20']/p[@class='size13']/span[@class='size28 bold counthotel']");

			$pages = floor((int)$page_node->item(0)->nodeValue / 20);

			$links = array();
			for($i = 0; $i <= $pages; $i++) {
				$page_links = $this->getAjaxAccommodations($i+1);
				foreach($page_links as $item) {
					array_push($links, $item);
				}
			}
			$this->_links = $links;			
		} else {
			return false;
		}
		return true;
	}

	/* gets the data from a URL */
	function getAjaxAccommodations($page) {

		$url = self::ROOT_PAGE . $page;

		$accs = $this->execute($url, "//div[@class='container']/div[@class='container pagecontainer offset-0']/div[@class='rightcontent col-md-9 col-md-push-3 offset-0']/div[@class='itemscontainer offset-1 paddingtp20']/div[@class='offset-2']");
		$xPath = $this->getXPath();
		$link_nodes = $xPath->query(".//div[@class='col-md-4 offset-0']/div[@class='listitem']/a");
		$short_description_nodes = $xPath->query(".//div[@class='col-md-8 offset-0']/div[@class='col-sm-9 labelleft2']/p");

		$links = array();
		$short_descriptions = array();

		foreach ($link_nodes as $node) {
			$node_url = explode("/",$node->getAttribute('href'));
			$this->_codes[$page][] = $node_url[3];
			$links[$node_url[3]] = $this->getAccommodationURL($node_url[3]);
		}

		foreach ($short_description_nodes as $key=>$node){
			$this->_short_descriptions[$this->_codes[$page][$key]] =$node->nodeValue;
		}

		return $links;
	}

	/**
	 * Create full URL to the accommodation page
	 *
	 * @param string $code
	 * @return string
	 */
	public function getAccommodationURL($code){
		return self::DETAIL_VIEW_URL . $code;
	}

	/**
	 * Get accomodation code based on available url
	 *
	 * @param string $url
	 * @return string
	 */
	public function getAccommodationCode($url){
		$code = str_replace(self::DETAIL_VIEW_URL, '', $url);
		return $code;
	}

	/**
	 * Function loops through all the accommodations URLs and calls the function that retrieves their details
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getAccommodations($limit = 0){
		// get the details of each accommodation
		$i=1;
		$urls = array();
		$links = $this->getLinks();

		foreach ($links as $key => $url) {

			$urls[$this->getAccommodationCode($url)] = $url;

			if($limit != 0 && $i >= $limit) {
				break;
			}
			$i++;
		}

		return $urls;
	}

	/**
	 * Get all accommodation details by parsing its URL
	 *
	 * @param string $url
	 * @return array
	 */
	public function getAccommodation($url) {

		$acc = array();

		$details_node = $this->execute($url, "//div[@class='container']/div[@class='container pagecontainer offset-0']/div[@class='col-md-5 detailsright offset-0']");
		$xPath = $this->getXPath();
		$name_node = $xPath->query(".//div[@class='padding20']/h3");
		$person_node1 = $xPath->query(".//div[@class='col-md-6 bordertype1 padding5-20']/span[@class='opensans size30 bold grey2']");
		$person_node2 = $xPath->query(".//div[@class='col-md-6 bordertype2 padding5-20']/span[@class='opensans size30 bold grey2']");
		$place_node = $xPath->query(".//div[@class='padding20']/span[@class='grey']");
		$place = explode(',',$place_node->item(0)->nodeValue);

		if($details_node) {
			// Accommodation name
			$acc["name"] = trim($name_node->item(0)->nodeValue);

			$accommodation_code = $this->getAccommodationCode($url);
			// Get current xPath

			// Accommodation short description
			//$acc["short_description"] = $this->_short_descriptions[$accommodation_code];
			$acc["short_description"] = '';
			//retriving the accomodation details
			$result = array();
			$result['Persoon'] = $person_node1->item(0)->nodeValue;
			$result['Badkamer'] = $person_node1->item(1)->nodeValue;
			$result['Objekttype'] = $person_node2->item(0)->nodeValue;
			$result['Slaapkamer'] = $person_node2->item(1)->nodeValue;
			$result['Wijk'] = $place[0];

			// Accommodation details
			$acc["details"] = $result;

			// Accommodation images
			$images = $xPath->query("//div[@class='container']/div[@class='container pagecontainer offset-0']/div[@class='col-md-7 details-slider']/div[@id='c-carousel']/div[@id='wrapper']/div[@id='inner']/div[@id='pager-wrapper']/div[@id='pager']/img/@src");
			$acc["images"] = $this->getNodesValues($images, true);

			// Accommodation description			
			$description_node = $xPath->query("//div[@class='container']/div[@class='mt25 offset-0']/div[@class='col-md-8 pagecontainer2 offset-0']/div[@class='paddingtp20']/div[@class='tab-pane fade active in']/p");
			$acc["description"] = trim($description_node->item(0)->nodeValue);

			return $this->formatValues($acc);
		} else {
			return false;
		}
	}

	/**
	 * Format the accommodation details as required by the cms_xmlnewimport.php
	 *
	 * @param array $value
	 * @return array
	 */
	private function formatValues($value) {

		$new = array();

		$value["name"] = iconv("UTF-8", "CP1252", $value["name"]);
		$first_number = $this->numberOffset($value["name"]);

		$new["accnaam"] = trim($this->formatSpecialCharacters(substr($value["name"],0,$first_number)));
		$new["typenaam"] = $this->formatSpecialCharacters($value["name"]);
		$new["skigebied_id"] = self::ZILLERTAL_REGION;
		$new["plaats_id"] = $value["details"]["Wijk"];
		$new["optimaalaantalpersonen"] = $value["details"]["Persoon"];
		$new["maxaantalpersonen"] = $value["details"]["Persoon"];
		$new["slaapkamers"] = $value["details"]["Slaapkamer"];
		$new["badkamers"] = $value["details"]["Badkamer"];
		$new["accindeling"] = iconv("UTF-8", "CP1252", $value["description"]);
		$new["accomschrijving"] =  iconv("UTF-8", "CP1252", $value["short_description"]);
		$new["accafbeelding"] = $value["images"];
		$new["typeafbeelding"] = $value["images"];
		$new["soortaccommodatie"] = strtolower($value["details"]["Objekttype"]);

		$new["accomschrijving"] = $this->formatSpecialCharacters($new["accomschrijving"]);
		$new["accindeling"] = $this->formatSpecialCharacters($new["accindeling"]);

		if(preg_match("/([0-9]+\.?[0-9]+)\s?m2\.?(.*)$/", $new["accindeling"].$new["accomschrijving"], $matches)) {
			$d["oppervlakte"]=trim($matches[1]);
		}

		return $new;
	}

	/**
	 * Return the nodes values from the result returned by xPath
	 *
	 * @param $domnodelist
	 * @param bool $prependURL
	 * @return array|bool
	 */
	private function getNodesValues($domnodelist, $prependURL = false) {
		$return = array();

		foreach ($domnodelist as $node) {
			$return[] = ($prependURL) ? self::ROOT_URL . $node->nodeValue : $node->nodeValue;
		}

		if(count($return) == 1) {
			$return = $return[0];
		} elseif(count($return) == 0) {
			$return = false;
		}

		return $return;
	}

	/**
	 * Run the dom parsing action
	 *
	 * @param string $url
	 * @param string $query used by xPath
	 * @return DOMNodeList
	 */
	private function execute($url, $query = NULL) {

		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$html = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // find HTTP status
		curl_close($ch);

		if($html && $status == 200) {
			// get all links to the accommodations
			$result = $this->load($html, $query);
		} else {
			$result = false;
		}
		return $result;
	}

	/**
	 * Load the html file into a DOMDocument object in order to execute XPath queries
	 *
	 * @param string $html
	 * @param null|string $query
	 * @return DOMNodeList|DOMXPath
	 */
	private function load($html, $query = NULL) {

		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		$xPath = new DOMXPath($dom);

		// Set the current xPath;
		$this->setXPath($xPath);

		$result = $xPath;

		if($query) {
		// get all links to the accommodations
			$result = $xPath->query($query);
		}

		return $result;
	}

	/**
	 * Set current xPath instance
	 *
	 * @param $xpath
	 */
	private function setXPath($xpath) {
		$this->_xpath = $xpath;
	}

	/**
	 * Return current xPath instance
	 *
	 * @return xPath instance
	 */
	private function getXPath() {
		return $this->_xpath;
	}

	/**
	 * Find the position of first number in string
	 *
	 * @param string $text
	 * @return int
	 */
	private function numberOffset($text) {
		preg_match('/\d/', $text, $m, PREG_OFFSET_CAPTURE);
		if (sizeof($m))
			return $m[0][1]; // 24 in your example

		// return anything you need for the case when there's no numbers in the string
		return strlen($text);
	}

	/**
	 * Replace the broken utf-8 characters with the correct cp1252 ones
	 *
	 * @param string $subject
	 * @return string
	 */
	public function formatSpecialCharacters($subject) {
		$search = array("Â²", "Ã«", "Ã¶", "Ã©", "Ã¤", "Ã¼", "Ã¯", "Ã¨", "éÂ", "?¤");
		$replace = array("2", "ë", "ö", "é", "ä", "ü", "ï", "è", "és");
		$text = str_replace($search, $replace, $subject);
		return $text;
	}
	
	/**
	 * Retrieves the HML based on the given URL
	 * @param type $accURL
	 * @return string $html
	 */
	public function getAccomodationHTML($accURL) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $accURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$html = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // find HTTP status
		curl_close($ch);
		return $html;
	}

	/**
	 * Extract prices from the accommodation HTML page
	 *
	 * @param string $html
	 * @param date $start_date
	 * @param date $end_date
	 * @return array
	 */
	public function getAvailability($html, $start_date, $end_date, $accCode) {
		$result = array();

		$start_date = strtotime($start_date);
		$end_date = strtotime($end_date);

		$query = "//div[@class='container']/div[@class='mt25 offset-0']/div[@class='col-md-8 pagecontainer2 offset-0']/div[@class='paddingtp20']/div[@class='tab-pane fade active in']/div[@id='collapse3']/div[@class='hpadding20']/div[@class='col-md-4 center']/div[@class='booking-schedule']/table";
		$details = $this->load($html, $query);

		$xPath = $this->getXPath();

		//perform our xpath sub-queries to each table
		foreach ($details as $table)
		{
			$th = $xPath->query(".//tr/th/text()", $table);
			$tmp = $this->getNodesValues($th);
			$tmp = explode(" ", $tmp);
			$month = array_search($this->formatSpecialCharacters($tmp[0]), $this->_de_months);
			$year = $tmp[1];

			// get sub-queries for each table row
			$node = $xPath->query(".//tr", $table);


			foreach($node as $tr) {
				// get each td in the table row
				$td = $xPath->query(".//td[6]", $tr);
				foreach ($td as $detail){
					$day = $detail->nodeValue;
					if($day != "Sa" && $day != "") {
						$week = strtotime($year."-".$month."-".$day);
						if($start_date <= $week && $week < $end_date) {
							if(date("w", $week) == 6) {
								$availability = $this->getAjaxAvailability($year."-".$month."-".$day, $accCode);
								if($availability) {
									$result['availability'][$week] = 1;
									$price = $this->getAvailablePrice($year."-".$month."-".$day, $accCode);
									$result['price'][$week] = str_replace('.', '', $price);
								}
							}
						} else {
							continue;
						}
					}
				}
			}
		}
		// Accommodation availability
		return $result;
	}

	public function getAjaxAvailability($start_date, $accCode) {

		$end_date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($start_date)) . " +1 week"));

		$ch = curl_init();
		$url = self::CHECK_AVAILABILITY_URL;
		$timeout = 5;
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "data[redirect]=detailView&data[anchor]=bookingSchedule&data[text]=" . $accCode ."&data[arrival]=".$start_date."&data[departure]=" . $end_date . "&data[adults]=0&data[children]=0&data[type]=0&data[props]=&data[regions]=");
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);

		$result = json_decode($data);
		return $result->ok;
	}

	/**
	 * Performs a CURL request to get the accommodation page including the prices as well
	 * @param $accURL
	 * @return mixed
	 */
	public function getAvailablePrice($start_date, $accCode) {

		$url = self::AJAX_FILTER_URL;
		$accURL = self::DETAIL_VIEW_URL.$accCode;

		$end_date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($start_date)) . " +1 week"));

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, SITE_ROOT . DS . "tmp" . DS . "direktholidays_cookies.txt");
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "data[redirect]=detailView&data[anchor]=bookingSchedule&data[text]=" . $accCode ."&data[arrival]=".$start_date."&data[departure]=" . $end_date . "&data[adults]=0&data[children]=0&data[type]=0&data[props]=&data[regions]=");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);

		curl_setopt($ch, CURLOPT_COOKIEFILE, SITE_ROOT . DS . "tmp" . DS . "direktholidays_cookies.txt");
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, $accURL);
		$html = curl_exec($ch);

		curl_close($ch);

		$query = "//div[@class='container']/div[@class='container pagecontainer offset-0']/div[@class='col-md-5 detailsright offset-0']/div[@class='hpadding20']/span[@class='size30 magenta bold']/b";
		$details = $this->load($html, $query);
		$price = explode(" ", $details->item(0)->nodeValue);

		return $price[1];
	}
	/**
	 * Set the related links based on the provided accommodation code.
	 * 
	 * @param type $code
	 */
	public function setLinks($code) {
		$this->_links[$code] = $this->getAccommodationURL($code);
	}
	
	/**
	 * Get the current available links
	 * 
	 * @return array
	 */
	private function getLinks() {
		return $this->_links;
	}
}

?>