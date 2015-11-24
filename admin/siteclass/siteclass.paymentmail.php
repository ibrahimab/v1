<?php

/**
* send a payment receipt to a client via email
*/
class paymentmail {


	public $gegevens;
	public $to;
	public $test = false;

	private $taal;
	private $totaal;
	private $voldaan;
	private $openstaand;

	function __construct() {

	}

	public function send_mail($boeking_id, $amount, $date) {

		// get data from database
		$this->get_gegevens($boeking_id);

		// construct mailtext
		$mailtekst_ontvangenbetaling = $this->get_mailtext($boeking_id, $amount, $date);

		$mail = new wt_mail;
		$mail->from=$mailtekst_ontvangenbetaling["from"];
		$mail->fromname=$mailtekst_ontvangenbetaling["fromname"];
		$mail->subject=$mailtekst_ontvangenbetaling["subject"];
		$mail->plaintext=$mailtekst_ontvangenbetaling["body"];

		if($mailtekst_ontvangenbetaling["to_1"] or $mailtekst_ontvangenbetaling["to_2"]) {
			$mail->to=$mailtekst_ontvangenbetaling["to_1"];
			if($this->test) {
				$mail->to="jeroen@webtastic.nl";
			}
			$mail->send();
			$this->mailed_to($mail->to);
			if($mailtekst_ontvangenbetaling["to_2"]) {
				$mail->to=$mailtekst_ontvangenbetaling["to_2"];
				if($this->test) {
					$mail->to="jeroen@webtastic.nl";
				}
				$mail->send();
				$this->mailed_to($mail->to);
			}
		} else {
			$mail->to=$mailtekst_ontvangenbetaling["to"];
			if($this->test) {
				$mail->to="jeroen@webtastic.nl";
			}
			$mail->send();
			$this->mailed_to($mail->to);
		}

		if(!$this->test) {
			// log that the mail has been sent
			boeking_log($boeking_id, "ontvangstbevestiging betaling gemaild aan ".$mailtekst_ontvangenbetaling["to_log"]);
		}
	}

	private function mailed_to($to) {
		if($this->to) {
			$this->to .= ", ".$to;
		} else {
			$this->to = $to;
		}
	}

	private function get_gegevens($boeking_id) {

		global $vars;

		$db = new DB_sql;

		$this->gegevens=get_boekinginfo($boeking_id);

		$this->taal=$this->gegevens["stap1"]["taal"];

		$this->totaal=round($this->gegevens["stap1"]["totale_reissom"], 2);
		$db->query("SELECT SUM(bedrag) AS bedrag FROM boeking_betaling WHERE boeking_id='".addslashes($this->gegevens["stap1"]["boekingid"])."';");
		if($db->next_record()) {
			$this->voldaan=$db->f("bedrag");
		}
		$this->voldaan=round($this->voldaan,2);
		$this->openstaand=$this->totaal-$this->voldaan;
		$this->openstaand=round($this->openstaand,2);

	}

