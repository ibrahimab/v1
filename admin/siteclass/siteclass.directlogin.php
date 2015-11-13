<?php

/**
* direct laten inloggen via een link
*/
class directlogin {

	// bij nagaan code: controleren of er niet al teveel ongeldige inlogpogingen zijn
	public $check_wrongcount = false;

	public $boeking_id;

	function __construct() {

	}

	public function maak_link($website,$soort,$user_id,$md5_password="") {
		global $vars;

		// $md5_password is not used right now

		// soort = 1 : doorlinken naar "Mijn boeking"
		// soort = 2 : doorlinken naar "betalen"
		// soort = 3 : doorlinken naar "factuur goedkeuren"

		// zie inloggen.php voor deze redirects


		$link=$vars["websiteinfo"]["basehref"][$website];

		$code=$this->code($user_id);

		if($code) {

			$link.="q/".$this->willekeurige_letter($user_id).$user_id.$this->willekeurige_letter($user_id+33).$code.$soort;

			if($this->boeking_id) {
				$link.="/".$this->boeking_id;
			}
		}
		return $link;
	}

	public function createLinkBasedOnBooking($soort, $gegevens) {

		$db = new DB_sql;

		$this->boeking_id = $gegevens["stap1"]["boekingid"];

		$db->query("SELECT user_id, password FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");

		if ($db->next_record()) {
			$link = $this->maak_link($gegevens["stap1"]["website"], $soort, $db->f( "user_id" ));
			return $link;
		} else {
			trigger_error( "no directlogin-link for boeking_id ".$this->boeking_id, E_USER_NOTICE );
			return false;
		}
	}

	public function code($user_id) {

		$db = new DB_sql;

		$db->query("SELECT password FROM boekinguser WHERE user_id='".intval($user_id)."' AND userlevel>0".($this->check_wrongcount ? " AND wrongcount<50" : "").";");
		if($db->next_record()) {
			$salted_password = $db->f("password");
		}

		if($salted_password) {
			$code=substr(sha1("jkljLKjlkjhhuUuhbb".$salted_password."kkKjjehhgfyyuq".$user_id."llkk299jjhhkkk"),0,6);

			return $code;
		} else {
			return false;
		}
	}

	// old function: check old direct-links
	public function check_code_old($user_id, $code) {

		global $vars;

		$db = new DB_sql;

		$login_okay = false;

		$db->query("SELECT directlogin_check FROM boekinguser WHERE user_id='".intval($user_id)."' AND userlevel>0".($this->check_wrongcount ? " AND wrongcount<50" : "").";");
		if($db->next_record()) {

			if ($db->f( "directlogin_check" ) == sha1("OLD_CODE_SALT_".$code.$vars["salt"])) {
				$login_okay = true;
			}
		}

		if ($login_okay) {
			return true;
		} else {
			return false;
		}
	}

	private function willekeurige_letter($gekoppeld_getal) {

		$keuze_getal=round(intval(substr($gekoppeld_getal,-2))/4);

		$keuze="abcdefghijklmnopqrstuvwxyz";
		$return=substr($keuze,$keuze_getal,1);
		return $return;
	}

}
