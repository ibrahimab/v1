<?php


/**
* calculate the payments of a booking (advance payments, final payment)
*/
class booking_payment {

	public $gegevens;
	public $text;

	public $bereken_bedragen_opnieuw = false;

	public $bereken_reeds_voldaan = true;

	function __construct($gegevens, $reeds_voldaan=0) {

		if($gegevens) {
			$this->gegevens = $gegevens;
		} else {
			trigger_error("empty \$gegevens parsed to class booking_payment",E_USER_NOTICE);
		}

		if(!$this->gegevens["stap1"]["factuurdatum_eerste_factuur"]) {
			// when no invoice has been created: use today as first invoice-date
			$this->gegevens["stap1"]["factuurdatum_eerste_factuur"]=mktime(0,0,0,date("m"),date("d"),date("Y"));
		}

		if($reeds_voldaan) {
			$this->reeds_voldaan = $reeds_voldaan;
		}
	}

	public function get_amounts() {

		global $vars;

		$db = new DB_sql;

		$reeds_voldaan=0;

		// determine reeds voldaan
		if($this->bereken_reeds_voldaan) {
			$db->query("SELECT bedrag, UNIX_TIMESTAMP(datum) AS datum FROM boeking_betaling WHERE boeking_id='".intval($this->gegevens["stap1"]["boekingid"])."' ORDER BY datum;");
			while($db->next_record()) {
				$reeds_voldaan=round($reeds_voldaan+$db->f("bedrag"),2);
				$reeds_voldaan_datum[]=array("date" => $db->f("datum"), "amount" => round($db->f("bedrag"), 2));
			}
		} else {
			$reeds_voldaan=$this->reeds_voldaan;
		}

		if($this->bereken_bedragen_opnieuw) {
			$this->totale_reissom = $this->gegevens["fin"]["totale_reissom"];
		} else {
			$this->totale_reissom = $this->gegevens["stap1"]["totale_reissom"];
		}

		if($reeds_voldaan>0) {
			//
			// reeds voldaan
			//

			$this->text["reedsvoldaan"]=txt("reedsvoldaan","factuur");
			$this->amount["reedsvoldaan"]=$reeds_voldaan;
			$this->amount["beschikbaar_voor_aanbetalingen"]=$reeds_voldaan;
			$this->payments["reedsvoldaan"]=$reeds_voldaan_datum;
		} else {
			$this->amount["reedsvoldaan"]=$reeds_voldaan;
		}

		// aantal dagen na boeken bepalen
		$dagennaboeken=round((mktime(0,0,0,date("m"),date("d"),date("Y"))-$this->gegevens["stap1"]["factuurdatum_eerste_factuur"])/86400);
		$aanbetaling1_dagen_over=$this->gegevens["stap1"]["aanbetaling1_dagennaboeken"]-$dagennaboeken;


		// calculate dates
		$this->date["aanbetaling1"] = mktime(0, 0, 0, date("m", $this->gegevens["stap1"]["factuurdatum_eerste_factuur"]), date("d", $this->gegevens["stap1"]["factuurdatum_eerste_factuur"])+$this->gegevens["stap1"]["aanbetaling1_dagennaboeken"], date("Y",$this->gegevens["stap1"]["factuurdatum_eerste_factuur"]));
		$this->date["aanbetaling2"] = $this->gegevens["stap1"]["aanbetaling2_datum"];
		$this->date["eindbetaling"] = mktime(0, 0, 0, date("m",$this->gegevens["stap1"]["aankomstdatum_exact"]), date("d",$this->gegevens["stap1"]["aankomstdatum_exact"])-$this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"], date("Y",$this->gegevens["stap1"]["aankomstdatum_exact"]));
		if($this->date["aanbetaling1"]>=$this->date["eindbetaling"]) {
			// if date of aanbetaling1 is after or equal eindbetaling: ask for downpayment 1 day prior to eindbetaling
			$this->date["aanbetaling1"] = mktime(0, 0, 0, date("m",$this->date["eindbetaling"]), date("d",$this->date["eindbetaling"])-1, date("Y",$this->date["eindbetaling"]));
		}

// echo date("r", $this->date["eindbetaling"]);

		# aantal dagen/weken voor vertrek
		if($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]%7==0 and $this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]<>7) {
			$aanbetaling_aantalweken=round($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]/7);
		} else {
			$aanbetaling_aantaldagen=$this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"];
		}
		// if($this->gegevens["stap1"]["dagen_voor_vertrek"]>($this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]+$this->gegevens["stap1"]["aanbetaling1_dagennaboeken"])) {

