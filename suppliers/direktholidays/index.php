<?php
if(!defined("DS")) define("DS", DIRECTORY_SEPARATOR);
if(!defined('SITE_ROOT')) define('SITE_ROOT', dirname(dirname(dirname(__FILE__))));

class DirektHolidays {

	private $_url = "http://www.direktholidays.at/";
	private $_region = "Zillertal"; // We assume that all the accommodations are in the Zillertal region
	private $_xpath = null;
	private $_session = array();
	private $_links = array();

	public function __construct($startPageURL = NULL) {
		// default start page is: http://www.direktholidays.at/index.php?id=3&L=2
		if($startPageURL) {
			$this->processDirektHolidays($startPageURL);
		}
	}

	/**
	 * Function goes parses all the accommodations pages and retrieves their URLs
	 *
	 * @param string $url;	The URL of the first page
	 */
	public function processDirektHolidays($url) {
		$ajaxLinks = $this->getAjaxAccommodations();
		if(!$ajaxLinks) {
			// Get all accommodations links from page
			$this->getNextLinks($url);

			// get all elements with a particular id and then loop through and print the href attribute
			$elements = $this->execute($url, "//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-right']/div[@id='c312']/div[@id='atonfewo_pi3-listview']/div[@id='atonfewo_pi3-listview-pagebrowser'][1]/div[@class='browseBoxWrap']/div[@class='browseLinksWrap']/span[@class='inactiveLinkWrap']/a/@href");

			foreach ($elements as $e) {

				parse_str($e->nodeValue, $output);

				if(!isset($output["tx_atonfewo_pi3"])) {
					continue;
				}

				if(isset($output["cHash"])) {
					unset($output["cHash"]);
				}

				if(isset($output["no_cache"])) {
					unset($output["no_cache"]);
				}

				$link = http_build_query($output);
				$link = urldecode($link);
				$link = str_replace(array("[", "]", "index_php"), array("%5B", "%5D", "index.php"), $link);

				if( !isset($this->_session["direktholidays"][$link]) ) {
					$this->_session["direktholidays"][$link] = 1;
					$this->processDirektHolidays($this->_url . $link);
				}
			}
		} else {
			$this->_links = $ajaxLinks;
		}
	}

	/**
	 * Gets all the accommodations URLs from a page
	 *
	 * @param $url
	 */
	private function getNextLinks($url) {

		// get all links to the accommodations
		$output = $this->execute($url, "//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-right']/div[@id='c312']/div[@id='atonfewo_pi3-listview']/div[@class='atonfewo_pi3-listview-item']/div[@class='atonfewo_pi3-listview-item-image']/a/@href");

		foreach ($output as $node) {

			$acc_url = $this->_url . $node->nodeValue;

			$query_str = parse_url($acc_url, PHP_URL_QUERY);
			parse_str($query_str, $query_params);

			// Accommodation code
			$code = $query_params["tx_atonfewo_pi1"]["ve"];

			$this->_links[$code] = $acc_url;
		}
	}

	/* gets the data from a URL */
	function getAjaxAccommodations() {

		$links = false;

		$url = $this->_url . "?eID=ajaxReq";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "tx_atonfewo_sv2[type]=0&tx_atonfewo_sv2[searchbox-destination][1]=true&tx_atonfewo_sv2[searchbox-destination][2]=true&tx_atonfewo_sv2[searchbox-destination][3]=true&tx_atonfewo_sv2[searchbox-destination][4]=true&tx_atonfewo_sv2[searchbox-destination][5]=true&tx_atonfewo_sv2[searchbox-destination][6]=true");
		$data = curl_exec($ch);
		curl_close($ch);
		if($data) {
			foreach(json_decode($data) as $code) {
				$links[$code] = $this->getAccommodationURL($code);
			}
		}

