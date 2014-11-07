<?php


/**
* When a new function or site-change requires changes to the database (bookings for example), this class can be used.
* Every change uses a new function
*/
class convert_changes {

	function __construct() {

	}

	function surtax_extra_persons_to_bijkomendekosten($save_changes=true, $show_output=false) {

		$db = new DB_Sql;
		$db2 = new DB_Sql;
		$db3 = new DB_Sql;

		// which bijkomendekosten_id belong to bijkomendekosten "Extra personen"?
		$db->query("SELECT bijkomendekosten_id FROM bijkomendekosten WHERE min_personen IS NOT NULL;");
		while($db->next_record()) {
			$inquery1.=",".$db->f("bijkomendekosten_id");
		}

		// which optie_onderdeel_id belong to optie_soort "Extra personen"?
		$db->query("SELECT optie_onderdeel_id FROM `view_optie` WHERE `optie_soort_id`=34; ");
		while($db->next_record()) {
			$inquery2.=",".$db->f("optie_onderdeel_id");
		}
		if($inquery1 and $inquery2) {

			// which optie_onderdeel_id are connected to current bookings?
			$db->query("SELECT b.boekingsnummer, t.type_id, bo.optie_onderdeel_id, v.optie_groep_id, b.boeking_id, t.accommodatie_id FROM boeking_optie bo, boeking b, type t, view_optie v WHERE v.optie_onderdeel_id=bo.optie_onderdeel_id AND b.type_id=t.type_id AND b.boeking_id=bo.boeking_id AND b.aankomstdatum_exact>".time()." AND bo.optie_onderdeel_id IN (".substr($inquery2,1).") AND b.boekingsnummer<>'' AND (t.bijkomendekosten1_id IN (".substr($inquery1,1).") OR t.bijkomendekosten2_id IN (".substr($inquery1,1).") OR t.bijkomendekosten3_id IN (".substr($inquery1,1).") OR t.bijkomendekosten4_id IN (".substr($inquery1,1).") OR t.bijkomendekosten5_id IN (".substr($inquery1,1).") OR t.bijkomendekosten6_id IN (".substr($inquery1,1).")) ORDER BY boeking_id;");
			// echo $db->lq;
			while($db->next_record()) {

				$db2->query("SELECT accommodatie_id FROM optie_accommodatie WHERE optie_groep_id='".$db->f("optie_groep_id")."' AND accommodatie_id='".$db->f("accommodatie_id")."';");
				if(!$db2->num_rows()) {
					if($show_output) {
						$output .= "<li>";
						if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
							$output .= "F".$db->f("type_id")." - ";
						}
						$output .= "<a href=\"cms_boekingen.php?show=21&21k0=".$db->f("boeking_id")."&menu=1\" target=\"_blank\">".$db->f("boekingsnummer")."</a>";
						$output .= "</li>";
					}

					if($save_changes) {
						$db3->query("DELETE FROM boeking_optie WHERE boeking_id='".$db->f("boeking_id")."' AND optie_onderdeel_id='".$db->f("optie_onderdeel_id")."';");
						bereken_bijkomendekosten($db->f("boeking_id"));
					}
				}
			}
		}
		if($show_output) {
			echo "<br/>";
			if($save_changes) {
				echo "&quot;Toeslag extra personen&quot; is omgezet naar het bijkomende-kosten-systeem:<ul>";
			} else {
				echo "Bij de volgende boekingen moet de &quot;Toeslag extra personen&quot; worden omgezet naar het bijkomende-kosten-systeem:<ul>";
			}
			echo $output;
			echo "</ul>";
			if(!$save_changes and $output) {
				echo "<p><a href=\"cms_convert_changes.php?omzetten=1\">NU OMZETTEN &raquo;</a></p>";
			}
		}
	}
}


?>