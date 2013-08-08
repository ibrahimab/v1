<?php


/**
* Tarieventabel (alleen nog winter-versie)
*/

class tarieventabel {

	public $voorraad;

	function __construct() {

		$this->toon_interne_informatie = false;

		$this->voorraad_doorlopen=array(
			"garantie" => "Garantie",
			"aflopen_allotment" => "Allotment loopt af op",
			"allotment" => "Allotment",
			"vervallen_allotment" => "Vervallen allotment",
			"optie_leverancier" => "Optie leverancier",
			"xml" => "XML",
			"request" => "Request",
			"optie_klant" => "Optie klant",
			"totaal" => "Totaal",
			"voorraad_bijwerken" => "Automatisch bijwerken",
			"blokkeren_wederverkoop" => "Blokkeren wederverkoop",
			"aantal_geboekt" => "Aantal geboekt"
		);


		// $this->seizoen_id=22;
		// $this->type_id=5940;
		// $this->type_id=115;
		// $this->type_id=174;
#		$this->type_id=240;

	}

	public function toontabel() {

		global $vars;

		$this->tarieven_uit_database();

		$return .= "<div class=\"tarieventabel_wrapper\" data-boek-url=\"".wt_he($vars["path"].txt("menu_boeken").".php?tid=".$this->type_id."&o=".urlencode($_GET["o"]).(!$this->arrangement && $_GET["ap"] ? "&ap=".intval($_GET["ap"]) : ""))."\">";

		$return .= $this->tabel_top();
		$return .= $this->tabel_content();
		$return .= $this->tabel_bottom();

		$return .= "</div>";

		return $return;

	}

	private function tabel_top() {

		global $vars;

		$return .= "<div class=\"tarieventabel_top\">";
		$return .= "<h1>".html("tarieven","tarieventabel")." ".wt_he($this->seizoen["naam"])."</h1>";

		if($this->arrangement) {
			$return .= html("ineuros","tarieventabel").", ".html("perpersooninclskipas","tarieventabel");
		} else {
			$return .= html("ineuros","tarieventabel").", ".html("peraccommodatie","tarieventabel");
		}
		$return .= "</div>";

		return $return;

	}

