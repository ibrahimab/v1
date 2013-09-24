<?php


/**
 * roominglist
 */

class roominglist {

	public $totaal;
	public $leverancier_id;
	public $date;

	public $regels;

	public $klantnamen_boekingen;
	public $klantnamen_garanties;
	public $garanties;
	public $naamswijzigingen;
	public $verberg_naamswijzigingen;
	public $onbesteld_opvallend;


	public $leverancier_id_inquery;

	public $van;
	public $tot;

	private $boeking_id_inquery;
	private $garantie_id_inquery;

	function __construct() {

		// Roominglist totaal of Roominglist op datum (=aankomstlijst)
		$this->totaal = true;

		// van = vandaag
		$this->van = mktime(0,0,0,date("m"),date("d"),date("Y"));
	}

	public function vergelijk_lijsten() {

		//
		// Vergelijk de oude met de nieuwe roominglist
		//

		$db = new DB_sql;
		$db2 = new DB_sql;

		// $db->query("SELECT leverancier_id, FROM leverancier WHERE leverancier_id IN (".$this->leverancier_id_inquery.");");
		$db->query( "SELECT leverancier_id, roominglist_inhoud_laatste_verzending FROM leverancier WHERE 1=1 ORDER BY leverancier_id;" );
		while ( $db->next_record() ) {

			unset( $nieuwe_roominglist, $roominglist_aantal_wijzigingen, $roominglist_inhoud_laatste_verzending_array, $roominglist_inhoud_nieuwe_verzending_array, $create_list );

			$roominglist_inhoud_laatste_verzending_array=explode( "\n", trim($db->f( "roominglist_inhoud_laatste_verzending" ) ) );

			$this->leverancier_id = $db->f( "leverancier_id" );
			$create_list = $this->create_list();

			$roominglist_inhoud_nieuwe_verzending_array=explode( "\n", trim($this->regels) );

			if ( $this->regels and is_array( $roominglist_inhoud_nieuwe_verzending_array ) ) {
				foreach ( $roominglist_inhoud_nieuwe_verzending_array as $key => $value ) {
					if ( ( is_array( $roominglist_inhoud_laatste_verzending_array ) and !in_array( $value, $roominglist_inhoud_laatste_verzending_array ) ) or !is_array( $roominglist_inhoud_laatste_verzending_array ) ) {
						$roominglist_aantal_wijzigingen++;
					}
				}
			}

			if ( $this->regels!=$db->f( "roominglist_inhoud_laatste_verzending" ) and !$roominglist_aantal_wijzigingen) {
				// trigger_error("roominglist_aantal_wijzigingen is 0 terwijl er wel wijzigingen zijn (leverancier_id ".$db->f("leverancier_id").")",E_USER_NOTICE);

				// echo "<br/><br/>Regels:".$this->regels."<br/><br/>";
				// echo "Database:".$db->f( "roominglist_inhoud_laatste_verzending" )."<br/><br/>";

				// echo wt_he(md5($db->f( "roominglist_inhoud_laatste_verzending" )))."<hr>";
				// echo wt_he(md5($this->regels));

				// echo "<hr>";
				// echo wt_he($db->f( "roominglist_inhoud_laatste_verzending" ))."<hr>";
				// echo "<hr>";
				// echo wt_he($this->regels);

				// echo wt_dump($roominglist_inhoud_nieuwe_verzending_array);
				// echo wt_dump($roominglist_inhoud_laatste_verzending_array);
			}

			$db2->query( "UPDATE leverancier SET roominglist_aantal_wijzigingen='".intval( $roominglist_aantal_wijzigingen )."' WHERE leverancier_id='".intval( $db->f( "leverancier_id" ) )."';" );

			// echo $db2->lq."<br>";
			// exit;
		}
	}

