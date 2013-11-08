<?php
if(isset($_SERVER["HTTP_HOST"])) {
	$unixdir="../";
} else {
    $unixdir=dirname(dirname(__FILE__))."/";
}
$cron=true;
include($unixdir."admin/vars.php");

if(file_exists($unixdir."suppliers/interhome/index.php")) {

	include_once($unixdir."suppliers/interhome/index.php");

	$interHome = new InterHome();

	if(!isset($argv[1])) die("Action not specified");

	// Helper function for escaping mysql values
	if(!function_exists("esc")) {
		function esc($val) {
			return mysql_real_escape_string($val);
		}
	}
	// Helper function to convert text from UTF-8 to ANSI to be saved in the database
	if(!function_exists("conv")) {
		function conv($val) {
			return iconv("UTF-8", "WINDOWS-1252", $val);
		}
	}

	// Update Interhome countries and regions database table
	if($argv[1] == "countries") {
		// Get all the countries and regions from Interhome XML service
		$countriesRegions = $interHome->getCountriesRegions();

		if(is_array($countriesRegions)) {
			$countries = $regions = 0;

			// Delete all current entries
			$db->query("TRUNCATE TABLE `interhome_countries_regions`");

			$db->query("SELECT naam FROM `land`");
			while($db->next_record()) {
				$land[$db->f("naam")] = true;
			}

			foreach($countriesRegions as $value) {
				if(!isset($land[conv($value["name"])])) continue;

				// Save the countries
				$sql  = "INSERT INTO `interhome_countries_regions`(`country_code`, `country`) ";
				$sql .= "VALUES ('" . esc($value["code"]) . "', '" . esc(conv($value["name"])) ."');";
				$db->query($sql);
				$countries++;

				// Check if a country has regions
				if(is_array($value["regions"])) {
					foreach($value["regions"] as $val) {
						// Save the regions
						$q  = "INSERT INTO `interhome_countries_regions`(`country_code`, `region_code`, `region`) ";
						$q .= "VALUES ('" . esc($value["code"]) . "', '" . esc($val["code"]) ."', '" . esc(conv($val["name"])) ."');";
						$db->query($q);
						$regions++;
					}
				}
			}

			echo "Imported " . $countries . " countries and " . $regions ." regions";
			exit;
		}
	} elseif($argv[1] == "checkforchanges") {
		// Check for changes of information from Interhome compared with the saved xml data

		$lev = 421; // Interhome supplier (leverancier) id

		# Te verbergen XML-codes
		$db->query("SELECT xmlcode FROM xml_verberg_accommodatie WHERE leverancier_id='".addslashes($lev)."';");
		while($db->next_record()) {
			$verberg_xmlcode[$db->f("xmlcode")]=true;
		}

		# Eerder opgeslagen XML-velden
		$db->query("SELECT content, veld, soort, xmlcode FROM xml_importvalues WHERE leverancier_id='".addslashes($lev)."';");
		while($db->next_record()) {
			$xml_importvalues[$db->f("soort")][$db->f("xmlcode")][$db->f("veld")]=$db->f("content");
		}

		// Get all imported accommodations
		function only_alphanum($text) {
			# Alles strippen behalve A-Z, a-z en 0-9 (om wijzigingen te kunnen bekijken)

			$return=$text;

			# Bepaalde woorden strippen
			$woorden_strippen=array("Beschrijving:","Catégorie :");
			while(list($key,$value)=each($woorden_strippen)) {
				$return=str_replace($value," ",$return);
			}

			$return=trim(strip_tags($return));
			$return=ereg_replace("[^a-zA-Z0-9]","",$return);
			return $return;
		}

		function checkforxmlchanges($xml_importvalues,$allevelden,$soort,$xmlcode) {
			global $lev;

			$db=new DB_sql;

			while(list($veld,$content)=each($allevelden)) {
				if($content and $xml_importvalues[$soort][$xmlcode][$veld] and only_alphanum($content)<>only_alphanum($xml_importvalues[$soort][$xmlcode][$veld])) {
					$changed[$xmlcode][$veld]=true;
				}
			}

			while(list($key,$value)=@each($changed)) {
				while(list($key2,$value2)=each($value)) {
					$db->query("UPDATE xml_importvalues SET wijziging_bekend=NOW() WHERE wijziging_bekend IS NULL AND leverancier_id='".addslashes($lev)."' AND veld='".addslashes($key2)."' AND soort='".addslashes($soort)."' AND xmlcode='".addslashes($key)."';");
				}
			}
		}

		// Get the distinct accommodations that were imported to be used to compare
		$sql = "SELECT DISTINCT(xmlcode) AS code FROM `xml_importvalues` WHERE soort=2 AND leverancier_id='".addslashes($lev)."'";
		$db->query($sql);
		$new = array();
		$property = array();
		while($db->next_record()) {
			if(!isset($verberg_xmlcode[$db->f("code")])) {
				$services[$db->f("code")] = $interHome->getAdditionalServices($db->f("code"));
				$property[$db->f("code")] = $interHome->getAccommodation($db->f("code"));
			}
		}
		$volgnummer = 0;

		while(list($key,$value)=each($property)) {
			if(is_object($value)) {
				$region = conv($value->getRegion());
				if($skigebied_xmlnamen[$region]) {
					$bekend_skigebied = $skigebied_xmlnamen[$region];
				} else {
					$onbekend_skigebied[$region]=true;
				}

				$place = conv($value->getPlace());
				if($plaats_xmlnamen[$place]) {
					$bekend_plaats = $plaats_xmlnamen[$place];
				} else {
					$onbekend_plaats[$place]=true;
				}

				$type_id = $vars["interhome_soortaccommodatie"][$value->getType()];

				$volgnummer++;

				$uniekeid = $value->getAccommodationCode();

				$tmp_uniekeid = explode(".", $uniekeid, -1);
				$tmp_uniekeid = implode(".", $tmp_uniekeid);
				$new[$uniekeid]["accid"] = $tmp_uniekeid;

				$new[$uniekeid]["volgnummer"] = $volgnummer;
				$new[$uniekeid]["accnaam"] = conv($value->getHouseName());
				$new[$uniekeid]["typenaam"] = conv($value->getHouseName());
				$new[$uniekeid]["skigebied_id"] = $value->getRegion();
				$new[$uniekeid]["plaats_id"] = $value->getPlace();
				$new[$uniekeid]["optimaalaantalpersonen"] = $value->getPax();
				$new[$uniekeid]["maxaantalpersonen"] = $value->getPax();
				$new[$uniekeid]["slaapkamers"] = $value->getBedRooms();
				$new[$uniekeid]["kwaliteit"] = $value->getQuality();
				$new[$uniekeid]["typeindeling"] = conv($value->getInsideDescription());
				$new[$uniekeid]["accomschrijving"] = conv($value->getOutsideDescription());
				$new[$uniekeid]["accafbeelding"] = $value->getPictures()->getString();
				$new[$uniekeid]["typeafbeelding"] = $value->getPictures()->getString();
				$new[$uniekeid]["soortaccommodatie"] = $type_id;
				$new[$uniekeid]["gps_lat"] = $value->getGeoLat();
				$new[$uniekeid]["gps_long"] = $value->getGeoLng();
				if(preg_match("/([0-9]+\.?[0-9]+) m2\.?(.*)$/", $new[$uniekeid]["typeindeling"], $matches)) {
					$new[$uniekeid]["oppervlakte"]=trim($matches[1]);
				}

				$included_services = "";
				$exclusive_services = "";
				$extra_services = "";

				if(isset($services[$uniekeid]) && is_array($services[$uniekeid])) {
					while(list($key,$service)=each($services[$uniekeid])) {
						if(!($service instanceof SoapFault)) {
							switch($service->getType()) {
								case "Y2":
								case "N2":
								case "N5":
									$included_services .= conv($service->getDescription()) . ". ";
									$txt = $service->getText();
									#if(!empty($txt)) $included_services .= " " . iconv("UTF-8", "CP1252", rtrim($service->getText(), ".")) . ". ";
									break;

								case "Y1":
								case "Y4":
								case "N1":
									$amount = $service->getAmount();
									$currency = $service->getCurrency();
									$txt = $service->getText();

									$exclusive_services .= conv($service->getDescription());
									if($amount > 0) $exclusive_services .= " (". $currency . " " .$amount .")";
									$exclusive_services .= ". ";
									#if(!empty($txt)) $exclusive_services .= " " . iconv("UTF-8", "CP1252", rtrim($txt, ".")) . ". ";
									break;

								case "Y5":
								case "Y6":
								case "N4":
									$amount = $service->getAmount();
									$currency = $service->getCurrency();
									$txt = $service->getText();

									$extra_services .= conv($service->getDescription());
									if($amount > 0) $extra_services .= " (". $currency . " " .$amount .")";
									$extra_services .= ". ";
									#if(!empty($txt)) $extra_services .= " " . iconv("UTF-8", "CP1252", rtrim($txt, ".")) . ". ";
									break;

								default:
									$amount = $service->getAmount();
									$currency = $service->getCurrency();
									$txt = $service->getText();

									$extra_services .= conv($service->getDescription());
									if($amount > 0) $extra_services .= " (". $currency . " " .$amount .")";
									$extra_services .= ". ";
									#if(!empty($txt)) $extra_services .= " " . iconv("UTF-8", "CP1252", rtrim($txt, ".")) . ". ";
									break;
							}
						} else {
							echo $service->getMessage();
						}
					}
				}

				$new[$uniekeid]["inclusief"] = trim($included_services);
				$new[$uniekeid]["exclusief"] = trim($exclusive_services . $extra_services);

				$new[$uniekeid]["typeinclusief"] = trim($included_services);
				$new[$uniekeid]["typeexclusief"] = trim($exclusive_services . $extra_services);
			}
		}

		if(is_array($new)) {
			reset($new);
			while(list($key,$value)=each($new)) {
				if(!isset($verberg_xmlcode[$key])) {
					checkforxmlchanges($xml_importvalues,array("naam"=>$value["accnaam"],"plaats_id"=>$value["plaats_id"],"omschrijving"=>$value["accomschrijving"],"inclusief"=>$value["inclusief"],"exclusief"=>$value["exclusief"],"extraopties"=>$value["extraopties"],"accindeling"=>$value["accindeling"]),1,$value["accid"]);
					checkforxmlchanges($xml_importvalues,array("naam"=>$value["typenaam"],"kwaliteit"=>$value["kwaliteit"],"optimaalaantalpersonen"=>$value["optimaalaantalpersonen"],"maxaantalpersonen"=>$value["maxaantalpersonen"],"oppervlakte"=>$value["oppervlakte"],"slaapkamers"=>$value["slaapkamers"],"badkamers"=>$value["badkamers"],"typeindeling"=>$value["typeindeling"],"typeinclusief"=>$value["typeinclusief"],"typeexclusief"=>$value["typeexclusief"]),2,$key);
				}
			}
		}

		exit;
	} else {
		die("Invalid action");
	}
}