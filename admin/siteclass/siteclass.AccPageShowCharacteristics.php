<?php

/**
 * show characteristics (kenmerken) in a list with columns on the accommodation-page
 *
 * @author Jeroen Boschman (jeroen@webtastic.nl)
 * @since: 2015-12-04 15:00
 *
 **/
class AccPageShowCharacteristics
{

	/**
	 * show the list
	 *
	 * @param array list with characteristics
	 * @return string
	 **/
	public static function show($kenmerken_array)
	{

		$return = "";

		$return .= "<div class=\"kenmerken_block\">";
		$return .= "<ul class=\"kenmerken\">";

		$kenmerken_kolomteller = 1;

		$aantal_kenmerken_per_kolom = ceil(count($kenmerken_array) / 3);
		$aantal_kenmerken_per_kolom_eerste_kolom = ceil(count($kenmerken_array) / 3);

		foreach ($kenmerken_array as $key => $value) {

			$kenmerken_teller ++;
			$return .= "<li>".wt_he($value)."</li>";

			if($kenmerken_kolomteller==1) {
				$check_aantal_kenmerken = $aantal_kenmerken_per_kolom_eerste_kolom;
			} else {
				$check_aantal_kenmerken = $aantal_kenmerken_per_kolom;
			}

			if(fmod($kenmerken_teller, $check_aantal_kenmerken)==0 and $kenmerken_teller<count($kenmerken_array)) {
				$return .= "</ul>";
				$return .= "<div class=\"kenmerken_divider\"></div>";
				$return .= "<ul class=\"kenmerken\">";

				$kenmerken_kolomteller++;
			}
		}

		$return .= "</ul>"; // close #kenmerken
		$return .= "</div>"; // close .kenmerken_block

		$return .= "<div class=\"clear\"></div>";

		return $return;
	}
}