	public function create_list() {

		global $vars;

		$db = new DB_sql;
		$db2 = new DB_sql;

		unset($this->klantnamen_boekingen, $this->klantnamen_garanties);

		if ( $mustlogin ) {
			if ( $this->totaal ) {
				cmslog_pagina_title( "Overzichten - Roominglist" );
			} else {
				cmslog_pagina_title( "Overzichten - Aankomstlijst" );
			}
		}

		// naamswijzingen verbergen?
		// if($this->verberg_naamswijzigingen) {
		// 	$verberg_naamswijzigingen_array_temp=preg_split("@,@",$this->verberg_naamswijzigingen);

		// 	foreach ($verberg_naamswijzigingen_array_temp as $key => $value) {
		// 		$verberg_naamswijzigingen_array[$value]=true;
		// 	}
		// }

		$colspan=9;

		// Leveranciersgegevens ophalen

		$db->query( "SELECT roominglist_toonaantaldeelnemers, roominglist_toontelefoonnummer, roominglist_site_benaming FROM leverancier WHERE leverancier_id='".intval( $this->leverancier_id )."';" );
		if ( $db->next_record() ) {
			if ( $db->f( "roominglist_toonaantaldeelnemers" ) ) {
				if ( !$this->totaal ) {
					$roominglist_toonaantaldeelnemers=true;
				}
			}
			if ( $db->f( "roominglist_toontelefoonnummer" ) ) {
				if ( !$this->totaal ) {
					$roominglist_toontelefoonnummer=true;
					$colspan++;
				}
			}
			$roominglist_site_benaming=$db->f( "roominglist_site_benaming" );
		}

		// garantie-koppeling
		$db->query("SELECT garantie_id, boeking_id, soort_garantie FROM garantie WHERE boeking_id>0;");
		while($db->next_record()) {
			$garantie_boeking[$db->f("boeking_id")]=$db->f("soort_garantie");
			$boeking_garantie[$db->f("garantie_id")]=$db->f("boeking_id");
		}


		// bepalen welke garanties moeten worden opgenomen
		$this->garantie_id_inquery=",0";
		if($this->garanties_doorgeven) {
			$garanties_doorgeven_array=explode(",",$this->garanties_doorgeven);
			if(is_array($garanties_doorgeven_array)) {
				foreach ($garanties_doorgeven_array as $key => $value) {
					if(preg_match("@^b([0-9]+)$@",$value,$regs)) {
						$boeking_doorgeven[$regs[1]]=true;
					} elseif($value) {
						$this->garantie_id_inquery.=",".$value;
					}
				}
			}
		}

		// bepalen welke garantie-boekingen niet opgenomen moeten worden (garantie-boekingen die niet zijn aangevinkt)
		$this->boeking_id_notinquery=",0";
		if(is_array($boeking_garantie)) {
			foreach ($boeking_garantie as $key => $value) {
				if(!$boeking_doorgeven[$value]) {
					$this->boeking_id_notinquery.=",".$value;
				}
			}
		}


		//
		// Gewone boekingen
		//

		if ( $this->totaal ) {
			$where.="b.aankomstdatum_exact>='".$this->van."' AND ";
			if($this->tot) {
				$where.="b.aankomstdatum_exact<='".$this->tot."' AND ";
			}
		} else {
			$where.="b.aankomstdatum='".addslashes( $this->date )."' AND ";
		}

		if($_POST["frm_filled"] and $this->boeking_id_notinquery) {
			$where.="b.boeking_id NOT IN (".substr($this->boeking_id_notinquery,1).") AND ";
		}

		$db->query( "SELECT b.boeking_id, b.boekingsnummer, b.type_id, b.aan_leverancier_doorgegeven_naam, b.bestelstatus, UNIX_TIMESTAMP(b.besteldatum) AS besteldatum, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.mobielwerk, bp.telefoonnummer, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." (b.leverancier_id=l.leverancier_id OR b.beheerder_id=l.leverancier_id) AND l.leverancier_id='".addslashes( $this->leverancier_id )."' AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;" );
		while ( $db->next_record() ) {

			if($db->f("besteldatum")>0 or isset($garantie_boeking[$db->f("boeking_id")])) {

				$sortkey=$db->f( "plaats" )."_".$db->f( "aankomstdatum_exact" )."_".$db->f( "accommodatie" )."_".$db->f( "type" );
				if ( !$leverancier ) {
					$leverancier=$db->f( "leverancier" );
					$aankomstdatum=$db->f( "aankomstdatum_exact" );
				}
				$aankomstdata[$db->f( "aankomstdatum_exact" )]=true;
				$accnaam=$db->f( "abestelnaam" )." ".( $db->f( "type" ) ? $db->f( "type" )." " : "" )."(".$db->f( "optimaalaantalpersonen" ).( $db->f( "optimaalaantalpersonen" )<>$db->f( "maxaantalpersonen" ) ? "-".$db->f( "maxaantalpersonen" ) : "" )." p)";
				$accnaam_kort=$db->f( "abestelnaam" )." ".( $db->f( "type" ) ? $db->f( "type" )." " : "" );
				$accnaam_kort_aanvullend="";
				if ( $db->f( "accommodatie" )<>$db->f( "abestelnaam" ) ) {
					$accnaam_kort_aanvullend.=" <i>(our name: ".htmlentities( $db->f( "accommodatie" ).( $db->f( "type" ) ? " ".$db->f( "type" ) : "" ) ).")</i>";
				}
				$tempplaatsid[$sortkey]=$db->f( "plaats_id" );

				// if($verberg_naamswijzigingen_array[$db->f("boeking_id")]) {
				// 	$naam=$db->f("aan_leverancier_doorgegeven_naam");
				// } else {
					$naam=wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) );

					// klantnamen in array plaatsen
					$this->klantnamen_boekingen[$db->f("boeking_id")] = wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) );
				// }

