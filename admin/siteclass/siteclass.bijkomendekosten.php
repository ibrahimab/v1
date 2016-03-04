<?php

use Chalet\RedisInterface;
use Chalet\AdditionalCosts\WeekDependentPriceRenderer;

/**
 * Additional Costs (bijkomende kosten - bkk)
 *
 * - enter and save them (through the CMS)
 * - (pre)calculate them
 * - request them
 * - render html to show them
 *
 * Summary: way too much for just one class :-)
 *
 * @todo: refactor this bastard
 *
 * @package Chalet
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 **/

class bijkomendekosten {

	private $combine_all_rows_for_log_first_item = true;
	public $arrangement = false;
	public $zoek_en_boek_popup = false;
	public $pre_calculate = false;

	/**
	 * whether the class is used by the new website (through the api)
	 *
	 * @var boolean
	 **/
	public $newWebsite = false;

	function __construct($id=0, $soort="type") {

		$this->id = $id;
		$this->soort = $soort;
	}

	public function setRedis(RedisInterface $redis)
	{
		$this->redis = $redis;
	}

	private function clear() {
		//
		// delete all vars of this class
		//
		foreach ($this as &$value) {
			if(!is_object($value)) {
				$value = null;
			}
		}

	}

	private function get_data() {

		// get data from database for 1 type or accommodation

		if(!$this->get_data_done) {

			global $vars;

			unset($this->wzt, $this->maxaantalpersonen, $this->cms_data_seizoenen, $this->seizoen_inquery, $this->data);

			$db = new DB_sql;

			// get data from database

			// wzt
			$db->query("SELECT wzt, maxaantalpersonen FROM view_accommodatie WHERE ".$this->soort."_id='".intval($this->id)."';");
			if($db->next_record()) {
				$this->wzt = $db->f("wzt");
				$this->maxaantalpersonen = $db->f("maxaantalpersonen");
			}

			// get seasons
			$this->seizoen_inquery .= ",0";
			$db->query("SELECT seizoen_id, naam".$vars["ttv"]." AS naam FROM seizoen WHERE ".($this->seasons_use_old_dates ? "1=1" : "eind>=(NOW() - INTERVAL 1 DAY)")." AND type='".intval($this->wzt)."'".($this->seasons_hide_inactive ? " AND tonen<>1" : "")." ORDER BY begin, eind;");
			while($db->next_record()) {
				$this->cms_data_seizoenen[$db->f("seizoen_id")] = $db->f("naam");
				$this->seizoen_inquery .= ",".$db->f("seizoen_id");
			}


			if(is_array($this->cms_data_seizoenen)) {
				foreach ($this->cms_data_seizoenen as $key => $value) {
					$db->query("SELECT bs.bk_soort_id, bs.naam".$vars["ttv"]." AS naam, bs.vouchernaam".$vars["ttv"]." AS vouchernaam, bs.factuurnaam".$vars["ttv"]." AS factuurnaam, bs.altijd_invullen, bs.altijd_diversen, bs.altijd_diversen_indien_niet_inclusief, bs.prijs_per_nacht, bs.opgeven_bij_boeken, ba.".$this->soort."_id, bs.toelichting".$vars["ttv"]." AS toelichting, bs.zonderleeftijd, bs.min_leeftijd, bs.max_leeftijd, ba.seizoen_id, ba.inclusief, ba.verplicht, ba.ter_plaatse, ba.eenheid, ba.borg_soort, ba.bedrag
							   FROM bk_soort bs LEFT JOIN bk_".$this->soort." ba ON (bs.bk_soort_id=ba.bk_soort_id AND ba.".$this->soort."_id='".intval($this->id)."' AND ba.seizoen_id='".intval($key)."') WHERE bs.wzt='".intval($this->wzt)."' AND (ba.".$this->soort."_id IS NOT NULL OR bs.altijd_invullen=1)
							   ORDER BY ba.borg_soort DESC, ba.inclusief DESC, bs.volgorde;");

					while($db->next_record()) {
						$seizoen_id = $key;

						$this->data[$seizoen_id][$db->f("bk_soort_id")]["naam"] = $db->f("naam");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["vouchernaam"] = $db->f("vouchernaam");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["factuurnaam"] = $db->f("factuurnaam");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["inclusief"] = $db->f("inclusief");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["verplicht"] = $db->f("verplicht");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["ter_plaatse"] = $db->f("ter_plaatse");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["eenheid"] = $db->f("eenheid");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["borg_soort"] = $db->f("borg_soort");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["bedrag"] = $db->f("bedrag");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["altijd_diversen"] = $db->f("altijd_diversen");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["altijd_diversen_indien_niet_inclusief"] = $db->f("altijd_diversen_indien_niet_inclusief");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["prijs_per_nacht"] = $db->f("prijs_per_nacht");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["opgeven_bij_boeken"] = $db->f("opgeven_bij_boeken");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["toelichting"] = $db->f("toelichting");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["zonderleeftijd"] = $db->f("zonderleeftijd");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["min_leeftijd"] = $db->f("min_leeftijd");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["max_leeftijd"] = $db->f("max_leeftijd");
						if($db->f("seizoen_id")) {
							$this->data[$seizoen_id][$db->f("bk_soort_id")]["filled"] = true;
						}
					}
				}
			}

			if($this->soort == "type") {

				// surcharge extra persons (toeslag extra personen)
				$db->query("SELECT
				           a.bijkomendekosten1_id, t.bijkomendekosten1_id AS tbijkomendekosten1_id,
				           a.bijkomendekosten2_id, t.bijkomendekosten2_id AS tbijkomendekosten2_id,
				           a.bijkomendekosten3_id, t.bijkomendekosten3_id AS tbijkomendekosten3_id,
				           a.bijkomendekosten4_id, t.bijkomendekosten4_id AS tbijkomendekosten4_id,
				           a.bijkomendekosten5_id, t.bijkomendekosten5_id AS tbijkomendekosten5_id,
				           a.bijkomendekosten6_id, t.bijkomendekosten6_id AS tbijkomendekosten6_id

				           FROM type t INNER JOIN accommodatie a USING (accommodatie_id)
				           WHERE t.type_id='".intval($this->id)."';");
				while($db->next_record()) {
					for($i=1;$i<=6;$i++) {
						if($db->f("bijkomendekosten".$i."_id")) {
							$bijkomendekosten_id_inquery .= ",".$db->f("bijkomendekosten".$i."_id");
						}
						if($db->f("tbijkomendekosten".$i."_id")) {
							$bijkomendekosten_id_inquery .= ",".$db->f("tbijkomendekosten".$i."_id");
						}
					}
				}

				if($bijkomendekosten_id_inquery) {

					if(constant("include_bkk")===true or $this->pre_calculate) {
						$only_zonderleeftijd = " AND (b.min_leeftijd IS NULL OR b.zonderleeftijd=1) AND (b.max_leeftijd IS NULL OR b.zonderleeftijd=1)";
					}

					// $db->query("SELECT b.bijkomendekosten_id, b.naam".$vars["ttv"]." AS naam, b.omschrijving".$vars["ttv"]." AS omschrijving, b.min_leeftijd, b.max_leeftijd, b.zonderleeftijd zonderleeftijd, bt.verkoop, bt.seizoen_id, bt.week, b.min_personen, b.max_personen FROM bijkomendekosten_tarief bt INNER JOIN bijkomendekosten b USING (bijkomendekosten_id) WHERE b.bijkomendekosten_id IN (".substr($bijkomendekosten_id_inquery, 1).") AND bt.seizoen_id IN (".substr($this->seizoen_inquery,1).") AND b.min_personen IS NOT NULL AND (b.min_leeftijd IS NULL OR b.zonderleeftijd=1) AND (b.max_leeftijd IS NULL OR b.zonderleeftijd=1) ORDER BY b.bijkomendekosten_id, bt.week;");
					$db->query("SELECT b.bijkomendekosten_id, b.naam".$vars["ttv"]." AS naam, b.omschrijving".$vars["ttv"]." AS omschrijving, b.min_leeftijd, b.max_leeftijd, b.zonderleeftijd zonderleeftijd, bt.verkoop, bt.seizoen_id, bt.week, b.min_personen, b.max_personen FROM bijkomendekosten_tarief bt INNER JOIN bijkomendekosten b USING (bijkomendekosten_id) WHERE b.bijkomendekosten_id IN (".substr($bijkomendekosten_id_inquery, 1).") AND bt.seizoen_id IN (".substr($this->seizoen_inquery,1).") AND b.min_personen IS NOT NULL".$only_zonderleeftijd." ORDER BY b.bijkomendekosten_id, bt.week;");
					while($db->next_record()) {

						$seizoen_id = $db->f("seizoen_id");

						if(!$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]) {
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["naam"] = $db->f("naam");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["omschrijving"] = $db->f("omschrijving");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["min_personen"] = $db->f("min_personen");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["max_personen"] = $db->f("max_personen");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["min_leeftijd"] = $db->f("min_leeftijd");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["max_leeftijd"] = $db->f("max_leeftijd");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["zonderleeftijd"] = $db->f("zonderleeftijd");
						}
						$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["verkoop"][$db->f("week")] = $db->f("verkoop");
					}
				}
			}

			$this->get_data_done = true;
		}
	}

	private function get_cms_data() {


		$this->get_data();


		// get data from database, used for the CMS

		if(!$this->get_cms_data_done) {

			global $vars;

			$db = new DB_sql;

			// get bijkomendekosten_checked
			$db->query("SELECT seizoen_id, bijkomendekosten_checked FROM ".$this->soort."_seizoen WHERE ".$this->soort."_id='".intval($this->id)."' AND seizoen_id IN (".substr($this->seizoen_inquery,1).");");
			while($db->next_record()) {
				$this->cms_data_bijkomendekosten_checked[$db->f("seizoen_id")] = $db->f("bijkomendekosten_checked");
			}

			$db->query("SELECT bk_soort_id, volgorde, naam".$vars["ttv"]." AS naam, eenheden, altijd_invullen, prijs_per_nacht, borg, hoort_bij_accommodatieinkoop FROM bk_soort WHERE wzt='".intval($this->wzt)."' ORDER BY volgorde, bk_soort_id;");
			while($db->next_record()) {
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["volgorde"] = $db->f("volgorde");
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["naam"] = $db->f("naam").($db->f("prijs_per_nacht") ? " (per nacht)" : "");

				if($db->f("eenheden")) {
					$eenheden=preg_split("@,@",$db->f("eenheden"));
					foreach ($eenheden as $key => $value) {
						$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["eenheden"][$value] = true;
					}
				}

				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["altijd_invullen"] = $db->f("altijd_invullen");
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["prijs_per_nacht"] = $db->f("prijs_per_nacht");
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["borg"] = $db->f("borg");
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["hoort_bij_accommodatieinkoop"] = $db->f("hoort_bij_accommodatieinkoop");
			}


			// check for differences between accommodatie and type
			if($this->soort=="accommodatie") {

				// get all types
				$db->query("SELECT begincode, type_id, optimaalaantalpersonen, maxaantalpersonen FROM view_accommodatie WHERE accommodatie_id='".intval($this->id)."';");
				while($db->next_record()) {
					$this->all_types[$db->f("type_id")] = $db->f("begincode").$db->f("type_id");
					$this->all_types_aantalpersonen[$db->f("type_id")] = $db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : ""). " p";
				}

				$db->query("SELECT bs.bk_soort_id, bs.naam".$vars["ttv"]." AS naam, bs.altijd_invullen, bt.seizoen_id, bt.inclusief, bt.verplicht, bt.ter_plaatse, bt.eenheid, bt.borg_soort, bt.bedrag, t.type_id, t.begincode FROM bk_type bt INNER JOIN bk_soort bs USING (bk_soort_id) INNER JOIN view_accommodatie t USING (type_id) WHERE t.accommodatie_id='".intval($this->id)."' AND bt.seizoen_id IN (".substr($this->seizoen_inquery,1).")
						   ORDER BY bt.type_id, bt.inclusief DESC, bs.volgorde;");

				while($db->next_record()) {
					foreach ($this->cms_data_seizoenen as $key => $value) {
						if($db->f("seizoen_id")) {
							$seizoen_id = $db->f("seizoen_id");
						} else {
							$seizoen_id = $key;
						}

						$this->cms_data_bk_soorten_type[$db->f("bk_soort_id")]["naam"] = $db->f("naam");


						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["naam"] = $db->f("naam");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["type"] = $db->f("begincode").$db->f("type_id");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["inclusief"] = $db->f("inclusief");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["verplicht"] = $db->f("verplicht");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["ter_plaatse"] = $db->f("ter_plaatse");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["eenheid"] = $db->f("eenheid");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["borg_soort"] = $db->f("borg_soort");
						$this->type_data[$seizoen_id][$db->f("bk_soort_id")][$db->f("type_id")]["bedrag"] = $db->f("bedrag");

					}
				}
			}

			$this->get_cms_data_done = true;
		}
	}

	private function cms_add_cost_type() {
		$return .= "<div class=\"cms_bk_add\">";
		$return .= "Bijkomende kosten toevoegen:<br/><select name=\"bk_new\">";
		$return .= "<option value=\"0\">-- kies toe te voegen kostensoort --</option>";

		if(is_array($this->cms_data_bk_soorten)) {
			foreach ($this->cms_data_bk_soorten as $key => $value) {
				$return .= "<option value=\"".$key."\">".wt_he($value["naam"])."</option>";
			}
		}

		$return .= "</select>";
		$return .= "</div>"; // close .cms_bk_add


		return $return;

	}

	public function cms_enter_costs_per_acc_type() {

		global $vars, $login;

		// hide seasons that are inactive
		$this->seasons_hide_inactive = true;

		$this->get_cms_data();

		$db = new DB_sql;


		if(is_array($this->cms_data_seizoenen))	{
			foreach ($this->cms_data_seizoenen as $key => $value) {

				unset($kopieer_seizoen_id, $kopieer_button_text, $inclusief_tekst_html, $exclusief_tekst_html, $in_exclusief_tekst);

				$this->seizoen_id = $key;

				if(!$this->seizoen_counter) {
					$return .= "<div id=\"bijkomendekosten\"></div>";
				}

				if($this->soort=="accommodatie") {

					if($this->seizoen_counter==1) {
						$kopieer_button_text = "&#x2193; kopieer bijkomende kosten naar onderstaand seizoen &#x2193;";
						$kopieer_seizoen_id = $this->last_seizoen_id;

					} elseif(count($this->cms_data_seizoenen)==1) {

						// get previous seizoen_id
						$db->query("SELECT ba.seizoen_id, s.naam FROM bk_accommodatie ba INNER JOIN seizoen s USING(seizoen_id) WHERE ba.seizoen_id<".$this->seizoen_id." AND ba.accommodatie_id='".intval($this->id)."' ORDER BY ba.seizoen_id DESC LIMIT 0,1;");
						if( $db->next_record() ) {
							$kopieer_button_text = "kopieer bijkomende kosten van vorig seizoen (".wt_he($db->f("naam")).")";
							$kopieer_seizoen_id = $db->f("seizoen_id");
						}
					}
					if( $kopieer_button_text and $kopieer_seizoen_id ) {
						// copy season button
						$return .= "<div class=\"cms_bk_kopieer cms_bk_kopieer_season\" data-last_seizoen_id=\"".intval($kopieer_seizoen_id)."\" data-seizoen_id=\"".intval($this->seizoen_id)."\" data-id=\"".intval($this->id)."\"><button>".$kopieer_button_text."</button>&nbsp;&nbsp;<img src=\"".$this->getImageRoot()."pic/ajax-loader-ebebeb.gif\"><br />&nbsp;&nbsp;&nbsp;<i>Let op: bestaande gegevens worden direct overschreven.</i></div>";
					}
				}

				$this->seizoen_counter++;
				$this->last_seizoen_id = $this->seizoen_id;


				$return .= "<div class=\"cms_bk_seizoen cms_bk_seizoen_".($this->seizoen_counter==1 ? "first" : "nth")."\" data-seizoen_id=\"".$key."\" id=\"bijkomendekosten_".$key."\"><h2>Bijkomende kosten ".wt_he($value).($this->soort=="type" ? " - type-niveau" : "")."</h2>";



				if($this->soort=="type") {
					$db->query("SELECT bk_opmerkingen_intern FROM accommodatie WHERE accommodatie_id=(SELECT accommodatie_id FROM type WHERE type_id='".intval($this->id)."');");
				} else {
					$db->query("SELECT bk_opmerkingen_intern FROM accommodatie WHERE accommodatie_id='".intval($this->id)."';");
				}
				if($db->next_record()) {
					$bk_opmerkingen_intern = $db->f("bk_opmerkingen_intern");
				}

				$return .= "<form method=\"post\">";
				$return .= "<input type=\"hidden\" name=\"seizoen_id\" value=\"".$key."\" />";
				$return .= "<input type=\"hidden\" name=\"soort\" value=\"".$this->soort."\" />";
				$return .= "<input type=\"hidden\" name=\"id\" value=\"".$this->id."\" />";

				$return .= "<div class=\"cms_bk_header_left\">";

				$return .= "<div class=\"cms_bk_kopieer\">";
				$return .= "Kopieer bijkomende kosten van: <input type=\"text\" name=\"copy_from_type\" autocomplete=\"off\" placeholder=\"type-code\">";
				$return .= "<button>kopieer kosten &raquo;</button>&nbsp;&nbsp;<img src=\"".$this->getImageRoot()."pic/ajax-loader-ebebeb.gif\">";
				$return .= "</div>";



				$return .= $this->cms_add_cost_type();

				$return .= "</div>"; // close .cms_bk_headerblock

				$return .= "<div class=\"cms_bk_opmerkingen_intern\">";
				$return .= "<h3>Interne opmerkingen bijkomende kosten</h3>";
				$return .= "<textarea onfocus=\"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')\">".wt_he($bk_opmerkingen_intern)."</textarea>";
				$return .= "</div>"; // close .cms_bk_opmerkingen_intern

				$return .= "<div class=\"clear\"></div>";



				$return .= "<div class=\"cms_bk_all_rows_wrapper\">";
				$return .= $this->cms_all_rows();
				$return .= "</div>"; // close .cms_bk_all_rows_wrapper

				$return .= "<div style=\"text-align:right;margin-top:15px;margin-bottom:15px;\"><input type=\"checkbox\" name=\"bijkomendekosten_checked\" value=\"1\" id=\"bijkomendekosten_checked_".$key."\"".($this->cms_data_bijkomendekosten_checked[$key] ? " checked" : "")."><label for=\"bijkomendekosten_checked_".$key."\">&nbsp;alle bijkomende kosten van ".($this->soort=="type" ? "dit type" : "deze accommodatie")." (".wt_he($value).") zijn gecontroleerd&nbsp;</label></div>";

				$return .= "<input type=\"submit\" value=\"OPSLAAN\"><img src=\"".$this->getImageRoot()."pic/ajax-loader-transparent.gif\" class=\"ajaxloader\">";
				$return .= "<div class=\"clear\"></div>";

				if($this->other_type_data) {
					$return .= "<div class=\"cms_bk_type_afwijkingen_overschrijven\"><input type=\"checkbox\" name=\"type_afwijkingen_overschrijven\" id=\"type_afwijkingen_overschrijven_".$key."\"><label for=\"type_afwijkingen_overschrijven_".$key."\">&nbsp;rode type-afwijkingen overschrijven</label></div>";
				}

				$return .= "<input type=\"hidden\" name=\"all_rows_for_log\" value=\"".wt_he($this->all_rows_for_log[$this->seizoen_id])."\" />";

				$return .= "</form>";

				$return .= "</div>"; // close .cms_bk_seizoen
			}
		}


		return $return;

	}

	public function cms_all_rows() {

		$this->get_cms_data();


		global $vars;

		$return .= "<div class=\"cms_bk_all_rows\">";

		$return .= "<div class=\"cms_bk_row cms_bk_row_header\" data-soort_id=\"0\">";
		$return .= "<div>Optie</div>";
		$return .= "<div>Incl./Excl.</div>";
		$return .= "<div>Verplicht</div>";
		$return .= "<div>Betaling</div>";
		$return .= "<div>Bedrag</div>";
		$return .= "<div>Eenheid</div>";
		$return .= "<div>&nbsp;</div>";
		$return .= "</div>"; // close .cms_bk_row


		$check_fields_other_type_data = array( "inclusief", "verplicht", "ter_plaatse", "eenheid", "borg_soort", "bedrag" );


		if(is_array($this->data[$this->seizoen_id])) {
			foreach ($this->data[$this->seizoen_id] as $key => $value) {
				$return .= $this->cms_new_row($key);

				$soort_id_gehad[$key] = true;

				if(is_array($this->all_types)) {
					foreach ($this->all_types as $key2 => $value2) {
						$other_type_data = false;
						$other_type_empty = false;

						if(is_array($this->type_data[$this->seizoen_id][$key][$key2])) {
							foreach ($this->type_data[$this->seizoen_id][$key][$key2] as $key3 => $value3) {
								if(in_array($key3, $check_fields_other_type_data) and $value3<>$value[$key3]) {
									$other_type_data = true;
								}
							}
						} elseif($this->data[$this->seizoen_id][$key]["filled"]) {
							$other_type_data = true;
							$other_type_empty = true;
						}

						if($other_type_data) {
							// costs that differ from type-level

							$this->other_type_data = true;
							$return .= "<div class=\"cms_bk_row cms_bk_row_afwijkend_type\" data-soort_id=\"".$key."\">";
							$return .= "<div>afwijking type <a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".intval($_GET["wzt"])."&archief=".intval($_GET["archief"])."&1k0=".intval($_GET["1k0"])."&2k0=".$key2."#bijkomendekosten_".$this->seizoen_id."\" target=\"_blank\">".$value2."</a> (".$this->all_types_aantalpersonen[$key2].")".($other_type_empty ? ": kosten gewist" : "")."</div>";
							$return .= "<div>".wt_he($vars["bk_inclusief"][$this->check_for_differences_type_accommodation("inclusief", $key, $key2)])."</div>";
							$return .= "<div>".wt_he($vars["bk_verplicht"][$this->check_for_differences_type_accommodation("verplicht", $key, $key2)])."</div>";
							if($this->cms_data_bk_soorten[$key]["borg"]) {
								$return .= "<div>".wt_he($vars["bk_borg_soort_cms"][$this->check_for_differences_type_accommodation("borg_soort", $key, $key2)])."</div>";
							} else {
								$return .= "<div>".wt_he($vars["bk_ter_plaatse_cms"][$this->check_for_differences_type_accommodation("ter_plaatse", $key, $key2)])."</div>";
							}
							$return .= "<div class=\"cms_bk_bedrag\">".wt_he($this->check_for_differences_type_accommodation("bedrag", $key, $key2))."</div>";
							$return .= "<div>".wt_he($vars["bk_eenheid_cms"][$this->check_for_differences_type_accommodation("eenheid", $key, $key2)])."</div>";
							$return .= "<div>&nbsp;</div>";
							$return .= "</div>"; // close .cms_bk_row
						}
					}
				}
			}
		}

		// costs that exist on type-level but not on accommodation-level
		if(is_array($this->type_data[$this->seizoen_id])) {
			foreach ($this->type_data[$this->seizoen_id] as $key => $value) {
				if(!$soort_id_gehad[$key]) {

					// $return .= "<div class=\"cms_bk_row".($empty ? " cms_bk_new_row cms_bk_to_be_filled" : "")."\" data-soort_id=\"".$bk_soort_id."\">";
					$return .= "<div class=\"cms_bk_row cms_bk_row_afwijkend_accommodatie\" data-soort_id=\"".$key."\">";
					$return .= "<div>".wt_he($this->cms_data_bk_soorten_type[$key]["naam"])."</div>";

					$return .= "<div>&nbsp;</div>";
					$return .= "<div>&nbsp;</div>";
					$return .= "<div>&nbsp;</div>";
					$return .= "<div>&nbsp;</div>";
					$return .= "<div>&nbsp;</div>";
					$return .= "<div class=\"delete\" title=\"regel wissen\">&#xd7;</div>";
					// $return .= "<div>&nbsp;</div>";

					$return .= "</div>";


					foreach ($value as $key2 => $value2) {

						$this->other_type_data = true;
						$return .= "<div class=\"cms_bk_row cms_bk_row_afwijkend_type\" data-soort_id=\"".$key."\">";
						$return .= "<div>afwijking type <a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".intval($_GET["wzt"])."&archief=".intval($_GET["archief"])."&1k0=".intval($_GET["1k0"])."&2k0=".$key2."#bijkomendekosten_".$this->seizoen_id."\" target=\"_blank\">".$value2["type"]."</a> (".$this->all_types_aantalpersonen[$key2].") ".($other_type_empty ? ": kosten gewist" : "")."</div>";
						$return .= "<div>".wt_he($vars["bk_inclusief"][$this->check_for_differences_type_accommodation("inclusief", $key, $key2)])."</div>";
						$return .= "<div>".wt_he($vars["bk_verplicht"][$this->check_for_differences_type_accommodation("verplicht", $key, $key2)])."</div>";
						if($this->cms_data_bk_soorten[$key]["borg"]) {
							$return .= "<div>".wt_he($vars["bk_borg_soort_cms"][$this->check_for_differences_type_accommodation("borg_soort", $key, $key2)])."</div>";
						} else {
							$return .= "<div>".wt_he($vars["bk_ter_plaatse_cms"][$this->check_for_differences_type_accommodation("ter_plaatse", $key, $key2)])."</div>";
						}
						$return .= "<div>".wt_he($this->check_for_differences_type_accommodation("bedrag", $key, $key2))."</div>";
						$return .= "<div>".wt_he($vars["bk_eenheid_cms"][$this->check_for_differences_type_accommodation("eenheid", $key, $key2)])."</div>";
						$return .= "<div>&nbsp;</div>";
						$return .= "</div>"; // close .cms_bk_row
					}
				}
			}
		}

		$return .= "</div>"; // close .cms_bk_all_rows

		if($this->all_rows_for_log[$this->seizoen_id]) {
			$this->all_rows_for_log[$this->seizoen_id] = trim($this->all_rows_for_log[$this->seizoen_id]);
		}

		return $return;
	}

	public function cms_list_to_be_checked($wzt) {
		// list of all types/accommodations (per season) that have to be checked

		$db = new DB_sql;

		// get active seasons
		$db->query("SELECT naam, seizoen_id FROM seizoen WHERE type='".intval($wzt)."' AND eind>NOW() ORDER BY begin, eind;");
		while($db->next_record()) {
			$active_season_inquery .= ",".$db->f("seizoen_id");
			$active_season[$db->f("seizoen_id")] = $db->f("naam");
		}

		// get filled accommodations
		$db->query("SELECT DISTINCT seizoen_id, accommodatie_id FROM bk_accommodatie WHERE seizoen_id IN (".substr($active_season_inquery, 1).");");
		while($db->next_record()) {
			$filled_acc[$db->f("seizoen_id")][$db->f("accommodatie_id")] = true;
		}

		// get filled types
		$db->query("SELECT DISTINCT seizoen_id, type_id FROM bk_type WHERE seizoen_id IN (".substr($active_season_inquery, 1).");");
		while($db->next_record()) {
			$filled_type[$db->f("seizoen_id")][$db->f("type_id")] = true;
		}

		// get types with prices
		$db->query("SELECT DISTINCT seizoen_id, type_id FROM tarief WHERE seizoen_id IN (".substr($active_season_inquery, 1).");");
		while($db->next_record()) {
			$filled_type_prices[$db->f("seizoen_id")][$db->f("type_id")] = true;
		}

		// get checked accommodation_id's
		$db->query("SELECT seizoen_id, accommodatie_id FROM accommodatie_seizoen WHERE seizoen_id IN (".substr($active_season_inquery, 1).") AND bijkomendekosten_checked=1;");
		while($db->next_record()) {
			$checked_acc[$db->f("seizoen_id")][$db->f("accommodatie_id")] = true;
		}

		// get checked type_id's
		$db->query("SELECT seizoen_id, type_id FROM type_seizoen WHERE seizoen_id IN (".substr($active_season_inquery, 1).") AND bijkomendekosten_checked=1;");
		while($db->next_record()) {
			$checked_type[$db->f("seizoen_id")][$db->f("type_id")] = true;
		}


		$db->query("SELECT v.wzt, v.naam, v.plaats, v.type_id, v.accommodatie_id, v.begincode, a.inclusief, a.exclusief, t.inclusief AS tinclusief, t.exclusief AS texclusief, CASE WHEN a.bk_opmerkingen_intern IS NULL THEN 0 WHEN a.bk_opmerkingen_intern='' THEN 0 ELSE 1 END AS has_opmerkingen_intern FROM view_accommodatie v, type t, accommodatie a WHERE t.type_id=v.type_id AND a.accommodatie_id=v.accommodatie_id AND v.archief=0 AND v.atonen=1 AND v.ttonen=1 AND v.wzt='".intval($wzt)."' ORDER BY has_opmerkingen_intern, v.plaats, v.naam, v.accommodatie_id, t.type_id;");



		foreach ($active_season as $seizoen_id => $seizoen_naam) {

			//
			// without bk at all
			//
			$db->seek();
			unset($html, $al_gehad);
			while($db->next_record()) {
				if($filled_type_prices[$seizoen_id][$db->f("type_id")] and !$filled_type[$seizoen_id][$db->f("type_id")]) {
					if(!$al_gehad[$db->f("accommodatie_id")]) {
						if($al_gehad) $html .= "</li></ul>";
						$html .= "<li>";
						$html .= "<a href=\"".$vars["path"]."cms_accommodaties.php?show=1&wzt=".$db->f("wzt")."&archief=0&1k0=".$db->f("accommodatie_id")."#bijkomendekosten_".$seizoen_id."\" target=\"_blank\">";
						$html .= wt_he($db->f("plaats")." - ".$db->f("naam"));
						$html .= "</a>";
						if($db->f("has_opmerkingen_intern")) {
							$html .= "&nbsp;&nbsp;(<b>interne opmerkingen</b>)";
						}
						$html .= "<ul>";

						$al_gehad[$db->f("accommodatie_id")] = true;

					}
					$html .= "<li>";
					$html .= "<a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".$db->f("wzt")."&archief=0&1k0=".$db->f("accommodatie_id")."&2k0=".$db->f("type_id")."#bijkomendekosten_".$seizoen_id."\" target=\"_blank\">";
					$html .= wt_he($db->f("begincode").$db->f("type_id"));
					$html .= "</a>";
					$html .= "</li>";
				}
			}
			if($al_gehad) {
				$return .= "<p><b class=\"error\">Nog ontbrekende bijkomende kosten ".wt_he($seizoen_naam).":</b>";
				$return .= "<ol>".$html."</ol>";
				$return .= "<br /><hr /><br />";
			}


			//
			// bk to check
			//
			$db->seek();
			unset($html, $al_gehad);
			while($db->next_record()) {
				if($filled_acc[$seizoen_id][$db->f("accommodatie_id")] or $filled_type[$seizoen_id][$db->f("type_id")]) {
					if(!$checked_acc[$seizoen_id][$db->f("accommodatie_id")] or !$checked_type[$seizoen_id][$db->f("type_id")]) {
						if(!$al_gehad[$db->f("accommodatie_id")]) {
							if($al_gehad) $html .= "</li></ul>";
							$html .= "<li>";
							$html .= "<a href=\"".$vars["path"]."cms_accommodaties.php?show=1&wzt=".$db->f("wzt")."&archief=0&1k0=".$db->f("accommodatie_id")."#bijkomendekosten_".$seizoen_id."\" target=\"_blank\">";
							$html .= wt_he($db->f("plaats")." - ".$db->f("naam"));
							$html .= "</a>";
							if($db->f("has_opmerkingen_intern")) {
								$html .= "&nbsp;&nbsp;(<b>interne opmerkingen</b>)";
							}
							$html .= "<ul>";

							$al_gehad[$db->f("accommodatie_id")] = true;

						}
						if(!$checked_type[$seizoen_id][$db->f("type_id")]) {
							$html .= "<li>";
							$html .= "<a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".$db->f("wzt")."&archief=0&1k0=".$db->f("accommodatie_id")."&2k0=".$db->f("type_id")."#bijkomendekosten_".$seizoen_id."\" target=\"_blank\">";
							$html .= wt_he($db->f("begincode").$db->f("type_id"));
							$html .= "</a>";
							$html .= "</li>";
						}
					}
				}
			}

			$return .= "<p><b>Nog te controleren bijkomende kosten ".wt_he($seizoen_naam).":</b>";
			if($al_gehad) {
				$return .= "<ol>".$html."</ol>";
			} else {
				$return .= "<p>Momenteel zijn er geen te controleren bijkomende kosten voor dit seizoen.</p>";
			}
			$return .= "<br /><hr /><br />";
		}

		return $return;
	}

	public function check_for_copy($seizoen_id, $type_id) {
		// check if there are any bk to copy

		$db = new DB_sql;

		$db->query("SELECT bk_soort_id FROM bk_type WHERE type_id='".intval($type_id)."' AND seizoen_id='".intval($seizoen_id)."' LIMIT 0,1;");
		if(!$db->num_rows()) {

			$db->query("SELECT MAX(seizoen_id) AS seizoen_id FROM bk_type WHERE type_id='".intval($type_id)."';");
			if($db->next_record()) {

				$from_seizoen_id = $db->f("seizoen_id");

				$db->query("SELECT accommodatie_id FROM type WHERE type_id='".intval($type_id)."';");
				if($db->next_record()) {

					$copydatabaserecord = new copydatabaserecord;
					$copydatabaserecord->copy_bijkomendekosten($db->f("accommodatie_id"), $from_seizoen_id, $seizoen_id);
				}
			}
		}
	}

	private function check_for_differences_type_accommodation($fieldname, $bk_soort_id, $type_id) {
		// check if the type field is different from the accommodation field
		if($this->type_data[$this->seizoen_id][$bk_soort_id][$type_id][$fieldname]<>$this->data[$this->seizoen_id][$bk_soort_id][$fieldname]) {
			$return = $this->type_data[$this->seizoen_id][$bk_soort_id][$type_id][$fieldname];
			if($fieldname=="bedrag" and ($return or $return=="0.00")) {
				$return = number_format($return, 2, ",", ".");
			}
			// echo $fieldname.": ".$return."<br/>";

		}
		return $return;
	}

	private function select_field($name, $items, $selected, $empty_value="") {

		if(isset($selected)) {
			$selected=intval($selected);
		}

		$return .= "<select name=\"".$name."\" required=\"required\">";
		$return .= "<option value=\"\">".wt_he($empty_value)."</option>";
		foreach ($items as $key => $value) {
			$return .= "<option value=\"".$key."\"".($selected===$key ? " selected" : "").">".wt_he($value)."</option>";
		}
		$return .= "</select>";

		return $return;
	}

	private function combine_all_rows_for_log($text, $line_end=false) {
		if($line_end) {
			$new_text .= $text."\n\n";
			$this->combine_all_rows_for_log_first_item = true;
		} elseif($text) {
			if(!$this->combine_all_rows_for_log_first_item) {
				$new_text .= " - ";
			}
			$new_text .= $text;
			$this->combine_all_rows_for_log_first_item = false;
		}
		$this->all_rows_for_log[$this->seizoen_id] .= $new_text;
	}

	public function cms_new_row($bk_soort_id, $empty=false) {

		global $vars;

		$this->get_cms_data();

		if(!$empty) {
			$data = $this->data[$this->seizoen_id][$bk_soort_id];
		}

		$return .= "<div class=\"cms_bk_row".($empty ? " cms_bk_save_row cms_bk_new_row cms_bk_to_be_filled" : "").($this->copy ? " cms_bk_save_row" : "")."\" data-soort_id=\"".$bk_soort_id."\">";
		$return .= "<div>".wt_he($this->cms_data_bk_soorten[$bk_soort_id]["naam"])."</div>";

		$this->combine_all_rows_for_log($this->cms_data_bk_soorten[$bk_soort_id]["naam"]);

		if($this->cms_data_bk_soorten[$bk_soort_id]["borg"]) {
			$return .= "<div>&nbsp;</div>";
			$return .= "<div>&nbsp;</div>";
			$return .= "<div>".$this->select_field("borg_soort[".$bk_soort_id."]", $vars["bk_borg_soort_cms"], $data["borg_soort"])."</div>";

			$this->combine_all_rows_for_log($vars["bk_borg_soort_cms"][$data["borg_soort"]]);

		} else {
			$return .= "<div>".$this->select_field("inclusief[".$bk_soort_id."]", $vars["bk_inclusief"], $data["inclusief"])."</div>";
			$return .= "<div>".$this->select_field("verplicht[".$bk_soort_id."]", $vars["bk_verplicht"], $data["verplicht"])."</div>";
			$return .= "<div>".$this->select_field("ter_plaatse[".$bk_soort_id."]", $vars["bk_ter_plaatse_cms"], $data["ter_plaatse"])."</div>";

			$this->combine_all_rows_for_log($vars["bk_inclusief"][$data["inclusief"]]);
			$this->combine_all_rows_for_log($vars["bk_verplicht"][$data["verplicht"]]);
			$this->combine_all_rows_for_log($vars["bk_ter_plaatse_cms"][$data["ter_plaatse"]]);

		}
		$bedrag = preg_replace("@\.@", ",", $data["bedrag"]);
		$return .= "<div><input type=\"text\" name=\"bedrag\" value=\"".wt_he($bedrag)."\" required=\"required\" pattern=\"^\d+(\.|\,)?\d{0,2}$\" autocomplete=\"off\"></div>";
		$this->combine_all_rows_for_log($bedrag);


		unset($eenheden);
		foreach ($vars["bk_eenheid_cms"] as $key => $value) {
			if($this->cms_data_bk_soorten[$bk_soort_id]["eenheden"][$key]) {
				$eenheden[$key] = $value;
			}
		}
		if(is_array($eenheden)) {
			$return .= "<div>".$this->select_field("eenheid[".$bk_soort_id."]", $eenheden, $data["eenheid"])."</div>";
			$this->combine_all_rows_for_log($eenheden[$data["eenheid"]]);
		} else {
			$return .= "<div>&nbsp;</div>";
		}

		if($this->cms_data_bk_soorten[$bk_soort_id]["altijd_invullen"]) {
			$return .= "<div>&nbsp;</div>";
		} else {
			$return .= "<div class=\"delete\" title=\"regel wissen\">&#xd7;</div>";
		}

		$this->combine_all_rows_for_log("", true);


		$return .= "</div>"; // close .cms_bk_row

		return $return;

	}

	public function pre_calculate_all_types($limit=0, $force=false) {

		global $cron;

		$db = new DB_sql;

		$db->query("SELECT DISTINCT type_id, wzt FROM view_accommodatie WHERE 1=1 ORDER BY type_id;");
		while($db->next_record()) {

			$last_save_time = $this->redis->hget("bk:".$db->f("wzt").":".$db->f("type_id"), "saved");

			if($last_save_time<(time()-82800) or $force) {

				$counter++;

				$this->pre_calculate_type($db->f("type_id"));

				$new[$db->f("wzt")] = true;

				if($limit>0 and $counter>=$limit) {
					break;
				}
			}
		}
		if($new[1]) {
			// winter cache
			$this->store_complete_cache(1);
		}
		if($new[2]) {
			// summer cache
			$this->store_complete_cache(2);
		}
	}

	public function pre_calculate_missing_types() {

		$db = new DB_sql;

		$db->query("SELECT DISTINCT type_id, wzt FROM view_accommodatie WHERE 1=1 ORDER BY type_id;");
		while($db->next_record()) {
			if(!$this->redis->hexists("bk:".$db->f("wzt").":".$db->f("type_id"), "saved")) {
				$this->pre_calculate_type($db->f("type_id"));
				$new[$db->f("wzt")] = true;
			}
		}
		if($new[1]) {
			// winter cache
			$this->store_complete_cache(1);
		}
		if($new[2]) {
			// summer cache
			$this->store_complete_cache(2);
		}
	}

	public function pre_calculate_accommodation($accommodatie_id) {

		$db = new DB_sql;

		$db->query("SELECT type_id FROM view_accommodatie WHERE accommodatie_id='".intval($accommodatie_id)."';");
		while($db->next_record()) {
			$this->pre_calculate_type($db->f("type_id"));
		}
	}

	public function pre_calculate_type($type_id) {
		//
		// calculate bijkomendekosten per type and save in Redis
		//

		global $vars;

		// delete all vars of this class
		$this->clear();

		$this->pre_calculate = true;

		$this->id = $type_id;
		$this->soort = "type";

		$this->seasons_use_old_dates = true;
		$this->seasons_hide_inactive = true;

		$this->get_data_done = false;
		$this->get_data();


		$this->redis->del("bk:".$this->wzt.":".$type_id);

		if(is_array($this->data) and constant("include_bkk")===true) {
			foreach ($this->data as $seizoen_id => $data) {

				unset($per_person, $per_accommodation);

				// reservation costs
				$per_accommodation += $vars["reserveringskosten"];

				foreach ($data as $key => $value) {

					// only without min_leeftijd/max_leeftijd, or when zonderleeftijd is set
					if((!$value["min_leeftijd"] or $value["zonderleeftijd"]) and (!$value["max_leeftijd"] or $value["zonderleeftijd"])) {

						if($value["filled"] and $value["verplicht"]==1 and $value["inclusief"]<>1 and $value["bedrag"]>0 and !$value["borg_soort"]) {

							// prijs_per_nacht + per dag (eenheid 3) + per nacht (eenheid 8)
							if($value["prijs_per_nacht"] or $value["eenheid"]==3 or $value["eenheid"]==8) {
								$value["bedrag"] = $value["bedrag"] * 7;
							}
							if($value["eenheid"]==2 or $value["eenheid"]==12 or $value["eenheid"]==9) {
								// eenheid = "per person" or "per person each time" or "per set"
								$per_person += $value["bedrag"];
							} else {
								// per accommodation
								$per_accommodation += $value["bedrag"];
							}
						}
					}
				}

				for($i=$this->maxaantalpersonen;$i>=1;$i--) {
					$total = $per_accommodation + $i * $per_person;
					$this->redis->hset("bk:".$this->wzt.":".$type_id, $seizoen_id.":".$i, $total);
					if($vars["tmp_info_tonen"]) {
						echo "bk:".$this->wzt.":".$type_id." - ".$seizoen_id.":".$i." - ".$total." <br/>\n";
						flush();
					}
				}
			}
		} else {
			// trigger_error("_notice: no this->data for type-id ".$type_id,E_USER_NOTICE);
		}

		//
		// surcharge extra persons (toeslag extra personen)
		//
		$this->redis->del("bk_per_week:".$this->wzt.":".$type_id);

		if(is_array($this->data_var)) {
			unset($save_redis_var);
			foreach ($this->data_var as $seizoen_id => $alldata) {

				foreach ($alldata as $bijkomendekosten_id => $data) {

					for($i=$data["min_personen"]; $i<=$data["max_personen"]; $i++) {
						foreach ($data["verkoop"] as $week => $amount) {
							$amount_total = $amount * ($i - $data["min_personen"] + 1);
							$save_redis_var[$i][$week] += $amount_total;
						}
					}
				}
			}

			if(is_array($save_redis_var)) {
				foreach ($save_redis_var as $persons => $data) {
					foreach ($data as $week => $total) {
						if($total<>0) {
							$this->redis->hset("bk_per_week:".$this->wzt.":".$type_id, $week.":".$persons, $total);
							if($vars["tmp_info_tonen"]) {
								echo "bk_per_week:".$this->wzt.":".$type_id." - ". $week.":".$persons." - ". $total." <br />\n";
								flush();
							}
						}
					}
				}
			}
		}

		$this->redis->hset("bk:".$this->wzt.":".$type_id, "saved", time());

		if(!$GLOBALS["class_bijkomendekosten_register_shutdown_".$this->wzt]) {
			register_shutdown_function(array($this, "store_complete_cache"), $this->wzt);
			$GLOBALS["class_bijkomendekosten_register_shutdown_".$this->wzt]=true;
		}

		$this->pre_calculate = false;

	}

	public function pre_calculate_variable_costs($bijkomendekosten_id) {
		// calculate variable costs (surcharge extra persons / toeslag extra persoon) based on bijkomendekosten_id
		$db = new DB_sql;
		global $vars;

		$db->query("SELECT DISTINCT type_id
		           FROM type t INNER JOIN accommodatie a USING (accommodatie_id)
		           WHERE
		           a.bijkomendekosten1_id='".intval($bijkomendekosten_id)."' OR t.bijkomendekosten1_id='".intval($bijkomendekosten_id)."' OR
		           a.bijkomendekosten2_id='".intval($bijkomendekosten_id)."' OR t.bijkomendekosten2_id='".intval($bijkomendekosten_id)."' OR
		           a.bijkomendekosten3_id='".intval($bijkomendekosten_id)."' OR t.bijkomendekosten3_id='".intval($bijkomendekosten_id)."' OR
		           a.bijkomendekosten4_id='".intval($bijkomendekosten_id)."' OR t.bijkomendekosten4_id='".intval($bijkomendekosten_id)."' OR
		           a.bijkomendekosten5_id='".intval($bijkomendekosten_id)."' OR t.bijkomendekosten5_id='".intval($bijkomendekosten_id)."' OR
		           a.bijkomendekosten6_id='".intval($bijkomendekosten_id)."' OR t.bijkomendekosten6_id='".intval($bijkomendekosten_id)."'
		           ;");
		while($db->next_record()) {
			$this->pre_calculate_type($db->f("type_id"));
		}
	}

	public function get_type_from_cache($type_id, $wzt, $seizoen_id, $aantalpersonen, $per_person=false) {
		if(!$aantalpersonen) {
			$aantalpersonen = 1;
		}
		$return = $this->redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$aantalpersonen);
		if(constant("include_bkk")===true and !$return) {
			$this->pre_calculate_type($type_id);

			$return = $this->redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$aantalpersonen);
			if(!$return) {
				trigger_error("geen bijkomende kosten gevonden voor type_id ".$type_id.", seizoen_id ".$seizoen_id.", ".$aantalpersonen." personen", E_USER_NOTICE);
			}
		}
		if($per_person) {
			$return = round($return / $aantalpersonen, 2);
		}
		return $return;

	}

