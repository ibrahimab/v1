<?php

/**
* class om de voorraad van een gekoppeld type bij te werken (op basis van het veld "voorraad_gekoppeld_type_id" uit de tabel "type")
*/
class voorraad_gekoppeld {

	function __construct() {

	}

	public function run_query() {

		global $vars;

		$db = new DB_sql;
		$db2 = new DB_sql;

		$db->query("SELECT DISTINCT t.type_id, t.verzameltype_parent, a.wzt FROM type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.voorraad_gekoppeld_type_id IS NOT NULL;");
		if($db->num_rows()) {

			$te_koppelen_velden = array(
				"beschikbaar",
				"voorraad_garantie",
				"voorraad_allotment",
				"voorraad_vervallen_allotment",
				"voorraad_optie_leverancier",
				"voorraad_xml",
				"voorraad_request",
				"voorraad_optie_klant",
				"voorraad_bijwerken"
			);

			foreach ($te_koppelen_velden as $key => $value) {
				$update_query.=", t2.".$value."=t1.".$value;
			}

			// actieve seizoenen bepalen
			$actieve_seizoenen="0";
			$db2->query("SELECT seizoen_id, type FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".(time()-864000)."' ORDER BY seizoen_id;");
			while($db2->next_record()) {
				$actieve_seizoenen.=",".$db2->f("seizoen_id");
				$actieve_seizoenen_array[$db2->f("seizoen_id")]=$db2->f("type");
			}

			while($db->next_record()) {
				$db2->query("UPDATE tarief t1, tarief t2, type t SET ".substr($update_query,1)." WHERE t2.type_id='".$db->f("type_id")."' AND t1.type_id=t.voorraad_gekoppeld_type_id AND t2.type_id=t.type_id AND t1.week=t2.week AND t1.seizoen_id=t2.seizoen_id AND t1.seizoen_id IN (".$actieve_seizoenen.");");
				// echo $db2->lq."<br/><br/><br/><br/>";
				// exit;

				if($db->f("verzameltype_parent") and $db2->affected_rows()>0) {
				// if($db->f("verzameltype_parent")) {
					$te_doorlopen_verzameltypes[$db->f("type_id")]=$db->f("wzt");
				}
			}
			if($te_doorlopen_verzameltypes) {
				foreach ($te_doorlopen_verzameltypes as $key => $value) {
					foreach ($actieve_seizoenen_array as $key2 => $value2) {
						if($value==$value2) {
							verzameltype_berekenen($key2, $key);
							// echo "verzameltype berekenen ".$key2." ".$key."<br/><br/>";
						}
					}
				}
			}
		}
	}

	public function koppeling_uitvoeren_na_einde_script() {
		//
		// zorgen dat koppeling wordt uitgevoerd aan het einde van het script
		//

		// slechts 1x toevoegen aan register_shutdown_function (daarvoor $GLOBALS["class_voorraad_gekoppeld_gerund"] gebruiken)
		if(!$GLOBALS["class_voorraad_gekoppeld_gerund"]) {
			register_shutdown_function(array($this, "run_query"));
			$GLOBALS["class_voorraad_gekoppeld_gerund"]=true;
		}
	}
}



?>