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

		$return .= "<script>

		try {
			dataLayer.push (".json_encode($array).");
		}
		catch(err) {

		}

		</script>";

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
			}
			$return = $this->datalayer_push($send);
		}

		return $return;

	}

}



?>