		return $links;
	}

	/**
	 * Create full URL to the accommodation page
	 *
	 * @param string $code
	 * @return string
	 */
	public function getAccommodationURL($code) {
		return $this->_url . "index.php?id=119&L=2&tx_atonfewo_pi1[ve]=" . $code;
	}

	/**
	 * Function loops through all the accommodations URLs and calls the function that retrieves their details
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getAccommodations($limit = 0) {
		// get the details of each accommodation
		$i=1;
		$urls = array();
		$links = $this->getLinks();

		foreach ($links as $key => $url) {

			$urls[$key] = $url;

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

		$name = $this->execute($url, "//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-left']/div[@id='c579']/div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-facts']/div[@id='atonfewo_pi1-detailview-facts-headline']/h1");

		// Accommodation name
		$acc["name"] = trim($name->item(0)->nodeValue);

		// Get current xPath
		$xPath = $this->getXPath();

		$short_description = $xPath->query("//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-left']/div[@id='c579']/div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-facts']/div[@id='atonfewo_pi1-detailview-facts-shortdescription']/p");

		// Accommodation short description
		$acc["short_description"] = trim($short_description->item(0)->nodeValue);

		$details = $xPath->query("//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-left']/div[@id='c579']/div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-facts']/div[@class='atonfewo_pi1-detailview-facts-table']/table/tr");

		//when we process the nodes below, we will be cycling through
		$result = array();

		//perform our xpath sub-queries to get the data
		foreach ($details as $node)
		{
			//we are now using each 'node' as the limit for the new xpath query to search within
			//Make the queries relative... start them with a dot (e.g. ".//…").
			$details = $xPath->query(".//td[@class='td-key']", $node);
			$values = $xPath->query(".//td[@class='td-value']", $node);

			$i = 0;
			foreach ($details as $detail){
				$result[trim($detail->nodeValue)] = trim($values->item($i)->nodeValue);
				$i++;
			}
		}

		// Accommodation details
		$acc["details"] = $result;

		$images = $xPath->query("//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-left']/div[@id='c579']/div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-image']/a/@href");

		// Accommodation images
		$acc["images"] = $this->getNodesValues($images, true);

		$description = $xPath->query("//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-left']/div[@id='c579']/div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-tabs']/div[@id='atonfewo_pi1-detailview-tabs-1']/p");

		// Accommodation description
		$acc["description"] = trim($description->item(0)->nodeValue);

		$place_description = $xPath->query("//div[@id='container']/div[@id='container-listview']/div[@id='container-listview-content']/div[@id='container-listview-content-left']/div[@id='c579']/div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-tabs']/div[@id='atonfewo_pi1-detailview-tabs-3']/p[1]");

		// Place description
		$acc["place_description"] = trim($place_description->item(0)->nodeValue);

		return $this->formatValues($acc);
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
		$new["skigebied_id"] = $this->_region;
		$new["plaats_id"] = $value["details"]["Wijk"];
		$new["optimaalaantalpersonen"] = $value["details"]["Persoon"];
		$new["maxaantalpersonen"] = $value["details"]["Persoon"];
		$new["slaapkamers"] = $value["details"]["Slaapkamer"];
		$new["badkamers"] = $value["details"]["Badkamer"];
		$new["accindeling"] = iconv("UTF-8", "CP1252", $value["description"]);
		$new["accomschrijving"] =  iconv("UTF-8", "CP1252", $value["short_description"]) . " " . iconv("UTF-8", "CP1252", $value["place_description"]);
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
			$return[] = ($prependURL) ? $this->_url . $node->nodeValue : $node->nodeValue;
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

		$html= file_get_contents($url);

		// get all links to the accommodations
		$result = $this->load($html, $query);

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

		$search = array("Â²", "Ã«", "Ã¶", "Ã©", "Ã¤", "Ã¼", "Ã¯", "Ã¨", "éÂ");
		$replace = array("2", "ë", "ö", "é", "ä", "ü", "ï", "è", "és");

		$text = str_replace($search, $replace, $subject);

		return $text;
	}

	/**
	 * Extract prices from the accommodation HTML page
	 *
	 * @param string $html
	 * @param date $start_date; 	Season start date
	 * @param date $end_date; 	Season end date
	 * @return array
	 */
	public function getPrices($html, $start_date, $end_date) {

		$result = array();

		$start_date = strtotime($start_date);
		$end_date = strtotime($end_date);

		$query = "//div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-bookingarea']/div[@id='atonfewo_pi1-detailview-bookingarea-alternativedate']/table/tr";
		$details = $this->load($html, $query);

		$xPath = $this->getXPath();

		//perform our xpath sub-queries to get the data
		foreach ($details as $node)
		{
			//we are now using each 'node' as the limit for the new xpath query to search within
			//Make the queries relative... start them with a dot (e.g. ".//…").
			$date = $xPath->query(".//td[@class='date']", $node);
			$price = $xPath->query(".//td[@class='price']", $node);

			$i = 0;
			foreach ($date as $detail){

				$price = trim(utf8_decode($price->item($i)->nodeValue));
				$price = preg_replace("/([^0-9])/i", "", $price);
				$price = round($price/100, 2);

				$date = trim($detail->nodeValue);
				$date = preg_replace("/([^0-9\.])/i", " ", $date);
				$date = preg_replace('/\s+/', ' ',$date);

				$date = explode(" ", $date);
				$week = strtotime($date[0]);

				if($start_date <= $week && $week < $end_date) {
					$result[$week] = $price;
				}
				$i++;
			}
		}

		// Accommodation details
		return $result;
	}

	/**
	 * Performs a CURL request to get the accommodation page including the prices as well
	 * @param $accURL
	 * @return mixed
	 */
	public function curlPricesRequest($accURL) {

		$url = $this->_url . "?eID=ajaxReq";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, SITE_ROOT . DS . "tmp" . DS . "direktholidays_cookies.txt");
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "tx_atonfewo_sv2[type]=0&tx_atonfewo_sv2[searchbox-destination][1]=true&tx_atonfewo_sv2[searchbox-destination][2]=true&tx_atonfewo_sv2[searchbox-destination][3]=true&tx_atonfewo_sv2[searchbox-destination][4]=true&tx_atonfewo_sv2[searchbox-destination][5]=true&tx_atonfewo_sv2[searchbox-destination][6]=true&tx_atonfewo_sv2[searchbox-datechooser-arrival]=&tx_atonfewo_sv2[searchbox-select-duration]=7&tx_atonfewo_sv2[searchbox-select-adults]=2&tx_atonfewo_sv2[searchbox-select-children]=0&tx_atonfewo_sv2[searchbox-checkbox-pet]=false&tx_atonfewo_sv2[searchbox-select-objecttype]=0&tx_atonfewo_sv2[searchbox-checkbox-smoke]=false");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);

		curl_setopt($ch, CURLOPT_COOKIEFILE, SITE_ROOT . DS . "tmp" . DS . "direktholidays_cookies.txt");
		curl_setopt($ch, CURLOPT_POST, false);

		curl_setopt($ch, CURLOPT_URL, $accURL);
		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
	}

	/**
	 * Extract prices from the accommodation HTML page
	 *
	 * @param string $html
	 * @param date $start_date
	 * @param date $end_date
	 * @return array
	 */
	public function getAvailability($html, $start_date, $end_date) {

		$result = array();

		$start_date = strtotime($start_date);
		$end_date = strtotime($end_date);

		$query = "//div[@id='atonfewo_pi1-detailview']/div[@id='atonfewo_pi1-detailview-tabs']/div[@id='atonfewo_pi1-detailview-tabs-2']/div[@class='atonfewo_pi1-detailview-tabs-2-calendar']/table";
		$details = $this->load($html, $query);

		$nl_months = array(1 => "januari", 2 => "februari", 3 => "maart", 4 => "april", 5 => "mei", 6 => "juni", 7 => "juli", 8 => "augustus", 9 => "september", 10 => "oktober", 11 => "november", 12 => "december");

		$xPath = $this->getXPath();

		//perform our xpath sub-queries to each table
		foreach ($details as $table)
		{
			$th = $xPath->query(".//tr/th/text()", $table);
			$tmp = $this->getNodesValues($th);
			$tmp = explode(" ", $tmp);

			$month = array_search($tmp[0], $nl_months);
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
								$available = trim($detail->getAttribute('class'));
								$available = explode(" ", $available);
								if(array_search("dayinpast", $available) === false && array_search("reservation", $available) === false && array_search("reservation-start", $available) === false) {
									$result[$week] = 1;
								} elseif(array_search("reservation", $available) !== false) {
									$last_week = strtotime("-7 days", $week);
									if(isset($result[$last_week])) unset($result[$last_week]);
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

	public function setLinks($code) {
		$this->_links[$code] = $this->getAccommodationURL($code);
	}

	private function getLinks() {
		return $this->_links;
	}
}

?>