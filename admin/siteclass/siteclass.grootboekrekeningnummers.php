<?php

/**
*
*/
class grootboekrekeningnummers {


	#
	# Grootboekrekeningnummers
	#

	# Actieve sites


	function __construct() {

		$db = new DB_sql;

		$this->grootboekrekeningnummers["C"]=							array(-1=>"1911",0=>"8100",1=>"1931");

		$this->grootboekrekeningnummers["B"]=							array(-1=>"1911",0=>"8850",1=>"1939");

		$this->grootboekrekeningnummers["E"]=							array(-1=>"1911",0=>"8300",1=>"1933");
		$this->grootboekrekeningnummers_wederverkoop["E"]=				array(-1=>"1911",0=>"8310",1=>"1953");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["E"]=	array(-1=>"1911",0=>"8320",1=>"1954");

		$this->grootboekrekeningnummers["T"]=							array(-1=>"1911",0=>"8400",1=>"1934");
		$this->grootboekrekeningnummers_wederverkoop["T"]=				array(-1=>"1911",0=>"8410",1=>"1942");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["T"]=	array(-1=>"1911",0=>"8420",1=>"1956");

		$this->grootboekrekeningnummers["V"]=							array(-1=>"1911",0=>"8700",1=>"1937");
		$this->grootboekrekeningnummers_wederverkoop["V"]=				array(-1=>"1911",0=>"8710",1=>"1951");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["V"]=	array(-1=>"1911",0=>"8720",1=>"1958");

		$this->grootboekrekeningnummers["Q"]=							array(-1=>"1911",0=>"8800",1=>"1938");
		$this->grootboekrekeningnummers_wederverkoop["Q"]=				array(-1=>"1911",0=>"8810",1=>"1952");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["Q"]=	array(-1=>"1911",0=>"8820",1=>"1959");

		$this->grootboekrekeningnummers["Z"]=							array(-1=>"1911",0=>"8600",1=>"1936");
		$this->grootboekrekeningnummers_wederverkoop["Z"]=				array(-1=>"1911",0=>"8610",1=>"1943");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["Z"]=	array(-1=>"1911",0=>"8620",1=>"1957");

		$this->grootboekrekeningnummers["I"]=							array(-1=>"1911",0=>"8830",1=>"1947");
		$this->grootboekrekeningnummers_wederverkoop["I"]=				array(-1=>"1911",0=>"8835",1=>"1948");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["I"]=	array(-1=>"1911",0=>"8840",1=>"1949");

		$this->grootboekrekeningnummers["K"]=							array(-1=>"1911",0=>"8860",1=>"1941");

		$this->grootboekrekeningnummers["H"]=							array(-1=>"1911",0=>"8870",1=>"1908");
		$this->grootboekrekeningnummers_wederverkoop["H"]=				array(-1=>"1911",0=>"8835",1=>"1948");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["H"]=	array(-1=>"1911",0=>"8840",1=>"1949");


		$this->grootboekrekeningnummers["X"]=							array(-1=>"1911",0=>"8200",1=>"1932");

		$this->grootboekrekeningnummers["Y"]=							array(-1=>"1911",0=>"8400",1=>"1971");
		$this->grootboekrekeningnummers_wederverkoop["Y"]=				array(-1=>"1911",0=>"8410",1=>"1975");
		$this->grootboekrekeningnummers_wederverkoop_buitenland["Y"]=	array(-1=>"1911",0=>"8420",1=>"1980");


		# Inactieve sites
		$this->grootboekrekeningnummers["N"]=							array(-1=>"1911",0=>"8860",1=>"1941");
		$this->grootboekrekeningnummers["S"]=							array(-1=>"1917",0=>"8700",1=>"1937");
		$this->grootboekrekeningnummers["O"]=							array(-1=>"1918",0=>"8800",1=>"1938");
		$this->grootboekrekeningnummers["W"]=							array(-1=>"1911",0=>"8200",1=>"1932");


		// buitenlandse partners bepalen
		$db->query("SELECT ru.user_id AS reisbureau_user_id, r.land FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id;");
		while($db->next_record()) {
			if(trim(strtolower($db->f("land")))<>"nederland") {
				$this->reisbureau_user_id_buitenland[$db->f("reisbureau_user_id")]=true;
			}
		}
	}

	function grootboekrekening_array() {
		foreach ($this->grootboekrekeningnummers as $key => $value) {
			$array[$value[-1]]=$value[-1];
			$array[$value[0]]=$value[0];
			$array[$value[1]]=$value[1];
		}
		foreach ($this->grootboekrekeningnummers_wederverkoop as $key => $value) {
			$array[$value[-1]]=$value[-1];
			$array[$value[0]]=$value[0];
			$array[$value[1]]=$value[1];
		}
		foreach ($this->grootboekrekeningnummers_wederverkoop_buitenland as $key => $value) {
			$array[$value[-1]]=$value[-1];
			$array[$value[0]]=$value[0];
			$array[$value[1]]=$value[1];
		}
		ksort($array);
		return $array;
	}

	function bepaal_grootboekrekeningnummer($boekjaar, $website, $aankomstdatum_exact, $grootboektype, $reisbureau_user_id=0) {

		unset($wederverkoop, $buitenland);

		if(($website=="Y") and $boekjaar==2012) {
			// Venturasol: eenmalig verlengd boekjaar in 2013
			$boekjaar=2013;
		}

		if($boekjaar<boekjaar($aankomstdatum_exact)) {
			$boekjaar_plusmin=1;
		} elseif($boekjaar>boekjaar($aankomstdatum_exact)) {
			$boekjaar_plusmin=-1;
		} else {
			$boekjaar_plusmin=0;
		}
		if($grootboektype==0) {
			$grootboek="";
		} elseif($grootboektype==1) {
			# Alleen vanaf boekjaar 2010 wederverkoop-grootboekrekeningen toepassen
			if($reisbureau_user_id and $boekjaar>=2010) {
				$wederverkoop=true;

				if($this->reisbureau_user_id_buitenland[$reisbureau_user_id]) {
					$buitenland=true;
				}
			}
			if($wederverkoop and $buitenland) {
				$grootboek=$this->grootboekrekeningnummers_wederverkoop_buitenland[$website][$boekjaar_plusmin];
			} elseif($wederverkoop) {
				$grootboek=$this->grootboekrekeningnummers_wederverkoop[$website][$boekjaar_plusmin];
			} else {
				$grootboek=$this->grootboekrekeningnummers[$website][$boekjaar_plusmin];
			}
		} elseif($grootboektype==2) {
			$grootboek="1513";
		}

		return $grootboek;
	}
}








?>