				$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid'".($this->onbesteld_opvallend&&$db->f("bestelstatus")<=1 ? " class='nog_niet_besteld'" : "")."><td valign=\"top\">".wt_he( $naam )."</td>";


				// naamswijzigingen in array plaatsen
				if($db->f( "aan_leverancier_doorgegeven_naam" )!=wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) )) {
					$this->naamswijzigingen[$db->f("boeking_id")] = "Boeking ".$db->f("boekingsnummer").": \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) )."\"";

					$this->naamswijzigingen_html[$db->f("boeking_id")] = "Boeking <a href=\"".$vars["path"]."cms_boekingen.php?show=21&21k0=".$db->f("boeking_id")."\" target=\"_blank\">".$db->f("boekingsnummer")."</a>:".wt_he(" \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) )."\"");

					// tonen of het om een garantie-boeking gaat
					if(isset($garantie_boeking[$db->f("boeking_id")])) {
						$this->naamswijzigingen_html[$db->f("boeking_id")]="<span class=\"soort_garantie_".$garantie_boeking[$db->f("boeking_id")]."\">".$this->naamswijzigingen_html[$db->f("boeking_id")]."</span>";
					}
				}

				// boeking die aan garantie is: opnemen in de lijst met uit te schakelen garanties
				if(isset($garantie_boeking[$db->f("boeking_id")])) {
					$this->garanties_html["b".$db->f("boeking_id")] = "<span class=\"soort_garantie_".$garantie_boeking[$db->f("boeking_id")]."\">".wt_he($vars["alletypes"][$db->f("type_id")])." - ".wt_he( wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) ) )." - <a href=\"".$vars["path"]."cms_boekingen.php?show=21&21k0=".$db->f("boeking_id")."\" target=\"_blank\">".$db->f("boekingsnummer")."</a> - ".date("d/m/Y",$db->f("aankomstdatum_exact"))."</span>";

					if($garantie_boeking[$db->f("boeking_id")]==2) {
						// "garantie: op naam en losse weken": standaard doorgeven
						$this->garanties_doorgeven.=","."b".$db->f("boeking_id");
					}
				}

				if ( $roominglist_toontelefoonnummer ) {
					$regels[$sortkey].="<td valign=\"top\">";
					if ( $db->f( "mobielwerk" ) ) {
						$regels[$sortkey].=htmlentities( $db->f( "mobielwerk" ) );
					} elseif ( $db->f( "telefoonnummer" ) ) {
						$regels[$sortkey].=htmlentities( $db->f( "telefoonnummer" ) );
					} else {
						$regels[$sortkey].="&nbsp;";
					}
					$regels[$sortkey].="</td>";
				}
				$regels[$sortkey].="<td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "aankomstdatum_exact" ) )."</td><td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "vertrekdatum_exact" ) )."</td><td valign=\"top\">".htmlentities( $db->f( "plaats" ) )."</td><td valign=\"top\">".htmlentities( $accnaam_kort ).$accnaam_kort_aanvullend."</td><td valign=\"top\">".( $db->f( "code" ) ? htmlentities( $db->f( "code" ) ) : "&nbsp;" )."</td>";
				if ( $roominglist_toonaantaldeelnemers ) {
					$db2->query( "SELECT COUNT(boeking_id) AS aantal FROM boeking_persoon WHERE boeking_id='".$db->f( "boeking_id" )."';" );
					if ( $db2->next_record() ) {
						$aantal=intval( $db2->f( "aantal" ) );
					} else {
						$aantal="";
					}
					$regels[$sortkey].="<td valign=\"top\">".$aantal."</td>";
				} else {
					$regels[$sortkey].="<td valign=\"top\">".$db->f( "maxaantalpersonen" )."</td>";
				}
				$regels[$sortkey].="<td valign=\"top\">".( $db->f( "leverancierscode" ) ? htmlentities( $db->f( "leverancierscode" ) ) : "&nbsp;" )."</td><td valign=\"top\">".( $db->f( "opmerkingen_voucher" ) ? nl2br( htmlentities( $db->f( "opmerkingen_voucher" ) ) ) : "&nbsp;" )."</td></tr>";
			}
		}

		//
		// Garanties
		//
		unset($where);
		if ( $this->totaal ) {
			$where="g.aankomstdatum_exact>='".$this->van."' AND ";
			if($this->tot) {
				$where.="g.aankomstdatum_exact<='".$this->tot."' AND ";
			}
		} else {
			$where="g.aankomstdatum='".addslashes( $this->date )."' AND ";
		}

		// aleen garanties die zijn aangevinkt opnemen
		if($_POST["frm_filled"]) {
			$where.="g.garantie_id IN (".substr($this->garantie_id_inquery,1).") AND ";
		}

		// if($this->verberg_garanties) {
		// 	$where.="g.garantie_id NOT IN (".$this->verberg_garanties.") AND ";
		// }

		$db->query( "SELECT g.garantie_id, g.naam, g.aan_leverancier_doorgegeven_naam, g.type_id, g.soort_garantie, g.aankomstdatum_exact, g.vertrekdatum_exact, g.factuurnummer, UNIX_TIMESTAMP(g.inkoopdatum) AS inkoopdatum, g.reserveringsnummer_extern, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM garantie g, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." g.leverancier_id=l.leverancier_id AND l.leverancier_id='".addslashes( $this->leverancier_id )."' AND g.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND g.boeking_id=0 ORDER BY g.aankomstdatum_exact, t.type_id, g.garantie_id;" );
		while ( $db->next_record() ) {
			$sortkey=$db->f( "plaats" )."_".$db->f( "aankomstdatum_exact" )."_".$db->f( "accommodatie" )."_".$db->f( "type" );
			if ( !$leverancier ) {
				$leverancier=$db->f( "leverancier" );
				$aankomstdatum=$db->f( "aankomstdatum_exact" );
			}
			$aankomstdata[$db->f( "aankomstdatum_exact" )]=true;
			$accnaam=$db->f( "accommodatie" )." ".( $db->f( "type" ) ? $db->f( "type" )." " : "" )."(".$db->f( "optimaalaantalpersonen" ).( $db->f( "optimaalaantalpersonen" )<>$db->f( "maxaantalpersonen" ) ? "-".$db->f( "maxaantalpersonen" ) : "" )." p)";
			$accnaam_kort=$db->f( "accommodatie" )." ".( $db->f( "type" ) ? $db->f( "type" )." " : "" );
			$tempplaatsid[$sortkey]=$db->f( "plaats_id" );

			// if($verberg_naamswijzigingen_array["g".$db->f("garantie_id")]) {
			// 	$naam=$db->f("aan_leverancier_doorgegeven_naam");
			// } else {
				$naam = $db->f( "naam" );

				# Klantnamen in array plaatsen
				$this->klantnamen_garanties[$db->f("garantie_id")] = $db->f( "naam" );

			// }

			if($db->f( "inkoopdatum" ) or $db->f( "factuurnummer" )) {
				$nog_niet_besteld=false;
			} else {
				$nog_niet_besteld=true;
			}

			$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid'".($this->onbesteld_opvallend&&$nog_niet_besteld ? " class='nog_niet_besteld'" : "")."><td valign=\"top\">".wt_he( $naam )."</td>";


			// naamswijzigingen in array plaatsen
			if($db->f( "aan_leverancier_doorgegeven_naam" )!=$db->f( "naam" )) {
				$this->naamswijzigingen["g".$db->f("garantie_id")] = "Garantie ".$db->f("reserveringsnummer_extern").": \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".$db->f( "naam" )."\"";

				$this->naamswijzigingen_html["g".$db->f("garantie_id")] = "<span class=\"soort_garantie_".$db->f("soort_garantie")."\">Garantie <a href=\"cms_garanties.php?edit=34&34k0=".$db->f("garantie_id")."\" target=\"_blank\">".$db->f("reserveringsnummer_extern")."</a>:".wt_he(" \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".$db->f( "naam" )."\"")."</span>";

			}

			# Garanties in array plaatsen
			$this->garanties[$db->f("garantie_id")] = $vars["alletypes"][$db->f("type_id")]." - ".$db->f( "naam" )." - ".$db->f( "reserveringsnummer_extern" )." - ".date("d/m/Y",$db->f("aankomstdatum_exact"));

			$this->garanties_html[$db->f("garantie_id")] = "<span class=\"soort_garantie_".$db->f("soort_garantie")."\">".wt_he($vars["alletypes"][$db->f("type_id")])." - ".wt_he($db->f( "naam" ))." - <a href=\"cms_garanties.php?edit=34&34k0=".$db->f("garantie_id")."\" target=\"_blank\">".$db->f( "reserveringsnummer_extern" )."</a> - ".date("d/m/Y",$db->f("aankomstdatum_exact"))."</span>";

			if($db->f("soort_garantie")==2) {
				// "garantie: op naam en losse weken": standaard doorgeven
				$this->garanties_doorgeven.=",".$db->f("garantie_id");
			}

			if ( $roominglist_toontelefoonnummer ) {
				$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
			}
			$regels[$sortkey].="<td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "aankomstdatum_exact" ) )."</td><td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "vertrekdatum_exact" ) )."</td><td valign=\"top\">".htmlentities( $db->f( "plaats" ) )."</td><td valign=\"top\">".htmlentities( $accnaam_kort )."</td><td valign=\"top\">".( $db->f( "code" ) ? htmlentities( $db->f( "code" ) ) : "&nbsp;" )."</td>";
			if ( $roominglist_toonaantaldeelnemers ) {
				$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
			} else {
				$regels[$sortkey].="<td valign=\"top\">".$db->f( "maxaantalpersonen" )."</td>";
			}
			$regels[$sortkey].="<td valign=\"top\">".( $db->f( "factuurnummer" ) ? htmlentities( $db->f( "factuurnummer" ) ) : ( $db->f( "inkoopdatum" )>0 ? "OK ".date( "d-m-Y", $db->f( "inkoopdatum" ) ) : "&nbsp;" ) )."</td><td valign=\"top\">".( $db->f( "opmerkingen_voucher" ) ? nl2br( htmlentities( $db->f( "opmerkingen_voucher" ) ) ) : "&nbsp;" )."</td></tr>";
		}

		unset($this->regels);

		if ( is_array( $regels ) ) {
			ksort( $regels );

			while ( list( $key, $value )=each( $regels ) ) {
				if ( $plaatsid_gehad and $tempplaatsid[$key]<>$plaatsid_gehad ) {
					$html.="<tr style='mso-yfti-irow:1'><td colspan=\"".$colspan."\">&nbsp;</td></tr>";
				}
				$plaatsid_gehad=$tempplaatsid[$key];
				$html.=$value;

				$this->regels.=$value."\n";
			}

			$this->regels=trim($this->regels);


			$totaal_html.="<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";
			$totaal_html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><td colspan=".$colspan.">";

			$totaal_html.="<table width=100%><thead><tr><td><h3>";
			if ( !$this->totaal ) {
				$totaal_html.="Arrivals ";
				// Datum in titel
				if ( @count( $aankomstdata )>1 ) {
					ksort( $aankomstdata );
					while ( list( $key, $value )=each( $aankomstdata ) ) {
						if ( $aankomstdatum_teller ) $totaal_html.=" + ";
						$aankomstdatum_teller++;
						$totaal_html.=date( "d-m-Y", $key );
					}
				} else {
					$totaal_html.=date( "d-m-Y", $aankomstdatum );
				}
			} else {
				$totaal_html.="Roominglist ".date( "d-m-Y" );
			}

			$totaal_html.=": ".wt_he( $vars["roominglist_site_benaming"][$roominglist_site_benaming] )."</h3>Chalet.nl B.V. - Wipmolenlaan 3 - 3447 GJ Woerden - The Netherlands - Tel: +31 348 - 43 46 49 - Fax: +31 348 - 69 07 52 - info@chalet.nl</td><td align=right>";
			$totaal_html.="<img width=92 height=79 src=\"http://www.chalet.nl/pic/factuur_logo_vakantiewoningen.png\"></td></tr></thead></table>";
			$totaal_html.="</td></tr>";
			$totaal_html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Clientsname</th>";

			if ( $roominglist_toontelefoonnummer ) {
				$totaal_html.="<th>Phone</th>";
			}
			$totaal_html.="<th>Arrival</th><th>Departure</th><th>Resort</th><th>Accommodation</th><th>Type</th>";

			if ( $roominglist_toonaantaldeelnemers ) {
				$totaal_html.="<th>People</th>";
			} else {
				$totaal_html.="<th>Cap.</th>";
			}
			$totaal_html.="<th>Reserv.<br>Nr.</th><th>Extra<br>Options</th></tr></thead>";

			$totaal_html.=$html;
			$totaal_html.="</table>";
			$totaal_html.="<br><i>".$leverancier." - printed ".date( "d-m-y" )."</i>";

		}

		$return["html"]=$totaal_html;

		return $return;

	}

	public function word_bestand($settings="") {

		global $vars;

		// Word-bestand
		include $vars["unixdir"]."admin/class.msoffice.php";
		$ms=new ms_office;
		$ms->author="Chalet.nl";
		$ms->company="Chalet.nl";
		if ( $_GET["t"]==7 ) {
			$ms->margin="0cm 0cm 0cm 0cm;";
		} else {
			$ms->landscape=true;
		}
		if ( $this->totaal ) {
			$ms->filename="roominglist_".strtolower( ereg_replace( " ", "_", $leverancier ) );
		} else {
			$ms->filename="arrivals_".strtolower( ereg_replace( " ", "_", $leverancier ) );
		}

		$ms->css="	body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 0.8em;
			line-height: 110%;
		}

		table {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 1em;
			margin: 0px;
			padding: 1px;
		}

		.betreft {
			width: 300px;
		}

		.rekentabel {
			width: 100%;
		}

		.kunsttabel {
			width: 500px;
		}

		.kunsttabel .links {
			width: 10%;
		}

		.kunsttabel .rechts {
			width: 90%;
			padding-left:7px;
		}

		.tekst {
			padding: 1px;
		}

		td {
			padding: 1px;
		}

		.koptekst {
			font-size: 1.3em;
			font-weight: bold;
		}

		hr {
			height: 2px;
			color: #000000;
			background-color: #000000;
		}
		";

		if ( $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" ) $ms->test=true;

		$create_list=$this->create_list();
		$ms->html=$create_list["html"];


		if($settings["save_filename"]) {

			$ms->headers=false;

			ob_clean();
			ob_start();
			$ms->create_word_document();
			$output = ob_get_clean();

			file_put_contents($settings["save_filename"], $output);

		} else {
			$ms->create_word_document();
		}
	}

	public function overzicht_te_verzenden($settings="") {

		global $vars;

		$db = new DB_sql;
		// $db2 = new DB_sql;

		if($settings["in_de_wacht"]) {
			$andquery.=" AND roominglist_volgende_controle>NOW()";
			$return.="<h1>In de wacht</h1>";
		} else {
			$andquery.=" AND (roominglist_volgende_controle IS NULL OR roominglist_volgende_controle<NOW())";
			$return.="<h1>Te verzenden roominglists</h1>";
		}


		$db->query("SELECT leverancier_id, naam, beheerder, roominglist_aantal_wijzigingen, UNIX_TIMESTAMP(roominglist_laatste_verzending_datum) AS roominglist_laatste_verzending_datum, UNIX_TIMESTAMP(roominglist_volgende_controle) AS roominglist_volgende_controle, roominglist_versturen FROM leverancier WHERE roominglist_aantal_wijzigingen>0".$andquery." ORDER BY roominglist_versturen, naam, roominglist_laatste_verzending_datum;");
		if($db->num_rows()) {
			$return.="<table cellspacing=\"0\" class=\"tbl striped\">";
			$return.="<tr><th>Leverancier</th><th>Laatste verzending</th><th>Gewijzigde boekingen t.o.v. laatste verzending</th>";
			if($settings["in_de_wacht"]) {
				$return.="<th>Herinneren vanaf</th>";
			}
			$return.="</tr>";

			while($db->next_record()) {
				$return.="<tr class=\"".($db->f("roominglist_versturen")==2 ? "tr_onopvallend" : "")."\"><td><a href=\"".$vars["path"]."cms_roomingaankomst.php?levid=".$db->f("leverancier_id")."\">".wt_he($db->f("naam"))."</a>".($db->f("beheerder") ? " (beheerder)" : "").($db->f("roominglist_versturen")==2 ? " (wil geen roominglists)" : "")."</td><td>".($db->f("roominglist_laatste_verzending_datum")>0 ? date("d-m-Y",$db->f("roominglist_laatste_verzending_datum")) : "&nbsp;")."</td><td>".intval($db->f("roominglist_aantal_wijzigingen"))."</td>";

				if($settings["in_de_wacht"]) {
					$return.="<td>".date("d-m-Y",$db->f("roominglist_volgende_controle"))."</td>";
				}

				$return.="</tr>";
			}

			$return.="</table>";

		} else {
			if($settings["in_de_wacht"]) {
				$return.="<p>Er staan geen roominglists in de wacht.</p>";
			} else {
			$return.="<p>Er zijn geen te verzenden roominglists.</p>";
			}
		}

		return $return;
	}

	public function overzicht_goedgekeurd($settings="") {

		global $vars;

		$db = new DB_sql;
		// $db2 = new DB_sql;

		if($settings["nog_niet_goedgekeurd"]) {
			$andquery.=" AND roominglist_goedgekeurd=''";
			$return.="<h1>Wacht op goedkeuring</h1>";
			$orderby=", roominglist_laatste_verzending_datum";
		} else {
			$andquery.=" AND roominglist_goedgekeurd<>''";
			$return.="<h1>Goedgekeurd</h1>";
			$orderby="";
		}


		$db->query("SELECT leverancier_id, naam, beheerder, roominglist_aantal_wijzigingen, UNIX_TIMESTAMP(roominglist_laatste_verzending_datum) AS roominglist_laatste_verzending_datum, UNIX_TIMESTAMP(roominglist_volgende_controle) AS roominglist_volgende_controle, roominglist_versturen FROM leverancier WHERE roominglist_laatste_verzending_datum IS NOT NULL".$andquery." ORDER BY roominglist_versturen".$orderby.", naam, roominglist_laatste_verzending_datum;");
		if($db->num_rows()) {
			$return.="<table cellspacing=\"0\" class=\"tbl striped\">";
			$return.="<tr><th>Leverancier</th><th>Verzonden</th>";
			if($settings["in_de_wacht"]) {
				$return.="<th>Verzenden vanaf</th>";
			}
			$return.="</tr>";

			while($db->next_record()) {
				$return.="<tr class=\"".($db->f("roominglist_versturen")==2 ? "tr_onopvallend" : "")."\"><td><a href=\"".$vars["path"]."cms_roomingaankomst.php?levid=".$db->f("leverancier_id")."\">".wt_he($db->f("naam"))."</a>".($db->f("beheerder") ? " (beheerder)" : "").($db->f("roominglist_versturen")==2 ? " (wil geen roominglists)" : "")."</td><td>".($db->f("roominglist_laatste_verzending_datum")>0 ? date("d-m-Y",$db->f("roominglist_laatste_verzending_datum")) : "&nbsp;")."</td>";

				if($settings["in_de_wacht"]) {
					$return.="<td>".date("d-m-Y",$db->f("roominglist_volgende_controle"))."</td>";
				}

				$return.="</tr>";
			}

			$return.="</table>";

		} else {
			if($settings["nog_niet_goedgekeurd"]) {
				$return.="<p>Er zijn geen goed te keuren roominglists.</p>";
			} else {
				$return.="<p>Er zijn geen goedgekeurde roominglists.</p>";
			}
		}

		return $return;

	}


}



?>
