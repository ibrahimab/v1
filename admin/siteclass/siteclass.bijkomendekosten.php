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
			if($this->soort=="type") {
				$db->query("SELECT seizoen_id, inclusief, verplicht, ter_plaatse, eenheid, borg_soort, bedrag FROM bk_type WHERE ;");
			} else {

				// wzt
				$db->query("SELECT wzt FROM accommodatie WHERE accommodatie_id='".intval($this->id)."';");
				// echo $db->lq;
				if($db->next_record()) {
					$this->wzt = $db->f("wzt");
				}

				$db->query("SELECT bs.bk_soort_id, bs.naam".$vars["ttv"]." AS naam, ba.seizoen_id, ba.inclusief, ba.verplicht, ba.ter_plaatse, ba.eenheid, ba.borg_soort, ba.bedrag FROM bk_accommodatie ba INNER JOIN bk_soort bs USING (bk_soort_id) INNER JOIN seizoen s USING(seizoen_id) WHERE ba.accommodatie_id='".intval($this->id)."' AND s.eind>=NOW() ORDER BY bs.volgorde;");
				while($db->next_record()) {
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["naam"] = $db->f("naam");
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["inclusief"] = $db->f("inclusief");
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["verplicht"] = $db->f("verplicht");
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["ter_plaatse"] = $db->f("ter_plaatse");
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["eenheid"] = $db->f("eenheid");
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["borg_soort"] = $db->f("borg_soort");
					$this->data[$db->f("seizoen_id")][$db->f("bk_soort_id")]["bedrag"] = $db->f("bedrag");
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

			// get seasons
			$db->query("SELECT seizoen_id, naam FROM seizoen WHERE eind>=(NOW() - INTERVAL 1 DAY) AND type='".intval($this->wzt)."' ORDER BY begin, eind;");
			echo $db->lq;
			while($db->next_record()) {
				$this->cms_data_seizoenen[$db->f("seizoen_id")] = $db->f("naam");
			}


			$this->get_cms_data_done = true;
		}
	}

	private function cms_add_cost_type() {
		$return .= "<form>Bijkomende kosten toevoegen: <select name=\"bk_new\">";
		$return .= "<option value=\"0\">-- kies kostensoort --</option>";

		foreach ($this->cms_data_bk_soorten as $key => $value) {
			$return .= "<option value=\"".$key."\">".wt_he($value["naam"])."</option>";
		}

		$return .= "</select></form>";


		return $return;

	}

	public function cms_enter_costs_per_acc_type() {

		$this->get_cms_data();


		foreach ($this->cms_data_seizoenen as $key => $value) {

			$this->seizoen_id = $key;

			$return .= "<div class=\"cms_bk_seizoen\" data-seizoen_id=\"".$key."\"><h2>Bijkomende kosten ".wt_he($value)." (nieuw systeem)</h2>";

			$return .= $this->cms_add_cost_type();
			$return .= $this->cms_all_rows();

			$return .= "</div>"; // close .cms_bk_seizoen
		}



		return $return;

	}

	public function cms_all_rows() {

		$return .= "<div class=\"cms_bk_all_rows\">";

		foreach ($this->data[$this->seizoen_id] as $key => $value) {
			$return .= wt_he($value["naam"]);
		}
		$return .= "</div>"; // close .cms_bk_all_rows
	}


	public function cms_new_row($bk_soort_id) {

		$this->get_cms_data();

		// $this->cms_data_bk_soorten[$db->f("bk_soort_id")]["volgorde"]

	}



}


?>