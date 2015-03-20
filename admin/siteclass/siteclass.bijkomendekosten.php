<?php

/**
* Bijkomende kosten
*/


/*

todo:
- doorlezen CMS-27 en vergelijken met ontwikkelde functionaliteit: alles afgedekt?



eventueel later:
- accommodatie-niveau per regel opslaan

bespreken:
- waar is "borg" "niet van toepassing" voor nodig?
- gegevens overnemen bij "accommodatie kopieren" nodig?

*/

class bijkomendekosten {

	private $combine_all_rows_for_log_first_item = true;
	public $arrangement = false;

	function __construct($id=0, $soort="type") {
		$this->id = $id;
		$this->soort = $soort;

		$this->wt_redis = new wt_redis;

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
			$db->query("SELECT seizoen_id, naam FROM seizoen WHERE eind>=(NOW() - INTERVAL 1 DAY) AND type='".intval($this->wzt)."'".($this->hide_inactive_seasons ? " AND tonen<>1" : "")." ORDER BY begin, eind;");
			// echo $db->lq;
			while($db->next_record()) {
				$this->cms_data_seizoenen[$db->f("seizoen_id")] = $db->f("naam");
				$this->seizoen_inquery .= ",".$db->f("seizoen_id");
			}


			if(is_array($this->cms_data_seizoenen)) {
				foreach ($this->cms_data_seizoenen as $key => $value) {
					$db->query("SELECT bs.bk_soort_id, bs.naam".$vars["ttv"]." AS naam, bs.altijd_invullen, bs.altijd_diversen, bs.prijs_per_nacht, bs.opgeven_bij_boeken, ba.".$this->soort."_id, ba.seizoen_id, ba.inclusief, ba.verplicht, ba.ter_plaatse, ba.eenheid, ba.borg_soort, ba.bedrag
							   FROM bk_soort bs LEFT JOIN bk_".$this->soort." ba ON (bs.bk_soort_id=ba.bk_soort_id AND ba.".$this->soort."_id='".intval($this->id)."' AND ba.seizoen_id='".intval($key)."') WHERE bs.wzt='".intval($this->wzt)."' AND (ba.".$this->soort."_id IS NOT NULL OR bs.altijd_invullen=1)
							   ORDER BY ba.borg_soort DESC, ba.inclusief DESC, bs.volgorde;");


					while($db->next_record()) {
						$seizoen_id = $key;

						$this->data[$seizoen_id][$db->f("bk_soort_id")]["naam"] = $db->f("naam");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["inclusief"] = $db->f("inclusief");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["verplicht"] = $db->f("verplicht");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["ter_plaatse"] = $db->f("ter_plaatse");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["eenheid"] = $db->f("eenheid");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["borg_soort"] = $db->f("borg_soort");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["bedrag"] = $db->f("bedrag");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["altijd_diversen"] = $db->f("altijd_diversen");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["prijs_per_nacht"] = $db->f("prijs_per_nacht");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["opgeven_bij_boeken"] = $db->f("opgeven_bij_boeken");
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

					$db->query("SELECT b.bijkomendekosten_id, b.naam, bt.verkoop, bt.seizoen_id, bt.week, b.min_personen, b.max_personen FROM bijkomendekosten_tarief bt INNER JOIN bijkomendekosten b USING (bijkomendekosten_id) WHERE b.bijkomendekosten_id IN (".substr($bijkomendekosten_id_inquery, 1).") AND bt.seizoen_id IN (".substr($this->seizoen_inquery,1).") AND b.min_personen IS NOT NULL AND (b.min_leeftijd IS NULL OR b.zonderleeftijd=1) AND (b.max_leeftijd IS NULL OR b.zonderleeftijd=1) ORDER BY b.bijkomendekosten_id, bt.week;");
					while($db->next_record()) {

						$seizoen_id = $db->f("seizoen_id");

						if(!$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]) {
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["naam"] = $db->f("naam");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["min_personen"] = $db->f("min_personen");
							$this->data_var[$seizoen_id][$db->f("bijkomendekosten_id")]["max_personen"] = $db->f("max_personen");
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
				// $db->query("SELECT bt.type_id FROM bk_type bt LEFT JOIN bk_accommodatie ba ON (bt.inclusief=ba.inclusief AND bt.verplicht=ba.verplicht AND bt.ter_plaatse=ba.ter_plaatse AND bt.eenheid=ba.eenheid AND bt.borg_soort=ba.borg_soort AND bt.bedrag=ba.bedrag) WHERE bt.bk_soort_id=ba.bk_soort_id AND bt.seizoen_id=ba.seizoen_id AND bt.type_id IN (SELECT type_id FROM type WHERE accommodatie_id='".intval($this->id)."') AND ba.accommodatie_id='".intval($this->id)."';");
				// echo $db->lq;


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
		$this->hide_inactive_seasons = true;

		$this->get_cms_data();


		if(is_array($this->cms_data_seizoenen))	{
			foreach ($this->cms_data_seizoenen as $key => $value) {

				unset($inclusief_tekst_html, $exclusief_tekst_html, $in_exclusief_tekst);

				$this->seizoen_id = $key;

				if(!$this->seizoen_counter) {
					$return .= "<div id=\"bijkomendekosten\"></div>";
				}

				if($this->seizoen_counter==1 and $this->soort=="accommodatie") {
					// copy season button
					$return .= "<div class=\"cms_bk_kopieer cms_bk_kopieer_season\" data-last_seizoen_id=\"".intval($this->last_seizoen_id)."\" data-seizoen_id=\"".intval($this->seizoen_id)."\" data-id=\"".intval($this->id)."\"><button>&#x2193; kopieer bijkomende kosten naar onderstaand seizoen &#x2193;</button>&nbsp;&nbsp;<img src=\"".$vars["path"]."pic/ajax-loader-ebebeb.gif\"><br />&nbsp;&nbsp;&nbsp;<i>Let op: bestaande gegevens worden direct overschreven.</i></div>";

				}

				$this->seizoen_counter++;
				$this->last_seizoen_id = $this->seizoen_id;


				$return .= "<div class=\"cms_bk_seizoen cms_bk_seizoen_".($this->seizoen_counter==1 ? "first" : "nth")."\" data-seizoen_id=\"".$key."\" id=\"bijkomendekosten_".$key."\"><h2>Bijkomende kosten ".wt_he($value).($this->soort=="type" ? " - type-niveau" : "")."</h2>";



				$db = new DB_sql;

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
				$return .= "<button>kopieer kosten &raquo;</button>&nbsp;&nbsp;<img src=\"".$vars["path"]."pic/ajax-loader-ebebeb.gif\">";
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

				$return .= "<input type=\"submit\" value=\"OPSLAAN\"><img src=\"".$vars["path"]."pic/ajax-loader-transparent.gif\" class=\"ajaxloader\">";
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

	public function pre_calculate_all_types($limit=0) {

		global $cron;

		$db = new DB_sql;

		$db->query("SELECT DISTINCT type_id, wzt FROM view_accommodatie WHERE 1=1 ORDER BY type_id;");
		while($db->next_record()) {

			$last_save_time = $this->wt_redis->hget("bk:".$db->f("wzt").":".$db->f("type_id"), "saved");

			if($last_save_time<(time()-108000)) {

				$counter++;

				$this->pre_calculate_type($db->f("type_id"));

				$new[$db->f("wzt")] = true;

				if($limit and $counter>=$limit) {
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
			if(!$this->wt_redis->hexists("bk:".$db->f("wzt").":".$db->f("type_id"), "saved")) {
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

		$this->clear();

		$this->id = $type_id;
		$this->soort = "type";


		$this->get_data_done = false;
		$this->get_data();


		$this->wt_redis->del("bk:".$this->wzt.":".$type_id);

		if(is_array($this->data)) {
			foreach ($this->data as $seizoen_id => $data) {

				unset($per_person, $per_accommodation);

				// reservation costs
				$per_accommodation += $vars["reserveringskosten"];

				foreach ($data as $key => $value) {

					if($value["filled"] and $value["verplicht"]==1 and $value["inclusief"]<>1 and $value["bedrag"]>0 and !$value["borg_soort"]) {

						if($value["prijs_per_nacht"]) {
							$value["bedrag"] = $value["bedrag"] * 7;
						}

						if($value["eenheid"]==2) {
							// per person
							$per_person += $value["bedrag"];
						} else {
							// per accommodation
							$per_accommodation += $value["bedrag"];
						}
					}
				}

				for($i=$this->maxaantalpersonen;$i>=1;$i--) {
					$total = $per_accommodation + $i * $per_person;
					$this->wt_redis->hset("bk:".$this->wzt.":".$type_id, $seizoen_id.":".$i, $total);
					if($vars["tmp_info_tonen"]) {
						echo "bk:".$this->wzt.":".$type_id." - ".$seizoen_id.":".$i." - ".$total."<br/>";
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
		$this->wt_redis->del("bk_per_week:".$this->wzt.":".$type_id);

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
							$this->wt_redis->hset("bk_per_week:".$this->wzt.":".$type_id, $week.":".$persons, $total);
							if($vars["tmp_info_tonen"]) {
								echo "bk_per_week:".$this->wzt.":".$type_id." - ". $week.":".$persons." - ". $total."<br />";
								flush();
							}
						}
					}
				}
			}
		}

		$this->wt_redis->hset("bk:".$this->wzt.":".$type_id, "saved", time());

		if(!$GLOBALS["class_bijkomendekosten_register_shutdown_".$this->wzt]) {
			register_shutdown_function(array($this, "store_complete_cache"), $this->wzt);
			$GLOBALS["class_bijkomendekosten_register_shutdown_".$this->wzt]=true;
		}
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
		$return = $this->wt_redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$aantalpersonen);
		if(!$return) {
			$this->pre_calculate_type($type_id);

			$return = $this->wt_redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$aantalpersonen);
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
			$bedrag = $this->wt_redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$i);
			if(!$bedrag) {
				$this->pre_calculate_type($type_id);

				$bedrag = $this->wt_redis->hget("bk:".$wzt.":".$type_id, $seizoen_id.":".$i);
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

		$bedrag = $this->wt_redis->hgetall("bk_per_week:".$wzt.":".$type_id);

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

		$all_bk = $this->wt_redis->keys("bk:".$wzt.":*");
		if(is_array($all_bk)) {
			foreach ($all_bk as $key => $value) {
				$content=$this->wt_redis->hgetall($value, false);
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

		if(is_array($bk)) {
			$this->wt_redis->store_array("bk:".$wzt, "all", $bk);
			unset($bk);
		}

		//
		// surcharge extra persons
		//

		$all_bk = $this->wt_redis->keys("bk_per_week:".$wzt.":*");
		if(is_array($all_bk)) {
			foreach ($all_bk as $key => $value) {
				$content=$this->wt_redis->hgetall($value, false);
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
				$this->wt_redis->store_array("bk_per_week:".$wzt, "all_persons:".$key, $value);
			}
			unset($bk_all_persons);
		}
		// if(is_array($bk_all_weeks)) {
		// 	foreach ($bk_all_weeks as $key => $value) {
		// 		$this->wt_redis->store_array("bk_per_week:".$wzt, "all_weeks:".$key, $value);
		// 	}
		// 	unset($bk_all_weeks);
		// }
	}

	public function get_complete_cache($wzt) {

		$return = $this->wt_redis->get_array("bk:".$wzt, "all");

		return $return;

	}

	public function get_complete_cache_per_persons($wzt, $aantalpersonen) {

		$return = $this->wt_redis->get_array("bk_per_week:".$wzt, "all_persons:".$aantalpersonen);

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

		$this->get_data();

		$db = new DB_sql;

		// get accinfo
		if(!$this->accinfo) {
			$this->accinfo=accinfo($this->id);
		}

		if($this->arrangement) {
			// Skipasgegevens uit database halen
			$db->query("SELECT s.website_omschrijving".$vars["ttv"]." AS website_omschrijving FROM skipas s, accommodatie a WHERE a.skipas_id=s.skipas_id AND a.accommodatie_id='".intval($this->accinfo["accommodatie_id"])."';");
			if($db->next_record()) {
				if($db->f("website_omschrijving")) $skipas_website_omschrijving=$db->f("website_omschrijving");
			}
		}
		if(!$vars["wederverkoop"] and $skipas_website_omschrijving) {
			$kosten["inclusief"]["skipas"] = wt_he($skipas_website_omschrijving);
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
					} else {
						$cat = "uitbreiding";
					}

					if($value["borg_soort"]) {
						//
						// borg
						//
						$kosten[$cat][$key] = wt_he($value["naam"]);
						if($value["borg_soort"]==1 or $value["borg_soort"]==2 or $value["borg_soort"]==3 or $value["borg_soort"]==6) {
							$kosten[$cat][$key] .= " ".wt_he("(€ ".$this->toonbedrag($value["bedrag"])." ".($value["eenheid"]==2 ? " ".$vars["bk_eenheid"][$value["eenheid"]].", " : "").$vars["bk_borg_soort"][$value["borg_soort"]].")");
						} elseif($value["borg_soort"]==4) {
							$kosten[$cat][$key] .= ": ".html("geen-borg-verschuldigd", "bijkomendekosten");
						} elseif($value["borg_soort"]==5) {
							$kosten[$cat][$key] .= " (".html("ter-plaatse-te-voldoen", "bijkomendekosten").")";
						}
					} elseif($value["prijs_per_nacht"]) {
						//
						// toeristenbelasting
						//
						if($value["inclusief"]==1) {
							$kosten[$cat][$key] = wt_he($value["naam"]);
						} else {
							if($value["bedrag"]=="0.00") {
								$cat = "diversen";
								$kosten[$cat][$key] = wt_he($value["naam"]);
								$kosten[$cat][$key] .= " ".html("ter-plaatse-te-voldoen", "bijkomendekosten");
							} else {
								$kosten[$cat][$key] = wt_he($value["naam"]);
								$kosten[$cat][$key] .= " ".wt_he("(€ ".$this->toonbedrag($value["bedrag"])." ".txt("pppn", "bijkomendekosten"));
								if($value["ter_plaatse"]==1) {
									$kosten[$cat][$key] .= ", ".$vars["bk_ter_plaatse"][$value["ter_plaatse"]];
								}
								$kosten[$cat][$key] .= ")";
							}
						}
					} else {

						//
						// other costs
						//
						$kosten[$cat][$key] = wt_he($value["naam"]);
						if($value["verplicht"]==2 and $value["bedrag"]=="0.00") {
							$kosten[$cat][$key] .= " (".wt_he($vars["bk_verplicht"][2]);
							if($value["eenheid"]) {
								$kosten[$cat][$key] .=" ".wt_he($vars["bk_eenheid"][$value["eenheid"]]);
							}
							if($value["ter_plaatse"]==1) {
								$kosten[$cat][$key] .= ", ".wt_he($vars["bk_ter_plaatse"][$value["ter_plaatse"]]);
							}
							$kosten[$cat][$key] .= ")";
						} elseif($value["bedrag"]=="0.00") {
							$kosten[$cat][$key] .= " (".html("tegen-betaling", "bijkomendekosten").")";
						} elseif($value["bedrag"]>0) {
							$kosten[$cat][$key] .= " (";
							if($value["verplicht"]==2) {
								$kosten[$cat][$key] .= wt_he($vars["bk_verplicht"][2].": ");
							}
							$kosten[$cat][$key] .= wt_he("€ ".$this->toonbedrag($value["bedrag"]));
							if($value["eenheid"]) {
								$kosten[$cat][$key] .=" ".wt_he($vars["bk_eenheid"][$value["eenheid"]]);
							}
							if($value["opgeven_bij_boeken"] and $value["inclusief"]==0 and $value["verplicht"]==0) {
								$kosten[$cat][$key] .= ", ".html("opgeven-bij-boeking", "bijkomendekosten");
							}
							if($value["ter_plaatse"]==1) {
								$kosten[$cat][$key] .= ", ".wt_he($vars["bk_ter_plaatse"][$value["ter_plaatse"]]);
							}
							$kosten[$cat][$key] .= ")";
						} elseif($value["verplicht"]==3) {
							$kosten[$cat][$key] .= " (".$vars["bk_verplicht"][3].")";
						}
					}
				}
			}
		}

		$kosten["inclusief"]["reserveringskosten"] = wt_he(txt("reserveringskosten", "vars")." (€ ".$vars["reserveringskosten"].",- ".txt("perboeking", "vars").")");


		$kosten["uitbreiding"]["extraopties"] = html("bekijk-ook-extra-opties","tarieventabel",array("h_1"=>"<a href=\"#extraopties\">","h_2"=>" &raquo;</a>"));

		return $kosten;
	}

	public function get_variable_costs() {

		global $vars;

		$db = new DB_sql;
		$db2 = new DB_sql;

		// variable surcharges
		$db->query("SELECT a.bijkomendekosten1_id, a.bijkomendekosten2_id, a.bijkomendekosten3_id, a.bijkomendekosten4_id, a.bijkomendekosten5_id, a.bijkomendekosten6_id, t.bijkomendekosten1_id AS tbijkomendekosten1_id, t.bijkomendekosten2_id AS tbijkomendekosten2_id, t.bijkomendekosten3_id AS tbijkomendekosten3_id, t.bijkomendekosten4_id AS tbijkomendekosten4_id, t.bijkomendekosten5_id AS tbijkomendekosten5_id, t.bijkomendekosten6_id AS tbijkomendekosten6_id FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($this->id)."';");
		if($db->next_record()) {
			for($i=1;$i<=6;$i++) {
				if($db->f("bijkomendekosten".$i."_id")) {
					$variabele_bijkomendekosten_inquery.=",".$db->f("bijkomendekosten".$i."_id");
				}
				if($db->f("tbijkomendekosten".$i."_id")) {
					$variabele_bijkomendekosten_inquery.=",".$db->f("tbijkomendekosten".$i."_id");
				}
			}
		}

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
							$html .= "&thinsp;".$info_link."<img src=\"".$vars["path"]."pic/information_icon_with_padding.png\" /></a> ";
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

	public function toon_type() {

		//
		// toon teksten "inclusief", "exclusief" en "bijkomende kosten"
		//

		global $vars, $isMobile;

		$kosten = $this->get_costs();

		if(is_array($kosten["inclusief"])) {
			$return .= "<h1>".html("getoonde-prijs-inclusief","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["inclusief"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
		}
		if(is_array($kosten["uitbreiding"])) {
			$return .= "<h1>".html("uitbreidingsmogelijkheden","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["uitbreiding"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
		}
		if(is_array($kosten["diversen"])) {
			$return .= "<h1>".html("diversen","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["diversen"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
		}


		if(!$isMobile){
			$return .= "<div class=\"toelichting_bereken_totaalbedrag\">";
			if(!$vars["wederverkoop"]) {
					$return.="<a href=\"".$vars["path"]."calc.php?tid=".intval($this->id)."&ap=".wt_he($this->get_aantal_personen)."&d=".wt_he($_GET["d"])."&back=".urlencode($_SERVER["REQUEST_URI"])."\">".html("berekentotaalbedrag","tarieventabel")." &raquo;</a>";
			}
			$return .= "</div>"; # afsluiten .toelichting_bereken_totaalbedrag
		}

		return $return;
	}

	public function get_costs_temporary() {

		//
		// get all costs for 1 specific type : temporary function <<<<=====
		//

		global $vars;

		$this->get_data();

		$db = new DB_sql;

		// get accinfo
		if(!$this->accinfo) {
			$this->accinfo=accinfo($this->id);
		}

		if($this->arrangement) {
			// Skipasgegevens uit database halen
			$db->query("SELECT s.website_omschrijving".$vars["ttv"]." AS website_omschrijving FROM skipas s, accommodatie a WHERE a.skipas_id=s.skipas_id AND a.accommodatie_id='".intval($this->accinfo["accommodatie_id"])."';");
			if($db->next_record()) {
				if($db->f("website_omschrijving")) $skipas_website_omschrijving=$db->f("website_omschrijving");
			}
		}
		if(!$vars["wederverkoop"] and $skipas_website_omschrijving) {
			$kosten["inclusief"]["skipas"] = wt_he($skipas_website_omschrijving);
		}

		if(is_array($this->data[$this->seizoen_id])) {
			foreach ($this->data[$this->seizoen_id] as $key => $value) {

				if($value["filled"]) {

					if($value["borg_soort"]==4) {
						$cat = "diversen";
					} elseif($value["borg_soort"]) {
						$cat = "exclusief";
					} elseif($value["altijd_diversen"]) {
						$cat = "diversen";
					} elseif($value["inclusief"]==1) {
						$cat = "inclusief";
					} elseif($value["verplicht"]==1) {
						$cat = "exclusief";
					} elseif($value["verplicht"]==3) {
						$cat = "diversen";
					} elseif($value["verplicht"]==2) {
						$cat = "exclusief";
					} else {
						$cat = "uitbreiding";
					}


					if($value["borg_soort"]) {
						//
						// borg
						//
						$kosten[$cat][$key] = wt_he($value["naam"]);
						if($value["borg_soort"]==1 or $value["borg_soort"]==2 or $value["borg_soort"]==3 or $value["borg_soort"]==6) {
							$kosten[$cat][$key] .= " ".wt_he("(€ ".$this->toonbedrag($value["bedrag"])." ".($value["eenheid"]==2 ? " ".$vars["bk_eenheid"][$value["eenheid"]].", " : "").$vars["bk_borg_soort"][$value["borg_soort"]].")");
						} elseif($value["borg_soort"]==4) {
							$kosten[$cat][$key] .= ": ".html("geen-borg-verschuldigd", "bijkomendekosten");
						} elseif($value["borg_soort"]==5) {
							$kosten[$cat][$key] .= " (".html("ter-plaatse-te-voldoen", "bijkomendekosten").")";
						}
					} elseif($value["prijs_per_nacht"]) {
						//
						// toeristenbelasting
						//
						if($value["inclusief"]==1) {
							$kosten[$cat][$key] = wt_he($value["naam"]);
						} else {
							if($value["bedrag"]=="0.00") {
								$kosten[$cat][$key] = wt_he($value["naam"]);
								$kosten[$cat][$key] .= " (".html("ter-plaatse-te-voldoen", "bijkomendekosten").")";
							} else {
								$kosten[$cat][$key] = wt_he($value["naam"]);
								$kosten[$cat][$key] .= " ".wt_he("(€ ".$this->toonbedrag($value["bedrag"])." ".txt("pppn", "bijkomendekosten"));
								if($value["ter_plaatse"]==1) {
									$kosten[$cat][$key] .= ", ".$vars["bk_ter_plaatse"][$value["ter_plaatse"]];
								}
								$kosten[$cat][$key] .= ")";
							}
						}
					} else {
						//
						// other costs
						//
						$kosten[$cat][$key] = wt_he($value["naam"]);
						if($value["verplicht"]==2 and $value["bedrag"]=="0.00") {
							$kosten[$cat][$key] .= " (".wt_he($vars["bk_verplicht"][2]);
							if($value["eenheid"]) {
								$kosten[$cat][$key] .=" ".wt_he($vars["bk_eenheid"][$value["eenheid"]]);
							}
							if($value["ter_plaatse"]==1) {
								$kosten[$cat][$key] .= ", ".wt_he($vars["bk_ter_plaatse"][$value["ter_plaatse"]]);
							}
							$kosten[$cat][$key] .= ")";
						} elseif($value["bedrag"]=="0.00") {
							$kosten[$cat][$key] .= " (".html("tegen-betaling", "bijkomendekosten").")";
						} elseif($value["bedrag"]>0) {
							$kosten[$cat][$key] .= " (";
							if($value["verplicht"]==2) {
								$kosten[$cat][$key] .= wt_he($vars["bk_verplicht"][2].": ");
							}
							$kosten[$cat][$key] .= wt_he("€ ".$this->toonbedrag($value["bedrag"]));
							if($value["eenheid"]) {
								$kosten[$cat][$key] .=" ".wt_he($vars["bk_eenheid"][$value["eenheid"]]);
							}
							if($value["opgeven_bij_boeken"] and $value["inclusief"]==0 and $value["verplicht"]==0) {
								$kosten[$cat][$key] .= ", ".html("opgeven-bij-boeking", "bijkomendekosten");
							}
							if($value["ter_plaatse"]==1) {
								$kosten[$cat][$key] .= ", ".wt_he($vars["bk_ter_plaatse"][$value["ter_plaatse"]]);
							}
							$kosten[$cat][$key] .= ")";
						} elseif($value["verplicht"]==3) {
							$kosten[$cat][$key] .= " (".$vars["bk_verplicht"][3].")";
						}
					}

				}
			}
		}

		// $kosten["exclusief"]["reserveringskosten"] = wt_he(txt("reserveringskosten", "vars")." (€ ".$vars["reserveringskosten"].",- ".txt("perboeking", "vars").")");

		$kosten["uitbreiding"]["extraopties"] = html("bekijk-ook-extra-opties","tarieventabel",array("h_1"=>"<a href=\"#extraopties\">","h_2"=>" &raquo;</a>"));

		return $kosten;
	}


	public function toon_type_temporary() {

		//
		// toon teksten "inclusief", "exclusief" en "bijkomende kosten"
		//

		global $vars, $isMobile;

		$kosten = $this->get_costs_temporary();
		$variabele_kosten = $this->get_variable_costs();


		// Inclusief
		if(is_array($kosten["inclusief"])) {
			$return .= "<h1>".html("inclusief","toonaccommodatie").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["inclusief"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
		}

		// Verplicht te voldoen
		$return .= "<h1>".html("verplichttevoldoen","tarieventabel").":</h1>";
		$return .= "<ul>";
		if(is_array($kosten["exclusief"])) {
			foreach ($kosten["exclusief"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
		}
		if(is_array($variabele_kosten)) {
			foreach ($variabele_kosten as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
		}
		$return .= "<li>".wt_he(txt("reserveringskosten", "vars")." (€ ".$vars["reserveringskosten"].",- ".txt("perboeking", "vars").")")."</li>";
		$return .= "</ul>";

		// Optioneel
		if(is_array($kosten["uitbreiding"])) {
			$return .= "<h1>".html("optioneel","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["uitbreiding"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
		}

		// Diversen
		if(is_array($kosten["diversen"])) {
			$return .= "<h1>".html("diversen","tarieventabel").":</h1>";
			$return .= "<ul>";
			foreach ($kosten["diversen"] as $key => $value) {
				$return .= "<li>".$value."</li>";
			}
			$return .= "</ul>";
		}

		if($this->accinfo["begincode"]=="Z") {
			$return .= "<div class=\"tarieventabel_kosten_zwitserse_franken\">".html("kosten-zwitserse-franken","bijkomendekosten")."</div>";
		}

		if(!$isMobile) {
			$return .= "<div class=\"toelichting_bereken_totaalbedrag\">";
			if(!$vars["wederverkoop"]) {
				$return.="<a href=\"".$vars["path"]."calc.php?tid=".intval($this->id)."&ap=".wt_he($this->get_aantal_personen)."&d=".wt_he($_GET["d"])."&back=".urlencode($_SERVER["REQUEST_URI"])."\">".html("berekentotaalbedrag","tarieventabel")." &raquo;</a>";
			}
			$return .= "</div>"; # afsluiten .toelichting_bereken_totaalbedrag
		}


		return $return;
	}

}

?>