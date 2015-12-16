<?php

/**
* class to show the distance in the correct unit (meters, kilometers)
*
* @author Jeroen Boschman (jeroen@webtastic.nl)
* @since  2015-12-04 16:00
*/
class distance
{

	/**
	 * maximum amount of meters to show in meters (everything above this value: show in kilometers)
	 **/
	const MAX_TO_SHOW_IN_METERS = 2000;

	/**
	 * show the distance on site, in meters or kilometers (depending on the actual distance)
	 * including an optional extraText
	 *
	 * @param integer distance in meters
	 * @param string extra text
	 * @return string
	 **/
	public static function show($distanceInMeters, $extraText)
	{


		$distanceInMeters = trim($distanceInMeters);
		$extraText = trim($extraText);


		if ($distanceInMeters=="0") {
			// on-site
			$distanceText = txt("ter-plaatse", "toonaccommodatie");

			// don't allow extraText that has only numbers (that would make no sense, combined with on-site)
			if ($extraText and !preg_match("@^[0-9]+$@", $extraText)) {
				$distanceText .= " (".$extraText.")";
			}


		} else {
			if ($distanceInMeters>static::MAX_TO_SHOW_IN_METERS) {
				$distanceText = static::convert($distanceInMeters);

				$unitText = txt("kilometer", "toonaccommodatie");

			} else {
				$distanceText = $distanceInMeters;
				$unitText = txt("meter", "toonaccommodatie");

			}

			if ($extraText) {
				if (preg_match("@^[0-9]+$@", $extraText)) {
					if ($extraText>$distanceInMeters) {
						if ( ($distanceInMeters>static::MAX_TO_SHOW_IN_METERS and $extraText<=static::MAX_TO_SHOW_IN_METERS) or ($distanceInMeters<=static::MAX_TO_SHOW_IN_METERS and $extraText>static::MAX_TO_SHOW_IN_METERS) ) {
							if ($extraText>static::MAX_TO_SHOW_IN_METERS) {
								$unitTextExtra = txt("kilometer", "toonaccommodatie");
							} else {
								$unitTextExtra = txt("meter", "toonaccommodatie");
							}
							$distanceText .= " ".$unitText." - ".static::convert($extraText)." ".$unitTextExtra;
						} else {
							$distanceText .= " - ".static::convert($extraText)." ".$unitText;
						}
					} else {
						$distanceText .= " ".$unitText;
					}
				} else {
					$distanceText .= " ".$unitText." (".$extraText.")";
				}
			} else {
				$distanceText .= " ".$unitText;
			}
		}

		return $distanceText;
	}

	/**
	 * convert distanceInMeters to correct value (e.g. 1000 becomes 1)
	 *
	 * @param integer distance in meters
	 * @return string
	 **/
	private static function convert($distanceInMeters)
	{

		if ($distanceInMeters>static::MAX_TO_SHOW_IN_METERS) {
			$distance = number_format($distanceInMeters/1000, 1, ",", ".");
			if (substr($distance, -2)==",0") {
				$distance = substr($distance, 0, -2);
			}
		} else {
			$distance = $distanceInMeters;
		}

		return $distance;
	}
}
