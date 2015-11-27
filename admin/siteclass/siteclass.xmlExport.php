<?php

/**
* class as a basis for XML-exports (e.g. TradeTracker)
*
* @author: Jeroen Boschman (jeroen@webtastic.nl)
* @since: 2015-05-29 11:00
*/

class xmlExport extends chaletDefault
{

	/**  general data of accommodation-types  */
	protected $type_data;

	/**  price-data of accommodation-types  */
	protected $type_price;

	/**  arrival-dates of accommodation-types  */
	protected $type_arrival;

	/**  departure-dates of accommodation-types  */
	protected $type_departure;

	/**  number of nights of accommodation-types  */
	protected $type_number_of_nights;

	/**  additional costs (bijkomende kosten) of accommodation-types  */
	protected $type_bkk;

	/**  facilities (kenmerken) of accommodation-types  */
	protected $type_kenmerken;

	/**  availability of accommodation-types  */
	protected $type_availability;

	/**  which facilities to use  */
	protected $facilites_show_array;

	/**  search only for available weeks  */
	protected $must_be_available = true;

	/**  XMLWriter-object  */
	protected $x;

	/**  Letter of the current website  */
	public $website;

	/**  which type-ids to include in the export  */
	public $type_ids;

	/**  Only use special offers (aanbiedingen)?  */
	public $aanbieding;

	/**  Use only these type_id's with provider-codes as id's  */
	public $type_codes;

	/**  Use wederverkoop-prices  */
	public $use_wederverkoop_prices = false;

	/**  Save the created XML as a cache-file  */
	public $save_cache = false;

	/**  Limit the number of results (for testing purposes)  */
	public $limit;

	/**
	 * call the parent constructor
	 *
	 * @return void
	 */
	function __construct()
	{

		parent::__construct();

	}