// echo $aanbetaling_aantaldagen.$aanbetaling_aantalweken;



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


			if($this->bereken_bedragen_opnieuw and !$this->gegevens["stap1"]["aanbetaling1_vastgezet"] and !$this->gegevens["stap1"]["aanbetaling1_gewijzigd"]) {
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
				$this->text["eindbetaling"]=txt("uiterlijkXwekenvoorvertrek","factuur",array("v_weken"=>$aanbetaling_aantalweken,"v_datum"=>date("d/m/Y",$this->date["eindbetaling"])));
			} else {
				$this->text["eindbetaling"]=txt("uiterlijkXdagenvoorvertrek","factuur",array("v_dagen"=>$aanbetaling_aantaldagen,"v_datum"=>date("d/m/Y",$this->date["eindbetaling"])));
			}
			$this->amount["eindbetaling"]=$this->totale_reissom-($this->amount["aanbetaling1"]+$this->amount["aanbetaling1_voldaan"])-($this->amount["aanbetaling2"]+$this->amount["aanbetaling2_voldaan"])-$this->amount["beschikbaar_voor_aanbetalingen"];

		} elseif($this->gegevens["stap1"]["dagen_voor_vertrek"]>$this->gegevens["stap1"]["totale_reissom_dagenvooraankomst"]) {
			//
			// Only final payment (down payments-period has passed)
			//

			if($aanbetaling_aantalweken) {
				$this->text["eindbetaling"]=txt("uiterlijkXwekenvoorvertrek","factuur",array("v_weken"=>$aanbetaling_aantalweken,"v_datum"=>date("d/m/Y",$this->date["eindbetaling"])));
			} else {
				$this->text["eindbetaling"]=txt("uiterlijkXdagenvoorvertrek","factuur",array("v_dagen"=>$aanbetaling_aantaldagen,"v_datum"=>date("d/m/Y",$this->date["eindbetaling"])));
			}
			$this->amount["eindbetaling"]=$this->totale_reissom-$this->amount["reedsvoldaan"];

		} elseif($this->gegevens["stap1"]["dagen_voor_vertrek"]>28) {
			//
			// final payment: within 5 days
			//
			$this->text["eindbetaling"]=txt("binnen5dagentevoldoen","factuur");
			$this->amount["eindbetaling"]=$this->totale_reissom-$this->amount["reedsvoldaan"];
		} elseif($this->gegevens["stap1"]["dagen_voor_vertrek"]>14) {

			//
			// final payment: directly
			//

			$this->text["eindbetaling"]=txt("perdirecttevoldoen","factuur");
			$this->amount["eindbetaling"]=$this->totale_reissom-$this->amount["reedsvoldaan"];
		} else {

			//
			// final payment: urgent
			//

			$this->text["eindbetaling"]=txt("metspoedopdrachttevoldoen","factuur");
			$this->amount["eindbetaling"]=$this->totale_reissom-$this->amount["reedsvoldaan"];
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
		if($this->totale_reissom-$this->amount["reedsvoldaan"]>0) {
			$this->text["totaal"]=$this->text["eindbetaling"];
			$this->amount["totaal"]=$this->totale_reissom-$this->amount["reedsvoldaan"];
		}

		if(is_array($this->amount)) {
			foreach ($this->amount as $key => $value) {
				if($value<>0) {
					$this->amount[$key] = round($value, 2);
				}
			}
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