	private function get_mailtext($boeking_id, $amount, $date) {

		global $vars, $txt, $txta;

		$db = new DB_sql;

		if($boeking_id) {

			$return["subject"]="[".$this->gegevens["stap1"]["boekingsnummer"]."] ".$txt[$this->taal]["vars"]["mailbetaling_subject"]." ".$this->gegevens["stap1"]["accinfo"]["plaats"];
			$return["from"]=$this->gegevens["stap1"]["website_specifiek"]["email"];
			$return["fromname"]=$this->gegevens["stap1"]["website_specifiek"]["websitenaam"];
			$return["boekingsnummer"]=$this->gegevens["stap1"]["boekingsnummer"];
	#		$return["plaats"]=$this->gegevens["stap1"]["accinfo"]["plaats"];
	#		$return["to"]=$this->gegevens["stap2"]["email"];
			if($this->gegevens["stap1"]["reisbureau_aanmaning_email_1"]) {
				# reisbureau
				if($this->gegevens["stap1"]["reisbureau_aanmaning_email_2"]) {
					$return["to_1"]=$this->gegevens["stap1"]["reisbureau_aanmaning_email_1"];
					$return["to_2"]=$this->gegevens["stap1"]["reisbureau_aanmaning_email_2"];
					$return["to_log"]=$this->gegevens["stap1"]["reisbureau_aanmaning_email_1"]." en ".$this->gegevens["stap1"]["reisbureau_aanmaning_email_2"];
				} else {
					$return["to_1"]=$this->gegevens["stap1"]["reisbureau_aanmaning_email_1"];
					$return["to_log"]=$this->gegevens["stap1"]["reisbureau_aanmaning_email_1"];
				}
			} else {
				$return["to"]=$this->gegevens["stap2"]["email"];
				$return["to_log"]=$this->gegevens["stap2"]["email"];
			}

	#		$return["mailverstuurd_opties"]=$this->gegevens["stap1"]["mailverstuurd_opties"];
			$return["soortvakantie"]=$txt[$this->taal]["vars"]["mailaanmaning_soortvakantie_wzt".$this->gegevens["stap1"]["accinfo"]["wzt"]];
	#		$return["bedrag"]=number_format($amount,2,",",".");

			#
			# Te betalen moet hoger dan 1 euro zijn (vanwege afrondingsverschillen) - 11 oktober 2011
			#
			if($this->openstaand>1) {
				$return["body"]=$txt[$this->taal]["vars"]["mailbetaling"]."\n\n";
			} else {
				$return["body"]=$txt[$this->taal]["vars"]["mailbetaling_voldaan"]."\n\n";
			}

			# Gegevens overzetten
			$return["body"]=ereg_replace("\[NAAM\]",$this->gegevens["stap2"]["voornaam"],$return["body"]);
			$return["body"]=ereg_replace("\[ACHTERNAAM\]",wt_naam("", $this->gegevens["stap2"]["tussenvoegsel"], $this->gegevens["stap2"]["achternaam"]),$return["body"]);
	#		$return["body"]=ereg_replace("\[PLAATS\]",$this->gegevens["stap1"]["accinfo"]["plaats"],$return["body"]);
			$return["body"]=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$date,$this->taal),$return["body"]);
	#		$return["body"]=ereg_replace("\[LINK\]",$vars["websites_basehref"][$this->gegevens["stap1"]["website"]].$txta[$this->taal]["menu_inloggen"].".php",$return["body"]);
			$return["body"]=ereg_replace("\[WEBSITE\]",$this->gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);
			$return["body"]=ereg_replace("\[SOORTVAKANTIE\]",$return["soortvakantie"],$return["body"]);
	#		$return["body"]=ereg_replace("\[SOORTBETALING\]",$return["soortbetaling"],$return["body"]);
			$return["body"]=ereg_replace("\[BEDRAG\]",number_format($amount,2,",","."),$return["body"]);

			if($this->gegevens["stap1"]["reisbureau_user_id"]) {
				$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$this->gegevens["stap1"]["boekingsnummer"]." (".wt_naam($this->gegevens["stap2"]["voornaam"],$this->gegevens["stap2"]["tussenvoegsel"],$this->gegevens["stap2"]["achternaam"]).")",$return["body"]);
			} else {
				$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$this->gegevens["stap1"]["boekingsnummer"],$return["body"]);
	#			$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$this->gegevens["stap1"]["boekingsnummer"]." (".wt_naam($this->gegevens["stap2"]["voornaam"],$this->gegevens["stap2"]["tussenvoegsel"],$this->gegevens["stap2"]["achternaam"]).")",$return["body"]);
			}

			$return["body"]=ereg_replace("\[BETALINGSINFO\]", betalingsinfo::get_text($this->gegevens, $this->voldaan), $return["body"]);
			$return["subject"]=ereg_replace("\[SOORTVAKANTIE\]",$return["soortvakantie"],$return["subject"]);

			// betaallink
			if($this->gegevens["stap1"]["reisbureau_user_id"]) {
				$directlogin_link = $vars["websiteinfo"]["basehref"][$this->gegevens["stap1"]["website"]]."reisagent.php";
			} else {
				$db->query("SELECT user_id, password FROM boekinguser WHERE user='".addslashes($this->gegevens["stap2"]["email"])."';");
				if($db->next_record()) {
					$directlogin = new directlogin;
					$directlogin->boeking_id=$this->gegevens["stap1"]["boekingid"];
					$directlogin_link = $directlogin->maak_link($this->gegevens["stap1"]["website"], 2, $db->f("user_id"));
				}
			}

			if(!$directlogin_link) {
				trigger_error("empty directlogin_link",E_USER_NOTICE);
			}

			$return["body"]=ereg_replace("\[BETAALLINK\]", $directlogin_link, $return["body"]);

			return $return;
		} else {
			return false;
		}
	}
}



?>