<?php

/**
* Bijkomende kosten
*/

class bijkomendekosten {

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
			$db->query("SELECT seizoen_id, naam FROM seizoen WHERE eind>=(NOW() - INTERVAL 1 DAY) AND type='".intval($this->wzt)."' ORDER BY begin, eind;");
			// echo $db->lq;
			while($db->next_record()) {
				$this->cms_data_seizoenen[$db->f("seizoen_id")] = $db->f("naam");
				$seizoen_inquery .= ",".$db->f("seizoen_id");
			}


			$db->query("SELECT bs.bk_soort_id, bs.naam".$vars["ttv"]." AS naam, bs.altijd_invullen, ba.".$this->soort."_id, ba.seizoen_id, ba.inclusief, ba.verplicht, ba.ter_plaatse, ba.eenheid, ba.borg_soort, ba.bedrag FROM bk_soort bs LEFT JOIN bk_".$this->soort." ba ON (bs.bk_soort_id=ba.bk_soort_id AND ba.".$this->soort."_id='".intval($this->id)."') WHERE ((ba.".$this->soort."_id IS NOT NULL AND ba.seizoen_id IN (".substr($seizoen_inquery,1).")) OR bs.altijd_invullen=1)
			           ORDER BY ba.inclusief DESC, bs.volgorde;");

			while($db->next_record()) {
				foreach ($this->cms_data_seizoenen as $key => $value) {
					if($db->f("seizoen_id")) {
						$seizoen_id = $db->f("seizoen_id");
					} else {
						$seizoen_id = $key;
					}

					$this->data[$seizoen_id][$db->f("bk_soort_id")]["naam"] = $db->f("naam");
					$this->data[$seizoen_id][$db->f("bk_soort_id")]["inclusief"] = $db->f("inclusief");
					$this->data[$seizoen_id][$db->f("bk_soort_id")]["verplicht"] = $db->f("verplicht");
					$this->data[$seizoen_id][$db->f("bk_soort_id")]["ter_plaatse"] = $db->f("ter_plaatse");
					$this->data[$seizoen_id][$db->f("bk_soort_id")]["eenheid"] = $db->f("eenheid");
					$this->data[$seizoen_id][$db->f("bk_soort_id")]["borg_soort"] = $db->f("borg_soort");
					$this->data[$seizoen_id][$db->f("bk_soort_id")]["bedrag"] = $db->f("bedrag");

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
			}

			$this->get_cms_data_done = true;
		}
	}

	private function cms_add_cost_type() {
		$return .= "Bijkomende kosten toevoegen: <select name=\"bk_new\">";
		$return .= "<option value=\"0\">-- kies toe te voegen kostensoort --</option>";

		foreach ($this->cms_data_bk_soorten as $key => $value) {
			$return .= "<option value=\"".$key."\">".wt_he($value["naam"])."</option>";
		}

		$return .= "</select>";


		return $return;

	}

	public function cms_enter_costs_per_acc_type() {

		global $vars;

		$this->get_cms_data();


		foreach ($this->cms_data_seizoenen as $key => $value) {

			$this->seizoen_id = $key;

			$return .= "<div class=\"cms_bk_seizoen\" data-seizoen_id=\"".$key."\"><h2>Bijkomende kosten ".wt_he($value).($this->soort=="type" ? " - type-niveau" : "")."</h2>";

			$return .= "<form method=\"post\">";
			$return .= "<input type=\"hidden\" name=\"seizoen_id\" value=\"".$key."\" />";
			$return .= "<input type=\"hidden\" name=\"soort\" value=\"".$this->soort."\" />";
			$return .= "<input type=\"hidden\" name=\"id\" value=\"".$this->id."\" />";

			$return .= $this->cms_add_cost_type();

			$return .= $this->cms_all_rows();

			$return .= "<input type=\"submit\" value=\"OPSLAAN\"><img src=\"".$vars["path"]."pic/ajax-loader.gif\" class=\"ajaxloader\">";

			$return .= "</form>";

			$return .= "</div>"; // close .cms_bk_seizoen
		}



		return $return;

	}

	public function cms_all_rows() {

		$return .= "<div class=\"cms_bk_all_rows\">";

		$return .= "<div class=\"cms_bk_row cms_bk_row_header\">";
		$return .= "<div>Optie</div>";
		$return .= "<div>Incl./Excl.</div>";
		$return .= "<div>Verplicht</div>";
		$return .= "<div>Betaling</div>";
		$return .= "<div>Bedrag</div>";
		$return .= "<div>Eenheid</div>";
		$return .= "<div>&nbsp;</div>";
		$return .= "</div>"; // close .cms_bk_row


		if(is_array($this->data[$this->seizoen_id])) {
			foreach ($this->data[$this->seizoen_id] as $key => $value) {
				// $return .= wt_he($value["naam"]);
				$return .= $this->cms_new_row($key);
			}
		}
		$return .= "</div>"; // close .cms_bk_all_rows

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

	public function cms_new_row($bk_soort_id, $empty=false) {

		global $vars;

		$this->get_cms_data();

		if(!$empty) {
			$data = $this->data[$this->seizoen_id][$bk_soort_id];
		}

		$return .= "<div class=\"cms_bk_row".($empty ? " cms_bk_new_row" : "")."\" data-soort_id=\"".$bk_soort_id."\">";
		$return .= "<div>".wt_he($this->cms_data_bk_soorten[$bk_soort_id]["naam"])."</div>";

		if($this->cms_data_bk_soorten[$bk_soort_id]["borg"]) {
			$return .= "<div>&nbsp;</div>";
			$return .= "<div>&nbsp;</div>";
			$return .= "<div>".$this->select_field("borg_soort[".$bk_soort_id."]", $vars["bk_borg_soort"], $data["borg_soort"])."</div>";
		} else {
			$return .= "<div>".$this->select_field("inclusief[".$bk_soort_id."]", $vars["bk_inclusief"], $data["inclusief"])."</div>";
			$return .= "<div>".$this->select_field("verplicht[".$bk_soort_id."]", $vars["bk_verplicht"], $data["verplicht"])."</div>";
			$return .= "<div>".$this->select_field("ter_plaatse[".$bk_soort_id."]", $vars["bk_ter_plaatse"], $data["ter_plaatse"])."</div>";
		}
		$return .= "<div><input type=\"text\" name=\"bedrag\" value=\"".wt_he(preg_replace("@\.@", ",", $data["bedrag"]))."\" required=\"required\" pattern=\"^\d+(\.|\,)?\d{0,2}$\"></div>";


		unset($eenheden);
		foreach ($vars["bk_eenheid"] as $key => $value) {
			if($this->cms_data_bk_soorten[$bk_soort_id]["eenheden"][$key]) {
				$eenheden[$key] = $value;
			}
		}
		if(is_array($eenheden)) {
			$return .= "<div>".$this->select_field("eenheid[".$bk_soort_id."]", $eenheden, $data["eenheid"])."</div>";
		} else {
			$return .= "<div>&nbsp;</div>";
		}

		if($this->cms_data_bk_soorten[$bk_soort_id]["altijd_invullen"]) {
			$return .= "<div>&nbsp;</div>";
		} else {
			$return .= "<div class=\"delete\" title=\"regel wissen\">&#x2716;</div>";
		}


		$return .= "</div>"; // close .cms_bk_row

		return $return;


	}



}


?>