	/**
	 * query all needed data from the database
	 *
	 * @return void
	 */
	protected function query_database()
	{

		$db = new DB_sql;

		// Distances
		if($this->config->seizoentype==1) {
			// Winter
			$doorloop_afstanden=array(
				"afstandwinkel"=>"distance_shop",
				"afstandrestaurant"=>"distance_restaurant",
				"afstandpiste"=>"distance_piste",
				"afstandskilift"=>"distance_skilift",
				"afstandloipe"=>"distance_crosscountry",
				"afstandskibushalte"=>"distance_skibusstop"
			);
		} elseif($this->config->seizoentype==2) {
			// Summer
			$doorloop_afstanden=array(
				"afstandwinkel"=>"distance_shop",
				"afstandrestaurant"=>"distance_restaurant",
				"afstandstrand"=>"distance_beach",
				"afstandzwembad"=>"distance_pool",
				"afstandzwemwater"=>"distance_swimmingwater",
				"afstandgolfbaan"=>"distance_golfcourse"
			);
		}

		if( $this->aanbieding ) {

			// aanbiedingen uit kortingensysteem ophalen
			$db->query("SELECT DISTINCT type_id FROM tarief WHERE 1=1 AND (c_bruto>0 OR bruto>0) AND beschikbaar=1 AND week>'".time()."' AND aanbiedingskleur_korting=1 AND (aanbieding_acc_percentage>0 OR aanbieding_acc_euro>0) AND kortingactief=1;");
			while($db->next_record()) {
				$aanbieding_inquery.=",".$db->f( "type_id" );
			}
		}


		if( is_array($this->type_codes) ) {

			foreach ($this->type_codes as $key => $value) {
				$this->type_ids .= ",".$key;
			}

			if( $this->type_ids ) {
				$this->type_ids = substr($this->type_ids, 1);
			}

		}

		$seizoen_id_inquery = "0";
		$skipas_id_inquery = "0";
		$type_id_inquery = "0";
		$type_id_inquery_acc = "0";
		$type_id_inquery_arr = "0";

		if ( $this->limit > 0 ) {
			$limit = $this->limit;
		} elseif ( $this->config->lokale_testserver ) {
			$limit = 650;
		}

		$db->query("SELECT DISTINCT t.type_id, a.accommodatie_id, a.toonper, a.naam, a.kenmerken AS kenmerken_accommodatie, a.aankomst_plusmin, a.vertrek_plusmin, a.skipas_id, t.naam".$this->config->ttv." AS tnaam, a.zoekvolgorde AS azoekvolgorde, a.omschrijving".$this->config->ttv." AS omschrijving, a.kwaliteit, a.gps_lat, a.gps_long, a.afstandwinkel, a.afstandwinkelextra".$this->config->ttv." AS afstandwinkelextra, a.afstandrestaurant, a.afstandrestaurantextra".$this->config->ttv." AS afstandrestaurantextra, a.afstandpiste, a.afstandpisteextra".$this->config->ttv." AS afstandpisteextra, a.afstandskilift, a.afstandskiliftextra".$this->config->ttv." AS afstandskiliftextra, a.afstandloipe, a.afstandloipeextra".$this->config->ttv." AS afstandloipeextra, a.afstandskibushalte, a.afstandskibushalteextra".$this->config->ttv." AS afstandskibushalteextra, a.afstandstrand, a.afstandstrandextra".$this->config->ttv." AS afstandstrandextra, a.afstandzwembad, a.afstandzwembadextra".$this->config->ttv." AS afstandzwembadextra, a.afstandzwemwater, a.afstandzwemwaterextra".$this->config->ttv." AS afstandzwemwaterextra, a.afstandgolfbaan, a.afstandgolfbaanextra".$this->config->ttv." AS afstandgolfbaanextra, t.kwaliteit AS tkwaliteit, t.omschrijving".$this->config->ttv." AS tomschrijving, t.zoekvolgorde AS tzoekvolgorde, lv.zoekvolgorde AS lzoekvolgorde, t.optimaalaantalpersonen, t.maxaantalpersonen, a.soortaccommodatie, t.slaapkamers, t.badkamers, t.kenmerken AS kenmerken_type, s.skigebied_id, s.naam".$this->config->ttv." AS skigebied, s.kenmerken AS kenmerken_skigebied, l.naam".$this->config->ttv." AS land, l.begincode, l.isocode, p.naam AS plaats, p.plaats_id, p.kenmerken AS kenmerken_plaats FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".$this->website."%' AND a.tonen=1 AND a.archief=0 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0".($aanbieding_inquery ? " AND t.type_id IN (".substr($aanbieding_inquery,1).")" : "").($this->type_ids ? " AND t.type_id IN (".$this->type_ids.")" : "")." ORDER BY type_id".($limit ? " LIMIT 0,".$limit : "").";");
		while($db->next_record()) {

			$type_id = $db->f( "type_id" );

			// all database-fields
			foreach ($db->Record as $key => $value) {

				if( !is_int($key) ) {
					$type_data[$type_id][$key] = $value;
				}
			}

			$type_id_inquery .= ",".$db->f( "type_id" );

			if (!$skipas_gehad[$db->f( "skipas_id" )]) {
				$skipas_id_inquery .= ",".$db->f( "skipas_id" );
				$skipas_gehad[$db->f( "skipas_id" )] = true;
			}

			if ( $db->f("toonper")==3 or $this->use_wederverkoop_prices ) {
				// losse accommodatie
				$type_id_inquery_acc .= ",".$db->f( "type_id" );
			} else {
				// arrangement
				$type_id_inquery_arr .= ",".$db->f( "type_id" );
			}

			if ( $this->config->wederverkoop ) {
				unset( $type_data[$type_id]["skipas_id"] );
			}

			// ANVR-codes
			switch ($type_data[$type_id]['soortaccommodatie']) {
				case 1:
					$type_data[$type_id]["accommodationType"] = 'CHA';
					break;
				case 2:
					$type_data[$type_id]["accommodationType"] = 'APP';
					break;
				case 3:
					$type_data[$type_id]["accommodationType"] = 'HOT';
					break;
				case 4:
					$type_data[$type_id]["accommodationType"] = 'APP';
					break;
				case 6:
					$type_data[$type_id]["accommodationType"] = 'HUI';
					break;
				case 7:
					$type_data[$type_id]["accommodationType"] = 'VIL';
					break;
				case 8:
					$type_data[$type_id]["accommodationType"] = 'KAS';
					break;
				case 9:
					$type_data[$type_id]["accommodationType"] = 'VAK';
					break;
				case 10:
					$type_data[$type_id]["accommodationType"] = 'HUI';
					break;
				case 11:
					$type_data[$type_id]["accommodationType"] = 'HUI';
					break;
				case 12:
					$type_data[$type_id]["accommodationType"] = 'PEN';
					break;
			}
			$type_data[$type_id]["unitType"] = $type_data[$type_id]['maxaantalpersonen'].'PK';
			if( preg_match("@catering@", $type_data[$type_id]["tnaam"] ) ) {
				$type_data[$type_id]["serviceType"] = 'AI';
			} else {
				$type_data[$type_id]["serviceType"] = 'LG';
			}




			// fullname
			$aantalpersonen=$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? txt("persoon") : txt("personen"));
			$accnaam=ucfirst($this->config->soortaccommodatie[$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".$aantalpersonen;
			$type_data[$type_id]["fullname"] = $accnaam;

			// description
			unset($description);
			if($db->f("omschrijving") or $db->f("tomschrijving")) {
				$description=$db->f("omschrijving");
				if($db->f("omschrijving") and $db->f("tomschrijving")) {
					$description.="\n\n";
				}
				$description.=$db->f("tomschrijving");
				$type_data[$type_id]["description"] = $description;
			}

			// url
			$url=$this->config->basehref.txt("menu_accommodatie")."/".$db->f("begincode").$db->f( "type_id" )."/";
			$type_data[$type_id]["url"] = $url;

			// main-image
			$imgurl="";
			if(file_exists( $this->config->unixdir."pic/cms/types_specifiek/".$db->f( "type_id" ).".jpg") ) {
				$imgurl = $this->config->basehref."pic/cms/types_specifiek/".$db->f( "type_id" ).".jpg";
			} elseif( file_exists($this->config->unixdir."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg") ) {
				$imgurl = $this->config->basehref."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
			}
			if( $imgurl ) {
				$type_data[$type_id]["main_image"] = $imgurl;
			}

			// additional images
			$foto = imagearray(array("accommodaties_aanvullend","types","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed","types_breed"),array($db->f("accommodatie_id"),$db->f( "type_id" ),$db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f( "type_id" )),"../");
			if( is_array($foto["pic"]) ) {
				$fototeller=0;
				while(list($key,$value)=each($foto["pic"])) {
					$fototeller++;
					$type_data[$type_id]["extra_image"][$fototeller] = $this->config->basehref."pic/cms/".$value;
				}
			}

			// kwaliteit
			if($db->f("kwaliteit") or $db->f("tkwaliteit")) {
				if($db->f("tkwaliteit")) {
					$kwaliteit=$db->f("tkwaliteit");
				} else {
					$kwaliteit=$db->f("kwaliteit");
				}
				$type_data[$type_id]["kwaliteit"] = $kwaliteit;
			}

			// kenmerken
			$kenmerken = new kenmerken();
			$this->type_kenmerken[$type_id] = $kenmerken->get_kenmerken($typeid,
					array(
						"type"=>$db->f( "kenmerken_type" ),
						"accommodatie"=>$db->f( "kenmerken_accommodatie" ),
						"plaats"=>$db->f( "kenmerken_plaats" ),
						"skigebied"=>$db->f( "kenmerken_skigebied" ),
						"toonper"=>$db->f( "toonper" ),
			));


			// Distances
			foreach ($doorloop_afstanden as $key => $value) {
				if($db->f($key)) {
					$type_data[$type_id]["distance"][$value] = $this->showDistance($db->f($key), $db->f($key."extra"), txt("meter","toonaccommodatie"));
				}
			}
		}


		// reviews
		if( $this->config->websitetype==7 ) {
			// Italissima: combine reviews from other types of the same accommodation
			$db->query("SELECT DISTINCT t.type_id, ROUND(AVG(be.vraag1_7), 1) AS totaaloordeel FROM boeking_enquete be, type t WHERE be.type_id=t.type_id AND be.vraag1_7>0 AND be.beoordeeld=1 AND be.type_id IN (".$type_id_inquery.") GROUP BY t.accommodatie_id HAVING COUNT(be.type_id)>1;");
		} else {
			// Other sites: only show reviews from this type
			$db->query("SELECT type_id, ROUND(AVG(vraag1_7), 1) AS totaaloordeel, COUNT(type_id) AS aantal FROM boeking_enquete WHERE vraag1_7>0 AND beoordeeld=1 AND type_id IN (".$type_id_inquery.") GROUP BY type_id HAVING COUNT(type_id)>1;");
		}
		while( $db->next_record() ) {
			if( isset( $type_data[$db->f( "type_id" )] )) {
				$type_data[$db->f( "type_id" )]["totaaloordeel"] = $db->f( "totaaloordeel" );
			}
		}

		//
		// kenmerken to show seperately
		//
		$this->facilites_show_array = array(
			"aan-de-piste",
			"catering-mogelijk",
			"jacuzzi-bubbelbad",
			"open-haard-houtkachel",
			"sauna-prive",
			"sauna-gemeenschappelijk",
			"wasmachine",
			"zwembad-prive",
			"airconditioning",
			"balkon-prive",
			"barbecue",
			"omheinde-tuin",
			"speeltoestellen",
			"tennisbaan",
			"tuin-terras-prive",
			"tuin-terras-of-balkon",
			"wellness-faciliteiten"
		);


		//
		// prices
		//

		$bijkomendekosten = new bijkomendekosten;
		$bk = $bijkomendekosten->get_complete_cache($this->config->seizoentype);

		// vertrekdagaanpassing
		$vertrekdag = new vertrekdagaanpassing($type_id_inquery);



		//
		// losse accommodaties
		//
		if ( $this->use_wederverkoop_prices ) {
			$price_field = "wederverkoop_verkoopprijs";
		} else {
			$price_field = "c_verkoop_site";
		}

		$query = "SELECT type_id, ".$price_field." AS prijs, week, seizoen_id, aanbiedingskleur_korting, aanbieding_acc_percentage, aanbieding_acc_euro, kortingactief, toonexactekorting, voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_xml, voorraad_request, voorraad_optie_klant FROM tarief WHERE type_id IN (".$type_id_inquery_acc.") AND week>'".(time()+604800)."' AND ".$price_field.">0";

		if ( $this->must_be_available ) {
			$query .= " AND beschikbaar=1";
		}
		if ( $this->use_wederverkoop_prices ) {
			$query .= " AND blokkeren_wederverkoop=0";
		}

		$query .= " ORDER BY type_id, week;";

		$db->query($query.";");
		while($db->next_record()) {

			unset($prijs);

			if (!$seizoen_gehad[$db->f( "seizoen_id" )]) {
				$seizoen_id_inquery .= ",".$db->f( "seizoen_id" );
				$seizoen_gehad[$db->f( "seizoen_id" )] = true;
			}

			$week_seizoen_id[$db->f( "week" )] = $db->f( "seizoen_id" );

			$prijs = $db->f("prijs");

			$bk_add_to_price = $bk[$db->f( "type_id" )][$db->f( "seizoen_id" )][1];
			$prijs += $bk_add_to_price;

			if( $prijs>0 ) {
				$this->type_price[$db->f( "type_id" )][$db->f( "week" )] = round($prijs, 2);

				// Maximale korting bepalen
				if($db->f("kortingactief") and $db->f("aanbiedingskleur_korting") and $db->f("toonexactekorting")) {
					if($db->f("aanbieding_acc_percentage")>0) {
						if($korting_percentage[$db->f( "type_id" )]<$db->f("aanbieding_acc_percentage")) $korting_percentage[$db->f( "type_id" )]=$db->f("aanbieding_acc_percentage");
					}
					if($db->f("aanbieding_acc_euro")>0) {
						if($korting_euro[$db->f( "type_id" )]<$db->f("aanbieding_acc_euro")) $korting_euro[$db->f( "type_id" )]=$db->f("aanbieding_acc_euro");
					}
				}

				// availability
				$stock = array(
				   "voorraad_garantie" => $db->f( "voorraad_garantie" ),
				   "voorraad_allotment" => $db->f( "voorraad_allotment" ),
				   "voorraad_vervallen_allotment" => $db->f( "voorraad_vervallen_allotment" ),
				   "voorraad_optie_leverancier" => $db->f( "voorraad_optie_leverancier" ),
				   "voorraad_xml" => $db->f( "voorraad_xml" ),
				   "voorraad_request" => $db->f( "voorraad_request" ),
				   "voorraad_optie_klant" => $db->f( "voorraad_optie_klant" ),
				);
				$this->type_availability[$db->f( "type_id" )][$db->f( "week" )] = $this->get_availability( $stock );
			}
		}

		//
		// arrangementen
		//
		$db->query("SELECT t.type_id, t.maxaantalpersonen, tp.prijs AS prijs, ta.week, ta.seizoen_id, ta.aanbiedingskleur_korting, ta.aanbieding_acc_percentage, ta.aanbieding_acc_euro, ta.kortingactief, ta.toonexactekorting, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_optie_klant FROM tarief ta, tarief_personen tp, type t WHERE t.type_id IN (".$type_id_inquery_arr.") AND tp.week>'".(time()+604800)."' AND tp.prijs>0 AND tp.personen=t.maxaantalpersonen AND ta.beschikbaar=1 AND ta.type_id=tp.type_id AND ta.type_id=t.type_id AND ta.week=tp.week AND ta.seizoen_id=tp.seizoen_id ORDER BY type_id, ta.week;");
		while($db->next_record()) {

			unset($prijs);

			if (!$seizoen_gehad[$db->f( "seizoen_id" )]) {
				$seizoen_id_inquery .= ",".$db->f( "seizoen_id" );
				$seizoen_gehad[$db->f( "seizoen_id" )] = true;
			}

			$week_seizoen_id[$db->f( "week" )] = $db->f( "seizoen_id" );

			$prijs = $db->f("prijs");

			$bk_add_to_price = $bk[$db->f( "type_id" )][$db->f( "seizoen_id" )][$db->f( "maxaantalpersonen" )] / $db->f( "maxaantalpersonen" );
			$prijs += $bk_add_to_price;

			if( $prijs>0 ) {
				$this->type_price[$db->f( "type_id" )][$db->f( "week" )] = round($prijs, 2);

				// Maximale korting bepalen
				if($db->f("kortingactief") and $db->f("aanbiedingskleur_korting") and $db->f("toonexactekorting")) {
					if($db->f("aanbieding_acc_percentage")>0) {
						if($korting_percentage[$db->f( "type_id" )]<$db->f("aanbieding_acc_percentage")) $korting_percentage[$db->f( "type_id" )]=$db->f("aanbieding_acc_percentage");
					}
				}
			}
		}

		if (is_array($this->type_price) ) {
			foreach ($this->type_price as $type_id => $value) {

				$vanafprijs = min($value);

				if ( $type_data[$type_id]["toonper"]==3 or $this->config->wederverkoop ) {
					$type_data[$type_id]["vanaf"] = txt("vanafeuroperaccommodatie", "xml", array("v_bedrag"=>number_format($vanafprijs,2,",",".")));
					$type_data[$type_id]["price_text"] = txt("peraccommodatie", "xml");
				} else {
					$type_data[$type_id]["vanaf"] = txt("vanafeuroperpersoon", "xml", array("v_bedrag"=>number_format($vanafprijs,2,",",".")));
					$type_data[$type_id]["price_text"] = txt("ppinclusiefskipas", "xml");
				}

				// arrival / departure
				unset( $aantalnachten_afwijking );
				foreach ($value as $week => $prijs) {

					$this->type_arrival[$type_id][$week] = $vertrekdag->get_arrival_unixtime($type_id, $week_seizoen_id[$week], $week);
					$this->type_number_of_nights[$type_id][$week] = $vertrekdag->get_number_of_nights($type_id, $week_seizoen_id[$week], $week );

					$this->type_departure[$type_id][$week] = mktime(0, 0, 0, date("m", $this->type_arrival[$type_id][$week]), date("d", $this->type_arrival[$type_id][$week]) + $this->type_number_of_nights[$type_id][$week], date("Y", $this->type_arrival[$type_id][$week]));

				}
			}
		}


		// Discounts
		if (is_array($type_data)) {
			foreach ($type_data as $type_id => $value) {
				if($korting_percentage[$type_id]) {
					$this->type_data[$type_id]["special_offer"]["percentage"] = floor($korting_percentage[$type_id]);
					$this->type_data[$type_id]["special_offer"]["description"] = txt("boek-nu-met-korting-tot", "xml", array("v_korting"=>floor($korting_percentage[$type_id])."%"));
				} elseif($korting_euro[$type_id]) {

					$this->type_data[$type_id]["special_offer"]["euro"] = floor($korting_euro[$type_id]);
					$this->type_data[$type_id]["special_offer"]["description"] = txt("boek-nu-met-korting-tot", "xml", array("v_korting"=>$korting_euro[$type_id]." ".txt("euro", "xml")));
				} elseif($this->aanbieding) {
					$this->type_data[$type_id]["special_offer"]["description"] = txt("boek-nu-met-korting", "xml");
				}
			}
		}


		//
		// additional costs (bijkomende kosten)
		//
		$db->query("SELECT bt.type_id, bt.seizoen_id, bs.bk_soort_id, bs.naam".$this->config->ttv." AS naam,
			bt.bedrag, bt.verplicht, bt.ter_plaatse, eenheid
			FROM bk_type bt
			INNER JOIN bk_soort bs USING (bk_soort_id)
			WHERE
				bt.seizoen_id IN (".$seizoen_id_inquery.") AND
				bt.type_id IN (".$type_id_inquery.") AND
				(bs.min_leeftijd IS NULL OR bs.zonderleeftijd=1) AND
				(bt.verplicht=1 OR bt.inclusief=1) AND
				altijd_diversen=0
			ORDER BY bt.type_id, bs.volgorde;");

		while( $db->next_record() ) {
			if ( $db->f( "verplicht" )==1 and $db->f( "ter_plaatse" )==1 and $db->f( "bedrag" )==0) {
				// verplicht ter plaatse, no amount know: don't add to "included"-list
				$in_ex_type = 'excluded';
			} elseif($db->f( "bedrag" )>0 and constant("include_bkk")===false) {
				$in_ex_type = 'excluded';
			} else {
				$in_ex_type = 'included';
			}
			if ($in_ex_type) {
				$type_bkk[$db->f( "type_id" )][$db->f( "seizoen_id" )][$in_ex_type][$db->f( "bk_soort_id" )] = $db->f( "naam" );

				if ($in_ex_type === 'excluded') {
					$type_bkk[$db->f( "type_id" )][$db->f( "seizoen_id" )]['excluded_price'][$db->f( "bk_soort_id" )] = $db->f( "bedrag" );
					$type_bkk[$db->f( "type_id" )][$db->f( "seizoen_id" )]['unit'][$db->f( "bk_soort_id" )]           = $this->config->bk_eenheid[$db->f( "eenheid" )];
				}
			}
		}

		//
		// skipas names
		//
		$db->query("SELECT skipas_id, website_omschrijving".$this->config->ttv." AS website_omschrijving FROM skipas WHERE skipas_id IN (".$skipas_id_inquery.");");
		while( $db->next_record() ) {
			$skipas[$db->f( "skipas_id" )] = $db->f( "website_omschrijving" );
		}

		//
		// convert to utf-8
		//
		if (is_array($type_data)) {
			foreach ($type_data as $type_id => $value) {

				foreach ($value as $key => $value2) {
					if( is_array($value2) ) {
						foreach ($value2 as $key2 => $value3) {
							$this->type_data[$type_id][$key][$key2] = iconv("Windows-1252", "UTF-8", $value3);
						}
					} else {
						$this->type_data[$type_id][$key] = iconv("Windows-1252", "UTF-8", $value2);
					}
				}

				if (is_array($type_bkk[$type_id]) ) {
					foreach ($type_bkk[$type_id] as $key => $value2) {

						// skipas
						if( $skipas[ $type_data[$type_id]["skipas_id"] ] ) {
							$this->type_bkk[$type_id][$key]["included"]["skipas"] = iconv("Windows-1252", "UTF-8", $skipas[ $type_data[$type_id]["skipas_id"] ]);
						}

						foreach ($value2 as $key2 => $value3) {
							foreach ($value3 as $key3 => $value4) {
								$this->type_bkk[$type_id][$key][$key2][$key3] = iconv("Windows-1252", "UTF-8", $value4);
							}
						}

						// reserveringskosten
						if($db->f( "bedrag" )>0 and constant("include_bkk")===true) {
							$this->type_bkk[$type_id][$key]["included"]["reserveringskosten"] 		= iconv("Windows-1252", "UTF-8", txt("reserveringskosten", "xml"));
						} else {
							$this->type_bkk[$type_id][$key]["excluded"]["reserveringskosten"] 		= iconv("Windows-1252", "UTF-8", txt("reserveringskosten", "xml"));
							$this->type_bkk[$type_id][$key]['excluded_price']['reserveringskosten'] = $this->config->reserveringskosten;
						}
					}
				}
			}
		}
	}

	/**
	 * check if all needed input is provided. If not: trigger error
	 * check for:
	 * - $this->name
	 * - $this->website
	 *
	 * @return void
	 */
	protected function check_input()
	{

		if( ! $this->name ) {
			trigger_error( "XML-export var name not set",E_USER_NOTICE );
			exit;
		}

		if( ! $this->website ) {
			trigger_error( "XML-export: var website not set",E_USER_NOTICE );
			exit;
		}
	}


	/**
	 * show distance text (combine distance with additional text and a unit)
	 *
	 * @param string the actual distance
	 * @param string additional text (optional)
	 * @param string unit (e.g. meters)
	 * @return void
	 */
	protected function showDistance($afstand, $extra, $maat) {
		$return=$afstand;
		if($extra) {
			if(ereg("^[0-9]+$",$extra)) {
				$return.=" - ".$extra." ".$maat;
			} else {
				$return.=" ".$maat." (".$extra.")";
			}
		} else {
			$return.=" ".$maat;
		}
		return $return;
	}

	/**
	 * calculate the availability
	 *
	 * @param array with stock
	 * @return string (directly or request)
	 */
	private function get_availability( $stock )
	{

		if($stock["voorraad_garantie"]+$stock["voorraad_allotment"]+$stock["voorraad_vervallen_allotment"]+$stock["voorraad_optie_leverancier"]+$stock["voorraad_xml"]-$stock["voorraad_optie_klant"]>=1) {
			$availability = "directly";
		} elseif($stock["voorraad_request"]>=1 or $stock["voorraad_optie_klant"]>=1) {
			$availability = "request";
		}

		if( $availability ) {
			return $availability;
		} else {
			return false;
		}

	}


	/**
	 * get the name of the cachefile
	 * include:
	 * - aanbieding yes/no
	 * - website-letter
	 *
	 * @return string full path of the cachefile
	 */
	private function cacheFilename() {

		$name = $this->name;

		if( $this->aanbieding ) {
			$name .= "-aanbieding";
		}

		$filename = $this->config->unixdir."cache/feed-new-".$name."-".$this->config->website.".xml";

		return $filename;

	}


	/**
	 * check input, query database, invoke XMLWriter object
	 * and call child-function createSpecificXML()
	 *
	 * @return void
	 */
	private function createXML()
	{

		$this->check_input();
		$this->query_database();

		$this->x = new XMLWriter;

		if( $this->save_cache ) {
			$this->x->openURI( $this->cacheFilename()."_tmp" );
		} else {
			$this->x->openMemory();
		}

		$this->x->setIndent(true);
		$this->x->startDocument('1.0','UTF-8');

		$xml = $this->createSpecificXML();


		if( !$this->save_cache ) {
			return $this->x->outputMemory();
		}

		$this->x->endDocument();
		$this->x->flush();

		if( $this->save_cache ) {

			if( file_exists( $this->cacheFilename()."_tmp" )) {

				if( file_exists( $this->cacheFilename() )) {
					unlink( $this->cacheFilename() );
				}
				rename ($this->cacheFilename()."_tmp", $this->cacheFilename() );

				filesync::add_to_filesync_table($this->cacheFilename());

				return "cachefile ".$this->cacheFilename()." created";

			} else {

				$msg = "ERROR in creating ".$this->cacheFilename();
				trigger_error( $msg,E_USER_NOTICE );
				return $msg;
			}
		}
	}

	/**
	 * call createXML and echo the created XML
	 *
	 * @return void
	 */
	public function showXML()
	{

		if( ! $this->save_cache and file_exists($this->cacheFilename() ) ) {
			$xml = file_get_contents( $this->cacheFilename() );
		} else {
			$xml = $this->createXML();
		}

		if( ! $this->save_cache ) {
			if( $this->config->lokale_testserver ) {
				// header("Content-Type: text/plain; charset=utf-8");
				header("Content-Type: text/xml; charset=utf-8");
			} else {
				header("Content-Type: text/xml; charset=utf-8");
			}
		}

		echo $xml;

	}

}

