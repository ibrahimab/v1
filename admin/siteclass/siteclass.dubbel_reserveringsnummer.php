<?php


/**
* class to check if a booking has an already existing booking number (boekingsnummer)
*/
class dubbel_reserveringsnummer {

	var $dubbel = false;

	function __construct($boeking_id) {
		$db = new DB_sql;

		$db->query("SELECT b2.boeking_id FROM boeking b1, boeking b2 WHERE b1.boekingsnummer<>'' AND b2.boekingsnummer<>'' AND SUBSTR(b1.boekingsnummer,2,8)=SUBSTR(b2.boekingsnummer,2,8) AND b1.boeking_id='".intval($boeking_id)."' AND b2.boeking_id<>'".intval($boeking_id)."' AND b1.boeking_id>92514;");
		if($db->next_record()) {
			$this->dubbel = true;
			$this->dubbel_boeking_id = $db->f("boeking_id");
		}

	}
}


?>