	private function tabel_content() {

		if($this->tarief) {

			//
			// kolomnamen
			//

			// bepalen welke aantallen personen direct zichtbaar moeten zijn

			if($this->arrangement) {
				$this->max_personen=max(array_keys($this->aantal_personen));
				$this->min_personen=min(array_keys($this->aantal_personen));

				if($_GET["ap"]) {
					$this->max_personen_tonen=$_GET["ap"]+2;
					$this->min_personen_tonen=$_GET["ap"]-2;
				} else {
					$this->max_personen_tonen=max(array_keys($this->aantal_personen));
					$this->min_personen_tonen=$this->max_personen_tonen-4;
				}
			}


			$return.="<table cellspacing=\"0\" cellpadding=\"0\" class=\"tarieventabel_border tarieventabel_titels_links\">";
			$return.="<tr class=\"tarieventabel_maanden\"><td class=\"tarieventabel_maanden_leeg\">&nbsp;</td></tr>";

			$return.="</td></tr>";
			$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdatum","tarieventabel")."</td></tr>";
			$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdag","tarieventabel")."</td></tr>";
			$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aantalnachten","tarieventabel")."</td></tr>";

			// voorraad - kolomnamen
			if($this->toon_interne_informatie) {
				foreach ($this->voorraad_doorlopen as $key => $value) {
					$return.="<tr class=\"tarieventabel_voorraad_tr tarieventabel_voorraad_".$key."\"><td>".wt_he($value)."</td></tr>";
				}

				$return.="<tr class=\"tarieventabel_maanden\"><td class=\"tarieventabel_maanden_leeg\">&nbsp;</td></tr>";

				$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdatum","tarieventabel")."</td></tr>";
				$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdag","tarieventabel")."</td></tr>";
				$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aantalnachten","tarieventabel")."</td></tr>";
			}


			// korting
			if($this->aanbieding_actief) {
				$return.="<tr><td>";
				if($this->accinfo["toonper"]==1) {
					$return.=html("kortingaccommodatie","tarieventabel");
				} else {
					$return.=html("korting","tarieventabel");
				}
				$return.="</td></tr>";
			}


			if($this->arrangement) {

				// regels met aantal personen tonen
				foreach ($this->aantal_personen as $key => $value) {
					$return.="<tr class=\"".trim(($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? "tarieventabel_aantal_personen_verbergen" : "").($_GET["ap"]==$key && !$_GET["d"] ? " tarieventabel_tarieven_gekozen" : ""))."\"><td>".$key."&nbsp;".($key==1 ? html("persoon","tarieventabel") : html("personen","tarieventabel"))."</td></tr>";
				}
			} else {
				$return.="<tr><td>".html("prijsperaccommodatie","tarieventabel")."</td></tr>";
			}


			$return.="</table>";

			$return .= $this->tabel_tarieven();

			// minder personen open/dichtklappen
			$return.="<div class=\"tarieventabel_toggle_personen\">";
			if($this->max_personen_tonen<$this->max_personen or $this->min_personen_tonen>$this->min_personen) {
				$return.="<a href=\"#\" data-default=\"".html("minderpersonen","tarieventabel")."\" data-hide=\"".html("minderpersonen_verbergen","tarieventabel")."\"><i class=\"icon-chevron-sign-down\"></i>&nbsp;<span>".html("minderpersonen","tarieventabel")."</span></a>";
			} else {
				$return.="&nbsp;";
			}
			$return.="</div>";


			// legenda
			$return.="<div class=\"tarieventabel_legenda\">";
			if($this->aanbieding_actief) {
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_aanbieding\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_aanbieding","tarieventabel")."</div>";
			}
			if($this->arrangement) {
				if($_GET["ap"] and $_GET["d"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum_aantal_personen","tarieventabel")."</div>";
				} elseif($_GET["ap"] and !$_GET["d"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_aantal_personen","tarieventabel")."</div>";
				} elseif($_GET["d"] and !$_GET["ap"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum","tarieventabel")."</div>";
				}
			} else {
				if($_GET["d"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum","tarieventabel")."</div>";
				}
			}
			if($this->tarieventabel_tarieven_niet_beschikbaar) {
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_niet_beschikbaar\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_niet_beschikbaar","tarieventabel")."</div>";
			}
			$return.="</div>";


			$return.="<div class=\"tarieventabel_pijl tarieventabel_pijl_links\">";
			$return.="<i class=\"icon-chevron-left\"></i>";
			$return.="</div>";

			$return.="<div class=\"tarieventabel_pijl tarieventabel_pijl_rechts\">";
			$return.="<i class=\"icon-chevron-right\"></i>";
			$return.="</div>";
		} else {
			trigger_error("lege tarieventabel",E_USER_NOTICE);
		}

		return $return;

	}

	private function datum_headers() {

		global $vars;

		# regel met maanden
		$return.="<tr class=\"tarieventabel_maanden\">";
		$kolomteller=0;
		foreach ($this->maand as $key => $value) {
			$kolomteller++;
			$kolomteller_onderliggend=$kolomteller_onderliggend+$value;

			$unixtime=mktime(0,0,0,substr($key,5,2),1,substr($key,0,4));

			if($kolomteller==1) {
				$return.="<td class=\"tarieventabel_tarieven_kolom_links\"";
			} elseif($kolomteller==count($this->maand)) {
				$return.="<td class=\"tarieventabel_tarieven_kolom_rechts\"";
			} else {
				$return.="<td";
			}

			$return.=" colspan=\"".$value."\" data-maand-kolom=\"".$kolomteller_onderliggend."\">".DATUM("MAAND JJJJ",$unixtime,$vars["taal"])."</td>";
		}
		$return.="</tr>";

		# regel met aankomstdatum
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content\">";
		foreach ($this->dag as $key => $value) {
			$return.="<td>".$value."</td>";
		}
		$return.="</tr>";

		# regel met dag van de week
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content tarieventabel_datumbalk_minder_opvallend\">";
		foreach ($this->dag_van_de_week as $key => $value) {
			$return.="<td".($this->dag_van_de_week_afwijkend[$key] ? " class=\"tarieventabel_datumbalk_opvallend\"" : "").">".$value."</td>";
		}
		$return.="</tr>";

		# regel met aantal nachten
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content tarieventabel_datumbalk_minder_opvallend\">";
		foreach ($this->dag_van_de_week as $key => $value) {
			$return.="<td".($this->aantalnachten[$key]<>7 ? " class=\"tarieventabel_datumbalk_opvallend\"" : "").">".$this->aantalnachten[$key]."</td>";
		}
		$return.="</tr>";

		return $return;

	}

	private function tabel_tarieven() {

		global $vars;

		$return.="<div class=\"tarieventabel_wrapper_rechts\"><table cellspacing=\"0\" class=\"tarieventabel_border tarieventabel_content\">";

		// datum-headers
		$return.=$this->datum_headers();

		// // voorraad
		if($this->toon_interne_informatie) {

			foreach ($this->voorraad_doorlopen as $key0 => $value0) {
				$kolomteller=0;

				$return.="<tr class=\"tarieventabel_voorraad_tr tarieventabel_voorraad_content_tr tarieventabel_voorraad_".$key0."\">";

				foreach ($this->dag as $key => $value) {
					$kolomteller++;

					$class="";

					if($kolomteller==1) {
						$class.=" tarieventabel_tarieven_kolom_links";
					} elseif($kolomteller==count($this->dag)) {
						$class.=" tarieventabel_tarieven_kolom_rechts";
					}

					$return.="<td class=\"".trim($class)."\">";

					if($this->voorraad[$key0][$key]) {
						if($key0=="aflopen_allotment") {
							$return.=date("d/m",$this->voorraad[$key0][$key]);
						} elseif($key0=="voorraad_bijwerken") {
							$return.="&nbsp;<img src=\"".$vars["path"]."pic/vinkje.gif\">&nbsp;";
						} else {
							$return .= $this->voorraad[$key0][$key];
						}
					} else {
						$return.="&nbsp;";
					}
					$return.="</td>";
				}
				$return.="</tr>";
			}



			// datum-headers
			$return.=$this->datum_headers();

		}

		# regel met korting
		if($this->aanbieding_actief) {
			$return.="<tr class=\"tarieventabel_korting_tr\">";
			$kolomteller=0;
			foreach ($this->dag_van_de_week as $key => $value) {

				$kolomteller++;

				if($kolomteller==1) {
					$return.="<td class=\"tarieventabel_tarieven_kolom_links\">";
				} elseif($kolomteller==count($this->dag_van_de_week)) {
					$return.="<td class=\"tarieventabel_tarieven_kolom_rechts\">";
				} else {
					$return.="<td>";
				}

				if($this->toonkorting_1[$key] or $this->toonkorting_2[$key]) {
					$return.="<div class=\"tarieventabel_tarieven_aanbieding\">";

					if($this->toonkorting_soort_1[$key] and !$this->toonkorting_soort_2[$key]) {
						$return.=number_format($this->toonkorting_1[$key],0,',','')."%";
					} elseif($this->toonkorting_soort_2[$key] and !$this->toonkorting_soort_1[$key]) {
						$return.=number_format($this->toonkorting_2[$key],0,',','');
					} else {
						$return.=html("ja","tarieventabel");
					}
					$return.="</div></td>";
				} else {
					$return.="&nbsp;</td>";
				}
			}
			$return.="</tr>";
		}

		# daadwerkelijke tarieven tonen
		if($this->arrangement) {

			//
			// arrangement
			//
			// tarieven-cellen tonen
			//

			foreach ($this->aantal_personen as $key => $value) {

				$return.="<tr class=\"tarieventabel_tarieven".($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? " tarieventabel_aantal_personen_verbergen" : "").($_GET["ap"]==$key && !$_GET["d"] ? " tarieventabel_tarieven_gekozen" : "")."\" data-aantalpersonen=\"".$key."\">";
				$kolomteller=0;
				foreach ($this->dag as $key2 => $value2) {
					$kolomteller++;

					$class="";
					if($this->tarief[$key][$key2]>0) {
						$class.=" tarieventabel_tarieven_beschikbaar";
					}

					if($kolomteller==1) {
						$class.=" tarieventabel_tarieven_kolom_links";
					} elseif($kolomteller==count($this->dag)) {
						$class.=" tarieventabel_tarieven_kolom_rechts";
					}

					if($_GET["ap"]==$key and $_GET["d"]==$key2) {
						$class.=" tarieventabel_tarieven_gekozen";
					} elseif($_GET["ap"]==$key and !$_GET["d"]) {
						$class.=" tarieventabel_tarieven_gekozen";
					} elseif($_GET["d"]==$key2 and !$_GET["ap"]) {
						$class.=" tarieventabel_tarieven_gekozen";
					}

					$return.="<td class=\"".trim($class)."\" data-week=\"".$key2."\">";

					$return.="<div class=\"tarieventabel_tarieven_div\">";

					if($this->tarief[$key][$key2]>0) {
						if($this->toonkorting_1[$key2] or $this->toonkorting_2[$key2]) {
							$return.="<div class=\"tarieventabel_tarieven_aanbieding\">";
						}

						if($this->tarief[$key][$key2]>=10000) {
							$return.=number_format($this->tarief[$key][$key2],0,",",".");
						} else {
							$return.=number_format($this->tarief[$key][$key2],0,",","");
						}
						$this->tarieven_getoond=true;

						if($this->toonkorting_1[$key2] or $this->toonkorting_2[$key2]) {
							$return.="</div>";
						}
					} else {
						$return.="<div class=\"tarieventabel_tarieven_niet_beschikbaar\">";
						$return.="&nbsp;";
						$return.="</div>";

						$this->tarieventabel_tarieven_niet_beschikbaar=true;
					}

					$return.="</div></td>";
				}
				$return.="</tr>";
			}


		} else {

			//
			// losse accommodatie
			//
			// tarieven-cellen tonen
			//

			$return.="<tr class=\"tarieventabel_tarieven\">";

			foreach ($this->dag as $key => $value) {
				$kolomteller++;

				$class="";
				if($this->tarief[$key]>0) {
					$class.=" tarieventabel_tarieven_beschikbaar";
				}

				if($kolomteller==1) {
					$class.=" tarieventabel_tarieven_kolom_links";
				} elseif($kolomteller==count($this->dag)) {
					$class.=" tarieventabel_tarieven_kolom_rechts";
				}

				if($_GET["d"]==$key) {
					$class.=" tarieventabel_tarieven_gekozen";
				}

				$return.="<td class=\"".trim($class)."\" data-week=\"".$key."\">";

				$return.="<div class=\"tarieventabel_tarieven_div\">";

				if($this->tarief[$key]>0) {
					if($this->toonkorting_1[$key] or $this->toonkorting_2[$key]) {
						$return.="<div class=\"tarieventabel_tarieven_aanbieding\">";
					}

					if($this->tarief[$key]>=10000) {
						$return.=number_format($this->tarief[$key],0,",",".");
					} else {
						$return.=number_format($this->tarief[$key],0,",","");
					}
					$this->tarieven_getoond=true;

					if($this->toonkorting_1[$key] or $this->toonkorting_2[$key]) {
						$return.="</div>";
					}
				} else {
					$return.="<div class=\"tarieventabel_tarieven_niet_beschikbaar\">";
					$return.="&nbsp;";
					$return.="</div>";

					$this->tarieventabel_tarieven_niet_beschikbaar=true;
				}

				$return.="</div></td>";
			}
			$return.="</tr>";
		}

		$return.="</table></div>";

		return $return;

	}

	private function tarieven_uit_database() {

		global $vars, $accinfo;

		$db = new DB_sql;
		$db2 = new DB_sql;


		# Accinfo
		if($accinfo) {
			$this->accinfo=$accinfo;
		} else {
			$this->accinfo=accinfo($this->type_id);
		}

		# seizoeninfo
		$db->query("SELECT naam".$vars["ttv"]." AS naam FROM seizoen WHERE seizoen_id='".intval($this->seizoen_id)."';");
		if($db->next_record()) {
			$this->seizoen["naam"]=$db->f("naam");
		}

		if($this->accinfo["toonper"]==3 or $vars["wederverkoop"]) {

		} else {
			$this->arrangement=true;
		}

		# Controle op vertrekdagaanpassing?
		include($vars["unixdir"]."content/vertrekdagaanpassing.html");

		// aflopen_allotment uit calculatiesjabloon halen
		$db2->query("SELECT week, aflopen_allotment FROM calculatiesjabloon_week WHERE seizoen_id='".intval($this->seizoen_id)."' AND leverancier_id='".intval($this->accinfo["leverancierid"])."';");
		while($db2->next_record()) {
			if($db2->f("aflopen_allotment")<>"") $this->aflopen_allotment[$db2->f("week")]=$db2->f("aflopen_allotment");
		}


		// tarieven uit database halen
		if($this->arrangement) {

			//
			// arrangement
			//
			// gegevens uit database halen
			//

			$db->query("SELECT t.bruto, t.arrangementsprijs, t.beschikbaar, t.blokkeren_wederverkoop, tp.week, tp.personen, tp.prijs, t.voorraad_garantie, t.voorraad_allotment, t.voorraad_vervallen_allotment, t.voorraad_optie_leverancier, t.voorraad_xml, t.voorraad_request, t.voorraad_optie_klant, t.voorraad_bijwerken, t.aanbiedingskleur, t.aanbiedingskleur_korting, t.aflopen_allotment, t.toonexactekorting, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.aanbieding_skipas_percentage, t.aanbieding_skipas_euro FROM tarief_personen tp, tarief t WHERE tp.seizoen_id='".addslashes($this->seizoen_id)."' AND tp.type_id='".addslashes($this->type_id)."' AND t.type_id=tp.type_id AND t.seizoen_id=tp.seizoen_id AND t.week=tp.week AND tp.week>UNIX_TIMESTAMP(NOW()) ORDER BY tp.week, tp.personen DESC;");
			if($db->num_rows()) {
				// $GLOBALS["tarieven_ingevoerd"][$this->seizoen_id]=true;
				$this->tarieven_ingevoerd=true;
			}

			while($db->next_record()) {

				if($this->toon_interne_informatie) {
					$this->voorraad["garantie"][$db->f("week")]=$db->f("voorraad_garantie");
					$this->voorraad["allotment"][$db->f("week")]=$db->f("voorraad_allotment");
					$this->voorraad["vervallen_allotment"][$db->f("week")]=$db->f("voorraad_vervallen_allotment");
					if($this->accinfo["aflopen_allotment"]<>"" or $aflopen_allotment[$db->f("week")]<>"" or $db->f("aflopen_allotment")<>"") {
						if($db->f("aflopen_allotment")<>"") {
							$temp_aflopen_allotment=$db->f("aflopen_allotment");
						} elseif($aflopen_allotment[$db->f("week")]<>"") {
							$temp_aflopen_allotment=$aflopen_allotment[$db->f("week")];
						} else {
							$temp_aflopen_allotment=$this->accinfo["aflopen_allotment"];
						}
						$this->voorraad["aflopen_allotment"][$db->f("week")]=mktime(0,0,0,date("m",$db->f("week")),date("d",$db->f("week"))-$temp_aflopen_allotment,date("Y",$db->f("week")));
					}
					$this->voorraad["optie_leverancier"][$db->f("week")]=$db->f("voorraad_optie_leverancier");
					$this->voorraad["xml"][$db->f("week")]=$db->f("voorraad_xml");
					$this->voorraad["request"][$db->f("week")]=$db->f("voorraad_request");
					$this->voorraad["optie_klant"][$db->f("week")]=$db->f("voorraad_optie_klant");
					$this->voorraad["totaal"][$db->f("week")]=$db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")+$db->f("voorraad_request")-$db->f("voorraad_optie_klant");
					$this->voorraad["voorraad_bijwerken"][$db->f("week")]=$db->f("voorraad_bijwerken");
					$this->voorraad["blokkeren_wederverkoop"][$db->f("week")]=$db->f("blokkeren_wederverkoop");
					// $tarieventabel_tonen[$db->f("week")]=1;
				}

				if($this->toon_interne_informatie and $db->f("prijs")>0 and ($db->f("bruto")>0 or $db->f("arrangementsprijs")>0)) {
					# Korting bepalen om intern te kunnen tonen
					if($db->f("inkoopkorting_percentage")>0) $this->korting["inkoopkorting_percentage"][$db->f("week")]=$db->f("inkoopkorting_percentage");
					if($db->f("inkoopkorting_euro")>0) $this->korting["inkoopkorting_euro"][$db->f("week")]=$db->f("inkoopkorting_euro");
					if($db->f("aanbieding_acc_percentage")>0) $this->korting["aanbieding_acc_percentage"][$db->f("week")]=$db->f("aanbieding_acc_percentage");
					if($db->f("aanbieding_acc_euro")>0) $this->korting["aanbieding_acc_euro"][$db->f("week")]=$db->f("aanbieding_acc_euro");
					if($db->f("aanbieding_skipas_percentage")>0) $this->korting["aanbieding_skipas_percentage"][$db->f("week")]=$db->f("aanbieding_skipas_percentage");
					if($db->f("aanbieding_skipas_euro")>0) $this->korting["aanbieding_skipas_euro"][$db->f("week")]=$db->f("aanbieding_skipas_euro");
				}

				if($db->f("prijs")>0 and $db->f("beschikbaar") and ($db->f("bruto")>0 or $db->f("arrangementsprijs")>0)) {

					if(!$this->begin) $this->begin=$db->f("week");
					$this->eind=$db->f("week");
					$this->tarief[$db->f("personen")][$db->f("week")]=$db->f("prijs");

					$this->aantal_personen[$db->f("personen")]=true;

					// if($db->f("personen")>$max) $max=$db->f("personen");
					// if(!$min) $min=$db->f("personen");
					// if($db->f("personen")<$min) $min=$db->f("personen");

					if($db->f("week")>time()) {

						# Aanbiedingskleur
						if($db->f("aanbiedingskleur")) $aanbiedingskleur[$db->f("week")]=true;

						if($db->f("aanbiedingskleur_korting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0 or $db->f("aanbieding_skipas_percentage")>0 or $db->f("aanbieding_skipas_euro")>0)) {
							$aanbiedingskleur[$db->f("week")]=true;
						}

						if($db->f("toonexactekorting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0)) {
							if($db->f("aanbieding_acc_percentage")>0) {
								// korting in percentage (korting-soort 1)
								$this->toonkorting_1[$db->f("week")]=$db->f("aanbieding_acc_percentage");
								$this->toonkorting_soort_1[$db->f("week")]=true;

								$this->aanbieding_actief=true;

							}
							if($db->f("aanbieding_acc_euro")>0) {
								// korting in euro's (korting-soort 2)
								$this->toonkorting_2[$db->f("week")]=$db->f("aanbieding_acc_euro");
								$this->toonkorting_soort_2[$db->f("week")]=true;

								$this->aanbieding_actief=true;
							}
						}
					}
				}
			}

		} else {

			//
			// losse accommodatie
			//
			// gegevens uit database halen
			//

			if($vars["wederverkoop"]) {
				$db->query("SELECT t.c_bruto, t.bruto, t.beschikbaar, t.blokkeren_wederverkoop, t.wederverkoop_verkoopprijs, t.wederverkoop_commissie_agent, t.week, t.c_verkoop_site, t.voorraad_garantie, t.voorraad_allotment, t.voorraad_vervallen_allotment, t.voorraad_optie_leverancier, t.voorraad_xml, t.voorraad_request, t.voorraad_optie_klant, t.voorraad_bijwerken, t.aanbiedingskleur, t.aanbiedingskleur_korting, t.aflopen_allotment, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.toonexactekorting FROM tarief t WHERE t.seizoen_id='".addslashes($this->seizoen_id)."' AND t.type_id='".addslashes($this->type_id)."';");
				if($db->num_rows()) {
					// $GLOBALS["tarieven_ingevoerd"][$this->seizoen_id]=true;
					$this->tarieven_ingevoerd=true;
				}
			} else {
				$db->query("SELECT t.c_bruto, t.beschikbaar, t.blokkeren_wederverkoop, t.week, t.c_verkoop_site, t.voorraad_garantie, t.voorraad_allotment, t.voorraad_vervallen_allotment, t.voorraad_optie_leverancier, t.voorraad_xml, t.voorraad_request, t.voorraad_optie_klant, t.voorraad_bijwerken, t.aanbiedingskleur, t.aanbiedingskleur_korting, t.aflopen_allotment, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.toonexactekorting FROM tarief t WHERE t.seizoen_id='".addslashes($this->seizoen_id)."' AND t.type_id='".addslashes($this->type_id)."';");
				if($db->num_rows()) {
					// $GLOBALS["tarieven_ingevoerd"][$this->seizoen_id]=true;
					$this->tarieven_ingevoerd=true;
				}
			}
			while($db->next_record()) {
				# Voorraad bepalen t.b.v. Chalet-medewerkers
				if($this->toon_interne_informatie) {
					$this->voorraad["garantie"][$db->f("week")]=$db->f("voorraad_garantie");
					$this->voorraad["allotment"][$db->f("week")]=$db->f("voorraad_allotment");
					$this->voorraad["vervallen_allotment"][$db->f("week")]=$db->f("voorraad_vervallen_allotment");
					if($this->accinfo["aflopen_allotment"]<>"" or $aflopen_allotment[$db->f("week")]<>"" or $db->f("aflopen_allotment")<>"") {
						if($db->f("aflopen_allotment")<>"") {
							$temp_aflopen_allotment=$db->f("aflopen_allotment");
						} elseif($aflopen_allotment[$db->f("week")]<>"") {
							$temp_aflopen_allotment=$aflopen_allotment[$db->f("week")];
						} else {
							$temp_aflopen_allotment=$this->accinfo["aflopen_allotment"];
						}
						$this->voorraad["aflopen_allotment"][$db->f("week")]=mktime(0,0,0,date("m",$db->f("week")),date("d",$db->f("week"))-$temp_aflopen_allotment,date("Y",$db->f("week")));
					}
					$this->voorraad["optie_leverancier"][$db->f("week")]=$db->f("voorraad_optie_leverancier");
					$this->voorraad["xml"][$db->f("week")]=$db->f("voorraad_xml");
					$this->voorraad["request"][$db->f("week")]=$db->f("voorraad_request");
					$this->voorraad["optie_klant"][$db->f("week")]=$db->f("voorraad_optie_klant");
					$this->voorraad["totaal"][$db->f("week")]=$db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")+$db->f("voorraad_request")-$db->f("voorraad_optie_klant");
					$this->voorraad["voorraad_bijwerken"][$db->f("week")]=$db->f("voorraad_bijwerken");
					$this->voorraad["blokkeren_wederverkoop"][$db->f("week")]=$db->f("blokkeren_wederverkoop");
					// $tarieventabel_tonen[$db->f("week")]=1;
				}

				unset($temp_beschikbaar,$temp_bruto);
				if($vars["wederverkoop"]) {
					$temp_beschikbaar=$db->f("beschikbaar");
					if($db->f("blokkeren_wederverkoop")) unset($temp_beschikbaar);
					if($this->accinfo["toonper"]==3) {
						$temp_bruto=$db->f("c_bruto");
					} else {
						$temp_bruto=$db->f("bruto");
					}
					$temp_verkoop_site=$db->f("wederverkoop_verkoopprijs");
				} else {
					$temp_beschikbaar=$db->f("beschikbaar");
					$temp_bruto=$db->f("c_bruto");
					$temp_verkoop_site=$db->f("c_verkoop_site");
				}

				if($temp_verkoop_site>0 and $temp_bruto>0) {
					# Korting bepalen om intern te kunnen tonen
					if($db->f("inkoopkorting_percentage")<>0) $korting["inkoopkorting_percentage"][$db->f("week")]=$db->f("inkoopkorting_percentage");
					if($db->f("inkoopkorting_euro")<>0) $korting["inkoopkorting_euro"][$db->f("week")]=$db->f("inkoopkorting_euro");
					if($db->f("aanbieding_acc_percentage")<>0) $korting["aanbieding_acc_percentage"][$db->f("week")]=$db->f("aanbieding_acc_percentage");
					if($db->f("aanbieding_acc_euro")<>0) $korting["aanbieding_acc_euro"][$db->f("week")]=$db->f("aanbieding_acc_euro");
				}

				if($temp_verkoop_site>0 and $temp_beschikbaar and $temp_bruto>0) {

					$this->tarief[$db->f("week")]=$temp_verkoop_site;

					if(!$this->begin) $this->begin=$db->f("week");
					$this->eind=$db->f("week");

					if($this->tarief[$db->f("week")]>0) {
						// $tarieventabel_tonen[$db->f("week")]=1;
						$commissie[$db->f("week")]=$db->f("wederverkoop_commissie_agent");
						if($vars["chalettour_aanpassing_commissie"]) {
							$commissie[$db->f("week")]=$commissie[$db->f("week")]+$vars["chalettour_aanpassing_commissie"];
						}
					}
					# Voorraad bepalen t.b.v. ingelogde reisbureaus
					# 1 = groen, 2 = oranje, 3 = zwart
					if($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")-$db->f("voorraad_optie_klant")>=1) {
						$this->voorraad["wederverkoop_kleur"][$db->f("week")]=1;
					} elseif($db->f("voorraad_request")>=1 or $db->f("voorraad_optie_klant")>=1 or $db->f("voorraad_vervallen_allotment")>=1) {
						$this->voorraad["wederverkoop_kleur"][$db->f("week")]=2;
					} else {
						$this->voorraad["wederverkoop_kleur"][$db->f("week")]=3;
					}

					if($db->f("week")>time()) {

						# Aanbiedingskleur
						if($db->f("aanbiedingskleur")) $aanbiedingskleur[$db->f("week")]=true;

						if($db->f("aanbiedingskleur_korting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0)) {
							$aanbiedingskleur[$db->f("week")]=true;
						}

						if($db->f("toonexactekorting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0)) {
							if($db->f("aanbieding_acc_percentage")>0) {
								$this->toonkorting_1[$db->f("week")]=$db->f("aanbieding_acc_percentage");
								$this->toonkorting_soort_1[$db->f("week")]=true;

								$this->aanbieding_actief=true;

							}
							if($db->f("aanbieding_acc_euro")>0) {
								$this->toonkorting_2[$db->f("week")]=$db->f("aanbieding_acc_euro");
								$this->toonkorting_soort_2[$db->f("week")]=true;

								$this->aanbieding_actief=true;

							}
						}
					}
				} else {
					$this->voorraad["wederverkoop_kleur"][$db->f("week")]=3;
				}
			}
		}


		// Aantal keer geboekt uit database halen
		$db->query("SELECT aankomstdatum, aankomstdatum_exact, vertrekdatum_exact FROM boeking WHERE goedgekeurd=1 AND geannuleerd=0 AND type_id='".intval($this->type_id)."' AND seizoen_id='".intval($this->seizoen_id)."';");
		while($db->next_record()) {
			$this->voorraad["aantal_geboekt"][$db->f("aankomstdatum")]++;

			// Kijken of de boeking langer dan 1 week is (en dan ook de volgende week/weken ophogen met 1)
			$aantalwekengeboekt=round(($db->f("vertrekdatum_exact")-$db->f("aankomstdatum_exact"))/86400)/7;
			if($aantalwekengeboekt>1) {
				for($i=2;$i<=$aantalwekengeboekt;$i++) {
					$aantalplusdagen=($i-1)*7;
					$this->voorraad["aantal_geboekt"][mktime(0,0,0,date("m",$db->f("aankomstdatum")),date("d",$db->f("aankomstdatum"))+$aantalplusdagen,date("Y",$db->f("aankomstdatum")))]++;
				}
			}
		}


		// maanden begin + eind bepalen
		for($i=1;$i<=5;$i++) {
			$week_eerder=mktime(0,0,0,date("m",$this->begin),date("d",$this->begin)-7,date("Y",$this->begin));
			if(date("m",$week_eerder)==date("m",$this->begin)) {
				$this->begin=$week_eerder;
			}
			$week_later=mktime(0,0,0,date("m",$this->eind),date("d",$this->eind)+7,date("Y",$this->eind));
			// echo $i." ".date("d",$this->eind)+(7*$i)." ".(7*$i)." ".date("r",$week_later)." ".date("r",$this->eind)."<br/>";
			if(date("m",$week_later)==date("m",$this->eind)) {
				$this->eind=$week_later;
			}
		}

		// aantal weken in een maand bepalen
		$week=$this->begin;
		while($week<=$this->eind) {

			if($vertrekdag[$this->seizoen_id][date("dm",$week)] or $this->accinfo["aankomst_plusmin"]) {
				$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$this->seizoen_id][date("dm",$week)]+$this->accinfo["aankomst_plusmin"],date("Y",$week));
			} else {
				$aangepaste_unixtime=$week;
				$exacte_unixtime=$week;
			}

			$this->dag[$week]=date("d",$aangepaste_unixtime);
			$this->dag_van_de_week[$week]=strftime("%a",$aangepaste_unixtime);
			if(date("w",$aangepaste_unixtime)<>6) {
				$this->dag_van_de_week_afwijkend[$week]=true;
			}
			$this->maand[date("Y-m",$aangepaste_unixtime)]++;


			$exacte_unixtime=$aangepaste_unixtime;

			$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}

		# Aantal nachten bepalen
		$week=$this->begin;
		$eind=mktime(0,0,0,date("m",$this->eind),date("d",$this->eind)+7,date("Y",$this->eind));
		while($week<=$eind) {
			# Afwijkende vertrekdag
			$aantalnachten_afwijking[date("dm",$week)]+=$vertrekdag[$this->seizoen_id][date("dm",$week)];
			$vorigeweek=mktime(0,0,0,date("n",$week),date("j",$week)-7,date("Y",$week));
			$aantalnachten_afwijking[date("dm",$vorigeweek)]-=$vertrekdag[$this->seizoen_id][date("dm",$week)];

			# Afwijkende verblijfsduur
			$aantalnachten_afwijking[date("dm",$week)]=$aantalnachten_afwijking[date("dm",$week)]+$this->accinfo["aankomst_plusmin"]-$this->accinfo["vertrek_plusmin"];

			if($aantalnachten_afwijking[date("dm",$week)]) {
				$this->aantalnachten[$week]=$aantalnachten_afwijking[date("dm",$week)];
			} else {
				$this->aantalnachten[$week]=7;
			}

			$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}

		return $return;
	}

	private function tabel_bottom() {
		$return .= "</td></tr></table>";
		return $return;
	}


	public function css() {

			?>

			<style>

			.tarieventabel_wrapper {
				width: 660px;
				position: relative;
				z-index: 1;
				border-right: 2px solid #d5e1f9;
				border-left: 2px solid #d5e1f9;
				border-bottom: 2px solid #d5e1f9;
			}

			.tarieventabel_top {
				padding: 5px;
				background-color: #d5e1f9;
				color: #003366;
				height: 38px;
				overflow: hidden;
			}

			.tarieventabel_top h1 {
				color: #003366;
				font-size: 13.3px;
				font-weight: bold;
			}

			.tarieventabel_border {
				border: 1px solid #d5e1f9;
			}

			.tarieventabel_border td {
				border: 1px solid #d5e1f9;
			}

			.tarieventabel_titels_links {
				float: left;
				border-top-width: 0px;
				border-left-width: 0px;
				border-right: 2px solid #d5e1f9;
			}

			.tarieventabel_titels_links td {
				border-right-width: 0px;
				border-left-width: 0px;
			}

			.tarieventabel_wrapper_rechts {
				overflow-x: scroll;
			}

			.tarieventabel_wrapper_rechts::-webkit-scrollbar {
				-webkit-appearance: none;
			}

			.tarieventabel_wrapper_rechts::-webkit-scrollbar:horizontal {
				height: 13px;
			}

			.tarieventabel_wrapper_rechts::-webkit-scrollbar-thumb {
				border-radius: 8px;
				border: 2px solid white;
				background-color: rgba(0, 0, 0, .5);
			}

			.tarieventabel_content {
				float: left;
				border-top-width: 0px;
				border-left-width: 0px;
				border-right-width: 0px;
			}

			.tarieventabel_content td {
				font-size: 12.283px;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				padding: 5px;
			}

			.tarieventabel_titels_links td {
				font-size: 12.283px;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				padding: 5px;
			}

			.tarieventabel_maanden_leeg {
				width: 150px !important;
			}

			.tarieventabel_maanden td {
				text-align: center;
				font-size: 14px;
				white-space: nowrap;
				border-top-width: 0px;
				height: 19px !important;
			}

			.tarieventabel_datumbalk {
				background-color: #e6f0fc;
			}

			.tarieventabel_datumbalk td {
				font-size: 11px;
				border-width: 0px;
				height: 15px !important;
				overflow: hidden;
			}

			.tarieventabel_korting_tr td {
				text-align: center;
			}

			.tarieventabel_datumbalk_content {
				text-align: center;
			}

			.tarieventabel_datumbalk_minder_opvallend td {
				color: #777777;
			}

			.tarieventabel_datumbalk_opvallend {
				color: #000000 !important;
				font-weight: bold;
			}

			.tarieventabel_tarieven_aanbieding {
				background-color: #ffa258;
				border-radius: 4px;
			}

			.tarieventabel_tarieven_niet_beschikbaar {
				background-color: #c0c0c0;
				border-radius: 4px;
				width: 40px;
				margin-left: auto;
				margin-right: auto;
			}

			.tarieventabel_tarieven_gekozen {
				background-color: #a8f4f0;
			}

			.tarieventabel_legenda_kleurenblokje {
				border-radius: 4px;
			}

			.tarieventabel_tarieven td {
				text-align: center;
				width: 60px;
			}

			.tarieventabel_tarieven td.tarieventabel_tarieven_beschikbaar {
				cursor: pointer;
			}

			.tarieventabel_tarieven td.tarieventabel_tarieven_beschikbaar:hover {
				background-color: #ffe933;
			}

			.tarieventabel_tarieven td:hover .tarieventabel_tarieven_aanbieding {
				background-color: #ffe933;
			}

			.tarieventabel_tarieven_div {
				width: 55px;
			}

			.tarieventabel_pijl {
				font-size: 12px;
				position: absolute;
				top: 77px;
				height: 15px;
				padding-top: 31px;
				padding-bottom: 31px;
				background-color: #5e7e9e;
				cursor: pointer;
				color: #ffffff;
				text-align: center;
				width: 8px;
				z-index: 10;
				overflow: hidden;
			}

			.tarieventabel_pijl:hover {
				background-color: #003366;
			}

			.tarieventabel_pijl_rechts {
				right: -2px;
			}

			.tarieventabel_pijl_links {
				left: 160px;
			}

			.tarieventabel_pijl_scrollstop, .tarieventabel_pijl_scrollstop:hover {
				background-color: red;
			}

			.tarieventabel_tarieven_kolom_links {
				border-left-width: 0px !important;
			}

			.tarieventabel_tarieven_kolom_rechts {
				border-right-width: 0px !important;
			}

			.tarieventabel_aantal_personen_verbergen {
				display: none;
			}

			.tarieventabel_toggle_personen {
				margin-left: 3px;
				margin-top: -12px;
				margin-bottom: 10px;
				width: 145px;
				font-size:10px;
			}

			.tarieventabel_toggle_personen a {
				text-decoration: none;
			}

			.tarieventabel_toggle_personen span {
				text-decoration: underline; !important;
			}

			.tarieventabel_legenda {
				margin-left: 162px;
			}

			.tarieventabel_legenda div {
				margin-bottom: 5px;
			}

			.tarieventabel_legenda span {
				font-size: 10px;
			}

			.tarieventabel_voorraad_content_tr {
				text-align: center;
			}

			.tarieventabel_voorraad_tr td {
				font-size: 11px !important;
			}

			.tarieventabel_voorraad_garantie {
				background-color: #00ff00;
			}

			.tarieventabel_voorraad_allotment {
				background-color: #ccffcc;
			}

			.tarieventabel_voorraad_aflopen_allotment {
				background-color: #ccffcc;
			}

			.tarieventabel_voorraad_vervallen_allotment {
				background-color: #f88912;
			}

			.tarieventabel_voorraad_optie_leverancier {
				background-color: #ccffcc;
			}

			.tarieventabel_voorraad_xml {
				background-color: #ff99cc;
			}

			.tarieventabel_voorraad_request {
				background-color: #ffcc99;
			}

			.tarieventabel_voorraad_optie_klant {
				background-color: #ccffff;
			}

			.tarieventabel_voorraad_totaal {
				background-color: #ffff99;
			}

			.tarieventabel_voorraad_aantal_geboekt {
				background-color: #ebebeb;
			}

			</style>

			<?php

	}

}



?>