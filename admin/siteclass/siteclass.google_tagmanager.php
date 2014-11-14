<?php


/**
*  calls to Google Tag Manager
*/
class google_tagmanager {

	CONST BRAND = "Chalet";
	CONST ID = "1";
	
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

	/**
	 * Retrieves the login details for the user login
	 * 
	 * @global array $vars
	 * @param array $login
	 * 
	 * @return string
	 */
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

	/**
	 * Retrieves the booking details for the measurement of product purchase
	 * 
	 * @global array $vars
	 * @param array $gegevens
	 * @return string $return
	 */
	public function boeking_bevestigd($gegevens) {
		global $vars;

		$send = array();
		$send["event"]= 'purchase';
		$send["ecommerce"]["purchase"]["actionField"]["id"] = $gegevens["stap1"]["boekingid"];
		$send["ecommerce"]["purchase"]["actionField"]["affiliation"] = "";
		$send["ecommerce"]["purchase"]["actionField"]["revenue"] = (string)$gegevens["fin"]["totale_reissom"];
		$send["ecommerce"]["purchase"]["actionField"]["tax"] = "";
		$send["ecommerce"]["purchase"]["actionField"]["shipping"] = "";
		$send["ecommerce"]["purchase"]["actionField"]["coupon"] = "";
		
		$send["ecommerce"]["purchase"]["products"] = array();
		array_push($send["ecommerce"]["purchase"]["products"], array(
			"name"=>wt_he(wt_stripaccents($gegevens["stap1"]["accinfo"]["begincode"].$gegevens["stap1"]["accinfo"]["type_id"]." ".ucfirst($gegevens["stap1"]["accinfo"]["soortaccommodatie"])." ".$gegevens["stap1"]["accinfo"]["naam_ap"])),
			"id"=>self::ID,			
			"price"=>$gegevens["fin"]["accommodatie_totaalprijs"],
			"brand" => self::BRAND,
			"category"=>wt_he(wt_stripaccents($gegevens["stap1"]["accinfo"]["plaats"])),
			"variant"=>wt_he(wt_stripaccents($gegevens["stap1"]["accinfo"]["land"])),
			"quantity"=>$gegevens["stap1"]["aantalpersonen"]
		));

		$return = $this->datalayer_push($send);

		return $return;

	}
	
	/**
	 * Retrieves the product details for the measurement of product impressions
	 * 
	 * @global type $vars
	 * @param type $product_impressions
	 * @return string $return
	 */
	public function product_impressions($product_impressions, $list = 'Zoek-en-boek') {
		global $vars;	

		$send = array();
		$send["event"]= 'productImpressions';
		$send["ecommerce"]["currencyCode"] = 'EUR';
		$send["ecommerce"]["impressions"] = array();
		if(is_array($product_impressions)) {
			foreach ($product_impressions as $product_key => $product_value) {
				array_push($send["ecommerce"]["impressions"], array(
					"name"=>wt_he(wt_stripaccents(ucfirst($vars["soortaccommodatie"][(int)$product_value['soortaccommodatie']]) . " " . $product_value['naam'])),
					"id"=>self::ID,
					"price"=>(string)$product_value['tarief'],
					"brand"=>self::BRAND,
					"category"=>wt_he(wt_stripaccents($product_value['land'])),
					"variant"=>wt_he(wt_stripaccents($product_value['plaats'])),
					"list"=>$list,
					"position"=>$product_key + 1
				));	
			}
		}

		$return = $this->datalayer_push($send);
		return $return;

	}	
	
	/**
	 * Retrieves the product details for the measurement of product click
	 * 
	 * @param array $product_click
	 * 
	 * @return array $send
	 */
	public function product_click($product_click) {
		$send = array();
		$send["event"]= 'productClick';
		$send["ecommerce"]["click"]["products"] = array();
		$send["ecommerce"]["click"]["actionField"]["list"] = $product_click['list'];
		array_push($send["ecommerce"]["click"]["products"], array(
			"name"=>wt_he(wt_stripaccents($product_click['name'])),
			"id"=>self::ID,
			"price"=>"",
			"brand"=>self::BRAND,
			"category"=>wt_he(wt_stripaccents($product_click['land'])),
			"variant"=>wt_he(wt_stripaccents($product_click['plaats']))
		));			
				
		return $send;
	}
	
	/**
	 * Retrieves the details of the selected accomodation for the 'product details impressions'
	 * 
	 * @param type $product_details_impressions
	 * 	 * @return string $return
	 */
	public function product_detail_impressions($product_details_impressions) {
		$send = array();
		$send["event"]= 'productDetailImpressions';
		$send["ecommerce"]["detail"]["actionField"]["list"]= 'Detailpagina weergave';
		$send["ecommerce"]["detail"]["products"] = array();
		array_push($send["ecommerce"]["detail"]["products"], array(
			"name"=>wt_stripaccents(wt_stripaccents($product_details_impressions['name'])),
			"id"=>self::ID,
			"price"=>"",
			"brand"=>self::BRAND,
			"category"=>wt_he(wt_stripaccents($product_details_impressions['category'])),
			"variant"=>wt_he(wt_stripaccents($product_details_impressions['variant']))
		));			
			
		$return = $this->datalayer_push($send);
		return $return;
	}	
}

?>