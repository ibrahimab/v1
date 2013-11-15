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
	} elseif($argv[1] == "customerfeedback") {
		// Import customer feedback for Interhome accommodations
		$lev = 421;
		// Get the entries from the Interhome server by XML
		$cf = $interHome->getCustomerFeedback();

		if($cf) {

			// Get all Interhome's accommodations from the database
			$db->query("SELECT leverancierscode, type_id FROM `type` WHERE leverancier_id = '$lev' AND leverancierscode <> ''");
			while($db->next_record()) {
				$type[$db->f("type_id")] = $db->f("leverancierscode");
			}

			// Get aleredy imported feedback's
			$db->query("SELECT hash, type_id FROM `boeking_enquete` WHERE hash <> '' AND source_leverancier_id='$lev' AND boeking_id = '0'");
			while($db->next_record()) {
				if(isset($type[$db->f("type_id")])) $existing[$db->f("hash")] = 1;
			}
			$i = 0;
			foreach($cf as $object) {
				$obj = serialize((array)$object);
				$hash = md5($obj);

				if(isset($existing[$hash])) {
					// Check if the feedback is alredy imported
					continue;
				} else {
					// Search the accommodation code in the database entries
					$type_id = array_search($object->code->__toString(), $type);
					if($type_id !== false) {

						$customername = $object->customername->__toString();
						$customername = iconv('utf8', 'windows-1252', $customername);
						$customername = esc($customername);
						$localservice = ceil($object->localservice->__toString());
						$furnishings = ceil($object->furnishings->__toString());
						$cleanliness = ceil($object->cleanliness->__toString());
						$valueformoney = ceil($object->valueformoney->__toString());
						$totalrating = ceil($object->totalrating->__toString());
						$periodoftravel = $object->periodoftravel->__toString();
						$aankomstdatum_exact = date("Y-m-d", strtotime("01.".$periodoftravel));

						if($localservice+$furnishings+$cleanliness+$valueformoney+$totalrating > 0) {
							// If the votes are valid, insert into the database
							$q  = "INSERT INTO `boeking_enquete` (`vraag1_1`,`vraag1_2`,`vraag1_3`,`vraag1_4`,`vraag1_5`,`vraag1_6`,`vraag1_7`,`aankomstdatum_exact`,`type_id`,`websitetekst_naam`,`hash`,`source_leverancier_id`,`invulmoment`) ";
							$q .= "VALUES ('$localservice', '$furnishings', 11, '$cleanliness', 11, '$valueformoney', '$totalrating', '$aankomstdatum_exact', '$type_id', '$customername', '$hash', '$lev', NOW())";
							$db2->query($q);

							$i++;
						}
					}
				}
				unset($obj);
			}
			echo "Successfully imported " . $i ." customer feedback's";
		}
	} else {
		die("Invalid action");
	}
}