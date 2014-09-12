<?php

/**
* Bijkomende kosten
*/


/*

todo:
- doorlezen PRO-27 en vergelijken met ontwikkelde functionaliteit: alles afgedekt?



eventueel later:
- accommodatie-niveau per regel opslaan
- kopieren vanaf ander seizoen


bespreken:
- waar is "borg" "niet van toepassing" voor nodig?
- gegevens overnemen bij "accommodatie kopieren" nodig?

*/

class bijkomendekosten {

	private $combine_all_rows_for_log_first_item = true;

	function __construct($id, $soort="type") {
		$this->id = $id;
		$this->soort = $soort;

	}

	private function get_data() {

		// get data from database for 1 type or accommodation

		if(!$this->get_data_done) {

			global $vars;

			$db = new DB_sql;

			// get data from database

			// wzt
			$db->query("SELECT wzt FROM view_accommodatie WHERE ".$this->soort."_id='".intval($this->id)."';");
			if($db->next_record()) {
				$this->wzt = $db->f("wzt");
			}

			// get seasons
			$this->seizoen_inquery .= ",0";
			$db->query("SELECT seizoen_id, naam FROM seizoen WHERE eind>=(NOW() - INTERVAL 1 DAY) AND type='".intval($this->wzt)."' ORDER BY begin, eind;");
			// echo $db->lq;
			while($db->next_record()) {
				$this->cms_data_seizoenen[$db->f("seizoen_id")] = $db->f("naam");
				$this->seizoen_inquery .= ",".$db->f("seizoen_id");
			}


			if(is_array($this->cms_data_seizoenen)) {
				foreach ($this->cms_data_seizoenen as $key => $value) {
					$db->query("SELECT bs.bk_soort_id, bs.naam".$vars["ttv"]." AS naam, bs.altijd_invullen, ba.".$this->soort."_id, ba.seizoen_id, ba.inclusief, ba.verplicht, ba.ter_plaatse, ba.eenheid, ba.borg_soort, ba.bedrag FROM bk_soort bs LEFT JOIN bk_".$this->soort." ba ON (bs.bk_soort_id=ba.bk_soort_id AND ba.".$this->soort."_id='".intval($this->id)."' AND ba.seizoen_id='".intval($key)."') WHERE (ba.".$this->soort."_id IS NOT NULL OR bs.altijd_invullen=1)
					           ORDER BY ba.inclusief DESC, bs.volgorde;");


					while($db->next_record()) {
						$seizoen_id = $key;

						$this->data[$seizoen_id][$db->f("bk_soort_id")]["naam"] = $db->f("naam");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["inclusief"] = $db->f("inclusief");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["verplicht"] = $db->f("verplicht");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["ter_plaatse"] = $db->f("ter_plaatse");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["eenheid"] = $db->f("eenheid");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["borg_soort"] = $db->f("borg_soort");
						$this->data[$seizoen_id][$db->f("bk_soort_id")]["bedrag"] = $db->f("bedrag");
						if($db->f("seizoen_id")) {
							$this->data[$seizoen_id][$db->f("bk_soort_id")]["filled"] = true;
						}
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


			// get tmp_teksten_omgezet
			$db->query("SELECT tmp_teksten_omgezet FROM ".$this->soort." WHERE ".$this->soort."_id='".intval($this->id)."';");
			if($db->next_record()) {
				$this->cms_data_tmp_teksten_omgezet = $db->f("tmp_teksten_omgezet");
			}

			$db->query("SELECT bk_soort_id, volgorde, naam".$vars["ttv"]." AS naam, eenheden, altijd_invullen, prijs_per_nacht, borg, hoort_bij_accommodatieinkoop FROM bk_soort ORDER BY volgorde, bk_soort_id;");
			while($db->next_record()) {
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["volgorde"] = $db->f("volgorde");
				$this->cms_data_bk_soorten[$db->f("bk_soort_id")]["naam"] = $db->f("naam");

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

		$this->get_cms_data();


		if(is_array($this->cms_data_seizoenen))	{
			foreach ($this->cms_data_seizoenen as $key => $value) {

				$this->seizoen_id = $key;

				$return .= "<div class=\"cms_bk_seizoen\" data-seizoen_id=\"".$key."\" id=\"bijkomendekosten\"><h2>Bijkomende kosten ".wt_he($value).($this->soort=="type" ? " - type-niveau" : "")."</h2>";



				$db = new DB_sql;

				if($this->soort=="type") {
					$db->query("SELECT inclusief, exclusief, bk_opmerkingen_intern FROM accommodatie WHERE accommodatie_id=(SELECT accommodatie_id FROM type WHERE type_id='".intval($this->id)."');");
				} else {
					$db->query("SELECT inclusief, exclusief, bk_opmerkingen_intern FROM accommodatie WHERE accommodatie_id='".intval($this->id)."';");
				}
				if($db->next_record()) {

					$bk_opmerkingen_intern = $db->f("bk_opmerkingen_intern");

					if($db->f("inclusief")) {
						$inclusief_tekst_html .= "<h5>Inclusief-tekst accommodatie-niveau</h5>".nl2br(wt_htmlent($db->f("inclusief")))."<br/>";
						if($this->soort=="accommodatie") {
							$in_exclusief_tekst = true;
						}
					}
					if($db->f("exclusief")) {
						$exclusief_tekst_html .= "<h5>Exclusief-tekst accommodatie-niveau</h5>".nl2br(wt_htmlent($db->f("exclusief")))."<br/>";
						if($this->soort=="accommodatie") {
							$in_exclusief_tekst = true;
						}
					}
				}
				if($this->soort=="type") {
					$db->query("SELECT inclusief, exclusief FROM type WHERE type_id='".intval($this->id)."';");
					if($db->next_record()) {
						if($db->f("inclusief")) {
							if($inclusief_tekst_html) {
								$inclusief_tekst_html .= "<br/><br/>";
							}
							$inclusief_tekst_html .= "<h5>Inclusief-tekst type-niveau</h5>".nl2br(wt_htmlent($db->f("inclusief")))."<br/>";
							$in_exclusief_tekst = true;
						}
						if($db->f("exclusief")) {
							if($exclusief_tekst_html) {
								$exclusief_tekst_html .= "<br/><br/>";
							}
							$exclusief_tekst_html .= "<h5>Exclusief-tekst type-niveau</h5>".nl2br(wt_htmlent($db->f("exclusief")))."<br/>";
							$in_exclusief_tekst = true;
						}
					}

				} else {
					$db->query("SELECT type_id, inclusief, exclusief FROM type WHERE accommodatie_id='".intval($this->id)."' ORDER BY type_id;");
					while($db->next_record()) {
						if($db->f("inclusief")) {
							if($inclusief_tekst_html) {
								$inclusief_tekst_html .= "<br/>";
							}
							$inclusief_tekst_html .= "<h5>Inclusief-tekst type-niveau: <a href=\"".$vars["path"]."cms_types.php?show=2&1k0=".intval($this->id)."&2k0=".$db->f("type_id")."#bijkomendekosten\" target=\"_blank\">".$this->all_types[$db->f("type_id")]."</a></h5>";
						}
						if($db->f("exclusief")) {
							if($exclusief_tekst_html) {
								$exclusief_tekst_html .= "<br/>";
							}
							$exclusief_tekst_html .= "<h5>Exclusief-tekst type-niveau: <a href=\"".$vars["path"]."cms_types.php?show=2&1k0=".intval($this->id)."&2k0=".$db->f("type_id")."#bijkomendekosten\" target=\"_blank\">".$this->all_types[$db->f("type_id")]."</a></h5>";
						}
					}
				}

				if($inclusief_tekst_html or $exclusief_tekst_html) {
					$return .= "<table class=\"cms_bk_oude_teksten\"><tr>";
					if($inclusief_tekst_html) {
						$return .= "<td>".$inclusief_tekst_html."</td>";
					}
					if($exclusief_tekst_html) {
						$return .= "<td>".$exclusief_tekst_html."</td>";
					}
					$return .= "</tr></table>";
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

				if($in_exclusief_tekst) {
					$return .= "<div style=\"text-align:right;margin-top:15px;margin-bottom:15px;\"><input type=\"checkbox\" name=\"tmp_teksten_omgezet\" value=\"1\" id=\"tmp_teksten_omgezet\"".($this->cms_data_tmp_teksten_omgezet ? " checked" : "")."><label for=\"tmp_teksten_omgezet\">&nbsp;alle in- en exclusief-teksten van ".($this->soort=="type" ? "dit type" : "deze accommodatie")." zijn verwerkt</label></div>";
					// $return .= "<input type=\"checkbox\" name=\"tmp_teksten_omgezet\" value=\"1\" id=\"tmp_teksten_omgezet\"".($this->cms_data_tmp_teksten_omgezet ? " checked" : "")."><label for=\"tmp_teksten_omgezet\">&nbsp;alle in- en exclusief-teksten van ".($this->soort=="type" ? "dit type" : "deze accommodatie")." zijn verwerkt</label>";
				}
				// $return .= "<input type=\"submit\" value=\"OPSLAAN\"".($this->other_type_data ? " disabled=\"disabled\"" : "")."><img src=\"".$vars["path"]."pic/ajax-loader.gif\" class=\"ajaxloader\">";
				$return .= "<input type=\"submit\" value=\"OPSLAAN\"><img src=\"".$vars["path"]."pic/ajax-loader.gif\" class=\"ajaxloader\">";
				$return .= "<div class=\"clear\"></div>";

				if($this->other_type_data) {
					$return .= "<div class=\"cms_bk_type_afwijkingen_overschrijven\"><input type=\"checkbox\" name=\"type_afwijkingen_overschrijven\" id=\"type_afwijkingen_overschrijven\"><label for=\"type_afwijkingen_overschrijven\">&nbsp;rode type-afwijkingen overschrijven</label></div>";
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
							$return .= "<div>&nbsp;&nbsp;&nbsp;afwijking type <a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".intval($_GET["wzt"])."&archief=".intval($_GET["archief"])."&1k0=".intval($_GET["1k0"])."&2k0=".$key2."#bijkomendekosten\" target=\"_blank\">".$value2."</a> (".$this->all_types_aantalpersonen[$key2].")".($other_type_empty ? ": kosten gewist" : "")."</div>";
							$return .= "<div>".wt_he($vars["bk_inclusief"][$this->check_for_differences_type_accommodation("inclusief", $key, $key2)])."</div>";
							$return .= "<div>".wt_he($vars["bk_verplicht"][$this->check_for_differences_type_accommodation("verplicht", $key, $key2)])."</div>";
							if($this->cms_data_bk_soorten[$key]["borg"]) {
								$return .= "<div>".wt_he($vars["bk_borg_soort"][$this->check_for_differences_type_accommodation("borg_soort", $key, $key2)])."</div>";
							} else {
								$return .= "<div>".wt_he($vars["bk_ter_plaatse"][$this->check_for_differences_type_accommodation("ter_plaatse", $key, $key2)])."</div>";
							}
							$return .= "<div class=\"cms_bk_bedrag\">".wt_he($this->check_for_differences_type_accommodation("bedrag", $key, $key2))."</div>";
							$return .= "<div>".wt_he($vars["bk_eenheid"][$this->check_for_differences_type_accommodation("eenheid", $key, $key2)])."</div>";
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
						$return .= "<div>&nbsp;&nbsp;&nbsp;afwijking type <a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".intval($_GET["wzt"])."&archief=".intval($_GET["archief"])."&1k0=".intval($_GET["1k0"])."&2k0=".$key2."#bijkomendekosten\" target=\"_blank\">".$value2["type"]."</a> (".$this->all_types_aantalpersonen[$key2].") ".($other_type_empty ? ": kosten gewist" : "")."</div>";
						$return .= "<div>".wt_he($vars["bk_inclusief"][$this->check_for_differences_type_accommodation("inclusief", $key, $key2)])."</div>";
						$return .= "<div>".wt_he($vars["bk_verplicht"][$this->check_for_differences_type_accommodation("verplicht", $key, $key2)])."</div>";
						if($this->cms_data_bk_soorten[$key]["borg"]) {
							$return .= "<div>".wt_he($vars["bk_borg_soort"][$this->check_for_differences_type_accommodation("borg_soort", $key, $key2)])."</div>";
						} else {
							$return .= "<div>".wt_he($vars["bk_ter_plaatse"][$this->check_for_differences_type_accommodation("ter_plaatse", $key, $key2)])."</div>";
						}
						$return .= "<div>".wt_he($this->check_for_differences_type_accommodation("bedrag", $key, $key2))."</div>";
						$return .= "<div>".wt_he($vars["bk_eenheid"][$this->check_for_differences_type_accommodation("eenheid", $key, $key2)])."</div>";
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
			$return .= "<div>".$this->select_field("borg_soort[".$bk_soort_id."]", $vars["bk_borg_soort"], $data["borg_soort"])."</div>";

			$this->combine_all_rows_for_log($vars["bk_borg_soort"][$data["borg_soort"]]);

		} else {
			$return .= "<div>".$this->select_field("inclusief[".$bk_soort_id."]", $vars["bk_inclusief"], $data["inclusief"])."</div>";
			$return .= "<div>".$this->select_field("verplicht[".$bk_soort_id."]", $vars["bk_verplicht"], $data["verplicht"])."</div>";
			$return .= "<div>".$this->select_field("ter_plaatse[".$bk_soort_id."]", $vars["bk_ter_plaatse"], $data["ter_plaatse"])."</div>";

			$this->combine_all_rows_for_log($vars["bk_inclusief"][$data["inclusief"]]);
			$this->combine_all_rows_for_log($vars["bk_verplicht"][$data["verplicht"]]);
			$this->combine_all_rows_for_log($vars["bk_ter_plaatse"][$data["ter_plaatse"]]);

		}
		$bedrag = preg_replace("@\.@", ",", $data["bedrag"]);
		$return .= "<div><input type=\"text\" name=\"bedrag\" value=\"".wt_he($bedrag)."\" required=\"required\" pattern=\"^\d+(\.|\,)?\d{0,2}$\" autocomplete=\"off\"></div>";
		$this->combine_all_rows_for_log($bedrag);


		unset($eenheden);
		foreach ($vars["bk_eenheid"] as $key => $value) {
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



}


?>