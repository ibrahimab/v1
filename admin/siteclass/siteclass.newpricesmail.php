<?php


/**
* create text for newprices-mails (mails to people who filled out there mail address to receive a mail when new prices are available)
*/
class newpricesmail {

	function __construct() {

	}

	public function mailtekst($newpricesmail_id) {

		global $vars, $txt;
		$db = new DB_sql;

		$db->query("SELECT email, type_id, seizoen_id, mailtekst, website FROM newpricesmail WHERE newpricesmail_id='".intval($newpricesmail_id)."' AND sent IS NULL;");
		if($db->next_record()) {
			$email = $db->f("email");
			$type_id = $db->f("type_id");
			$seizoen_id = $db->f("seizoen_id");
			$website = $db->f("website");
			$mailtekst = trim($db->f("mailtekst"));
		}



		# Als het een boeking betreft, taal afstemmen op boeking
		if($vars["websiteinfo"]["taal"][$website]=="nl") {
			$ttv="";
			$vars["taal"] = "nl";
		} elseif($vars["websiteinfo"]["taal"][$website]) {
			$ttv="_".$vars["websiteinfo"]["taal"][$website];
			$vars["taal"] = $vars["websiteinfo"]["taal"][$website];
		}




		$db->query("SELECT naam".$ttv." AS naam FROM seizoen WHERE seizoen_id='".intval($seizoen_id)."';");
		if($db->next_record()) {
			$seizoenjaar = trim(preg_replace("@[a-zA-Z]@","", $db->f("naam")));
		}

		$db->query("SELECT plaats, soortaccommodatie, naam, tnaam AS type, optimaalaantalpersonen, maxaantalpersonen, type_id, begincode FROM view_accommodatie WHERE type_id='".intval($type_id)."' AND websites LIKE '%".addslashes($website)."%';");
		if($db->next_record()) {

			$return["website"] = $website;
			$return["to"] = $email;
			$return["toname"] = "";
			$return["subject"] = $txt[$vars["taal"]]["vars"]["newpricesmail_subject"];
			$return["subject"] = str_replace("[PLAATS]", $db->f("plaats"), $return["subject"]);

			if($mailtekst) {
				$return["body"] = $mailtekst;
				$return["bewerkt"] = true;
			} else {
				$return["body"] = $txt[$vars["taal"]]["vars"]["newpricesmail_".$vars["websiteinfo"]["seizoentype"][$website]];

				$return["body"] = str_replace("[SEIZOEN]", $seizoenjaar, $return["body"]);
				$return["body"] = str_replace("[WEBSITE]", $vars["websiteinfo"]["websitenaam"][$website], $return["body"]);

				$acc_name = ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("type") ? " ".$db->f("type") : "")." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? " - ".$db->f("maxaantalpersonen") : "")." ".txt("pers").")";

				$return["body"] = str_replace("[ACCOMMODATIENAAM]", $acc_name, $return["body"]);

				$return["body"] = str_replace("[LINK_ACC]",($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "http://ss.postvak.net/chalet/" : $vars["websites_basehref"][$website]).txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/"."?utm_source=email&utm_medium=email&utm_campaign=mail-volgend-seizoen",$return["body"]);
			}


		} else {
			$return["error"] = "Accommodatie niet gevonden";
		}

		return $return;

	}

}



?>