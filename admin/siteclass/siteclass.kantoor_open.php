<?php


/**
* class om te controleren of het kantoor geopend is
*/
class kantoor_open {

	function __construct() {

	}


	public function is_het_kantoor_geopend($time=0) {

		//
		// controleer op:
		// - openingstijden door de week, openingstijden zaterdag
		// - zaterdag, zondag, nieuwjaarsdag, eerste en tweede paasdag, Koninginnedag/Koningsdag, Hemelvaart, eerste en tweede pinksterdag, eerste en tweede kerstdag
		//

		if(!$time) {
			$time=time();
		}

		$kantoor_open = false;


		// controle op zaterdagen
		if(date("w",$time)==6 and date("H",$time)>=10 and date("Hi",$time)<1730) {
			$kantoor_open = true;
		}

		// controle op doordeweekse dagen
		if (date("w",$time)>=1 and date("w",$time)<=5 and date("H",$time)>=9 and date("Hi",$time)<1730) {
			$kantoor_open = true;
		}

		// controle op feestdagen
		$jaar=date("Y",$time);
		$feestdag["0101"]="Nieuwjaarsdag";

		if($jaar<2014) {
			if(date("w",mktime(0,0,0,4,30,$jaar))==0) {
				$feestdag["2904"]="Koninginnedag";
			} else {
				$feestdag["3004"]="Koninginnedag";
			}
		} else {
			if(date("w",mktime(0,0,0,4,27,$jaar))==0) {
				$feestdag["2604"]="Koningsdag";
			} else {
				$feestdag["2704"]="Koningsdag";
			}
		}

		if(function_exists("easter_date")) {
			$pasen=easter_date($jaar);
			$feestdag[date("dm",$pasen)]="1e paasdag";
			$feestdag[date("dm",mktime(0,0,0,date("m",$pasen),date("d",$pasen)+1,date("Y",$pasen)))]="2e paasdag";
			$feestdag[date("dm",mktime(0,0,0,date("m",$pasen),date("d",$pasen)+39,date("Y",$pasen)))]="Hemelvaartsdag";
			$feestdag[date("dm",mktime(0,0,0,date("m",$pasen),date("d",$pasen)+49,date("Y",$pasen)))]="1e pinksterdag";
			$feestdag[date("dm",mktime(0,0,0,date("m",$pasen),date("d",$pasen)+50,date("Y",$pasen)))]="2e pinksterdag";
		} else {
			trigger_error("Functie easter_date() niet beschikbaar",E_USER_NOTICE);
		}

		$feestdag["2512"]="1e Kerstdag";
		$feestdag["2612"]="2e Kerstdag";

		if($feestdag[date("dm",$time)]) {
			$kantoor_open = false;

		}

		return $kantoor_open;
	}

}



?>