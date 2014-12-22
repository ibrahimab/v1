<?php


/**
* get payment-text of a booking (when to pay what amount)
*/

class betalingsinfo {

	public static function get_text($gegevens, $voldaan) {
		global $vars, $txt, $txta;


		$taal=$gegevens["stap1"]["taal"];

		$totaal=$gegevens["stap1"]["totale_reissom"];
		$openstaand=$totaal-$voldaan;
		$openstaand=$openstaand;

		$booking_payment = new booking_payment($gegevens, $voldaan);
		$booking_payment->bereken_reeds_voldaan=false;
		$booking_payment->combine_aantaling1_2_if_applicable=false;
		$booking_payment->get_amounts();

		# Voldaan
		if($voldaan>0) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($voldaan,2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_ontvangenbedrag"])."\n";
		}

		// aanbetaling1
		if($booking_payment->amount["aanbetaling1"]>0) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($booking_payment->amount["aanbetaling1"],2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_nogteontvangenaanbetaling"])."\n";
			$return=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$booking_payment->date["aanbetaling1"],$taal),$return);
			$getoond["aanbetaling1"]=true;
		}

		// aanbetaling2
		if($booking_payment->amount["aanbetaling2"]>0) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($booking_payment->amount["aanbetaling2"],2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_nogteontvangenaanbetaling"])."\n";
			$return=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$booking_payment->date["aanbetaling2"],$taal),$return);
			$getoond["aanbetaling2"]=true;
		}

		// eindbetaling
		if($booking_payment->amount["eindbetaling"]>0.01) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($booking_payment->amount["eindbetaling"],2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_nogteontvangeneindbetaling"])."\n";
			$return=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$booking_payment->date["eindbetaling"],$taal),$return);

			$getoond["eindbetaling"]=true;

		}

		// Te betalen bedrag moet hoger dan 0.01 zijn (vanwege afrondingsverschillen) - 25 november 2010
		if(@count($getoond)>1 and $booking_payment->amount["totaal"]>0.01) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($booking_payment->amount["totaal"],2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_totaalnogteontvangen"])."\n";
			$getoond["totaal"]=true;
		}

		//over te maken naar bankrekeningnummer 84.93.06.671 onder vermelding van het reserveringsnummer [RESERVERINGSNUMMER].\n\nGegevens voor internationale betaling:\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nABN AMRO - Woerden

		return $return;
	}

}



?>