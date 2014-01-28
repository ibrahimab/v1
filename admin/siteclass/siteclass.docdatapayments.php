<?php


/**
*  calculate and show if Docdata-payments are paid out by Docdata
*/
class docdatapayments {

	function __construct() {
		$this->soort = $_GET["soort"];
	}

	function show_docdata_payments_table() {

		global $vars;

		$db = new DB_sql;

		// boekingen bij juiste B.V. zoeken
		unset($andquery);
		if($_GET["bedrijf"]=="venturasol") {
			$andquery.=" AND b.valt_onder_bedrijf=2";
		} else {
			$andquery.=" AND b.valt_onder_bedrijf=1";
		}

		// nog te ontvangen, reeds ontvangen
		if($this->soort=="te_ontvangen") {
			$andquery.=" AND bb2.datum IS NULL";
		} else {

			# frm = formname (mag ook wat anders zijn)
			$form=new form2("frm");
			$form->settings["fullname"]="Naam";
			$form->settings["layout"]["css"]=false;
			$form->settings["db"]["table"]="lid";
			$form->settings["message"]["submitbutton"]["nl"]="OK";
			$form->settings["type"]="get";
			#_field: (obl),id,title,db,prevalue,options,layout

			$form->field_htmlrow("","<b>Uitbetaaldatum</b>");

			$vandaag = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$form->field_date(0,"begindatum","Van","","",array("startyear"=>date("Y")-2,"endyear"=>date("Y")+2),array("calendar"=>true));
			$form->field_date(1,"einddatum","Tot en met","",array("time"=>$vandaag),array("startyear"=>date("Y")-2,"endyear"=>date("Y")+2),array("calendar"=>true));

			$form->check_input();

			if($form->filled) {
				if($form->input["begindatum"]["unixtime"] and $form->input["einddatum"]["unixtime"] and $form->input["begindatum"]["unixtime"]>$form->input["einddatum"]["unixtime"]) {
					$form->error("einddatum","moet later zijn dan begindatum");
				}
			}

			$form->end_declaration();

			if($form->okay) {
				$einddatum=$form->input["einddatum"]["unixtime"];
				if($form->input["begindatum"]["unixtime"]) {
					$begindatum=$form->input["begindatum"]["unixtime"];
				}
			} else {
				$einddatum = $vandaag;
			}
			$einddatum = mktime(23, 59, 59, date("m",$einddatum), date("d", $einddatum), date("Y",$einddatum));

			$form->display_all();

			$return .= "<br />";

			$andquery.=" AND bb2.datum IS NOT NULL AND UNIX_TIMESTAMP(bb2.datum)<='".$einddatum."'";
			if($begindatum) {
				$andquery.=" AND UNIX_TIMESTAMP(bb2.datum)>='".$begindatum."'";
			}
		}

		$db->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, bb1.boeking_id, bb1.bedrag, bb1.type, UNIX_TIMESTAMP(bb1.datum) AS datum, UNIX_TIMESTAMP(bb2.datum) AS uitbetaaldatum FROM boeking_betaling bb1 LEFT JOIN boeking_betaling bb2 ON (bb1.boeking_id=bb2.boeking_id AND bb1.bedrag=bb2.bedrag AND bb2.type=2 AND bb2.datum>=bb1.datum) INNER JOIN boeking b ON (b.boeking_id=bb1.boeking_id) WHERE bb1.type IN (4,5,6)".$andquery." ORDER BY bb1.datum, b.aankomstdatum_exact, SUBSTRING(b.boekingsnummer,2);");


		//

		// Creditcard: 70 dagen
		// iDeal: 15 dagen
		// Mr Cash: 15 dagen

		// show table

		if($db->num_rows()) {

			$soort_betaling = array(4=>"iDEAL", 5=>"creditcard", 6=>"Mister Cash");
			$soort_betaling_dagen = array(4=>15, 5=>70, 6=>15);

			if($this->soort=="te_ontvangen") {
				$width=800;
			} else {
				$width=800;
			}
			$return .= "<table cellspacing=\"0\" class=\"tbl\" style=\"width:".$width."px;\">";
			$return .= "<tr><th>reserveringsnr</th><th>aankomst</th><th style=\"text-align:right;\">bedrag</th><th>soort</th><th>door klant betaald op&nbsp;</th><th>".($this->soort=="te_ontvangen" ? "status" : "uitbetaald door Docdata op")."</th></tr>";
			while($db->next_record()) {

				$aantal_dagen_geleden = round((time()-$db->f("datum"))/86400);
				$dagen_te_laat = $aantal_dagen_geleden-$soort_betaling_dagen[$db->f("type")];
				if($dagen_te_laat>0 and $this->soort=="te_ontvangen") {
					$return .= "<tr style=\"background-color:#fdff86;\">";

					$this->te_laat_aantal ++;
				} else {
					$return .= "<tr>";
				}
				$return .= "<td><a href=\"".$vars["path"]."cms_boekingen_betalingen.php?bid=".$db->f("boeking_id")."\" target=\"_blank\">".wt_he($db->f("boekingsnummer"))."</a></td>";
				$return .= "<td>".date("d-m-Y", $db->f("aankomstdatum_exact"))."</td>";
				$return .= "<td style=\"text-align:right;\">&euro;&nbsp;".number_format($db->f("bedrag"),2,",",".")."</td>";
				$return .= "<td>".wt_he($soort_betaling[$db->f("type")])."</td>";
				$return .= "<td>".date("d-m-Y", $db->f("datum"))."</td>";
				if($this->soort=="te_ontvangen") {
					if($dagen_te_laat>0) {
						$return .= "<td>".$dagen_te_laat." ".($dagen_te_laat==1 ? "dag" : "dagen")." te laat</td>";
					} else {
						$return .= "<td>&nbsp;</td>";
					}
				} else {
					$return .= "<td>".($db->f("uitbetaaldatum") ? date("d-m-Y", $db->f("uitbetaaldatum")) : "")."</td>";
				}
				$return .= "</tr>";
			}
			$return .= "</table>";
		} else {
			$return .= "<p><i>Geen Docdata-betalingen gevonden die aan de zoekcriteria voldoen.</i></p>";
		}

		return $return;

	}
}


?>