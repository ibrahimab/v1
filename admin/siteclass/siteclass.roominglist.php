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
	public $naamswijzigingen_html;
	public $verberg_naamswijzigingen;
	public $onbesteld_opvallend;

	public $frm_filled;

	public $aankomstlijst_gele_wijziging;

	public $leverancier_id_inquery;

	public $van;
	public $tot;

	private $boeking_id_inquery;
	private $garantie_id_inquery;

	function __construct() {

		// Roominglist totaal of Roominglist op datum (=aankomstlijst)
		$this->totaal = true;

		// van = vandaag
		$this->van = mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) );
	}

	public function vergelijk_lijsten() {

		//
		// Vergelijk de oude met de nieuwe roominglist
		//

		$db = new DB_sql;
		$db2 = new DB_sql;

		$db->query( "SELECT naam, leverancier_id, roominglist_inhoud_laatste_verzending FROM leverancier WHERE 1=1".( $this->leverancier_id_inquery ? " AND leverancier_id IN (".$this->leverancier_id_inquery.")" : "" )." ORDER BY leverancier_id;" );
		while ( $db->next_record() ) {

			unset( $nieuwe_roominglist, $roominglist_aantal_wijzigingen, $roominglist_inhoud_laatste_verzending_array, $roominglist_inhoud_laatste_verzending_stripped_array, $roominglist_inhoud_nieuwe_verzending_array, $roominglist_inhoud_nieuwe_verzending_stripped_array, $create_list );

			$roominglist_inhoud_laatste_verzending_array=explode( "</td></tr>", trim( $db->f( "roominglist_inhoud_laatste_verzending" ) ) );

			foreach ( $roominglist_inhoud_laatste_verzending_array as $key => $value ) {
				if ( $value ) {
					$value = preg_replace( "@&nbsp;@", " ", $value );
					$value = preg_replace( "@ {2,}@", " ", $value );
					$value = trim( html_entity_decode( strip_tags( $value ) ) );
					if ( $value ) {
						$roominglist_inhoud_laatste_verzending_stripped_array[$value]=true;
					}
				}
			}

			$this->leverancier_id = $db->f( "leverancier_id" );
			$create_list = $this->create_list();

			$roominglist_inhoud_nieuwe_verzending_array=explode( "</td></tr>", trim( $this->regels ) );
			foreach ( $roominglist_inhoud_nieuwe_verzending_array as $key => $value ) {
				if ( $value ) {
					$value = preg_replace( "@&nbsp;@", " ", $value );
					$value = preg_replace( "@ {2,}@", " ", $value );
					$value = trim( html_entity_decode( strip_tags( $value ) ) );
					if ( $value ) {
						$roominglist_inhoud_nieuwe_verzending_stripped_array[$value]=true;
					}
				}
			}

			if ( $this->regels and is_array( $roominglist_inhoud_nieuwe_verzending_stripped_array ) ) {
				foreach ( $roominglist_inhoud_nieuwe_verzending_stripped_array as $key => $value ) {
					if ( ( is_array( $roominglist_inhoud_laatste_verzending_stripped_array ) and !$roominglist_inhoud_laatste_verzending_stripped_array[$key] ) or !is_array( $roominglist_inhoud_laatste_verzending_stripped_array ) ) {
						$roominglist_aantal_wijzigingen++;
					}
				}
			}

			$db2->query( "UPDATE leverancier SET roominglist_aantal_wijzigingen='".intval( $roominglist_aantal_wijzigingen )."' WHERE leverancier_id='".intval( $db->f( "leverancier_id" ) )."';" );
		}
	}

	public function vergelijk_lijsten_arrivals() {

		//
		// Vergelijk de oude met de nieuwe aankomstlijst
		//

		$db = new DB_sql;
		$db2 = new DB_sql;

		$db->query( "SELECT leverancier_id, UNIX_TIMESTAMP(aankomstdatum) AS aankomstdatum, inhoud_laatste_verzending FROM leverancier_aankomstlijst WHERE aankomstdatum>NOW() ORDER BY aankomstdatum, leverancier_id;" );
		while ( $db->next_record() ) {

			unset( $nieuwe_roominglist, $aantal_wijzigingen, $inhoud_laatste_verzending_array, $inhoud_laatste_verzending_stripped_array, $inhoud_nieuwe_verzending_array, $inhoud_nieuwe_verzending_stripped_array, $create_list );

			$inhoud_laatste_verzending_array=explode( "</td></tr>", trim( $db->f( "inhoud_laatste_verzending" ) ) );

			foreach ( $inhoud_laatste_verzending_array as $key => $value ) {
				if ( $value ) {
					$value = preg_replace( "@&nbsp;@", " ", $value );
					$value = preg_replace( "@ {2,}@", " ", $value );
					$value = trim( html_entity_decode( strip_tags( $value ) ) );
					if ( $value ) {
						$inhoud_laatste_verzending_stripped_array[$value]=true;
					}
				}
			}

			$this->leverancier_id = $db->f( "leverancier_id" );
			$this->totaal = false;
			$this->vergelijk_lijsten = true;
			$this->date = $db->f( "aankomstdatum" );
			$create_list = $this->create_list();

			$inhoud_nieuwe_verzending_array=explode( "</td></tr>", trim( $this->regels ) );
			foreach ( $inhoud_nieuwe_verzending_array as $key => $value ) {
				if ( $value ) {
					$value = preg_replace( "@&nbsp;@", " ", $value );
					$value = preg_replace( "@ {2,}@", " ", $value );
					$value = trim( html_entity_decode( strip_tags( $value ) ) );
					if ( $value ) {
						$inhoud_nieuwe_verzending_stripped_array[$value]=true;
					}
				}
			}

			if ( $this->regels and is_array( $inhoud_nieuwe_verzending_stripped_array ) ) {
				foreach ( $inhoud_nieuwe_verzending_stripped_array as $key => $value ) {
					if ( ( is_array( $inhoud_laatste_verzending_stripped_array ) and !$inhoud_laatste_verzending_stripped_array[$key] ) or !is_array( $inhoud_laatste_verzending_stripped_array ) ) {
						$aantal_wijzigingen++;
					}
				}
			}

			$db2->query( "UPDATE leverancier_aankomstlijst SET niet_verzenden=0 WHERE aantal_wijzigingen<".intval( $aantal_wijzigingen )." AND leverancier_id='".intval( $db->f( "leverancier_id" ) )."' AND aankomstdatum='".date( "Y-m-d", $db->f( "aankomstdatum" ) )."';" );
			$db2->query( "UPDATE leverancier_aankomstlijst SET aantal_wijzigingen='".intval( $aantal_wijzigingen )."' WHERE leverancier_id='".intval( $db->f( "leverancier_id" ) )."' AND aankomstdatum='".date( "Y-m-d", $db->f( "aankomstdatum" ) )."';" );
		}
	}

	public function create_list() {

		global $vars, $mustlogin;

		$db = new DB_sql;
		$db2 = new DB_sql;

		unset( $this->klantnamen_boekingen, $this->klantnamen_garanties );

		if ( $mustlogin ) {
			if ( $this->totaal ) {
				cmslog_pagina_title( "Overzichten - Roominglist" );
			} else {
				cmslog_pagina_title( "Overzichten - Aankomstlijst" );
			}
		}

		// naamswijzingen opnemen?
		if ( $this->naamswijzigingen_doorgeven ) {
			$naamswijzigingen_doorgeven_array_temp=preg_split( "@,@", $this->naamswijzigingen_doorgeven );

			if ( is_array( $naamswijzigingen_doorgeven_array_temp ) ) {
				foreach ( $naamswijzigingen_doorgeven_array_temp as $key => $value ) {
					$naamswijzigingen_doorgeven_array[$value]=true;
				}
			}
		}

		$colspan=9;

		// Leveranciersgegevens ophalen
		$db->query( "SELECT bestelmailfax_taal, roominglist_toonaantaldeelnemers, roominglist_toontelefoonnummer, roominglist_site_benaming, roominglist_garanties_doorgeven FROM leverancier WHERE leverancier_id='".intval( $this->leverancier_id )."';" );
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

			if ( $this->totaal ) {
				$roominglist_garanties_doorgeven=$db->f( "roominglist_garanties_doorgeven" );
			}

			$this->bestelmailfax_taal=$db->f( "bestelmailfax_taal" );

		}


		if ( !$this->totaal ) {



			// aankomstlijst: welke garanties?
			$db->query( "SELECT garanties_doorgeven, inhoud_laatste_verzending FROM leverancier_aankomstlijst WHERE leverancier_id='".intval( $this->leverancier_id )."' AND aankomstdatum='".date( "Y-m-d", $this->date )."';" );
			if ( $db->next_record() ) {
				$roominglist_garanties_doorgeven=$db->f( "garanties_doorgeven" );
			}
			if ( !$roominglist_garanties_doorgeven ) $roominglist_garanties_doorgeven="0";

			// vergelijking kunnen maken met oude lijst
			unset( $inhoud_laatste_verzending_array, $inhoud_laatste_verzending_stripped_array );
			$inhoud_laatste_verzending_array=explode( "</td></tr>", trim( $db->f( "inhoud_laatste_verzending" ) ) );
			foreach ( $inhoud_laatste_verzending_array as $key => $value ) {
				if ( $value ) {

					if ( preg_match( "@data-list-id=\"([a-z][0-9]+)\"@", $value, $regs ) ) {
						$data_list_id=$regs[1];
					} else {
						$data_list_id=false;
					}

					if ( $data_list_id ) {
						$value = preg_replace( "@&nbsp;@", " ", $value );
						$value = preg_replace( "@ {2,}@", " ", $value );
						$value = trim( html_entity_decode( strip_tags( $value ) ) );
						if ( $value ) {
							$inhoud_laatste_verzending_stripped_array[$value]=$data_list_id;
						}
					}
				}
			}
		}

		// garantie-koppeling
		$db->query( "SELECT garantie_id, boeking_id, soort_garantie FROM garantie WHERE 1=1;" );
		while ( $db->next_record() ) {
			if ( $db->f( "boeking_id" )>0 ) {
				$garantie_boeking[$db->f( "boeking_id" )]=$db->f( "soort_garantie" );
				$garantie_boeking_garantie_id[$db->f( "boeking_id" )]=$db->f( "garantie_id" );
				$boeking_garantie[$db->f( "garantie_id" )]=$db->f( "boeking_id" );
			}
			$garantie_soort[$db->f( "garantie_id" )]=$db->f( "soort_garantie" );
		}


		// bepalen welke garanties moeten worden opgenomen
		$this->garantie_id_inquery=",0";
		if ( !$this->frm_filled and ( $roominglist_garanties_doorgeven or $roominglist_garanties_doorgeven=="0" ) ) {
			// nog geen formulier gepost: aan te vinken halen uit database roominglist_garanties_doorgeven
			$garanties_doorgeven_array=explode( ",", $roominglist_garanties_doorgeven );
			if ( is_array( $garanties_doorgeven_array ) ) {
				foreach ( $garanties_doorgeven_array as $key => $value ) {
					if ( $value and $boeking_garantie[$value] ) {
						$this->garanties_doorgeven.=",b".$boeking_garantie[$value];
					} else {
						$this->garanties_doorgeven.=",".$value;
					}
				}
			}
		}

		if ( $this->garanties_doorgeven ) {
			$garanties_doorgeven_array=explode( ",", $this->garanties_doorgeven );
			if ( is_array( $garanties_doorgeven_array ) ) {
				foreach ( $garanties_doorgeven_array as $key => $value ) {
					if ( preg_match( "@^b([0-9]+)$@", $value, $regs ) ) {
						$boeking_doorgeven[$regs[1]]=true;
						if ( $garantie_boeking[$regs[1]]==1 ) {
							// seizoen/bulk-garantie (via boeking): opslaan
							$this->garanties_doorgeven_opslaan_array[$garantie_boeking_garantie_id[$regs[1]]]=true;
						}
					} elseif ( $value ) {
						$this->garantie_id_inquery.=",".$value;
						if ( $garantie_soort[$value]==1 or !$this->totaal ) {
							// seizoen/bulk-garantie: opslaan
							$this->garanties_doorgeven_opslaan_array[$value]=true;
						}
					}
				}
			}
		}

		// bepalen welke garantie-boekingen opgenomen moeten worden (garantie-boekingen die zijn aangevinkt)
		$this->boeking_id_notinquery=",0";
		if ( is_array( $boeking_garantie ) and $this->totaal ) {
			foreach ( $boeking_garantie as $key => $value ) {
				if ( !$boeking_doorgeven[$value] ) {
					$this->boeking_id_notinquery.=",".$value;
				}
			}
		}


		//
		// Gewone boekingen
		//

		if ( $this->totaal ) {
			$where.="b.aankomstdatum_exact>='".$this->van."' AND ";
			if ( $this->tot ) {
				$where.="b.aankomstdatum_exact<='".$this->tot."' AND ";
			}
		} else {
			$where.="b.aankomstdatum='".addslashes( $this->date )."' AND ";
		}

		if ( $this->frm_filled and $this->boeking_id_notinquery ) {
			$where.="b.boeking_id NOT IN (".substr( $this->boeking_id_notinquery, 1 ).") AND ";
		}

		$db->query( "SELECT b.boeking_id, b.boekingsnummer, b.type_id, b.aan_leverancier_doorgegeven_naam, b.bestelstatus, UNIX_TIMESTAMP(b.besteldatum) AS besteldatum, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.mobielwerk, bp.telefoonnummer, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." (b.leverancier_id=l.leverancier_id OR b.beheerder_id=l.leverancier_id) AND l.leverancier_id='".addslashes( $this->leverancier_id )."' AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;" );
		while ( $db->next_record() ) {

			if ( $db->f( "besteldatum" )>0 or isset( $garantie_boeking[$db->f( "boeking_id" )] ) ) {

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
					$accnaam_kort_aanvullend.=" <i>(our name: ".wt_he( $db->f( "accommodatie" ).( $db->f( "type" ) ? " ".$db->f( "type" ) : "" ) ).")</i>";
				}
				$tempplaatsid[$sortkey]=$db->f( "plaats_id" );




				$naam=$db->f( "aan_leverancier_doorgegeven_naam" );

				if ( $this->totaal ) {

					// naamswijzigingen in array plaatsen
					unset( $naamswijziging_opvallend );
					if ( $db->f( "aan_leverancier_doorgegeven_naam" )!=wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) ) ) {
						$this->naamswijzigingen[$db->f( "boeking_id" )] = "Boeking ".$db->f( "boekingsnummer" ).": \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) )."\"";

						$this->naamswijzigingen_html[$db->f( "boeking_id" )] = "Boeking <a href=\"".$vars["path"]."cms_boekingen.php?show=21&21k0=".$db->f( "boeking_id" )."\" target=\"_blank\">".$db->f( "boekingsnummer" )."</a>:".wt_he( " \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) )."\"" );

						// tonen of het om een garantie-boeking gaat
						if ( isset( $garantie_boeking[$db->f( "boeking_id" )] ) ) {
							$this->naamswijzigingen_html[$db->f( "boeking_id" )]="<span class=\"soort_garantie_".$garantie_boeking[$db->f( "boeking_id" )]."\">".$this->naamswijzigingen_html[$db->f( "boeking_id" )]."</span>";
						}

						// kijken of naamswijziging in roominglist moet worden opgenomen
						if ( $naamswijzigingen_doorgeven_array[$db->f( "boeking_id" )] ) {
							$naam=wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) );

							// klantnamen in array plaatsen
							$this->klantnamen_boekingen[$db->f( "boeking_id" )] = wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) );

							// naamswijzigingen laten opvallen in de roominglist
							$naamswijziging_opvallend="font-weight:bold;background-color:yellow;";
						}
					}
				} else {
					if ( $db->f( "aan_leverancier_doorgegeven_naam" )!=wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) ) ) {
						$naamswijziging_opvallend="font-weight:bold;background-color:yellow;";
						$this->aankomstlijst_gele_wijziging = true;

						// klantnamen in array plaatsen
						$this->klantnamen_boekingen[$db->f( "boeking_id" )] = wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) );

					} else {
						$naamswijziging_opvallend="";
					}
					$naam = wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) );
				}

				$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid;".$naamswijziging_opvallend."'".( $this->onbesteld_opvallend&&$db->f( "bestelstatus" )<=1 ? " class='nog_niet_besteld'" : "" )." data-list-id=\"b".$db->f( "boeking_id" )."\"><td valign=\"top\">".wt_he( $naam )."</td>";

				// boeking die aan garantie is: opnemen in de lijst met te selecteren garanties
				if ( isset( $garantie_boeking[$db->f( "boeking_id" )] ) ) {
					if ( $this->totaal ) {
						$this->garanties_html["b".$db->f( "boeking_id" )] = "<span class=\"soort_garantie_".$garantie_boeking[$db->f( "boeking_id" )]."\">".wt_he( $vars["alletypes"][$db->f( "type_id" )] )." - ".wt_he( wt_naam( $db->f( "voornaam" ), $db->f( "tussenvoegsel" ), $db->f( "achternaam" ) ) )." - <a href=\"".$vars["path"]."cms_boekingen.php?show=21&21k0=".$db->f( "boeking_id" )."\" target=\"_blank\">".$db->f( "boekingsnummer" )."</a> - ".date( "d/m/Y", $db->f( "aankomstdatum_exact" ) )."</span>";
					}

					if ( $garantie_boeking[$db->f( "boeking_id" )]==2 and $this->totaal ) {
						// "garantie: op naam en losse weken": standaard doorgeven
						$this->garanties_doorgeven.=","."b".$db->f( "boeking_id" );
					}
				}

				if ( $roominglist_toontelefoonnummer ) {
					$regels[$sortkey].="<td valign=\"top\">";
					if ( $db->f( "mobielwerk" ) ) {
						$regels[$sortkey].=wt_he( $db->f( "mobielwerk" ) );
					} elseif ( $db->f( "telefoonnummer" ) ) {
						$regels[$sortkey].=wt_he( $db->f( "telefoonnummer" ) );
					} else {
						$regels[$sortkey].="&nbsp;";
					}
					$regels[$sortkey].="</td>";
				}
				$regels[$sortkey].="<td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "aankomstdatum_exact" ) )."</td><td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "vertrekdatum_exact" ) )."</td><td valign=\"top\">".wt_he( $db->f( "plaats" ) )."</td><td valign=\"top\">".wt_he( $accnaam_kort ).$accnaam_kort_aanvullend."</td><td valign=\"top\">".( $db->f( "code" ) ? wt_he( $db->f( "code" ) ) : "&nbsp;" )."</td>";
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
				$regels[$sortkey].="<td valign=\"top\">".( $db->f( "leverancierscode" ) ? wt_he( $db->f( "leverancierscode" ) ) : "&nbsp;" )."</td><td valign=\"top\">".( $db->f( "opmerkingen_voucher" ) ? nl2br( wt_he( $db->f( "opmerkingen_voucher" ) ) ) : "&nbsp;" )."</td></tr>";
			}
		}

		//
		// Garanties
		//
		unset( $where );
		if ( $this->totaal ) {
			$where="g.aankomstdatum_exact>='".$this->van."' AND ";
			if ( $this->tot ) {
				$where.="g.aankomstdatum_exact<='".$this->tot."' AND ";
			}
		} else {
			$where="g.aankomstdatum='".addslashes( $this->date )."' AND ";
		}

		// aleen garanties die zijn aangevinkt opnemen
		if ( $this->frm_filled or $this->vergelijk_lijsten ) {
			$where.="g.garantie_id IN (".substr( $this->garantie_id_inquery, 1 ).") AND ";
		}

		// if($this->verberg_garanties) {
		//  $where.="g.garantie_id NOT IN (".$this->verberg_garanties.") AND ";
		// }

		$db->query( "SELECT g.garantie_id, g.naam, g.aan_leverancier_doorgegeven_naam, g.type_id, g.soort_garantie, g.aankomstdatum_exact, g.vertrekdatum_exact, g.factuurnummer, UNIX_TIMESTAMP(g.inkoopdatum) AS inkoopdatum, g.reserveringsnummer_extern, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM garantie g, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." ((g.leverancier_id=l.leverancier_id AND g.leverancier_id='".intval( $this->leverancier_id )."') OR (t.beheerder_id='".intval( $this->leverancier_id )."' AND l.leverancier_id='".intval( $this->leverancier_id )."')) AND g.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND g.boeking_id=0 ORDER BY g.aankomstdatum_exact, t.type_id, g.garantie_id;" );
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

			if ( $db->f( "inkoopdatum" ) or $db->f( "factuurnummer" ) ) {
				$nog_niet_besteld=false;
			} else {
				$nog_niet_besteld=true;
			}



			$naam=$db->f( "aan_leverancier_doorgegeven_naam" );

			if ( $this->totaal ) {

				// naamswijzigingen in array plaatsen
				unset( $naamswijziging_opvallend );
				if ( $db->f( "aan_leverancier_doorgegeven_naam" )!=$db->f( "naam" ) ) {
					$this->naamswijzigingen["g".$db->f( "garantie_id" )] = "Garantie ".$db->f( "reserveringsnummer_extern" ).": \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".$db->f( "naam" )."\"";

					$this->naamswijzigingen_html["g".$db->f( "garantie_id" )] = "<span class=\"soort_garantie_".$db->f( "soort_garantie" )."\">Garantie <a href=\"cms_garanties.php?edit=34&34k0=".$db->f( "garantie_id" )."\" target=\"_blank\">".$db->f( "reserveringsnummer_extern" )."</a>:".wt_he( " \"".$db->f( "aan_leverancier_doorgegeven_naam" )."\" is nu \"".$db->f( "naam" )."\"" )."</span>";

					// kijken of naamswijziging in roominglist moet worden opgenomen
					if ( $naamswijzigingen_doorgeven_array["g".$db->f( "garantie_id" )] ) {
						$naam = $db->f( "naam" );

						// Klantnamen in array plaatsen
						$this->klantnamen_garanties[$db->f( "garantie_id" )] = $db->f( "naam" );

						// naamswijzigingen laten opvallen in de roominglist
						$naamswijziging_opvallend="font-weight:bold;background-color:yellow;";
					}
				}
			} else {

				if ( $db->f( "aan_leverancier_doorgegeven_naam" )!=$db->f( "naam" ) ) {
					$naamswijziging_opvallend="font-weight:bold;background-color:yellow;";
					$this->aankomstlijst_gele_wijziging = true;

					// Klantnamen in array plaatsen
					$this->klantnamen_garanties[$db->f( "garantie_id" )] = $db->f( "naam" );

				} else {
					$naamswijziging_opvallend="";
				}

				$naam = $db->f( "naam" );
			}

			$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid;".$naamswijziging_opvallend."'".( $this->onbesteld_opvallend&&$nog_niet_besteld ? " class='nog_niet_besteld'" : "" )." data-list-id=\"g".$db->f( "garantie_id" )."\"><td valign=\"top\">".wt_he( $naam )."</td>";

			// Garanties in array plaatsen
			$this->garanties[$db->f( "garantie_id" )] = $vars["alletypes"][$db->f( "type_id" )]." - ".$db->f( "naam" )." - ".$db->f( "reserveringsnummer_extern" )." - ".date( "d/m/Y", $db->f( "aankomstdatum_exact" ) );

			$this->garanties_html[$db->f( "garantie_id" )] = "<span class=\"soort_garantie_".$db->f( "soort_garantie" )."\">".wt_he( $vars["alletypes"][$db->f( "type_id" )] )." - ".wt_he( $db->f( "naam" ) )." - <a href=\"cms_garanties.php?edit=34&34k0=".$db->f( "garantie_id" )."\" target=\"_blank\">".$db->f( "reserveringsnummer_extern" )."</a> - ".date( "d/m/Y", $db->f( "aankomstdatum_exact" ) )."</span>";

			if ( $db->f( "soort_garantie" )==2 and $this->totaal ) {
				// "garantie: op naam en losse weken": standaard doorgeven
				$this->garanties_doorgeven.=",".$db->f( "garantie_id" );
			}

			if ( $roominglist_toontelefoonnummer ) {
				$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
			}
			$regels[$sortkey].="<td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "aankomstdatum_exact" ) )."</td><td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "vertrekdatum_exact" ) )."</td><td valign=\"top\">".wt_he( $db->f( "plaats" ) )."</td><td valign=\"top\">".wt_he( $accnaam_kort )."</td><td valign=\"top\">".( $db->f( "code" ) ? wt_he( $db->f( "code" ) ) : "&nbsp;" )."</td>";
			if ( $roominglist_toonaantaldeelnemers ) {
				$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
			} else {
				$regels[$sortkey].="<td valign=\"top\">".$db->f( "maxaantalpersonen" )."</td>";
			}
			$regels[$sortkey].="<td valign=\"top\">".( $db->f( "factuurnummer" ) ? wt_he( $db->f( "factuurnummer" ) ) : ( $db->f( "inkoopdatum" )>0 ? "OK ".date( "d-m-Y", $db->f( "inkoopdatum" ) ) : "&nbsp;" ) )."</td><td valign=\"top\">".( $db->f( "opmerkingen_voucher" ) ? nl2br( wt_he( $db->f( "opmerkingen_voucher" ) ) ) : "&nbsp;" )."</td></tr>";
		}



		//
		// eigenaar_blokkering
		//

		unset( $where );
		if ( $this->totaal ) {
			$where="UNIX_TIMESTAMP(e.begin)>='".$this->van."' AND ";
			if ( $this->tot ) {
				$where.="UNIX_TIMESTAMP(e.begin)<='".$this->tot."' AND ";
			}
		} else {
			$where="e.week='".addslashes( $this->date )."' AND ";
		}

		$db->query( "SELECT e.type_id, e.soort, UNIX_TIMESTAMP(e.begin) AS aankomstdatum_exact, UNIX_TIMESTAMP(e.eind) AS vertrekdatum_exact, e.deelnemers, e.tekst_extra_options, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM eigenaar_blokkering e, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." t.leverancier_id=l.leverancier_id AND (t.leverancier_id='".addslashes( $this->leverancier_id )."' OR t.beheerder_id='".intval($this->leverancier_id)."') AND e.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND e.soort=1 ORDER BY e.begin, e.eind, t.type_id;" );
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

			$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid;' data-list-id=\"e".$db->f( "type_id" ).$db->f( "aankomstdatum_exact" )."\"><td valign=\"top\"><i>accommodation-owner</i></td>";
			if ( $roominglist_toontelefoonnummer ) {
				$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
			}
			$regels[$sortkey].="<td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "aankomstdatum_exact" ) )."</td><td valign=\"top\" nowrap>".date( "d-m-y", $db->f( "vertrekdatum_exact" ) )."</td><td valign=\"top\">".wt_he( $db->f( "plaats" ) )."</td><td valign=\"top\">".wt_he($accnaam_kort)."</td><td valign=\"top\">".( $db->f( "code" ) ? wt_he( $db->f( "code" ) ) : "&nbsp;" )."</td>";
			if ( $roominglist_toonaantaldeelnemers ) {
				$regels[$sortkey].="<td valign=\"top\">".$db->f("deelnemers")."</td>";
			} else {
				$regels[$sortkey].="<td valign=\"top\">".$db->f( "maxaantalpersonen" )."</td>";
			}
			$regels[$sortkey].="<td valign=\"top\">&nbsp;</td><td valign=\"top\">".( $db->f( "tekst_extra_options" ) ? nl2br( wt_he( $db->f( "tekst_extra_options" ) ) ) : "&nbsp;" )."</td></tr>";

		}



		//
		// process regels
		//
		unset( $this->regels );

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

			$this->regels=trim( $this->regels );


			// check for changes for arrivals
			if ( !$this->totaal and is_array( $inhoud_laatste_verzending_stripped_array ) ) {
				$inhoud_nieuwe_verzending_array=explode( "</td></tr>", trim( $this->regels ) );
				foreach ( $inhoud_nieuwe_verzending_array as $key => $value ) {
					if ( $value ) {

						if ( preg_match( "@data-list-id=\"([a-z][0-9]+)\"@", $value, $regs ) ) {
							$data_list_id = $regs[1];
						} else {
							$data_list_id = false;
						}

						if ( $data_list_id ) {
							$value = preg_replace( "@&nbsp;@", " ", $value );
							$value = preg_replace( "@ {2,}@", " ", $value );
							$value = trim( html_entity_decode( strip_tags( $value ) ) );
							if ( $value ) {
								$inhoud_nieuwe_verzending_stripped_array[$value]=$data_list_id;
							}
						}
					}
				}

				if ( $this->regels and is_array( $inhoud_nieuwe_verzending_stripped_array ) ) {
					foreach ( $inhoud_nieuwe_verzending_stripped_array as $key => $value ) {
						if ( $inhoud_laatste_verzending_stripped_array[$key] ) {
							$data_list_id_ongewijzigd[$value] = true;
						}
					}
				}

				// echo wt_dump($data_list_id_ongewijzigd);

				unset($while_teller);
				while(preg_match("@(((<tr style=')([^>]+))data-list-id=\"([a-z][0-9]+)\")@", $html, $regs)) {
					$while_teller++;
					// echo wt_dump($regs);
					$replace = $regs[2];
					if($data_list_id_ongewijzigd[$regs[5]]) {

					} else {
						$replace = str_replace($regs[3] , $regs[3]."font-weight:bold;background-color:yellow;", $replace);
						$this->aankomstlijst_gele_wijziging = true;
					}
					// echo wt_he("==".$replace."==");
					// echo "replace ".wt_he($regs[1])." with ".wt_he($replace)."<hr/>";
					$html = str_replace($regs[1], $replace, $html);
					// exit;

					if($while_teller>1000) {
						break;
					}
				}
			}


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
			$totaal_html.="<img width=92 height=79 src=\"https://www.chalet.nl/pic/factuur_logo_vakantiewoningen.png\"></td></tr></thead></table>";
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

	public function word_bestand( $settings=array("") ) {

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


		if ( $settings["save_filename"] ) {

			$ms->headers=false;

			ob_clean();
			ob_start();
			$ms->create_word_document();
			$output = ob_get_clean();

			save_data_to_file( $settings["save_filename"], $output );

		} else {
			$ms->create_word_document();
		}
	}

	public function overzicht_te_verzenden( $settings=array("") ) {

		global $vars;

		$db = new DB_sql;
		// $db2 = new DB_sql;

		if ( $settings["in_de_wacht"] ) {
			$andquery.=" AND roominglist_volgende_controle>NOW()";
			$return.="<h1>In de wacht</h1>";
		} else {
			$andquery.=" AND (roominglist_volgende_controle IS NULL OR roominglist_volgende_controle<NOW())";
			$return.="<h1>Te verzenden roominglists</h1>";
		}


		$db->query( "SELECT leverancier_id, naam, beheerder, roominglist_aantal_wijzigingen, UNIX_TIMESTAMP(roominglist_laatste_verzending_datum) AS roominglist_laatste_verzending_datum, UNIX_TIMESTAMP(roominglist_volgende_controle) AS roominglist_volgende_controle, roominglist_versturen FROM leverancier WHERE roominglist_aantal_wijzigingen>0".$andquery." ORDER BY roominglist_versturen, naam, roominglist_laatste_verzending_datum;" );
		if ( $db->num_rows() ) {
			$return.="<table cellspacing=\"0\" class=\"tbl striped\">";
			$return.="<tr><th>Leverancier</th><th>Laatste verzending</th><th>Niet in laatste verzending opgenomen</th>";
			if ( $settings["in_de_wacht"] ) {
				$return.="<th>Herinneren vanaf</th>";
			}
			$return.="</tr>";

			while ( $db->next_record() ) {
				$return.="<tr class=\"".( $db->f( "roominglist_versturen" )==2 ? "tr_onopvallend" : "" )."\"><td><a href=\"".$vars["path"]."cms_roomingaankomst.php?t=1&levid=".$db->f( "leverancier_id" )."\">".wt_he( $db->f( "naam" ) )."</a>".( $db->f( "beheerder" ) ? " (beheerder)" : "" ).( $db->f( "roominglist_versturen" )==2 ? " (wil geen roominglists)" : "" )."</td><td>".( $db->f( "roominglist_laatste_verzending_datum" )>0 ? date( "d-m-Y", $db->f( "roominglist_laatste_verzending_datum" ) ) : "&nbsp;" )."</td><td>".intval( $db->f( "roominglist_aantal_wijzigingen" ) )."</td>";

				if ( $settings["in_de_wacht"] ) {
					$return.="<td>".date( "d-m-Y", $db->f( "roominglist_volgende_controle" ) )."</td>";
				}

				$return.="</tr>";
			}

			$return.="</table>";

		} else {
			if ( $settings["in_de_wacht"] ) {
				$return.="<p>Er staan geen roominglists in de wacht.</p>";
			} else {
				$return.="<p>Er zijn geen te verzenden roominglists.</p>";
			}
		}

		return $return;
	}

	public function overzicht_goedgekeurd( $settings=array("") ) {

		global $vars;

		$db = new DB_sql;
		// $db2 = new DB_sql;

		if ( $settings["nog_niet_goedgekeurd"] ) {
			$andquery.=" AND roominglist_goedgekeurd=''";
			$return.="<h1>Wacht op goedkeuring</h1>";
			$orderby=", roominglist_laatste_verzending_datum";
		} else {
			$andquery.=" AND roominglist_goedgekeurd<>''";
			$return.="<h1>Goedgekeurd</h1>";
			$orderby="";
		}


		$db->query( "SELECT leverancier_id, naam, beheerder, roominglist_aantal_wijzigingen, UNIX_TIMESTAMP(roominglist_laatste_verzending_datum) AS roominglist_laatste_verzending_datum, UNIX_TIMESTAMP(roominglist_volgende_controle) AS roominglist_volgende_controle, roominglist_versturen FROM leverancier WHERE roominglist_laatste_verzending_datum IS NOT NULL".$andquery." ORDER BY roominglist_versturen".$orderby.", naam, roominglist_laatste_verzending_datum;" );
		if ( $db->num_rows() ) {
			$return.="<table cellspacing=\"0\" class=\"tbl striped\">";
			$return.="<tr><th>Leverancier</th><th>Verzonden</th>";
			if ( $settings["in_de_wacht"] ) {
				$return.="<th>Verzenden vanaf</th>";
			}
			$return.="</tr>";

			while ( $db->next_record() ) {
				$return.="<tr class=\"".( $db->f( "roominglist_versturen" )==2 ? "tr_onopvallend" : "" )."\"><td><a href=\"".$vars["path"]."cms_roomingaankomst.php?t=1&levid=".$db->f( "leverancier_id" )."\">".wt_he( $db->f( "naam" ) )."</a>".( $db->f( "beheerder" ) ? " (beheerder)" : "" ).( $db->f( "roominglist_versturen" )==2 ? " (wil geen roominglists)" : "" )."</td><td>".( $db->f( "roominglist_laatste_verzending_datum" )>0 ? date( "d-m-Y", $db->f( "roominglist_laatste_verzending_datum" ) ) : "&nbsp;" )."</td>";

				if ( $settings["in_de_wacht"] ) {
					$return.="<td>".date( "d-m-Y", $db->f( "roominglist_volgende_controle" ) )."</td>";
				}

				$return.="</tr>";
			}

			$return.="</table>";

		} else {
			if ( $settings["nog_niet_goedgekeurd"] ) {
				$return.="<p>Er zijn geen goed te keuren roominglists.</p>";
			} else {
				$return.="<p>Er zijn geen goedgekeurde roominglists.</p>";
			}
		}

		return $return;

	}


	public function overzicht_aankomstlijsten( $settings=array("") ) {

		global $vars;

		$db = new DB_sql;

		if ( $settings["nog_niet_goedgekeurd"] ) {
			$return.="<h1>Wacht op goedkeuring</h1>";
			$andquery.=" AND la.laatste_verzending IS NOT NULL AND la.goedgekeurd IS NULL";
		} elseif ( $settings["niet_verzenden"] ) {
			$return.="<h1>Verzenden niet nodig</h1>";
			$andquery.=" AND la.niet_verzenden=1";
		} elseif ( $settings["goedgekeurd"] ) {
			$return.="<h1>Goedgekeurd</h1>";
			$andquery.=" AND la.goedgekeurd IS NOT NULL";
		} else {
			$return.="<h1>Te verzenden</h1>";
			$andquery.=" AND (la.leverancier_id IS NULL OR la.aantal_wijzigingen>0 AND la.niet_verzenden=0)";
		}


		$inquery="0";

		// Gewone boekingen
		$db->query( "SELECT DISTINCT l.naam, l.leverancier_id FROM boeking b, accommodatie a, type t, leverancier l WHERE b.aankomstdatum='".addslashes( $settings["date"] )."' AND b.leverancier_id=l.leverancier_id AND b.goedgekeurd=1 AND b.geannuleerd=0 AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id;" );
		while ( $db->next_record() ) {
			$inquery.=",".$db->f( "leverancier_id" );
		}

		// Gewone boekingen (via beheerder)
		$db->query( "SELECT DISTINCT l.naam, l.leverancier_id FROM boeking b, accommodatie a, type t, leverancier l WHERE b.aankomstdatum='".addslashes( $settings["date"] )."' AND b.beheerder_id=l.leverancier_id AND b.goedgekeurd=1 AND b.geannuleerd=0 AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id;" );
		while ( $db->next_record() ) {
			$inquery.=",".$db->f( "leverancier_id" );
		}

		// Garanties
		$db->query( "SELECT DISTINCT l.naam, l.leverancier_id FROM garantie g, accommodatie a, type t, leverancier l WHERE g.aankomstdatum='".addslashes( $settings["date"] )."' AND g.leverancier_id=l.leverancier_id AND g.boeking_id=0 AND g.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id;" );
		while ( $db->next_record() ) {
			$inquery.=",".$db->f( "leverancier_id" );
		}


		$db->query( "SELECT l.leverancier_id, naam, beheerder, aankomstlijst_versturen, UNIX_TIMESTAMP(la.laatste_verzending) AS laatste_verzending, la.goedgekeurd, la.aantal_wijzigingen FROM leverancier l LEFT JOIN leverancier_aankomstlijst la ON (la.leverancier_id=l.leverancier_id AND la.aankomstdatum='".date( "Y-m-d", $settings["date"] )."') WHERE l.leverancier_id IN (".$inquery.")".$andquery." ORDER BY aankomstlijst_versturen, naam;" );
		if ( $db->num_rows() ) {
			$return.="<table cellspacing=\"0\" class=\"tbl striped\">";
			$return.="<tr><th>Leverancier</th>";
			$return.="<th>Verzonden</th>";
			if ( !$settings["nog_niet_goedgekeurd"] and !$settings["goedgekeurd"] ) {
				$return.="<th>Niet in laatste verzending opgenomen</th>";
			}
			if ( $settings["goedgekeurd"] ) {
				$return.="<th>Goedgekeurd</th>";
			}
			$return.="</tr>";

			while ( $db->next_record() ) {
				$return.="<tr class=\"".( $db->f( "aankomstlijst_versturen" )==2 ? "tr_onopvallend" : "" )."\"><td><a href=\"".$vars["path"]."cms_roomingaankomst.php?t=2&date=".intval( $settings["date"] )."&levid=".$db->f( "leverancier_id" )."\">".wt_he( $db->f( "naam" ) )."</a>".( $db->f( "beheerder" ) ? " (beheerder)" : "" ).( $db->f( "aankomstlijst_versturen" )==2 ? " (wil geen aankomstlijsten)" : "" )."</td>";

				$return.="<td>".( $db->f( "laatste_verzending" )>0 ? date( "d-m-Y", $db->f( "laatste_verzending" ) ) : "&nbsp;" )."</td>";

				if ( !$settings["nog_niet_goedgekeurd"] and !$settings["goedgekeurd"] ) {
					$return.="<td>".( $db->f( "aantal_wijzigingen" )>0 ? $db->f( "aantal_wijzigingen" ) : "&nbsp;" )."</td>";
				}

				if ( $settings["goedgekeurd"] ) {
					$return.="<td>".( $db->f( "goedgekeurd" ) ? wt_he( preg_replace( "@:@", "", substr( $db->f( "goedgekeurd" ), 0, 10 ) ) ) : "&nbsp;" )."</td>";
				}
				$return.="</tr>";
			}

			$return.="</table>";

		} else {
			if ( !$settings["nog_niet_goedgekeurd"] and !$settings["goedgekeurd"] ) {
				$return.="<p>Er zijn geen aankomstlijsten voor deze datum.</p>";
			}
		}

		return $return;
	}



}



?>