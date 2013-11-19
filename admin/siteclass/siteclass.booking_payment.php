<?php


/**
* calculate the payments of a booking (advance payments, final payment)
*/
class booking_payment {

	public $gegevens;
	public $text;

	public $bereken_aanbetaling_opnieuw = false;

	function __construct($gegevens) {

		if($gegevens) {
			$this->gegevens = $gegevens;
		} else {
			trigger_error("empty \$gegevens parsed to class booking_payment",E_USER_NOTICE);
		}

		if(!$this->gegevens["stap1"]["factuurdatum_eerste_factuur"]) {
			// when no invoice has been created: use today as first invoice-date
			$this->gegevens["stap1"]["factuurdatum_eerste_factuur"]=mktime(0,0,0,date("m"),date("d"),date("Y"));
		}
	}

	public function get_amounts() {

		global $vars;

		$db = new DB_sql;


		// determine reeds voldaan
		$db->query("SELECT bedrag, UNIX_TIMESTAMP(datum) AS datum FROM boeking_betaling WHERE boeking_id='".intval($this->gegevens["stap1"]["boekingid"])."' ORDER BY datum;");
		while($db->next_record()) {
			$reedsvoldaan=round($reedsvoldaan+$db->f("bedrag"),2);
		}

		if($reedsvoldaan>0) {
			//
			// reeds voldaan
			//

			$this->text["reedsvoldaan"]=txt("reedsvoldaan","factuur");
			$this->amount["reedsvoldaan"]=$reedsvoldaan;
			$this->amount["beschikbaar_voor_aanbetalingen"]=$reedsvoldaan;
		}

		// aantal dagen na boeken bepalen
		$dagennaboeken=round((mktime(0,0,0,date("m"),date("d"),date("Y"))-$this->gegevens["stap1"]["factuurdatum_eerste_factuur"])/86400);
		$aanbetaling1_dagen_over=$this->gegevens["stap1"]["aanbetaling1_dagennaboeken"]-$dagennaboeken;


		# aantal dagen voor vertrek
		$datum_weken_voorvertrek_unixtime=mktime(0,0,0,date("m",$this->gegevens["stap1"]["aankomstdatum_exact"]),date("d",$this->gegevens["stap1"]["aankomstdatum_exact"])-$this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"],date("Y",$this->gegevens["stap1"]["aankomstdatum_exact"]));
		$datum_weken_voorvertrek=date("d/m/Y",$datum_weken_voorvertrek_unixtime);

		if($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]%7==0 and $this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]<>7) {
			$aanbetaling_aantalweken=round($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]/7);
		} else {
			$aanbetaling_aantaldagen=$this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"];
		}
		// if($this->gegevens["stap1"]["dagen_voor_vertrek"]>($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]+$this->gegevens["stap1"]["aanbetaling1_dagennaboeken"])) {

		// tot 45 dagen voor aankomst: aanbetaling tonen
		if($this->gegevens["stap1"]["dagen_voor_vertrek"]>($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]+3)) {

			//
			// Down payments
			//

			// aanbetaling 1
			if($aanbetaling1_dagen_over<=3) {
				$this->text["aanbetaling1"]=txt("perdirecttevoldoen","factuur");
			} else {
				$this->text["aanbetaling1"]=txt("binnenXdagentevoldoen","factuur",array("v_dagen"=>$aanbetaling1_dagen_over));
			}
			if($this->bereken_aanbetaling_opnieuw and !$this->gegevens["stap1"]["aanbetaling1_vastgezet"] and !$this->gegevens["stap1"]["aanbetaling1_gewijzigd"]) {
				$this->amount["aanbetaling1"]=$this->gegevens["fin"]["aanbetaling_ongewijzigd"];
			} else {
				$this->amount["aanbetaling1"]=$this->gegevens["fin"]["aanbetaling"];
			}

			if($this->amount["beschikbaar_voor_aanbetalingen"]>=$this->amount["aanbetaling1"]) {
				$this->amount["aanbetaling1_voldaan"]=$this->amount["aanbetaling1"];
				$this->amount["aanbetaling1"]=0;
			} elseif($this->amount["beschikbaar_voor_aanbetalingen"]) {
				$this->amount["aanbetaling1_voldaan"]=$this->amount["beschikbaar_voor_aanbetalingen"];
				$this->amount["aanbetaling1"]=$this->amount["aanbetaling1"]-$this->amount["beschikbaar_voor_aanbetalingen"];
			}
			$this->amount["beschikbaar_voor_aanbetalingen"]=$this->amount["beschikbaar_voor_aanbetalingen"]-$this->amount["aanbetaling1_voldaan"];

			// aanbetaling 2
			if($this->gegevens["stap1"]["aanbetaling2"] and $this->gegevens["stap1"]["aanbetaling2_datum"]) {
				if($this->gegevens["stap1"]["aanbetaling2_datum"]>time()) {
					$this->text["aanbetaling2"]=txt("uiterlijkdatumtebetalen","factuur",array("v_datum"=>date("d/m/Y",$this->gegevens["stap1"]["aanbetaling2_datum"])));

					$this->amount["aanbetaling2"]=$this->gegevens["stap1"]["aanbetaling2"];

					if($this->amount["beschikbaar_voor_aanbetalingen"]>=$this->amount["aanbetaling2"]) {
						$this->amount["aanbetaling2_voldaan"]=$this->amount["aanbetaling2"];
						$this->amount["aanbetaling2"]=0;
					} elseif($this->amount["beschikbaar_voor_aanbetalingen"]) {
						$this->amount["aanbetaling2_voldaan"]=$this->amount["beschikbaar_voor_aanbetalingen"];
						$this->amount["aanbetaling2"]=$this->amount["aanbetaling2"]-$this->amount["beschikbaar_voor_aanbetalingen"];
					}
				}

				$this->amount["beschikbaar_voor_aanbetalingen"]=$this->amount["beschikbaar_voor_aanbetalingen"]-$this->amount["aanbetaling2_voldaan"];

			}

			// final payment
			if($aanbetaling_aantalweken) {
				$this->text["eindbetaling"]=txt("uiterlijkXwekenvoorvertrek","factuur",array("v_weken"=>$aanbetaling_aantalweken,"v_datum"=>$datum_weken_voorvertrek));
			} else {
				$this->text["eindbetaling"]=txt("uiterlijkXdagenvoorvertrek","factuur",array("v_dagen"=>$aanbetaling_aantaldagen,"v_datum"=>$datum_weken_voorvertrek));
			}
			$this->amount["eindbetaling"]=$this->gegevens["fin"]["totale_reissom"]-($this->amount["aanbetaling1"]+$this->amount["aanbetaling1_voldaan"])-($this->amount["aanbetaling2"]+$this->amount["aanbetaling2_voldaan"])-$this->amount["beschikbaar_voor_aanbetalingen"];

		} elseif($this->gegevens["stap1"]["dagen_voor_vertrek"]>$this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]) {
			//
			// Only final payment (down payments-period has passed)
			//

			if($aanbetaling_aantalweken) {
				$this->text["eindbetaling"]=txt("uiterlijkXwekenvoorvertrek","factuur",array("v_weken"=>$aanbetaling_aantalweken,"v_datum"=>$datum_weken_voorvertrek));
			} else {
				$this->text["eindbetaling"]=txt("uiterlijkXdagenvoorvertrek","factuur",array("v_dagen"=>$aanbetaling_aantaldagen,"v_datum"=>$datum_weken_voorvertrek));
			}
			$this->amount["eindbetaling"]=$this->gegevens["fin"]["totale_reissom"]-$this->amount["reedsvoldaan"];

		} elseif($this->gegevens["stap1"]["dagen_voor_vertrek"]>28) {
			//
			// final payment: within 5 days
			//
			$this->text["eindbetaling"]=txt("binnen5dagentevoldoen","factuur");
			$this->amount["eindbetaling"]=$this->gegevens["fin"]["totale_reissom"]-$this->amount["reedsvoldaan"];
		} elseif($this->gegevens["stap1"]["dagen_voor_vertrek"]>14) {

			//
			// final payment: directly
			//

			$this->text["eindbetaling"]=txt("perdirecttevoldoen","factuur");
			$this->amount["eindbetaling"]=$this->gegevens["fin"]["totale_reissom"]-$this->amount["reedsvoldaan"];
		} else {

			//
			// final payment: urgent
			//

			$this->text["eindbetaling"]=txt("metspoedopdrachttevoldoen","factuur");
			$this->amount["eindbetaling"]=$this->gegevens["fin"]["totale_reissom"]-$this->amount["reedsvoldaan"];
		}

		// if($this->gegevens["stap1"]["factuurdatum"] and $this->amount["reedsvoldaan"]<>0) {
		// 	$this->amount["aanbetaling1"]=$this->amount["reedsvoldaan"];
		// }

		if($this->amount["eindbetaling"]<0) {
			//
			// To be refunded
			//
			$this->text["eindbetaling"]=txt("terugteontvangen","factuur");
		}

		// total amount
		if($this->gegevens["fin"]["totale_reissom"]-$this->amount["reedsvoldaan"]>0) {
			$this->text["totaal"]=$this->text["eindbetaling"];
			$this->amount["totaal"]=$this->gegevens["fin"]["totale_reissom"]-$this->amount["reedsvoldaan"];
		}


		//
		// extra text on invoice
		//
		if($this->gegevens["stap1"]["factuurdatum"]) {
			if($this->gegevens["stap1"]["dagen_voor_vertrek"]>10) {
				$this->text["afsluiting"]=txt("bedanktgecorboeking10dagen","factuur");
			} else {
				$this->text["afsluiting"]=txt("bedanktgecorboekingreispapieren","factuur");
			}
		} else {
			$this->text["afsluiting"]=txt("tercontrolebinnen24uur","factuur")."\n\n".txt("tot6wekenvoorvertrek","factuur");
		}

		// return $this->gegevens;

	}

}


?>