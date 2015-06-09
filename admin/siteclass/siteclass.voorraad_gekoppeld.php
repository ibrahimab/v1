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


		// $db2->log_slow_queries="/tmp/slow.log";
		// $db2->log_slow_queries_time=0;

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

		// vanaf-prijzen berekenen

		// nog doen: nieuw seizoen wel/niet opnemen: $vars["themainfo"]["tarievenbekend_seizoen_id"]

		if(is_array($GLOBALS["class_voorraad_gekoppeld_vanaf_prijzen_types"])) {


			$inquery=",0";
			foreach ($GLOBALS["class_voorraad_gekoppeld_vanaf_prijzen_types"] as $key => $value) {
				$inquery.=",".$key;
			}
			$inquery=substr($inquery,1);


			// $start_time=microtime(true);

			// tarievenbekend nieuw seizoen: zomer
			$tarievenbekend_seizoen_id.=",25"; // Italissima

			// tarievenbekend nieuw seizoen: winter
			$db->query("SELECT DISTINCT tarievenbekend_seizoen_id FROM thema WHERE actief=1;");
			while($db->next_record()) {
				$tarievenbekend_seizoen_id.=",".$db->f("tarievenbekend_seizoen_id");
			}


			// check for verzameltypes
			$db->query("SELECT DISTINCT verzameltype_parent FROM type WHERE verzameltype_parent IS NOT NULL AND type_id IN (".$inquery.");");
			while($db->next_record()) {
				$GLOBALS["class_voorraad_gekoppeld_vanaf_prijzen_types"].=",".$db->f("verzameltype_parent");
			}


			$db->query("UPDATE cache_vanafprijs_type SET wis=1 WHERE type_id IN (".$inquery.");");
			$db->query("SELECT type_id, toonper, wzt FROM view_accommodatie WHERE type_id IN (".$inquery.");");
			while($db->next_record()) {
				unset($prijs, $update);
				if($db->f("toonper")==1) {
					//
					// arrangement (toonper=1)
					//
					$db2->query("SELECT MIN(tp.prijs) AS prijs_per_persoon FROM tarief tr, tarief_personen tp, seizoen sz WHERE tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id='".intval($db->f("type_id"))."' GROUP BY tp.type_id;");
					if($db2->next_record()) {
						$prijs["normaal"]=$db2->f("prijs_per_persoon");
					}
					$db2->query("SELECT MIN(tp.prijs) AS prijs_per_persoon FROM tarief tr, tarief_personen tp, seizoen sz WHERE tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id='".intval($db->f("type_id"))."' GROUP BY tp.type_id;");
					if($db2->next_record()) {
						$prijs["intern"]=$db2->f("prijs_per_persoon");
					}
					if($tarievenbekend_seizoen_id) {
						$db2->query("SELECT MIN(tp.prijs) AS prijs_per_persoon FROM tarief tr, tarief_personen tp, seizoen sz WHERE sz.seizoen_id IN (".substr($tarievenbekend_seizoen_id,1).") AND tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id='".intval($db->f("type_id"))."' GROUP BY tp.type_id;");
						if($db2->next_record()) {
							$prijs["normaal_nieuw_seizoen"]=$db2->f("prijs_per_persoon");
						}
					}

				} else {
					//
					// accommodatie (toonper=3)
					//

					$db2->query("SELECT MIN(tr.c_verkoop_site) AS c_verkoop_site FROM accommodatie a, type t, tarief tr, seizoen sz WHERE a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."' GROUP BY tr.type_id;");
					if($db2->next_record()) {
						$prijs["normaal"]=$db2->f("c_verkoop_site");
					}
					$db2->query("SELECT MIN(tr.c_verkoop_site) AS c_verkoop_site FROM accommodatie a, type t, tarief tr, seizoen sz WHERE a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."' GROUP BY tr.type_id;");
					if($db2->next_record()) {
						$prijs["intern"]=$db2->f("c_verkoop_site");
					}
					if($tarievenbekend_seizoen_id) {
						$db2->query("SELECT MIN(tr.c_verkoop_site) AS c_verkoop_site FROM accommodatie a, type t, tarief tr, seizoen sz WHERE sz.seizoen_id IN (".substr($tarievenbekend_seizoen_id,1).") AND a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen=4 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."' GROUP BY tr.type_id;");
						if($db2->next_record()) {
							$prijs["normaal_nieuw_seizoen"]=$db2->f("c_verkoop_site");
						}
					}
				}

				if($db->f("wzt")==1) {
					// wederverkoop-prijs bepalen
					$db2->query("SELECT tr.type_id, MIN(tr.wederverkoop_verkoopprijs) AS wederverkoop_verkoopprijs FROM accommodatie a, type t, tarief tr, seizoen sz WHERE (a.toonper=1 OR a.toonper=3) AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.blokkeren_wederverkoop=0 AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."' GROUP BY tr.type_id;");
					if($db2->next_record()) {
						$prijs["wederverkoop"]=$db2->f("wederverkoop_verkoopprijs");
					}

					// wederverkoop-prijs intern bepalen
					$db2->query("SELECT tr.type_id, MIN(tr.wederverkoop_verkoopprijs) AS wederverkoop_verkoopprijs FROM accommodatie a, type t, tarief tr, seizoen sz WHERE (a.toonper=1 OR a.toonper=3) AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.blokkeren_wederverkoop=0 AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."' GROUP BY tr.type_id;");
					if($db2->next_record()) {
						$prijs["wederverkoop_intern"]=$db2->f("wederverkoop_verkoopprijs");
					}

				}

				$update.=", prijs='".addslashes($prijs["normaal"])."'";
				$update.=", prijs_intern='".addslashes($prijs["intern"])."'";
				$update.=", prijs_wederverkoop='".addslashes($prijs["wederverkoop"])."'";
				$update.=", prijs_wederverkoop_intern='".addslashes($prijs["wederverkoop_intern"])."'";
				$update.=", prijs_nieuw_seizoen='".addslashes($prijs["normaal_nieuw_seizoen"])."'";
				$update.=", wis=0";

				$update=substr($update,2);
				$db2->query("INSERT INTO cache_vanafprijs_type SET type_id='".intval($db->f("type_id"))."', ".$update.", adddatetime=NOW(), editdatetime=NOW() ON DUPLICATE KEY UPDATE ".$update.", editdatetime=NOW();");
				// echo $db2->lq."<br>";

			}
			$db->query("DELETE FROM cache_vanafprijs_type WHERE wis=1 AND type_id IN (".$inquery.");");
			// echo $db->lq."<br>";

			// $query_time=microtime(true)-$start_time;
			// echo "Time:".$query_time;

		}
	}

	public function vanaf_prijzen_berekenen($type_id) {
		$type_id = intval($type_id);
		$GLOBALS["class_voorraad_gekoppeld_vanaf_prijzen_types"][$type_id]=true;

		// use new vanafprijs-class
		vanafprijs::set_type_to_calculate($type_id);

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