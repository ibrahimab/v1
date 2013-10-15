<?php
ini_set('memory_limit','-1');
ini_set('display_errors', true);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
#error_reporting(-1);
if(!defined("DS")) define("DS", DIRECTORY_SEPARATOR);

abstract class SoapClass {

	private $tmpDir = "./tmp/"; // Local temporary directory
	private $unzip = "unzip"; // Server extract command

	/**
	 * Get the nearest Saturday based on provided date
	 * It is used for setting the season weeks correctly
	 */
	protected function _nearestSaturday($time) {

		if(date("w",$time)==0) $plusmin=-1;
		if(date("w",$time)==1) $plusmin=-2;
		if(date("w",$time)==2) $plusmin=-3;
		if(date("w",$time)==3) $plusmin=3;
		if(date("w",$time)==4) $plusmin=2;
		if(date("w",$time)==5) $plusmin=1;
		if(date("w",$time)==6) $plusmin=0;

		$return=mktime(0,0,0,date("m",$time),date("d",$time)+$plusmin,date("Y",$time));
		return $return;
	}

	/**
	 * Function added to handle GMT dates conversion
	 * It is used only on our local server and it should not be committed
	 *
	 * @param string $timeteller
	 * @return int
	 */
	protected function date_to_gmdate( $timeteller = '' ) {
		$date = date("Y-m-d", $timeteller);
		$timeteller = gmdate("U", strtotime($date));
		$offset = $this->getServerTimeOffset();

		return strtotime("+$offset hour", $timeteller);
	}

	/**
	 * Function used to get the current server datetime offset compared with "Europe/Amsterdam"
	 *
	 * @return integer
	 */
	private function getServerTimeOffset() {
		$localTimezone = date_default_timezone_get();

		$dateTimeZoneAmsterdam = new DateTimeZone("Europe/Amsterdam");
		$dateTimeZoneLocal = new DateTimeZone($localTimezone);

		// Create two DateTime objects that will contain the same Unix timestamp, but
		// have different timezones attached to them.
		$dateTimeAmsterdam = new DateTime("now", $dateTimeZoneAmsterdam);
		$dateTimeLocal = new DateTime("now", $dateTimeZoneLocal);

		// Calculate the GMT offset for the date/time contained in the $dateTimeAmsterdam
		// object, but using the timezone rules as defined for Local ($dateTimeZoneLocal).

		// Should show int(32400) (for dates after Sat Sep 8 01:00:00 1951 JST).
		//Number of seconds Local is ahead of GMT at the specific time: $timeOffset = $dateTimeZoneLocal->getOffset($dateTimeAmsterdam);
		//Number of seconds Amsterdam is ahead of GMT at the specific time: $dateTimeZoneAmsterdam->getOffset($dateTimeAmsterdam)

		//Number of seconds Local is ahead of Amsterdam at the specific time:
		$diff = ($dateTimeZoneLocal->getOffset($dateTimeAmsterdam)-$dateTimeZoneAmsterdam->getOffset($dateTimeAmsterdam));

		// Convert seconds to hours
		return floor($diff/3600);
	}

	/**
	 * The function downloads a XML zip file from the specified ftp URL and extracts it to local server
	 *
	 * @param string $url; The ftp address (including username and password)
	 * @param string $file; The name of the archive file
	 * @return string
	 */
	protected function getFileFromZip($url = null, $file = null){

		$opts = stream_context_create(array(
			'http' => array(
				'timeout' => 20
				)
			)
		);

		$zipFile = $this->tmpDir . $file;
		$extractedFile = basename($zipFile, ".zip");
		
		if($zip = @file_get_contents($url . $file, false, $opts)) {			
				$fh = fopen($zipFile,"w",false);
				fwrite($fh, $zip);
				fclose($fh);

			# Zip-file extract
			@unlink($this->tmpDir . $extractedFile);


			if(file_exists($zipFile)) {
				exec($this->unzip . " " . $zipFile . " -d ". $this->tmpDir);
			}
		}
		
		return $this->tmpDir . $extractedFile;
	}
}