	public function get_type_from_cache_all_persons($type_id, $wzt, $seizoen_id, $maxaantalpersonen, $per_person) {

		for($i=1;$i<=$maxaantalpersonen;$i++) {
			$bedrag = $this->redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$i);

			if(constant("include_bkk")===true and !$bedrag) {
				$this->pre_calculate_type($type_id);

				$bedrag = $this->redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$i);
				if(!$bedrag) {
					trigger_error("geen bijkomende kosten gevonden voor type_id ".$type_id.", seizoen_id ".$seizoen_id.", ".$i." personen", E_USER_NOTICE);
				}

			}
			if($per_person) {
				$bedrag = round($bedrag / $i, 2);
			}

			$return[$i] = $bedrag;
		}
		return $return;
	}

	public function get_type_from_cache_all_persons_all_weeks($type_id, $wzt) {

		//
		// get surcharge extra persons (toeslag extra personen)
		//

		$bedrag = $this->redis->hgetall("bk_per_week:".$wzt.":".$type_id);

		if(is_array($bedrag)) {
			foreach ($bedrag as $key => $value) {
				if(preg_match("@([0-9]+):([0-9]+)$@", $key, $regs)) {
					$return[$regs[2]][$regs[1]] = $value;
				}
			}
		}

		return $return;
	}

	public function store_complete_cache($wzt) {

		$all_bk = $this->redis->keys("bk:".$wzt.":*");
		if(is_array($all_bk)) {
			foreach ($all_bk as $key => $value) {
				$content=$this->redis->hgetall($value, false);
				foreach ($content as $key2 => $value2) {

					if(preg_match("@bk:([12]):([0-9]+)$@", $value, $regs)) {

						$wzt = $regs[1];
						$type_id = $regs[2];

						if(preg_match("@^([0-9]+):([0-9]+)$@", $key2, $regs)) {

							$seizoen_id = $regs[1];
							$aantalpersonen = $regs[2];

							$bk[$type_id][$seizoen_id][$aantalpersonen] = $value2;
						}
					}
				}
			}
		}

		if(!is_array($bk)) {
			$bk = array();
		}

		$this->redis->store_array("bk:".$wzt, "all", $bk);
		unset($bk);

		//
		// surcharge extra persons
		//

		$all_bk = $this->redis->keys("bk_per_week:".$wzt.":*");
		if(is_array($all_bk)) {
			foreach ($all_bk as $key => $value) {
				$content=$this->redis->hgetall($value, false);
				foreach ($content as $key2 => $value2) {

					if(preg_match("@bk_per_week:([12]):([0-9]+)$@", $value, $regs)) {

						$wzt = $regs[1];
						$type_id = $regs[2];

						if(preg_match("@^([0-9]+):([0-9]+)$@", $key2, $regs)) {

							$week = $regs[1];
							$aantalpersonen = $regs[2];

							$bk_all_persons[$aantalpersonen][$type_id][$week] = $value2;
							// $bk_all_weeks[$week][$aantalpersonen][$type_id] = $value2;
						}
					}
				}
			}
		}

		if(is_array($bk_all_persons)) {
			foreach ($bk_all_persons as $key => $value) {
				$this->redis->store_array("bk_per_week:".$wzt, "all_persons:".$key, $value);
			}
			unset($bk_all_persons);
		}
		// if(is_array($bk_all_weeks)) {
		// 	foreach ($bk_all_weeks as $key => $value) {
		// 		$this->redis->store_array("bk_per_week:".$wzt, "all_weeks:".$key, $value);
		// 	}
		// 	unset($bk_all_weeks);
		// }
	}

	public function get_complete_cache($wzt) {

		$return = $this->redis->get_array("bk:".$wzt, "all");

		return $return;

	}

	public function get_complete_cache_per_persons($wzt, $aantalpersonen) {

		$return = $this->redis->get_array("bk_per_week:".$wzt, "all_persons:".$aantalpersonen);

		return $return;

	}

	private function toonbedrag($bedrag) {
		$return = number_format($bedrag, 2, ",", ".");
		$return = preg_replace("@,00$@", ",-", $return);
		return $return;
	}

	public function get_costs() {

		//
		// get all costs for 1 specific type
		//

		global $vars;

		$db = new DB_sql;

		// get accinfo
		if(!$this->accinfo) {
			$this->accinfo=accinfo($this->id);
		}

		// Chaletsinvallandry: correct seizoentype
		if($vars["websitetype"]==6 and isset($this->accinfo["wzt"])) {
			$vars["seizoentype"]=$this->accinfo["wzt"];
		}

		$this->get_data();


		if($this->arrangement) {
			// Skipasgegevens uit database halen
			$db->query("SELECT s.website_omschrijving".$vars["ttv"]." AS website_omschrijving FROM skipas s, accommodatie a WHERE a.skipas_id=s.skipas_id AND a.accommodatie_id='".intval($this->accinfo["accommodatie_id"])."';");
			if($db->next_record()) {
				if($db->f("website_omschrijving")) $skipas_website_omschrijving=$db->f("website_omschrijving");
			}
		}
		if(!$vars["wederverkoop"] and $skipas_website_omschrijving) {
			$kosten["html"]["inclusief"]["skipas"] = wt_he($skipas_website_omschrijving);
			$kosten["vars"]["inclusief"]["skipas"]["naam"] = $skipas_website_omschrijving;
		}

		if(is_array($this->data_var[$this->seizoen_id])) {
			//
			// surcharge extra persons (toeslag extra personen)
			//
			foreach ($this->data_var[$this->seizoen_id] as $key => $value) {

				unset($html);

				if( is_array( $value["verkoop"] ) ) {
					unset($min, $max);
					foreach ($value["verkoop"] as $key2 => $value2) {
						if($value2<>0) {

							if(!isset($min)) {
								$min=$value2;
								$max=$value2;
							}
							if($value2<$min) $min=$value2;
							if($value2>$max) $max=$value2;
						}
					}

					if(isset($min)) {

						if ($min < $max) {
							$week_determines_price = true;
						} else {
							$week_determines_price = false;
						}

						$html .= wt_he($value["naam"]);

						if ($this->newWebsite) {
							$info_link = "<a href=\"#\" onclick=\"popwindow(500,0,'".$vars["path"]."popup.php?tid=".intval($this->id)."&id=bijkomendekosten&bkid=".$key."');return false;\" rel=\"nofollow\">";
							$info_link = '<a href="#" class="bkk_toggle_more_information" data-bkid="' . $key . '">';
						} else {
							$info_link = "<a href=\"#\" onclick=\"popwindow(500,0,'".$vars["path"]."popup.php?tid=".intval($this->id)."&id=bijkomendekosten&bkid=".$key."');return false;\" rel=\"nofollow\">";
						}

						if ($value["omschrijving"] && !$this->newWebsite) {
							$html .= "&thinsp;".$info_link."<img src=\"".$this->getImageRoot()."pic/information_icon_with_padding.png\" /></a> ";
						} else {
							$html .= " ";
						}

						$html .= "(";
						if($min==$max) {
							$html .= wt_he("€ ".$this->toonbedrag($min));
						} else {
							$html .= html("vantot","tarieventabel", array("v_bedragmin"=>number_format($min, 2, ",", "."), "v_bedragmax"=>number_format($max, 2, ",", ".")));
						}
						$html .= " ";
						if($max<0) {
							$html .= html("korting","vars")." ";
						}
						$html .= html("perpersoonafk", "tarieventabel");
						if ($week_determines_price) {
							$html .= " ".$info_link.html("afhankelijk-van-week", "tarieventabel")."</a>";
						}
						$html .= ")";

						if($value["omschrijving"] && $this->newWebsite) {
							if (!$week_determines_price) {
								$html	.= ' - ' . $info_link . html("meerinformatie", "bijkomendekosten") . '&nbsp;&raquo;</a>';
							}
							$html .= '<div class="bkk_more_information" data-bkid="' . $key . '">' . nl2br(wt_he($value["omschrijving"]));
							if ($week_determines_price) {
								$weekDependentPriceRenderer = new WeekDependentPriceRenderer($db, $this->accinfo, $this->id, $key, $this->seizoen_id);
								$html .= $weekDependentPriceRenderer->render();
							}
							$html .= '</div>';
						}

						if ($this->zoek_en_boek_popup) {
							if ($_GET["ap"]) {
								if((!$value["min_leeftijd"] or $value["zonderleeftijd"]) and (!$value["max_leeftijd"] or $value["zonderleeftijd"])) {
									$cat = "inclusief";
								} else {
									$cat = "diversen";
								}
							} else {
								$cat = "inclusief";
							}
						} else {
							$cat = "diversen";
						}
						$kosten["html"][$cat]["var_".$key] .= $html;


						if($this->pre_boeken) {

							if( $this->aantalpersonen and $value["min_personen"] and $this->aantalpersonen>=$value["min_personen"] and $this->aantalpersonen<=$value["max_personen"] ) {

								$kosten["vars"]["inclusief"]["var_".$key]["naam"] = $value["naam"];
								$kosten["vars"]["inclusief"]["var_".$key]["vouchernaam"] = $value["naam"];
								$kosten["vars"]["inclusief"]["var_".$key]["factuurnaam"] = $value["naam"];
								$kosten["vars"]["inclusief"]["var_".$key]["verplicht"] = 1;
								$kosten["vars"]["inclusief"]["var_".$key]["ter_plaatse"] = 0;
								$kosten["vars"]["inclusief"]["var_".$key]["bedrag"] = $value["verkoop"][$this->aankomstdatum];
								$kosten["vars"]["inclusief"]["var_".$key]["borg_soort"] = $value["borg_soort"];
								$kosten["vars"]["inclusief"]["var_".$key]["eenheid"] = 2;
								$kosten["vars"]["inclusief"]["var_".$key]["inclusief"] = 0;
								$kosten["vars"]["inclusief"]["var_".$key]["min_leeftijd"] = $value["min_leeftijd"];
								$kosten["vars"]["inclusief"]["var_".$key]["max_leeftijd"] = $value["max_leeftijd"];
								$kosten["vars"]["inclusief"]["var_".$key]["zonderleeftijd"] = $value["zonderleeftijd"];

								$kosten["vars"]["inclusief"]["var_".$key]["min_personen"] = $value["min_personen"];
								$kosten["vars"]["inclusief"]["var_".$key]["max_personen"] = $value["max_personen"];

							}
						}
					}
				}
			}
		}

		if(is_array($this->data[$this->seizoen_id])) {
			foreach ($this->data[$this->seizoen_id] as $key => $value) {

				if($value["filled"]) {

					if($value["borg_soort"]) {
						$cat = "diversen";
					} elseif($value["altijd_diversen"]) {
						$cat = "diversen";
					} elseif($value["inclusief"]==1 or $value["verplicht"]==1) {
						$cat = "inclusief";
					} elseif($value["inclusief"]==0 and $value["verplicht"]==3) {
						// exclusief + zelf te verzorgen
						$cat = "diversen";
					} elseif( $value["altijd_diversen_indien_niet_inclusief"] ) {
						$cat = "diversen";
					} else {
						$cat = "uitbreiding";
					}

					$kosten["vars"][$cat][$key]["naam"] = $value["naam"];
					$kosten["vars"][$cat][$key]["vouchernaam"] = $value["vouchernaam"];
					$kosten["vars"][$cat][$key]["factuurnaam"] = $value["factuurnaam"];
					$kosten["vars"][$cat][$key]["verplicht"] = $value["verplicht"];
					$kosten["vars"][$cat][$key]["ter_plaatse"] = $value["ter_plaatse"];
					$kosten["vars"][$cat][$key]["bedrag"] = $value["bedrag"];
					$kosten["vars"][$cat][$key]["prijs_per_nacht"] = $value["prijs_per_nacht"];
					$kosten["vars"][$cat][$key]["borg_soort"] = $value["borg_soort"];
					$kosten["vars"][$cat][$key]["eenheid"] = $value["eenheid"];
					$kosten["vars"][$cat][$key]["inclusief"] = $value["inclusief"];
					$kosten["vars"][$cat][$key]["min_leeftijd"] = $value["min_leeftijd"];
					$kosten["vars"][$cat][$key]["max_leeftijd"] = $value["max_leeftijd"];
					$kosten["vars"][$cat][$key]["zonderleeftijd"] = $value["zonderleeftijd"];

					if($value["borg_soort"]) {
						//
						// borg
						//
						$kosten["html"][$cat][$key] = wt_he($value["naam"]);

						if($value["borg_soort"]==1 or $value["borg_soort"]==2 or $value["borg_soort"]==3 or $value["borg_soort"]==6) {
							$kosten["html"][$cat][$key] .= " ".wt_he("(€ ".$this->toonbedrag($value["bedrag"])." ".($value["eenheid"]==2 ? " ".$vars["bk_eenheid"][$value["eenheid"]].", " : "").$vars["bk_borg_soort"][$value["borg_soort"]].")");
						} elseif($value["borg_soort"]==4) {
							$kosten["html"][$cat][$key] .= ": ".html("geen-borg-verschuldigd", "bijkomendekosten");
						} elseif($value["borg_soort"]==5) {
							$kosten["html"][$cat][$key] .= " (".html("ter-plaatse-te-voldoen", "bijkomendekosten").")";
						}
					} elseif($value["prijs_per_nacht"]) {
						//
						// toeristenbelasting
						//
						if($value["inclusief"]==1) {
							$kosten["html"][$cat][$key] = wt_he($value["naam"]);
						} else {
							if($value["bedrag"]=="0.00") {
								$cat = "diversen";
								$kosten["html"][$cat][$key] = wt_he($value["naam"]);
								$kosten["html"][$cat][$key] .= " ".html("ter-plaatse-te-voldoen", "bijkomendekosten");
							} else {
								$kosten["html"][$cat][$key] = wt_he($value["naam"]);
								$kosten["html"][$cat][$key] .= " ".wt_he("(€ ".$this->toonbedrag($value["bedrag"])." ".txt("pppn", "bijkomendekosten"));
								if($value["ter_plaatse"]==1) {
									$kosten["html"][$cat][$key] .= ", ".$vars["bk_ter_plaatse"][$value["ter_plaatse"]];
								}
								$kosten["html"][$cat][$key] .= ")";
							}
						}
					} else {

						//
						// other costs
						//
						$kosten["html"][$cat][$key] = wt_he($value["naam"]);

						if($value["toelichting"]) {
							if (!$this->newWebsite) {
								// info-icon
								$info_link = "<a href=\"#\" onclick=\"popwindow(500,0,'".wt_he($vars["path"]."popup.php?tid=".intval($this->id)."&id=bijkomendekosten&bksid=".$key)."');return false;\">";
								$kosten["html"][$cat][$key] .= "&thinsp;".$info_link."<img src=\"".$this->getImageRoot()."pic/information_icon_with_padding.png\" /></a> ";
							}
						}

						if($value["verplicht"]==2 and $value["bedrag"]=="0.00") {
							$kosten["html"][$cat][$key] .= " (".wt_he($vars["bk_verplicht"][2]);
							if($value["eenheid"]) {
								$kosten["html"][$cat][$key] .=" ".wt_he($vars["bk_eenheid"][$value["eenheid"]]);
							}
							if($value["ter_plaatse"]==1) {
								$kosten["html"][$cat][$key] .= ", ".wt_he($vars["bk_ter_plaatse"][$value["ter_plaatse"]]);
							}
							$kosten["html"][$cat][$key] .= ")";
						} elseif($value["bedrag"]=="0.00") {
							$kosten["html"][$cat][$key] .= " (".html("tegen-betaling", "bijkomendekosten").")";
						} elseif($value["bedrag"]>0) {
							$kosten["html"][$cat][$key] .= " (";
							if($value["verplicht"]==2) {
								$kosten["html"][$cat][$key] .= wt_he($vars["bk_verplicht"][2].": ");
							}
							$kosten["html"][$cat][$key] .= wt_he("€ ".$this->toonbedrag($value["bedrag"]));
							if($value["eenheid"]) {
								$kosten["html"][$cat][$key] .=" ".wt_he($vars["bk_eenheid"][$value["eenheid"]]);
							}
							if($value["opgeven_bij_boeken"] and $value["inclusief"]==0 and $value["verplicht"]==0) {
								$kosten["html"][$cat][$key] .= ", ".html("opgeven-bij-boeking", "bijkomendekosten");
							}
							if($value["ter_plaatse"]==1) {
								$kosten["html"][$cat][$key] .= ", ".wt_he($vars["bk_ter_plaatse"][$value["ter_plaatse"]]);
							}
							$kosten["html"][$cat][$key] .= ")";
						} elseif($value["verplicht"]==3) {
							$kosten["html"][$cat][$key] .= " (".$vars["bk_verplicht"][3].")";
						}

						if($value["toelichting"] && $this->newWebsite) {
							$kosten["html"][$cat][$key]	.= ' - <a href="#" class="bkk_toggle_more_information" data-bkid="' . $key . '">' . html("meerinformatie", "bijkomendekosten") . '&nbsp;&raquo;</a>';
							$kosten["html"][$cat][$key]	.= '<div class="bkk_more_information" data-bkid="' . $key . '">' . nl2br(wt_he($value["toelichting"])) . '</div>';
						}

					}
				}
			}
		}

		$reserveringskosten_calamiteiten = (in_array($vars['website'], $vars['sgr_c']));
		$kosten["html"]["inclusief"]["reserveringskosten"] = html("reserveringskosten" . ($reserveringskosten_calamiteiten ? '_calamiteiten' : ''), "vars")." (&euro; " . toonreserveringskosten($vars['reserveringskosten'])." ".html("perboeking", "vars").")";

		if ($this->newWebsite) {
			$extraOptionsLink = 'extras';
		} else {
			$extraOptionsLink = 'extraopties';
		}

		$kosten["html"]["uitbreiding"]["extraopties"] = html("bekijk-ook-extra-opties","tarieventabel",array("h_1"=>"<a href=\"#" . $extraOptionsLink . "\">","h_2"=>" &raquo;</a>"));

		return $kosten;
	}

	public function get_variable_costs() {

		global $vars;

		$db = new DB_sql;
		$db2 = new DB_sql;

		// Bijkomende kosten gekoppeld aan skipassen
		if($this->accinfo["skipasid"]) {
			$db->query("SELECT bijkomendekosten_id FROM skipas WHERE skipas_id='".addslashes($this->accinfo["skipasid"])."';");
			if($db->next_record()) {
				if($db->f("bijkomendekosten_id")) {
					$variabele_bijkomendekosten_inquery.=",".$db->f("bijkomendekosten_id");
				}
			}
		}

		if($variabele_bijkomendekosten_inquery and $this->seizoen_id) {
			$db->query("SELECT b.bijkomendekosten_id, b.naam".$vars["ttv"]." AS naam, b.omschrijving".$vars["ttv"]." AS omschrijving, b.perboekingpersoon, b.min_personen FROM bijkomendekosten b WHERE b.bijkomendekosten_id IN (".substr($variabele_bijkomendekosten_inquery, 1).") AND (b.min_personen IS NOT NULL OR b.gekoppeldaan<>1) ORDER BY b.naam".$vars["ttv"].";");
			if($db->num_rows()) {
				while($db->next_record()) {

					unset($min, $max, $html);

					// get actual costs
					$db2->query("SELECT DISTINCT week, verkoop FROM bijkomendekosten_tarief WHERE bijkomendekosten_id='".$db->f("bijkomendekosten_id")."' AND seizoen_id IN (".$this->seizoen_id.");");
					while($db2->next_record()) {
						if($db2->f("verkoop")<>0) {

							if(!isset($min)) {
								$min=$db2->f("verkoop");
								$max=$db2->f("verkoop");
							}
							if($db2->f("verkoop")<$min) $min=$db2->f("verkoop");
							if($db2->f("verkoop")>$max) $max=$db2->f("verkoop");

						}
					}
					if(isset($min)) {

						$info_link = "<a href=\"#\" onclick=\"popwindow(500,0,'".$vars["path"]."popup.php?tid=".intval($this->id)."&id=bijkomendekosten&bkid=".$db->f("bijkomendekosten_id")."');return false;\">";
						// $info_link = "<a href=\"".wt_he($vars["path"]."popup.php?fancybox=1&tid=".intval($this->id)."&id=bijkomendekosten&bkid=".$db->f("bijkomendekosten_id"))."\" class=\"popup_fancybox\" rel=\"nofollow\">";

						$html .= wt_he($db->f("naam"));
						if($db->f("omschrijving")) {
							$html .= "&thinsp;".$info_link."<img src=\"".$this->getImageRoot()."pic/information_icon_with_padding.png\" /></a> ";
						} else {
							$html .= " ";
						}

						$html .= "(";
						if($min==$max) {
							$html .= wt_he("€ ".$this->toonbedrag($min));
						} else {
							$html .= html("vantot","tarieventabel", array("v_bedragmin"=>number_format($min, 2, ",", "."), "v_bedragmax"=>number_format($max, 2, ",", ".")));
						}
						$html .= " ";
						if($max<0) {
							$html .= html("korting","vars")." ";
						}
						if($db->f("perboekingpersoon")==1) {
							$html .= html("perboeking", "tarieventabel");
						} else {
							$html .= html("perpersoonafk", "tarieventabel");
						}
						if($min<$max) {
							$html .= " ".$info_link.html("afhankelijk-van-week", "tarieventabel")."</a>";
						}
						$html .= ")";

						$return[$db->f("bijkomendekosten_id")] = $html;
					}
				}
			}
		}
		return $return;
	}


	public function add_to_booking($gegevens) {

		//
		// add extra_option-records to a booking based on bijkomendekosten
		//

		global $vars;

		$db=new DB_sql;
		$db2=new DB_sql;


		//
		// bijkomendekosten_id (variable price)
		//

		while(list($key,$value)=@each($gegevens["stap4"]["bijkomendekosten"])) {
			if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$key; else $bijkomendekosten_inquery=$key;
		}

		// Oude gegevens inlezen
		$db->query("SELECT bijkomendekosten_id, naam, verkoop, inkoop, korting, omzetbonus, hoort_bij_accommodatieinkoop, optiecategorie FROM extra_optie WHERE bijkomendekosten_id IS NOT NULL AND boeking_id='".$gegevens["stap1"]["boekingid"]."';");
		while($db->next_record()) {
			$voorheen[$db->f("bijkomendekosten_id")]["naam"]=$db->f("naam");
			$voorheen[$db->f("bijkomendekosten_id")]["verkoop"]=$db->f("verkoop");
			$voorheen[$db->f("bijkomendekosten_id")]["inkoop"]=$db->f("inkoop");
			$voorheen[$db->f("bijkomendekosten_id")]["korting"]=$db->f("korting");
			$voorheen[$db->f("bijkomendekosten_id")]["omzetbonus"]=$db->f("omzetbonus");
			$voorheen[$db->f("bijkomendekosten_id")]["hoort_bij_accommodatieinkoop"]=$db->f("hoort_bij_accommodatieinkoop");
			$voorheen[$db->f("bijkomendekosten_id")]["optiecategorie"]=$db->f("optiecategorie");
		}

		// Bijkomende kosten gekoppeld aan accommodatie en type
		$db->query("SELECT a.bijkomendekosten1_id, a.bijkomendekosten2_id, a.bijkomendekosten3_id, a.bijkomendekosten4_id, a.bijkomendekosten5_id, a.bijkomendekosten6_id, t.bijkomendekosten1_id AS tbijkomendekosten1_id, t.bijkomendekosten2_id AS tbijkomendekosten2_id, t.bijkomendekosten3_id AS tbijkomendekosten3_id, t.bijkomendekosten4_id AS tbijkomendekosten4_id, t.bijkomendekosten5_id AS tbijkomendekosten5_id, t.bijkomendekosten6_id AS tbijkomendekosten6_id FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($gegevens["stap1"]["typeid"])."';");
		if($db->next_record()) {
			for($i=1;$i<=6;$i++) {
				if($db->f("bijkomendekosten".$i."_id")) {
					if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db->f("bijkomendekosten".$i."_id"); else $bijkomendekosten_inquery=$db->f("bijkomendekosten".$i."_id");
				}
				if($db->f("tbijkomendekosten".$i."_id")) {
					if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db->f("tbijkomendekosten".$i."_id"); else $bijkomendekosten_inquery=$db->f("tbijkomendekosten".$i."_id");
				}
			}
		}

		// Bijkomende kosten gekoppeld aan skipas
		if($gegevens["stap1"]["accinfo"]["skipasid"]) {
			$db->query("SELECT bijkomendekosten_id FROM skipas WHERE skipas_id='".addslashes($gegevens["stap1"]["accinfo"]["skipasid"])."';");
			if($db->next_record()) {
				if($db->f("bijkomendekosten_id")) {
					if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db->f("bijkomendekosten_id"); else $bijkomendekosten_inquery=$db->f("bijkomendekosten_id");
				}
			}
		}

		// Oude gegevens wissen (alleen bij bijkomende kosten die nu ook nog aanwezig zijn bij de accommodatie/type)
		if($bijkomendekosten_inquery) {
			$db->query("DELETE FROM extra_optie WHERE bijkomendekosten_id IS NOT NULL AND bijkomendekosten_id IN (".$bijkomendekosten_inquery.") AND boeking_id='".$gegevens["stap1"]["boekingid"]."';");
		}

		// Alle deelnemers in $alle_deelnemers
		for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
			if($alle_deelnemers) $alle_deelnemers.=",".$i; else $alle_deelnemers=$i;
		}

		if($bijkomendekosten_inquery) {
			$db->query("SELECT b.bijkomendekosten_id, b.gekoppeldaan, b.hoort_bij_accommodatieinkoop, b.optiecategorie, bt.verkoop, bt.inkoop, bt.korting, bt.omzetbonus, b.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam, b.perboekingpersoon, b.min_leeftijd, b.max_leeftijd, b.zonderleeftijd, b.min_personen, b.max_personen FROM bijkomendekosten b, bijkomendekosten_tarief bt WHERE bt.bijkomendekosten_id=b.bijkomendekosten_id AND bt.seizoen_id='".$gegevens["stap1"]["seizoenid"]."' AND bt.week='".$gegevens["stap1"]["aankomstdatum"]."' AND b.bijkomendekosten_id IN (".$bijkomendekosten_inquery.") AND (b.min_personen IS NOT NULL OR b.gekoppeldaan<>1);");
			while($db->next_record()) {
				unset($save, $alg_aantal);
				if($db->f("perboekingpersoon")==1) {
					$save["persoonnummer"]="alg";
					$alg_aantal = 1;
				} else {
					$save["persoonnummer"]="pers";
					if($db->f("gekoppeldaan")==3) {
						$save["deelnemers"]=$gegevens["stap4"]["bijkomendekosten"][$db->f("bijkomendekosten_id")];
					} else {
						if($db->f("min_personen") and $db->f("max_personen")) {
							//
							// check for number of persons for this booking (min_personen / max_personen)
							//

							// toeslag (surcharge) = general option ("alg")
							$save["persoonnummer"]="alg";

							if($db->f("min_leeftijd") or $db->f("max_leeftijd")) {

								//
								// check for age
								//

								unset($geboortedatum_sort);
								for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
									if(isset($gegevens["stap3"][$i]["geboortedatum"])) {
										$geboortedatum_sort[$i] = $gegevens["stap3"][$i]["geboortedatum"];
									} else {
										// fake birth date: today minus 30 years
										$geboortedatum_sort[$i] = mktime(0,0,0, date("m"), date("d"), date("Y")-30);
									}
								}

								// order by age (from old to young)
								asort($geboortedatum_sort);
								unset($persoon_counter);
								foreach ($geboortedatum_sort as $i => $geboortedatum) {
									$persoon_counter++;
									if($persoon_counter>=$db->f("min_personen") and $persoon_counter<=$db->f("max_personen")) {

										$leeftijd=wt_leeftijd($geboortedatum, mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
										if($db->f("min_leeftijd") and $db->f("max_leeftijd")) {
											if($leeftijd>=$db->f("min_leeftijd") and $leeftijd<=$db->f("max_leeftijd")) {
												$alg_aantal++;
											}
										} elseif($db->f("min_leeftijd")) {
											if($leeftijd>=$db->f("min_leeftijd")) {
												$alg_aantal++;
											}
										} elseif($db->f("max_leeftijd")) {
											if($leeftijd<=$db->f("max_leeftijd")) {
												$alg_aantal++;
											}
										}
									}
								}

							} else {
								for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
									if($i>=$db->f("min_personen") and $i<=$db->f("max_personen")) {
										$alg_aantal++;
									}
								}
							}
						} elseif($db->f("min_leeftijd") or $db->f("max_leeftijd")) {
							//
							// check for age
							//
							for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
								if(isset($gegevens["stap3"][$i]["geboortedatum"])) {
									$leeftijd=wt_leeftijd($gegevens["stap3"][$i]["geboortedatum"],mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
									if($db->f("min_leeftijd") and $db->f("max_leeftijd")) {
										if($leeftijd>=$db->f("min_leeftijd") and $leeftijd<=$db->f("max_leeftijd")) {
											if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
										}
									} elseif($db->f("min_leeftijd")) {
										if($leeftijd>=$db->f("min_leeftijd")) {
											if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
										}
									} elseif($db->f("max_leeftijd")) {
										if($leeftijd<=$db->f("max_leeftijd")) {
											if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
										}
									}
								} elseif($db->f("zonderleeftijd")) {
									if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
								}
							}
						} else {
							$save["deelnemers"]=$alle_deelnemers;
						}
					}
				}

				if($voorheen[$db->f("bijkomendekosten_id")]) {
					$save["naam"]=$voorheen[$db->f("bijkomendekosten_id")]["naam"];
					$save["verkoop"]=$voorheen[$db->f("bijkomendekosten_id")]["verkoop"];
					$save["inkoop"]=$voorheen[$db->f("bijkomendekosten_id")]["inkoop"];
					$save["korting"]=$voorheen[$db->f("bijkomendekosten_id")]["korting"];
					$save["omzetbonus"]=$voorheen[$db->f("bijkomendekosten_id")]["omzetbonus"];
					$save["hoort_bij_accommodatieinkoop"]=$voorheen[$db->f("bijkomendekosten_id")]["hoort_bij_accommodatieinkoop"];
					$save["optiecategorie"]=$voorheen[$db->f("bijkomendekosten_id")]["optiecategorie"];
				} else {
					$save["naam"]=$db->f("naam");
					$save["verkoop"]=$db->f("verkoop");
					$save["inkoop"]=$db->f("inkoop");
					$save["korting"]=$db->f("korting");
					$save["omzetbonus"]=$db->f("omzetbonus");
					$save["hoort_bij_accommodatieinkoop"]=$db->f("hoort_bij_accommodatieinkoop");
					$save["optiecategorie"]=$db->f("optiecategorie");
				}
				if($save["verkoop"]<>0 or $save["verkoop"]=="0.00") {
					# Alleen opslaan als verkoopprijs is gezet
					$db2->query("INSERT INTO extra_optie SET boeking_id='".$gegevens["stap1"]["boekingid"]."', persoonnummer='".$save["persoonnummer"]."', deelnemers='".$save["deelnemers"]."', ".($save["persoonnummer"]=="alg" ? "alg_aantal='".intval($alg_aantal)."', " : "")."naam='".addslashes($save["naam"])."', verkoop='".addslashes($save["verkoop"])."', inkoop='".addslashes($save["inkoop"])."', korting='".addslashes($save["korting"])."', omzetbonus='".addslashes($save["omzetbonus"])."', hoort_bij_accommodatieinkoop='".addslashes($save["hoort_bij_accommodatieinkoop"])."', optiecategorie='".addslashes($save["optiecategorie"])."', bijkomendekosten_id='".addslashes($db->f("bijkomendekosten_id"))."';");
				}
			}
		}


		//
		// bk_soort (fixed price)
		//

		unset($voorheen);

		// read old data
		$db->query("SELECT bk_soort_id, naam, verkoop, inkoop, korting, omzetbonus, hoort_bij_accommodatieinkoop, optiecategorie FROM extra_optie WHERE bk_soort_id IS NOT NULL AND boeking_id='".$gegevens["stap1"]["boekingid"]."';");
		while($db->next_record()) {
			$voorheen[$db->f("bk_soort_id")]["naam"]=$db->f("naam");
			$voorheen[$db->f("bk_soort_id")]["verkoop"]=$db->f("verkoop");
			$voorheen[$db->f("bk_soort_id")]["inkoop"]=$db->f("inkoop");
			$voorheen[$db->f("bk_soort_id")]["korting"]=$db->f("korting");
			$voorheen[$db->f("bk_soort_id")]["omzetbonus"]=$db->f("omzetbonus");
			$voorheen[$db->f("bk_soort_id")]["hoort_bij_accommodatieinkoop"]=$db->f("hoort_bij_accommodatieinkoop");
			$voorheen[$db->f("bk_soort_id")]["optiecategorie"]=$db->f("optiecategorie");
		}

		// Oude gegevens wissen (alleen bij bijkomende kosten die nu ook nog aanwezig zijn bij de accommodatie/type)
		$db->query("DELETE FROM extra_optie WHERE bk_soort_id IS NOT NULL AND boeking_id='".$gegevens["stap1"]["boekingid"]."';");

		// get bk
		$db->query("SELECT bs.bk_soort_id, bs.factuurnaam".$vars["ttv"]." AS factuurnaam, bs.prijs_per_nacht, bs.opgeven_bij_boeken, bs.zonderleeftijd, bs.min_leeftijd, bs.max_leeftijd, bs.hoort_bij_accommodatieinkoop, bs.optiecategorie, ba.eenheid, ba.borg_soort, ba.bedrag
				   FROM bk_soort bs INNER JOIN bk_type ba ON (bs.bk_soort_id=ba.bk_soort_id AND ba.type_id='".intval($this->id)."' AND ba.seizoen_id='".intval($this->seizoen_id)."') WHERE (ba.inclusief=0 AND ba.verplicht=1 AND ba.ter_plaatse=0)
				   ORDER BY bs.volgorde;");

		while($db->next_record()) {

			unset($save, $alg_aantal);

			if($db->f("eenheid")==2 or $db->f("eenheid")==12 or $db->f("eenheid")==9) {

				// eenheid = "per person" or "per person each time" or "per set"
				if($db->f("min_leeftijd") or $db->f("max_leeftijd")) {

					$save["persoonnummer"]="pers";
					if( is_array($gegevens["stap3"]) ) {
						foreach ($gegevens["stap3"] as $persoonnummer => $persoon) {
							if(is_int($persoonnummer)) {
								if($persoon["geboortedatum"]) {

									$leeftijd = wt_leeftijd($persoon["geboortedatum"], mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
									if($db->f("min_leeftijd") and $db->f("max_leeftijd")) {
										if($leeftijd>=$db->f("min_leeftijd") and $leeftijd<=$db->f("max_leeftijd")) {
											if($save["deelnemers"]) $save["deelnemers"].=",".$persoonnummer; else $save["deelnemers"]=$persoonnummer;
										}
									} elseif($db->f("min_leeftijd")) {
										if($leeftijd>=$db->f("min_leeftijd")) {
											if($save["deelnemers"]) $save["deelnemers"].=",".$persoonnummer; else $save["deelnemers"]=$persoonnummer;
										}
									} elseif($db->f("max_leeftijd")) {
										if($leeftijd<=$db->f("max_leeftijd")) {
											if($save["deelnemers"]) $save["deelnemers"].=",".$persoonnummer; else $save["deelnemers"]=$persoonnummer;
										}
									}
								} elseif($db->f("zonderleeftijd")) {
									if($save["deelnemers"]) $save["deelnemers"].=",".$persoonnummer; else $save["deelnemers"]=$persoonnummer;
								}
							}
						}
					}

				} else {
					$save["persoonnummer"]="alg";
					$alg_aantal = $gegevens["stap1"]["aantalpersonen"];
				}
			} else {
				$save["persoonnummer"]="alg";
				$alg_aantal = 1;
			}

			if($voorheen[$db->f("bk_soort_id")]) {
				$save["naam"]=$voorheen[$db->f("bk_soort_id")]["naam"];
				$save["verkoop"]=$voorheen[$db->f("bk_soort_id")]["verkoop"];
				$save["inkoop"]=$voorheen[$db->f("bk_soort_id")]["inkoop"];
				$save["korting"]=$voorheen[$db->f("bk_soort_id")]["korting"];
				$save["omzetbonus"]=$voorheen[$db->f("bk_soort_id")]["omzetbonus"];
				$save["hoort_bij_accommodatieinkoop"]=$voorheen[$db->f("bk_soort_id")]["hoort_bij_accommodatieinkoop"];
				$save["optiecategorie"]=$voorheen[$db->f("bk_soort_id")]["optiecategorie"];
			} else {
				$save["naam"]=$db->f("factuurnaam");

				$save["verkoop"] = $db->f("bedrag");

				// prijs_per_nacht + per dag (eenheid 3) + per nacht (eenheid 8)
				if($db->f("prijs_per_nacht") or $db->f("eenheid")==3 or $db->f("eenheid")==8) {
					// per night
					$save["verkoop"] = $save["verkoop"] * $gegevens["stap1"]["aantalnachten"];
				} elseif($db->f("eenheid")==10) {
					// per week
					$aantalweken = round($gegevens["stap1"]["aantalnachten"]/7);
					if($aantalweken==0) {
						$aantalweken = 1;
					}
					$save["verkoop"] = $save["verkoop"] * $aantalweken;
				}
				$save["inkoop"]=$save["verkoop"];
				$save["korting"]=0;
				$save["omzetbonus"]=0;
				$save["hoort_bij_accommodatieinkoop"]=$db->f("hoort_bij_accommodatieinkoop");
				$save["optiecategorie"]=$db->f("optiecategorie");
			}
			if($save["verkoop"]<>0) {
				// Alleen opslaan als verkoopprijs is gezet
				$db2->query("INSERT INTO extra_optie SET boeking_id='".$gegevens["stap1"]["boekingid"]."', persoonnummer='".$save["persoonnummer"]."', deelnemers='".$save["deelnemers"]."', ".($save["persoonnummer"]=="alg" ? "alg_aantal='".intval($alg_aantal)."', " : "")."naam='".addslashes($save["naam"])."', verkoop='".addslashes($save["verkoop"])."', inkoop='".addslashes($save["inkoop"])."', korting='".addslashes($save["korting"])."', omzetbonus='".addslashes($save["omzetbonus"])."', hoort_bij_accommodatieinkoop='".addslashes($save["hoort_bij_accommodatieinkoop"])."', optiecategorie='".addslashes($save["optiecategorie"])."', bk_soort_id='".addslashes($db->f("bk_soort_id"))."';");
			}

		}

		// Kijken of het factuurbedrag afwijkt van de berekende totale reissom (en dan "factuur_bedrag_wijkt_af" aanpassen)
		if($gegevens["stap1"]["totale_reissom"]>0) {
			$gegevens=get_boekinginfo($boekingid);
			if($gegevens["fin"]["totale_reissom"]>0) {
				$verschil=round($gegevens["stap1"]["totale_reissom"]-$gegevens["fin"]["totale_reissom"],2);
				if(abs($verschil)>0.01) {
					$factuur_bedrag_wijkt_af=1;
				} else {
					$factuur_bedrag_wijkt_af=0;
				}
				if($gegevens["stap1"]["aankomstdatum"]>time() and (($gegevens["stap1"]["factuur_bedrag_wijkt_af"] and !$factuur_bedrag_wijkt_af) or (!$gegevens["stap1"]["factuur_bedrag_wijkt_af"] and $factuur_bedrag_wijkt_af))) {
					$db2->query("UPDATE boeking SET factuur_bedrag_wijkt_af='".$factuur_bedrag_wijkt_af."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				}
			}
		}
	}

	public function get_booking_data($gegevens) {
		//
		// get data to show on invoice/factuur
		//

		global $vars;

		if($gegevens["stap1"]["accinfo"]["skipasid"] and !$gegevens["stap1"]["wederverkoop"]) {
			$this->arrangement = true;
		}

		$kosten = $this->get_costs();

		if($this->pre_boeken) {
			//
			// get data without a booking
			//

			// fill booking data
			$gegevens["stap1"]["aantalpersonen"] = $this->aantalpersonen;
			$gegevens["stap1"]["accinfo"] = $this->accinfo;

			for($i=1; $i<=$this->aantalpersonen ; $i++) {
				$gegevens["stap3"][$i] = true;
			}

			// toeristenbelasting: always 7 nights
			$gegevens["stap1"]["aantalnachten"] = 7;
		}

		if($this->vertrekinfo) {
			if(is_array($kosten["vars"])) {
				foreach ($kosten["vars"] as $key => $value) {
					foreach ($value as $key2 => $value2) {
						unset($use_key);
						if($value2["borg_soort"]==4) {
							$use_key = "diversen";
						} elseif($value2["borg_soort"]) {
							$use_key = "ter_plaatse";
						} elseif($key=="diversen") {
							$use_key = "diversen";
						} elseif($key=="uitbreiding") {
							$use_key = "uitbreiding";
						}
						if($use_key) {
							$return[$use_key][$key2]["naam"] = $value2["vouchernaam"];
							$return[$use_key][$key2]["bedrag"] = $value2["bedrag"];
							$return[$use_key][$key2]["eenheid"] = $value2["eenheid"];
							$return[$use_key][$key2]["zonderleeftijd"] = $value2["zonderleeftijd"];
							$return[$use_key][$key2]["min_leeftijd"] = $value2["min_leeftijd"];
							$return[$use_key][$key2]["max_leeftijd"] = $value2["max_leeftijd"];
							$return[$use_key][$key2]["borg_soort"] = $value2["borg_soort"];

							if($value2["borg_soort"]==4) {
								$return[$use_key][$key2]["naam"] .= ": ".$vars["bk_borg_soort"][4];
							} elseif($value2["bedrag"]>0) {
								$return[$use_key][$key2]["toonbedrag"] = "€ ".$this->toonbedrag($value2["bedrag"]);
								if($value2["prijs_per_nacht"]) {
									$return[$use_key][$key2]["toonbedrag"] .= " ".txt("pppn", "bijkomendekosten");
								} else {
									$return[$use_key][$key2]["toonbedrag"] .= " ".$vars["bk_eenheid"][$value2["eenheid"]];
								}
							} else {
								if($value2["verplicht"]==3) {
									// zelf te verzorgen
									$return[$use_key][$key2]["toonbedrag"] = txt("zelf-te-verzorgen", "bijkomendekosten");
								} elseif($value2["bedrag"]=="0.00") {
									// exacte hoogte onbekend
									$return[$use_key][$key2]["toonbedrag"] = txt("exact-bedrag-onbekend", "bijkomendekosten");
								}
								$return[$use_key][$key2]["bedragonbekend"] = true;
							}

							// borg accommodatie
							if($value2["borg_soort"] and $value2["borg_soort"]<>4 and $value2["borg_soort"]<>5) {
								$return[$use_key][$key2]["toonbedrag"] .= ", ".$vars["bk_borg_soort"][$value2["borg_soort"]];
							}

						}
					}
				}
			}
		}

		if(is_array($kosten["vars"]["inclusief"])) {

			foreach ($kosten["vars"]["inclusief"] as $key => $value) {
				$aantal = 0;
				if($key=="skipas" and $this->vertrekinfo) {
					//
					// show no ski lift pass on vertrekinfo
					//
					continue;
				} else {
					if($value["ter_plaatse"]==1) {
						//
						// ter_plaatse
						//
						if($this->vertrekinfo) {
							$return["ter_plaatse"][$key]["naam"] = $value["vouchernaam"];
						} else {
							$return["ter_plaatse"][$key]["naam"] = $value["factuurnaam"];
						}
						$return["ter_plaatse"][$key]["bedrag"] = $value["bedrag"];
						$return["ter_plaatse"][$key]["eenheid"] = $value["eenheid"];
						$return["ter_plaatse"][$key]["zonderleeftijd"] = $value["zonderleeftijd"];
						$return["ter_plaatse"][$key]["min_leeftijd"] = $value["min_leeftijd"];
						$return["ter_plaatse"][$key]["max_leeftijd"] = $value["max_leeftijd"];
						$aantal = 0;
						if($value["eenheid"]==2 or $value["eenheid"]==12 or $value["eenheid"]==9) {
							// eenheid = "per person" or "per person each time" or "per set"
							if($value["min_leeftijd"] or $value["max_leeftijd"]) {

								if( is_array($gegevens["stap3"]) ) {
									foreach ($gegevens["stap3"] as $persoonnummer => $persoon) {
										if(is_int($persoonnummer)) {
											if($persoon["geboortedatum"]) {

												$leeftijd = wt_leeftijd($persoon["geboortedatum"], mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
												if($value["min_leeftijd"] and $value["max_leeftijd"]) {
													if($leeftijd>=$value["min_leeftijd"] and $leeftijd<=$value["max_leeftijd"]) {
														$aantal++;
													}
												} elseif($value["min_leeftijd"]) {
													if($leeftijd>=$value["min_leeftijd"]) {
														$aantal++;
													}
												} elseif($value["max_leeftijd"]) {
													if($leeftijd<=$value["max_leeftijd"]) {
														$aantal++;
													}
												}
											} elseif($value["zonderleeftijd"]) {
												$aantal ++;
											}
										}
									}
								}
								$geboortedatum_ingevuld = @count($gegevens["stap3"]["geboortedatum_ingevuld"]);
								if(!$aantal and $geboortedatum_ingevuld==$gegevens["stap1"]["aantalpersonen"]) {
									unset($return["ter_plaatse"][$key]);
									continue;
								}

							} else {
								$aantal = $gegevens["stap1"]["aantalpersonen"];
							}
						} else {
							$aantal = 1;
						}

						$return["ter_plaatse"][$key]["aantal"] = $aantal;
						$return["ter_plaatse"][$key]["subtotaal"] = $value["bedrag"];
						$return["ter_plaatse"][$key]["totaalbedrag"] = $value["bedrag"] * $aantal;

						// prijs_per_nacht + per dag (eenheid 3) + per nacht (eenheid 8)
						if($value["prijs_per_nacht"] or $value["eenheid"]==3 or $value["eenheid"]==8) {
							$return["ter_plaatse"][$key]["totaalbedrag"] = $return["ter_plaatse"][$key]["totaalbedrag"] * $gegevens["stap1"]["aantalnachten"];
							$return["ter_plaatse"][$key]["subtotaal"] = $return["ter_plaatse"][$key]["subtotaal"] * $gegevens["stap1"]["aantalnachten"];
						}
						$return["ter_plaatse"][$key]["totaalbedrag"] = round($return["ter_plaatse"][$key]["totaalbedrag"], 2);

						if($return["ter_plaatse"][$key]["totaalbedrag"]>0) {
							$return["ter_plaatse_actief"] = true;
						}

						if($value["bedrag"]>0) {
							$return["ter_plaatse"][$key]["toonbedrag"] = "€ ".$this->toonbedrag($value["bedrag"]);
							if($value["prijs_per_nacht"]) {
								$return["ter_plaatse"][$key]["toonbedrag"] .= " ".txt("pppn", "bijkomendekosten");
							} else {
								$return["ter_plaatse"][$key]["toonbedrag"] .= " ".$vars["bk_eenheid"][$value["eenheid"]];
							}
						} else {
							$return["ter_plaatse"][$key]["toonbedrag"] = txt("exact-bedrag-onbekend", "bijkomendekosten");
							$return["ter_plaatse"][$key]["bedragonbekend"] = true;
						}
					} elseif($value["inclusief"]==1) {
						//
						// inclusief
						//
						if($this->vertrekinfo) {
							$return["voldaan"][$key]["naam"] = $value["vouchernaam"];
						} else {
							$return["voldaan"][$key]["naam"] = $value["factuurnaam"];
						}

					} elseif($value["verplicht"]==1 and $value["inclusief"]==0) {

						if($this->pre_boeken) {

							if($value["bedrag"]>0) {

								if($this->vertrekinfo) {
									$return["aan_chalet_nl"][$key]["naam"] = $value["vouchernaam"];
								} else {
									$return["aan_chalet_nl"][$key]["naam"] = $value["factuurnaam"];
								}

								// eenheid = "per person" or "per person each time"
								if($value["eenheid"]==2 or $value["eenheid"]==12 or $value["eenheid"]==9) {
									// eenheid = "per person" or "per person each time" or "per set"

									if((!$value["min_leeftijd"] and !$value["max_leeftijd"]) or $value["zonderleeftijd"]) {

										if($value["min_personen"] and $value["max_personen"]) {
											for( $i=1; $i<=$gegevens["stap1"]["aantalpersonen"]; $i++ ) {
												if($i>=$value["min_personen"] and $i<=$value["max_personen"]) {
													$aantal++;
												}
											}
										} else {
											$aantal = $gegevens["stap1"]["aantalpersonen"];
										}
									}
								} else {
									$aantal = 1;
								}

								$return["aan_chalet_nl"][$key]["aantal"] = $aantal;
								$return["aan_chalet_nl"][$key]["totaalbedrag"] = $value["bedrag"] * $aantal;

								// prijs_per_nacht + per dag (eenheid 3) + per nacht (eenheid 8)
								if($value["prijs_per_nacht"] or $value["eenheid"]==3 or $value["eenheid"]==8) {
									$return["aan_chalet_nl"][$key]["totaalbedrag"] = $return["aan_chalet_nl"][$key]["totaalbedrag"] * $gegevens["stap1"]["aantalnachten"];
								}

								$return["aan_chalet_nl"][$key]["toonbedrag"] = "€ ".$this->toonbedrag($value["bedrag"]);
								if($value["prijs_per_nacht"]) {
									$return["aan_chalet_nl"][$key]["toonbedrag"] .= " ".txt("pppn", "bijkomendekosten");
								} else {
									$return["aan_chalet_nl"][$key]["toonbedrag"] .= " ".$vars["bk_eenheid"][$value["eenheid"]];
								}
							}
						} else {
							if($this->vertrekinfo) {
								// include "verplicht vooraf" on vertrekinfo
								$return["voldaan"][$key]["naam"] = $value["vouchernaam"];
							}
						}
					}
				}
			}
		}

		if($this->pre_boeken) {

			// always reserveringskosten
			$reserveringskosten_calamiteiten = (in_array($vars['website'], $vars['sgr_c']));
			$return["aan_chalet_nl"]["reserveringskosten"]["naam"] = txt("reserveringskosten" . ($reserveringskosten_calamiteiten ? '_calamiteiten' : ''), "vars");
			$return["aan_chalet_nl"]["reserveringskosten"]["aantal"] = 1;
			$return["aan_chalet_nl"]["reserveringskosten"]["totaalbedrag"] = $vars["reserveringskosten"];

		}
		return $return;
	}

	public function get_specification() {

		//
		// get specification for 1 specific type/date/persons
		//

		global $vars;

		$this->get_data();

		$db = new DB_sql;

		$kosten = $this->get_costs()["html"];


		return $return;


	}

	public function toon_type() {

		//
		// toon teksten "inclusief", "exclusief" en "bijkomende kosten"
		//

		global $vars, $isMobile;

		$kosten = $this->get_costs()["html"];
		$variabele_kosten = $this->get_variable_costs();


		$return .= '<div class="tarieventabel_bkk_groups">';

		if(is_array($kosten["inclusief"])) {
			$return .= '<div class="tarieventabel_bkk_group">';
			$return .= "<h1>";
			if(constant("include_bkk")===true) {
				$return .= html("getoonde-prijs-inclusief","tarieventabel");
			} else {
				$return .= html("inclusief","tarieventabel");
			}
			$return .= ":</h1>";
			$return .= "<ul>";
			foreach ($kosten["inclusief"] as $key => $value) {
				// hide items with prices (based on euro-sign) when include_bkk=false
				if(constant("include_bkk")===true or strpos($value, "&euro;")===false) {
					$return .= "<li>".$value."</li>";
				}
			}
			$return .= "</ul>";
			$return .= '</div>'; // close .tarieventabel_bkk_group
		}
		if(is_array($kosten["uitbreiding"])) {
			$return .= '<div class="tarieventabel_bkk_group">';
			$return .= "<h1>".html("uitbreidingsmogelijkheden","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["uitbreiding"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
			$return .= '</div>'; // close .tarieventabel_bkk_group
		}
		if(is_array($kosten["diversen"])) {
			$return .= '<div class="tarieventabel_bkk_group">';
			$return .= "<h1>".html("diversen","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["diversen"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			if(is_array($variabele_kosten)) {
				foreach ($variabele_kosten as $key => $value) {
					$return .= "<li>".$value."</li>";
				}
			}
			$return .= "</ul>";
			$return .= '</div>'; // close .tarieventabel_bkk_group
		}

		$return .= '</div>'; // close .tarieventabel_bkk_groups

		$return .= "<div class=\"tarieventabel_kosten_ter_plaatse_indicatief\">";
		if($this->accinfo["begincode"]=="Z") {
			$return .= html("kosten-zwitserse-franken-indicatief","bijkomendekosten");
		} else {
			$return .= html("kosten-indicatief","bijkomendekosten");
		}
		$return .= "</div>";

		if(!$isMobile && !$this->newWebsite) {
			$return .= "<div class=\"toelichting_bereken_totaalbedrag\">";
			if (!$vars["wederverkoop"]) {
				$return.="<a href=\"".$vars["path"]."calc.php?tid=".intval($this->id)."&ap=".wt_he($_GET["ap"])."&d=".wt_he($_GET["d"])."&back=".urlencode($_SERVER["REQUEST_URI"])."\">".html("berekentotaalbedrag","tarieventabel")." &raquo;</a>";
			}
			$return .= "</div>"; # afsluiten .toelichting_bereken_totaalbedrag
		}

		return $return;
	}

	/**
	 * get the root for images, based on newWebsite
	 *
	 * @return string
	 **/
	private function getImageRoot()
	{
		// @todo: convert to DI
		global $vars;

		if ($this->newWebsite) {
			return 'https://www.chalet.nl/';
		} else {
			return $vars['path'];
		}
	}

}
