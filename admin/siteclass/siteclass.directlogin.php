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

	// old function: create code based on password_uc
	public function code_old($user_id,$md5_password="") {

		if(!$md5_password) {
			$db = new DB_sql;

			$db->query("SELECT password_uc FROM boekinguser WHERE user_id='".intval($user_id)."' AND userlevel>0".($this->check_wrongcount ? " AND wrongcount<50" : "").";");
			if($db->next_record()) {
				$md5_password = md5($db->f("password_uc"));
			}
		}
		if($md5_password) {
			$code=substr(sha1("jkljLKjlkjhhuUuhbb".$md5_password."kkKjjehhgfyyuq".$user_id."llkk299jjhhkkk"),0,6);

			return $code;
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
