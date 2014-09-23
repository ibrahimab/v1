<?php


/**
*  calls to Google Tag Manager
*/
class google_tagmanager {

	function __construct() {

	}

	public function place_start_script() {
		$return = <<<EOT

		<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-5CPQNN"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-5CPQNN');</script>
		<!-- End Google Tag Manager -->

EOT;

		return $return;

	}


	private function datalayer_push($array) {

		if(is_array($array)) {

			$return .= "<script>

			try {
				dataLayer.push (".json_encode($array).");
			}
			catch(err) {

			}

			</script>";
		}

		return $return;
	}


	public function mijnboeking_login($login) {

		global $vars;

		if(!$_SESSION["google_tagmanager"]["mijnboeking_login"] and is_object($login) and $login->logged_in) {

			$_SESSION["google_tagmanager"]["mijnboeking_login"] = true;

			$db = new DB_sql;

			$db->query("SELECT COUNT(b.boeking_id) AS aantal, MAX(UNIX_TIMESTAMP(b.bevestigdatum)) AS laatsteboeking FROM boeking b, boeking_persoon bp WHERE b.boeking_id=bp.boeking_id AND b.website='".$vars["website"]."' AND bp.email='".addslashes($login->username)."' AND b.bevestigdatum IS NOT NULL AND b.geannuleerd=0 AND b.stap_voltooid=5 AND b.goedgekeurd=1;");
			if($db->next_record()) {

				$send["event"] = "loggedIn";

				if($db->f("aantal")>1) {
					$send["dlvKlant"] = "bestaande klant";
				} else {
					$send["dlvKlant"] = "nieuwe klant";
				}
				$send["dlvBoekingen"] = $db->f("aantal");
				$send["dlvLaatsteBoeking"] = date("dmY", $db->f("laatsteboeking"));

				if($db->f("aantal")>0) {
					$return = $this->datalayer_push($send);
				}

			}
		}

		return $return;

	}

	public function boeking_bevestigd($gegevens) {
		global $vars;

		$send["event"] = "TrackTrans";

		$send["transactionId"] = $gegevens["stap1"]["boekingid"];
		$send["transactionAffiliation"] = "";
		$send["transactionTotal"] = $gegevens["fin"]["totale_reissom"];
		$send["transactionTax"] = "";
		$send["transactionShipping"] = "";

		$send["transactionProducts"] = array();
		array_push($send["transactionProducts"], array(
			"sku"=>"1",
			"name"=>wt_stripaccents($gegevens["stap1"]["accinfo"]["begincode"].$gegevens["stap1"]["accinfo"]["type_id"]." ".ucfirst($gegevens["stap1"]["accinfo"]["soortaccommodatie"])." ".$gegevens["stap1"]["accinfo"]["naam_ap"]),
			"category"=>wt_stripaccents($gegevens["stap1"]["accinfo"]["plaats"]),
			"price"=>$gegevens["fin"]["accommodatie_totaalprijs"],
			"quantity"=>"1"
		));


		if($NU_EVEN_NIET) {

			// opties per persoon
			if(is_array($gegevens["stap4"]["optie_onderdeelid_teller"])) {
				foreach ($gegevens["stap4"]["optie_onderdeelid_teller"] as $key => $value) {

					$bedrag=$gegevens["stap4"]["optie_onderdeelid_verkoop_key_verkoop"][$key];
					$key=$gegevens["stap4"]["optie_onderdeelid_verkoop_key"][$key];

					if($gegevens["stap4"]["optie_onderdeelid_reisverzekering"][$key]) {
						// reisverzekering
						array_push($send["transactionProducts"], array(
							"sku"=>"1",
							"name"=>wt_stripaccents($gegevens["stap4"]["optie_onderdeelid_naam"][$key]),
							"category"=>wt_stripaccents($gegevens["stap4"]["optie_onderdeelid_naam"][$key]),
							"price"=>$gegevens["stap4"]["optie_onderdeelid_verkoop_key_verkoop"][$key],
							"quantity"=>$value
						));
					} elseif($gegevens["stap4"]["optie_onderdeelid_onderdeel"][$key]) {
						// other options
						array_push($send["transactionProducts"], array(
							"sku"=>"1",
							"name"=>wt_stripaccents($gegevens["stap4"]["optie_onderdeelid_onderdeel"][$key]),
							"category"=>wt_stripaccents(ucfirst($gegevens["stap4"]["optie_onderdeelid_soort"][$key])),
							"price"=>$bedrag,
							"quantity"=>$value
						));
					} else {
						array_push($send["transactionProducts"], array(
							"sku"=>"1",
							"name"=>wt_stripaccents($gegevens["stap4"]["optie_onderdeelid_naam"][$key]),
							"category"=>wt_stripaccents($gegevens["stap4"]["optie_onderdeelid_naam"][$key]),
							"price"=>$bedrag,
							"quantity"=>$value
						));
					}
				}
			}

			// annuleringsverzekering
			if(is_array($gegevens["stap4"]["annuleringsverzekering_soorten"])) {
				foreach ($gegevens["stap4"]["annuleringsverzekering_soorten"] as $key => $value) {
					array_push($send["transactionProducts"], array(
						"sku"=>"1",
						"name"=>wt_stripaccents($vars["annverz_soorten"][$key]),
						"category"=>wt_stripaccents(txt("annuleringsverzekering","vars")),
						"price"=>$gegevens["fin"]["annuleringsverzekering_variabel_".$key],
						"quantity"=>1
					));
				}
			}
		}

		$return = $this->datalayer_push($send);

		return $return;

	}

}



?>