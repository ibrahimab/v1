<?php


/**
* Tarieventabel
*/

class tarieventabel {

	public $voorraad;
	public $commissie;

	public $toon_interne_informatie;
	public $toon_beschikbaarheid;
	public $toon_commissie;

	private $actieve_kolom;

	public $meerdere_valuta;

	function __construct() {

		$this->toon_interne_informatie = false;
		$this->toon_beschikbaarheid = false;
		$this->toon_commissie = false;
		$this->meerdere_valuta = false;
		// $this->show_afwijkend_legenda = true;
		$this->show_afwijkend_legenda = false;

		$this->get_aantal_personen = $_GET["ap"];


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
	}

	public function toontabel() {

		global $vars, $isMobile;

		$db = new DB_sql;

		$this->tarieven_uit_database();


		// link to new season
		if($vars["seizoentype"]==1) {
			if($this->seizoen_counter>1) {
				$return .= "<div class=\"tarieventabel_nextseason\"><a href=\"#\" class=\"tarieventabel_jump_jaarmaand\" data-jaarmaand=\"".date("Ym", $this->seizoeninfo[$this->last_seizoen_id]["begin"])."\">".html("nualteboeken", "tarieventabel", array("v_seizoennaam"=>$this->seizoeninfo[$this->last_seizoen_id]["naam"]))." &raquo;</a></div>";
			} else {
				// check for seasons without prices
				$db->query("SELECT seizoen_id, naam FROM seizoen WHERE show_newpricesmail=1 AND type='".intval($vars["seizoentype"])."' AND seizoen_id NOT IN (".$this->seizoen_id.") ORDER BY eind DESC LIMIT 0,1;");
				if($db->next_record()) {
					$seizoennaam_kort = trim(preg_replace("@winter@","",$db->f("naam")));

					// Laat je emailadres achter en je ontvangt een bericht zodra deze accommodatie te boeken is voor

					$this->mailmijvolgendseizoen_form .= "<div class=\"tarieventabel_newpricesmail\"><a href=\"#\">".html("mailmijvolgendseizoen_button", "tarieventabel", array("v_seizoennaam"=>$seizoennaam_kort))." &raquo;</a></div>";
					$this->mailmijvolgendseizoen_form .= "<div class=\"tarieventabel_newpricesmail_form\" data-seizoen_id=\"".intval($db->f("seizoen_id"))."\" data-type_id=\"".intval($this->type_id)."\" data-seizoen_name=\"".wt_he($db->f("naam"))."\">";
					$this->mailmijvolgendseizoen_form .= "<h1>".html("mailmijvolgendseizoen_button", "tarieventabel", array("v_seizoennaam"=>$seizoennaam_kort))."</h1>";
					$this->mailmijvolgendseizoen_form .= "<p>".html("mailmijvolgendseizoen_inleiding", "tarieventabel", array("v_seizoennaam"=>$db->f("naam")))."</p>";
					$this->mailmijvolgendseizoen_form .= "<form method=\"post\">";
					$this->mailmijvolgendseizoen_form .= "<label>".html("mailmijvolgendseizoen_email", "tarieventabel").":</label>";
					$this->mailmijvolgendseizoen_form .= "<input type=\"email\">";
					$this->mailmijvolgendseizoen_form .= "&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"".html("mailmijvolgendseizoen_send", "tarieventabel")."\">";
					$this->mailmijvolgendseizoen_form .= "<img src=\"".$vars["path"]."pic/icon_okay.png\" class=\"okay\"><img src=\"".$vars["path"]."pic/icon_notokay.png\" class=\"notokay\">";
					$this->mailmijvolgendseizoen_form .= "</form>";
					$this->mailmijvolgendseizoen_form .= "</div>"; // close

					$return .= $this->mailmijvolgendseizoen_form;

				}
			}
		}

		if($vars["websitetype"]<>4) {
			//
			// tekst "hulp bij online boeken" tonen
			//

			$kantoor_open = new kantoor_open;

			if($kantoor_open->is_het_kantoor_geopend()) {
				$return .= "<div class=\"tarieventabel_hulp_bij_online_boeken\">";
				if($isMobile) {
					if($vars["livechat_code"]) {
						$return .= html("hulpbijonlineboeken","tarieventabel",array("h_1"=>"<a href=\"tel:".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."\"><b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b></a>","h_2"=>"<a onclick=\"LC_API.open_chat_window();return false;\" href=\"/\">","h_3"=>"</a>", "h_4"=>"<a href=\"".$vars["path"]."veelgestelde-vragen\">", "h_5"=>"</a>"));
					} else {
						$return .= html("hulpbijonlineboeken_zonderchat","tarieventabel",array("h_1"=>"<a href=\"tel:".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."\"><b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b></a>", "h_4"=>"<a href=\"".$vars["path"]."veelgestelde-vragen\">", "h_5"=>"</a>"));
					}
				} else {
					if($vars["livechat_code"]) {
						$return .= html("hulpbijonlineboeken","tarieventabel",array("h_1"=>"<b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b>","h_2"=>"<span class=\"trigger_livechat_button\">","h_3"=>"</span>", "h_4"=>"<a href=\"".$vars["path"]."veelgestelde-vragen\">", "h_5"=>"</a>"));
					} else {
						$return .= html("hulpbijonlineboeken_zonderchat","tarieventabel",array("h_1"=>"<b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b>", "h_4"=>"<a href=\"".$vars["path"]."veelgestelde-vragen\">", "h_5"=>"</a>"));
					}
				}
				$return .= "</div>"; # afsluiten .tarieventabel_hulp_bij_online_boeken
			}
		}

		if($vars["seizoentype"]==1 and !$_GET["d"]) {
			// winter: always scroll to december
			$this->scroll_first_monthyear = date("Ym", $this->seizoeninfo[$this->first_seizoen_id]["begin"]);
		}

		$return .= "<div class=\"tarieventabel_wrapper\" data-boek-url=\"".wt_he($vars["path"].txt("menu_boeken").".php?tid=".$this->type_id."&o=".urlencode($_GET["o"]).(!$this->arrangement && $this->get_aantal_personen ? "&ap=".intval($this->get_aantal_personen) : ""))."\" data-actieve-kolom=\"".intval($this->actieve_kolom)."\" data-scroll_first_monthyear=\"".wt_he($this->scroll_first_monthyear)."\" data-type_id=\"".intval($this->type_id)."\" data-seizoen_id_inquery=\"".wt_he($this->seizoen_id)."\" data-toon_bijkomendekosten=\"".($this->toon_bijkomendekosten ? "1" : "0")."\">";


		$return .= $this->tabel_top();
		$return .= $this->tabel_content();
		$return .= $this->tabel_bottom();

		if($this->toon_bijkomendekosten and $this->first_seizoen_id) {

			$bijkomendekosten = new bijkomendekosten($this->type_id, "type");
			$bijkomendekosten->seizoen_id = $this->first_seizoen_id;
			$bijkomendekosten->arrangement = $this->arrangement;
			$bijkomendekosten->accinfo = $this->accinfo;

			$toelichting = $bijkomendekosten->toon_type();
			// $toelichting = $bijkomendekosten->toon_type_temporary();

		} else {

			if(($vars["lokale_testserver"] or $vars["acceptatie_testserver"] or $vars["taal"]=="de") and $vars["seizoentype"]==1) {

				if(!$this->first_seizoen_id) {
					trigger_error("missing first_season_id",E_USER_NOTICE);
				}

				$bijkomendekosten = new bijkomendekosten($this->type_id, "type");
				$bijkomendekosten->seizoen_id = $this->first_seizoen_id;
				$bijkomendekosten->arrangement = $this->arrangement;
				$bijkomendekosten->accinfo = $this->accinfo;

				$toelichting = $bijkomendekosten->toon_type_temporary();

			} else {
				$toelichting = $this->toelichting();
			}
		}

		if($toelichting) {
			$return .= "<div class=\"tarieventabel_toelichting\">";


			// info totaalprijs
			if($this->toon_bijkomendekosten) {
				$return .= "<div class=\"tarieventabel_totaalprijs_wrapper\">";
				if($_GET["ap"] and $_GET["d"]) {
					$return .= $this->info_totaalprijs($_GET["ap"], $_GET["d"]);
				}
				$return .= "</div>";
			}

			$return .= $toelichting;

			$return .= "</div>"; # afsluiten .tarieventabel_toelichting
		}

		$return .= "</div>"; # afsluiten .tarieventabel_wrapper

		return $return;

	}

	public function info_totaalprijs($aantalpersonen, $aankomstdatum) {

		global $vars;

		$this->tarieven_uit_database();

		$return .= "<div class=\"tarieventabel_totaalprijs\">";

		$return .= "<div class=\"tarieventabel_totaalprijs_left\">".html("geselecteerde-aankomstdatum", "tarieventabel").":</div>";
		$return .= "<span class=\"tarieventabel_totaalprijs_right datum\">".datum("DAG D MAAND JJJJ", $this->unixtime_week[$aankomstdatum], $vars["taal"])."</span>";

		$return .= "<div class=\"tarieventabel_totaalprijs_left\">Totaalprijs op basis van ";
		$return .= " ".$aantalpersonen." ".($aantalpersonen==1 ? html("persoon","tarieventabel") : html("personen","tarieventabel")).":</div>";
		$return .= "<span class=\"tarieventabel_totaalprijs_right\">&euro;&nbsp;".number_format($aantalpersonen*$this->tarief_exact[$aantalpersonen][$aankomstdatum], 2, ",", ".")."</span>";
		$return .= "<button data-aantalpersonen=\"".$aantalpersonen."\" data-week=\"".$aankomstdatum."\">".html("boeknu", "toonaccommodatie")." &raquo;</button>";
		$return .= "<div class=\"tarieventabel_totaalprijs_klik\">".html("klik-op-datum-personen", "tarieventabel")."</div>";

		$return .= "</div>"; // close .tarieventabel_totaalprijs

		return $return;

	}

	private function tabel_top() {

		global $vars;

		$return .= "<div class=\"tarieventabel_top\">";
		$return .= "<div class=\"tarieventabel_top_left\">";
		$return .= "<h1>".html("tarieven","tarieventabel")."</h1>";

		if($this->meerdere_valuta) {
			$return .= "<span class=\"tarieventabel_top_valutanaam\" data-euro=\"".html("ineuros","tarieventabel")."\" data-gbp=\"".html("inponden","tarieventabel")."\">";
			if($this->actieve_valuta=="gbp") {
				$return .= html("inponden","tarieventabel");
			} else {
				$return .= html("ineuros","tarieventabel");
			}
			$return .= "</span>";
		} else {
			$return .= html("ineuros","tarieventabel");
		}
		if($this->toon_accommodatie_per_persoon) {
			$return .= ", ".html("perpersoon","tarieventabel");
		} elseif($this->arrangement) {
			$return .= ", ".html("perpersooninclskipas","tarieventabel");
		} else {
			$return .= ", ".html("peraccommodatie","tarieventabel");
		}

		$return .= "</div>"; // afsluiten .tarieventabel_top_left
		$return .= "<div class=\"tarieventabel_top_right\">";

		if($this->toon_interne_informatie) {
			//
			// link naar cms
			//
			$return.="<div class=\"tarieventabel_top_interne_link\">Wederverkoop:&nbsp;";
			if($this->accinfo["wederverkoop"]) $return.="ja"; else $return.="nee";
			$return.="&nbsp;&nbsp;&nbsp;<a href=\"";
			if($vars["website"]<>"C" and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") {
				$return.="https://www.chalet.nl";
			}
			if($vars["lokale_testserver"]) {
				$return.="/chalet";
			}

			if(preg_match("@,@",$this->seizoen_id,$regs)) {
				$seizoenid_array=explode(",",$this->seizoen_id);
				$seizoenid=max($seizoenid_array);
			} else {
				$seizoenid=$this->seizoen_id;
			}

			$return.=ereg_replace("[a-z]+/$","",$vars["path"])."cms_tarieven.php?sid=".$seizoenid."&tid=".$this->type_id."&from=".urlencode("http".($_SERVER["HTTPS"]=="on" ? "s" : "")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."#prijsinformatie")."\" title=\"tarieven bewerken\">";
			$return.="<img src=\"".$vars["path"]."pic/class.cms_edit.gif\" border=\"0\" alt=\"Tarieven bewerken\" width=\"14\" height=\"14\"></a>";
			$return.="</div>";
			$return.="<div class=\"clear\"></div>\n";
		}
		if($this->meerdere_valuta) {
			$return.="<div class=\"tarieventabel_top_valuta\">";

			$return.=html("valuta","tarieventabel").":&nbsp;";

			// $return.="<select class=\"option_valuta_".($this->actieve_valuta=="gbp" ? "gbp" : "euro")."\">";
			// $return.="<option value=\"euro\" class=\"option_valuta_euro\"".($this->actieve_valuta!="gbp" ? " selected" : "").">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".html("euro","tarieventabel")."&nbsp;</option>";
			// $return.="<option value=\"gbp\" class=\"option_valuta_gbp\"".($this->actieve_valuta=="gbp" ? " selected" : "").">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".html("gbp","tarieventabel")."&nbsp;</option>";
			// $return.="</select>";

			$return.="<div class=\"tarieventabel_top_valuta_select\">";
			$return.="<select class=\"currency_select\">";
			$return.="<option value=\"euro\"".($this->actieve_valuta!="gbp" ? " selected" : "")." data-image=\"".$vars["path"]."pic/flag-eu.png\">".html("euro","tarieventabel")."&nbsp;&nbsp;</option>";
			$return.="<option value=\"gbp\"".($this->actieve_valuta=="gbp" ? " selected" : "")." data-image=\"".$vars["path"]."pic/flag-uk.png\">".html("gbp","tarieventabel")."&nbsp;&nbsp;</option>";
			$return.="</select>";
			$return.="</div>"; // afsluiten .tarieventabel_top_valuta_select

			$return.="<div class=\"tarieventabel_top_valuta_toelichting1\" data-euro=\"\" data-gbp=\"".html("valuta_toelichting1_gbp","tarieventabel")."\">";
			if($this->actieve_valuta=="gbp") {
				$return.=html("valuta_toelichting1_gbp","tarieventabel");
			} else {
				// $return.=html("valuta_toelichting1_euro","tarieventabel");
			}
			$return.="</div>"; // afsluiten .tarieventabel_top_valuta_toelichting1

			$return.="<div class=\"tarieventabel_top_valuta_toelichting2\" data-euro=\"\" data-gbp=\"".html("valuta_toelichting2_gbp","tarieventabel")."\">";
			if($this->actieve_valuta=="gbp") {
				$return.=html("valuta_toelichting2_gbp","tarieventabel");
			} else {
				// $return.=html("valuta_toelichting2_euro","tarieventabel");
			}
			$return.="</div>"; // afsluiten .tarieventabel_top_valuta_toelichting2

			$return.="</div>"; // afsluiten .tarieventabel_top_valuta
		}

		$return .= "</div>"; // afsluiten .tarieventabel_top_right

		$return.= "<div class=\"clear\"></div>";

		$return .= "</div>"; // afsluiten .tarieventabel_top

		return $return;

	}

	private function tabel_content() {

		global $vars;

		if($this->tarief) {

			//
			// kolomnamen
			//

			// bepalen welke aantallen personen direct zichtbaar moeten zijn

			if($this->arrangement or $this->toon_accommodatie_per_persoon) {
				$this->max_personen=max(array_keys($this->aantal_personen));
				$this->min_personen=min(array_keys($this->aantal_personen));

				if($this->get_aantal_personen) {
					// valt het aantal personen binnen de capaciteit van deze accommodatie?
					if($this->get_aantal_personen<$this->min_personen or $this->get_aantal_personen>$this->max_personen) {
						// zo niet: aantal personen niet gebruiken bij tonen tarieventabel
						unset($this->get_aantal_personen);
					}
				}

				if($this->get_aantal_personen) {
					$this->max_personen_tonen=$this->get_aantal_personen+2;
					$this->min_personen_tonen=$this->get_aantal_personen-2;
				} else {
					$this->max_personen_tonen=max(array_keys($this->aantal_personen));
					$this->min_personen_tonen=$this->max_personen_tonen-4;
				}
			}


			$return.="<table cellspacing=\"0\" cellpadding=\"0\" class=\"tarieventabel_border tarieventabel_titels_links\">";
			$return.="<tr class=\"tarieventabel_maanden".($this->toon_interne_informatie ? "" : " tarieventabel_maanden_top")."\"><td class=\"tarieventabel_maanden_leeg\">&nbsp;</td></tr>";

#			$return.="</td></tr>";
			$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdatum","tarieventabel")."</td></tr>";
			$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdag","tarieventabel")."</td></tr>";
			$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aantalnachten","tarieventabel")."</td></tr>";

			// interne informatie
			if($this->toon_interne_informatie) {

				// voorraad - kolomnamen
				foreach ($this->voorraad_doorlopen as $key => $value) {
					$return.="<tr class=\"tarieventabel_voorraad_tr tarieventabel_voorraad_".$key."\"><td>".wt_he($value)."</td></tr>";
				}

				// korting - kolomnamen
				if($this->korting) {

					$return.="<tr class=\"tarieventabel_tr_leeg tarieventabel_voorraad_tr\"><td>&nbsp;</td></tr>";

					if($this->arrangement) {
						$this->korting_doorlopen=array(
							"inkoopkorting_percentage" => "Inkoopkorting acc %",
							"aanbieding_acc_percentage" => "Korting acc %",
							"aanbieding_skipas_percentage" => "Korting skipas %",
							"inkoopkorting_euro" => "Inkoopkorting acc €",
							"aanbieding_acc_euro" => "Korting acc €",
							"aanbieding_skipas_euro" => "Korting skipas €"
						);
					} else {
						$this->korting_doorlopen=array(
							"inkoopkorting_percentage" => "Inkoopkorting %",
							"aanbieding_acc_percentage" => "Korting %",
							"inkoopkorting_euro" => "Inkoopkorting €",
							"aanbieding_acc_euro" => "Korting €"
						);
					}

					foreach ($this->korting_doorlopen as $key => $value) {
						$return.="<tr class=\"tarieventabel_voorraad_tr tarieventabel_voorraad_korting\"><td>".wt_he($value)."</td></tr>";
					}
				}



				$return.="<tr class=\"tarieventabel_maanden tarieventabel_maanden_top\"><td class=\"tarieventabel_maanden_leeg\">&nbsp;</td></tr>";

				$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdatum","tarieventabel")."</td></tr>";
				$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aankomstdag","tarieventabel")."</td></tr>";
				$return.="<tr class=\"tarieventabel_datumbalk\"><td>".html("aantalnachten","tarieventabel")."</td></tr>";
			}


			// beschikbaarheid voor reisagenten
			if($this->toon_beschikbaarheid) {
				$return.="<tr><td>";
				$return.=html("wederverkoop_beschikbaarheid","tarieventabel");
				$return.="</td></tr>";
			}


			// korting
			if($this->aanbieding_actief and ($this->toonkorting_soort_1 or $this->toonkorting_soort_2)) {
				$return.="<tr><td>";
				if($this->accinfo["toonper"]==1 or $this->toon_accommodatie_per_persoon) {
					$return.=html("kortingaccommodatie","tarieventabel");
				} else {
					$return.=html("korting","tarieventabel");
				}
				$return.="</td></tr>";
			}


			if($this->arrangement) {

				// regels met aantal personen tonen
				foreach ($this->aantal_personen as $key => $value) {
					$return.="<tr class=\"".trim(($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? "tarieventabel_verbergen" : "").($this->get_aantal_personen==$key && !$_GET["d"] ? " tarieventabel_tarieven_gekozen" : ""))."\"><td>".$key."&nbsp;".($key==1 ? html("persoon","tarieventabel") : html("personen","tarieventabel"))."</td></tr>";
				}
			} else {
				if($this->toon_accommodatie_per_persoon) {

					// regels met aantal personen tonen
					foreach ($this->aantal_personen as $key => $value) {
						$return.="<tr class=\"".trim(($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? "tarieventabel_verbergen" : "").($this->get_aantal_personen==$key && !$_GET["d"] ? " tarieventabel_tarieven_gekozen" : ""))."\"><td>".$key."&nbsp;".($key==1 ? html("persoon","tarieventabel") : html("personen","tarieventabel"))."</td></tr>";
					}
				} else {
					$return.="<tr><td>".html("prijsperaccommodatie","tarieventabel")."</td></tr>";

					if($this->toon_commissie) {
						// commissie tonen aan reisagenten
						$return.="<tr class=\"tarieventabel_verbergen\"><td>".html("wederverkoop_commissie","tarieventabel")."</td></tr>";
					}
				}
			}


			$return.="</table>";

			$return .= $this->tabel_tarieven();

			// minder personen open/dichtklappen
			if($this->arrangement or $this->toon_accommodatie_per_persoon) {
				$return.="<div class=\"tarieventabel_toggle_toon_verberg\">";
				if($this->max_personen_tonen<$this->max_personen or $this->min_personen_tonen>$this->min_personen) {
					$return.="<a href=\"#\" data-default=\"".html("minderpersonen","tarieventabel")."\" data-hide=\"".html("minderpersonen_verbergen","tarieventabel")."\"><i class=\"icon-chevron-sign-down\"></i>&nbsp;<span>".html("minderpersonen","tarieventabel")."</span></a>";
				} else {
					$return.="&nbsp;";
				}
				$return.="</div>";
			} elseif($this->toon_commissie) {
				// commissie open/dichtklappen
				$return.="<div class=\"tarieventabel_toggle_toon_verberg\">";
				$return.="<a href=\"#\" data-default=\"".html("wederverkoop_tooncommissie1","tarieventabel")."\" data-hide=\"".html("wederverkoop_tooncommissie2","tarieventabel")."\"><i class=\"icon-chevron-sign-down\"></i>&nbsp;<span>".html("wederverkoop_tooncommissie1","tarieventabel")."</span></a>";
				$return.="</div>";
			}



			// legenda
			$return.="<div class=\"tarieventabel_legenda\">";

			$return .= $this->button_to_other_website($this->accinfo["zomerwinterkoppeling_accommodatie_id"]);

			if($this->aanbieding_actief) {
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_legenda_kleurenblokje_aanbieding tarieventabel_tarieven_aanbieding\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_aanbieding","tarieventabel")."</div>";
			}
			if($this->arrangement) {
				if($this->get_aantal_personen and $_GET["d"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum_aantal_personen","tarieventabel")."</div>";
				} elseif($this->get_aantal_personen and !$_GET["d"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_aantal_personen","tarieventabel")."</div>";
				} elseif($_GET["d"] and !$this->get_aantal_personen) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum","tarieventabel")."</div>";
				}
			} else {
				if($_GET["d"]) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum","tarieventabel")."</div>";
				}
			}
			if($this->toon_beschikbaarheid) {
				$return.="<br/><div><b>".html("wederverkoop_beschikbaarheid","tarieventabel").":</b></div>";
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_beschikbaarheid_1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("wederverkoop_groen","tarieventabel")."</div>";
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_beschikbaarheid_2\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("wederverkoop_oranje","tarieventabel")."</div>";
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_beschikbaarheid_3\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("wederverkoop_zwart","tarieventabel")."</div>";
			}
			if($this->tarieventabel_tarieven_niet_beschikbaar and !$this->toon_beschikbaarheid) {
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_niet_beschikbaar\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_niet_beschikbaar","tarieventabel")."</div>";
			}

			// check for afwijkende vertrekdag
			if($this->show_afwijkend_legenda and $this->afwijkend_aantal_nachten) {
				foreach ($this->aantalnachten as $key_datum => $value_nachten) {
					if($key_datum>time()) {
						if($value_nachten and ($value_nachten<>7 or $this->dag_van_de_week_afwijkend[$key_datum])) {
							if($this->unixtime_week[$key_datum]) {
								$eind=mktime(0,0,0,date("m",$this->unixtime_week[$key_datum]),date("d",$this->unixtime_week[$key_datum])+$value_nachten,date("Y",$this->unixtime_week[$key_datum]));
								$afwijkend .= "<div>".DATUM("DAG D MND JJJJ", $this->unixtime_week[$key_datum])." - ".DATUM("DAG D MND JJJJ", $eind)." (".$value_nachten." ".($value_nachten==1 ? html("nacht", "vars") : html("nachten", "vars")).")</div>";
							}
						}
					}
				}
				if($afwijkend) {
					$return .= "<div class=\"tarieventabel_legenda_vertrekdagaanpassing_div\"><span class=\"tarieventabel_legenda_kleurenblokje\">*</span><span>&nbsp;=&nbsp;</span><span>".html("legenda_vertrekdagaanpassing","tarieventabel").":</span></div>";
					$return .= "<div class=\"tarieventabel_legenda_vertrekdagaanpassing\">";
					$return .= $afwijkend;
					$return .= "</div>"; // close .tarieventabel_legenda_vertrekdagaanpassing
				}
			}

			$return.="</div>"; // close .tarieventabel_legenda

			$return .= "<div class=\"clear\"></div>";


			$return.="<div class=\"tarieventabel_pijl tarieventabel_pijl_boven tarieventabel_pijl_links\">";
			$return.="<i class=\"icon-chevron-left\"></i>";
			$return.="</div>";

			$return.="<div class=\"tarieventabel_pijl tarieventabel_pijl_boven tarieventabel_pijl_rechts\">";
			$return.="<i class=\"icon-chevron-right\"></i>";
			$return.="</div>";

			if($this->toon_interne_informatie) {

				// scrollbuttons ook tonen bij 2e datumblok
				$return.="<div class=\"tarieventabel_pijl tarieventabel_pijl_links tarieventabel_pijl_onder\">";
				$return.="<i class=\"icon-chevron-left\"></i>";
				$return.="</div>";

				$return.="<div class=\"tarieventabel_pijl tarieventabel_pijl_rechts tarieventabel_pijl_onder\">";
				$return.="<i class=\"icon-chevron-right\"></i>";
				$return.="</div>";
			}

		} else {
			// trigger_error("lege tarieventabel",E_USER_NOTICE);
		}

		return $return;

	}

	private function seizoenswissel($week, $begin=true) {
		// kijk of een week aan het begin of eind van een seizoen ligt
		if($begin) {
			$checkweek=mktime(0,0,0,date("m",$week),date("d",$week)-7,date("Y",$week));
		} else {
			$checkweek=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}

		if($this->seizoen_counter>1 and !$this->binnen_seizoen[date("Ym",$checkweek)] and (($begin and $checkweek>$this->begin) or (!$begin and $checkweek<$this->eind))) {
			return true;
		} else {
			return false;
		}
	}

	private function datum_headers($data_counter=0) {

		global $vars;

		# regel met maanden
		$return.="<tr class=\"tarieventabel_maanden\">";
		$kolomteller=0;
		foreach ($this->maand as $key => $value) {
			$kolomteller++;

			$class="";

			$kolomteller_onderliggend=$kolomteller_onderliggend+$value;

			$unixtime=mktime(0,0,0,substr($key,5,2),1,substr($key,0,4));

			if($kolomteller==1) {
				$class.=" tarieventabel_tarieven_kolom_links";
			} elseif($kolomteller==count($this->maand)) {
				$class.=" tarieventabel_tarieven_kolom_rechts";
			}

			$vorige_week=mktime(0,0,0,date("m",$unixtime)-1,date("d",$unixtime),date("Y",$unixtime));
			$volgende_week=mktime(0,0,0,date("m",$unixtime)+1,date("d",$unixtime),date("Y",$unixtime));

			if(!$this->binnen_seizoen[date("Ym",$vorige_week)] and $kolomteller>1) {
				$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
			}
			if(!$this->binnen_seizoen[date("Ym",$volgende_week)] and $kolomteller<count($this->maand)) {
				$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
			}

			$return.="<td class=\"".trim($class)."\"";

			$return .= " colspan=\"".$value."\" data-jaarmaand=\"".date("Ym", $unixtime)."\" data-maand-eerste-kolom=\"".intval($kolomteller_onderliggend-$value+1)."\" data-maand-kolom=\"".$kolomteller_onderliggend."\">";
			$return .= DATUM("MAAND JJJJ",$unixtime,$vars["taal"]);
			// $return .= " - ".$kolomteller."==".count($this->maand);

			$return .= "</td>";
		}
		$return.="</tr>";

		# regel met aankomstdatum
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content\" data-counter=\"".$data_counter."\">";
		$kolomteller=0;
		foreach ($this->dag as $key => $value) {
			$kolomteller++;

			$class = "";

			if($this->seizoenswissel($key,true)) {
				$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
			}
			if($this->seizoenswissel($key,false)) {
				$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
			}
			// if($_GET["d"] and $_GET["d"]==$key) {
			// 	$class.=" tarieventabel_tarieven_kolom_actieve_datum";
			// }
			$return.="<td class=\"".trim($class)."\">".$value."</td>";
		}
		$return.="</tr>";

		# regel met dag van de week
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content tarieventabel_datumbalk_minder_opvallend\">";
		$kolomteller=0;
		foreach ($this->dag_van_de_week as $key => $value) {

			$kolomteller++;

			$class = "";
			$star = "";

			if($this->seizoenswissel($key,true)) {
				$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
			}
			if($this->seizoenswissel($key,false)) {
				$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
			}

			if($this->dag_van_de_week_afwijkend[$key] and $key>time()) {
				$class = " tarieventabel_datumbalk_opvallend";
				if($this->show_afwijkend_legenda and $this->afwijkend_aantal_nachten) {
					$star = "*";
				}
			}

			$return.="<td class=\"".trim($class)."\">".$value.$star."</td>";
		}
		$return.="</tr>";

		# regel met aantal nachten
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content tarieventabel_datumbalk_minder_opvallend\">";
		$kolomteller=0;
		foreach ($this->dag_van_de_week as $key => $value) {

			$kolomteller++;

			$class = "";
			$star = "";

			if($this->seizoenswissel($key,true)) {
				$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
			}
			if($this->seizoenswissel($key,false)) {
				$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
			}

			if($this->aantalnachten[$key]<>7 and $key>time()) {
				$class.=" tarieventabel_datumbalk_opvallend";
				if($this->show_afwijkend_legenda and $this->afwijkend_aantal_nachten) {
					$star = "*";
				}
			}

			$return.="<td class=\"".trim($class)."\">".$this->aantalnachten[$key].$star."</td>";
		}
		$return.="</tr>";

		return $return;

	}

	private function tabel_tarieven() {

		global $vars;

		$return.="<div class=\"tarieventabel_wrapper_rechts\"><table cellspacing=\"0\" class=\"tarieventabel_border tarieventabel_content\">";

		// datum-headers
		$return.=$this->datum_headers("1");

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

					if($this->seizoenswissel($key,true)) {
						$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
					}
					if($this->seizoenswissel($key,false)) {
						$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
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

			if($this->korting) {

				// lege regel
				$return.="<tr class=\"tarieventabel_tr_leeg tarieventabel_voorraad_tr\">";

				foreach ($this->dag as $key => $value) {

					$class="";

					if($this->seizoenswissel($key,true)) {
						$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
					}
					if($this->seizoenswissel($key,false)) {
						$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
					}

					$return.="<td class=\"".trim($class)."\">&nbsp;</td>";
				}
				$return.="</tr>";

				foreach ($this->korting_doorlopen as $key0 => $value0) {
					$kolomteller=0;

					$return.="<tr class=\"tarieventabel_voorraad_tr tarieventabel_voorraad_content_tr tarieventabel_voorraad_korting\">";

					foreach ($this->dag as $key => $value) {
						$kolomteller++;

						$class="";

						if($kolomteller==1) {
							$class.=" tarieventabel_tarieven_kolom_links";
						} elseif($kolomteller==count($this->dag)) {
							$class.=" tarieventabel_tarieven_kolom_rechts";
						}

						if($this->seizoenswissel($key,true)) {
							$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
						}
						if($this->seizoenswissel($key,false)) {
							$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
						}


						$return.="<td class=\"".trim($class)."\">";

						if($this->korting[$key0][$key]) {
							$return .= number_format($this->korting[$key0][$key],0,",",".");
							if(preg_match("@perc@",$key0)) $return.="%";
							// if($key0=="aflopen_allotment") {
							// 	$return.=date("d/m",$this->voorraad[$key0][$key]);
							// } elseif($key0=="voorraad_bijwerken") {
							// 	$return.="&nbsp;<img src=\"".$vars["path"]."pic/vinkje.gif\">&nbsp;";
							// } else {
							// 	$return .= $this->voorraad[$key0][$key];
							// }
						} else {
							$return.="&nbsp;";
						}
						$return.="</td>";
					}
					$return.="</tr>";
				}
			}

			// datum-headers
			$return.=$this->datum_headers("2");

		}


		// beschikbaarheid voor reisagenten
		if($this->toon_beschikbaarheid) {
			$return.="<tr class=\"tarieventabel_beschikbaarheid_tr\">";
			$kolomteller=0;
			foreach ($this->dag_van_de_week as $key => $value) {

				$class="";

				$kolomteller++;

				if($kolomteller==1) {
					$class.=" tarieventabel_tarieven_kolom_links";
				} elseif($kolomteller==count($this->dag_van_de_week)) {
					$class.=" tarieventabel_tarieven_kolom_rechts";
				}

				// echo $this->voorraad["wederverkoop"][$key]." ";

				$div_class="tarieventabel_tarieven_beschikbaarheid_".($this->voorraad["wederverkoop"][$key] ? $this->voorraad["wederverkoop"][$key] : "3");


				$return.="<td class=\"".trim($class)."\"><div class=\"".$div_class."\">&nbsp;</div></td>";
			}
			$return.="</tr>";
		}

		# regel met korting
		if($this->aanbieding_actief and ($this->toonkorting_soort_1 or $this->toonkorting_soort_2)) {
			$return.="<tr class=\"tarieventabel_korting_tr\">";
			$kolomteller=0;
			foreach ($this->dag_van_de_week as $key => $value) {

				$kolomteller++;

				$class="";

				if($kolomteller==1) {
					$class.=" tarieventabel_tarieven_kolom_links";
				} elseif($kolomteller==count($this->dag_van_de_week)) {
					$class.=" tarieventabel_tarieven_kolom_rechts";
				}

				if($this->seizoenswissel($key,true)) {
					$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
				}
				if($this->seizoenswissel($key,false)) {
					$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
				}

				$return.="<td class=\"".trim($class)."\">";




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
		if($this->arrangement or $this->toon_accommodatie_per_persoon) {

			//
			// arrangement
			//
			// tarieven-cellen tonen
			//

			foreach ($this->aantal_personen as $key => $value) {

				$return.="<tr class=\"tarieventabel_tarieven".($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? " tarieventabel_verbergen" : "").($this->get_aantal_personen==$key && !$_GET["d"] ? " tarieventabel_tarieven_gekozen" : "")."\" data-aantalpersonen=\"".$key."\">";
				$kolomteller=0;
				foreach ($this->dag as $key2 => $value2) {
					$kolomteller++;

					$class="";

					if($kolomteller==1) {
						$class.=" tarieventabel_tarieven_kolom_links";
					} elseif($kolomteller==count($this->dag)) {
						$class.=" tarieventabel_tarieven_kolom_rechts";
					}

					if($this->seizoenswissel($key2,true)) {
						$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
					}
					if($this->seizoenswissel($key2,false)) {
						$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
					}

					if($this->tarief[$key][$key2]>0) {
						$class.=" tarieventabel_tarieven_beschikbaar";
					}

					if($this->get_aantal_personen==$key and $_GET["d"]==$key2) {
						$class.=" tarieventabel_tarieven_gekozen";
					} elseif($this->get_aantal_personen==$key and !$_GET["d"]) {
						$class.=" tarieventabel_tarieven_gekozen";
					} elseif($_GET["d"]==$key2 and !$this->get_aantal_personen) {
						$class.=" tarieventabel_tarieven_gekozen";
					}

					// voor Selina
					if($this->tarief[$key][$key2]>0) {

					} else {
						$class.=" tarieventabel_tarieven_niet_beschikbaar_td";
					}

					$return.="<td class=\"".trim($class)."\" data-week=\"".$key2."\">";

					$return.="<div class=\"tarieventabel_tarieven_div\">";

					if($this->tarief[$key][$key2]>0) {
						if($this->toonkorting_1[$key2] or $this->toonkorting_2[$key2] or $this->toonkorting_3[$key2]) {
							$return.="<div class=\"tarieventabel_tarieven_aanbieding\">";
						}

						if($this->tarief[$key][$key2]>=10000) {
							$return.=number_format($this->tarief[$key][$key2],0,",",".");
						} else {
							$return.=number_format($this->tarief[$key][$key2],0,",","");
						}
						$this->tarieven_getoond[$this->week_seizoen_id[$key2]]=true;

						if($this->toonkorting_1[$key2] or $this->toonkorting_2[$key2] or $this->toonkorting_3[$key2]) {
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
			$kolomteller=0;
			foreach ($this->dag as $key => $value) {
				$kolomteller++;

				$class="";

				if($kolomteller==1) {
					$class.=" tarieventabel_tarieven_kolom_links";
				} elseif($kolomteller==count($this->dag)) {
					$class.=" tarieventabel_tarieven_kolom_rechts";
				}

				if($this->seizoenswissel($key,true)) {
					$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
				}
				if($this->seizoenswissel($key,false)) {
					$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
				}

				if($this->tarief[$key]>0) {
					$class.=" tarieventabel_tarieven_beschikbaar";
				}

				if($_GET["d"]==$key) {
					$class.=" tarieventabel_tarieven_gekozen";
				}

				// voor Selina
				if($this->tarief[$key]>0) {

				} else {
					$class.=" tarieventabel_tarieven_niet_beschikbaar_td";
				}

				$return.="<td class=\"".trim($class)."\" data-week=\"".$key."\">";

				$return.="<div class=\"tarieventabel_tarieven_div\">";

				if($this->tarief[$key]>0) {

					if($this->tarief[$key]>=10000) {
						$te_tonen_bedrag["euro"]=number_format($this->tarief[$key],0,",",".");
					} else {
						$te_tonen_bedrag["euro"]=number_format($this->tarief[$key],0,",","");
					}

					if($this->toonkorting_1[$key] or $this->toonkorting_2[$key] or $this->toonkorting_3[$key]) {
						$return.="<div class=\"tarieventabel_tarieven_aanbieding\"";
					} else {
						$return.="<div";
					}

					if($this->meerdere_valuta) {
						$bedrag_andere_valuta["gbp"]=round($this->wisselkoers["gbp"]*$this->tarief[$key]);

						if($bedrag_andere_valuta["gbp"]>=10000) {
							$te_tonen_bedrag["gbp"]=number_format($bedrag_andere_valuta["gbp"],0,",",".");
						} else {
							$te_tonen_bedrag["gbp"]=number_format($bedrag_andere_valuta["gbp"],0,",","");
						}
						$return.=" data-euro=\"".wt_he($te_tonen_bedrag["euro"])."\" data-gbp=\"".wt_he($te_tonen_bedrag["gbp"])."\"";
					}
					$return.=">";

					if($this->meerdere_valuta and $this->actieve_valuta=="gbp") {
						$return.=$te_tonen_bedrag["gbp"];
					} else {
						$return.=$te_tonen_bedrag["euro"];
					}

					$this->tarieven_getoond[$this->week_seizoen_id[$key]]=true;

					// if($this->toonkorting_1[$key] or $this->toonkorting_2[$key] or $this->toonkorting_3[$key]) {
						$return.="</div>";
					// }
				} else {
					$return.="<div class=\"tarieventabel_tarieven_niet_beschikbaar\">";
					$return.="&nbsp;";
					$return.="</div>";

					$this->tarieventabel_tarieven_niet_beschikbaar=true;
				}

				$return.="</div></td>";
			}
			$return.="</tr>";


			// commissie voor reisagenten
			if($this->toon_commissie) {
				$return.="<tr class=\"tarieventabel_commissie_tr tarieventabel_verbergen\">";
				$kolomteller=0;
				foreach ($this->dag_van_de_week as $key => $value) {

					$class="";

					$kolomteller++;

					if($kolomteller==1) {
						$class.=" tarieventabel_tarieven_kolom_links";
					} elseif($kolomteller==count($this->dag_van_de_week)) {
						$class.=" tarieventabel_tarieven_kolom_rechts";
					}

					$return.="<td class=\"".trim($class)."\">".($this->commissie[$key]>0 ? number_format($this->commissie[$key],0,",","")."%" : "&nbsp;")."</td>";
				}
				$return.="</tr>";
			}
		}

		$return.="</table></div>";

		return $return;

	}

	private function bijkomende_kosten() {

		global $vars;

		$db = new DB_sql;
		$db2 = new DB_sql;
		$db3 = new DB_sql;


		$db2->query("SELECT a.bijkomendekosten1_id, a.bijkomendekosten2_id, a.bijkomendekosten3_id, a.bijkomendekosten4_id, a.bijkomendekosten5_id, a.bijkomendekosten6_id, t.bijkomendekosten1_id AS tbijkomendekosten1_id, t.bijkomendekosten2_id AS tbijkomendekosten2_id, t.bijkomendekosten3_id AS tbijkomendekosten3_id, t.bijkomendekosten4_id AS tbijkomendekosten4_id, t.bijkomendekosten5_id AS tbijkomendekosten5_id, t.bijkomendekosten6_id AS tbijkomendekosten6_id FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($this->type_id)."';");
		if($db2->next_record()) {
			for($i=1;$i<=6;$i++) {
				if($db2->f("bijkomendekosten".$i."_id")) {
					if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db2->f("bijkomendekosten".$i."_id"); else $bijkomendekosten_inquery=$db2->f("bijkomendekosten".$i."_id");
				}
				if($db2->f("tbijkomendekosten".$i."_id")) {
					if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db2->f("tbijkomendekosten".$i."_id"); else $bijkomendekosten_inquery=$db2->f("tbijkomendekosten".$i."_id");
				}
			}
		}

		// Bijkomende kosten gekoppeld aan skipassen
		if($this->accinfo["skipasid"]) {
			$db2->query("SELECT bijkomendekosten_id FROM skipas WHERE skipas_id='".addslashes($this->accinfo["skipasid"])."';");
			if($db2->next_record()) {
				if($db2->f("bijkomendekosten_id")) {
					if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db2->f("bijkomendekosten_id"); else $bijkomendekosten_inquery=$db2->f("bijkomendekosten_id");
				}
			}
		}

		if($bijkomendekosten_inquery) {
			$db2->query("SELECT b.bijkomendekosten_id, b.naam".$vars["ttv"]." AS naam, b.omschrijving".$vars["ttv"]." AS omschrijving, b.perboekingpersoon, b.min_personen FROM bijkomendekosten b WHERE b.bijkomendekosten_id IN (".$bijkomendekosten_inquery.") ORDER BY b.naam".$vars["ttv"].";");
			if($db2->num_rows()) {
				while($db2->next_record()) {
					// $db3->query("SELECT DISTINCT week, verkoop FROM bijkomendekosten_tarief WHERE bijkomendekosten_id='".$db2->f("bijkomendekosten_id")."' AND seizoen_id IN (".$seizoenen_inquery.")".($_GET["optie_datum"] ? " AND week='".addslashes($_GET["optie_datum"])."'" : "").";");
					$db3->query("SELECT DISTINCT week, verkoop FROM bijkomendekosten_tarief WHERE bijkomendekosten_id='".$db2->f("bijkomendekosten_id")."' AND seizoen_id IN (".$this->seizoen_id.");");
					while($db3->next_record()) {
						if($db3->f("verkoop")<>0) {
							if(!isset($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"])) {
								$bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"]=$db3->f("verkoop");
								$bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"]=$db3->f("verkoop");
							}
							if($db3->f("verkoop")<$bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"]) $bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"]=$db3->f("verkoop");
							if($db3->f("verkoop")>$bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"]) $bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"]=$db3->f("verkoop");

							if($_GET["optie_datum"] and $db3->f("week")==$_GET["optie_datum"]) {
								$bijkomendekosten[$db2->f("bijkomendekosten_id")]["exact"]=$db3->f("verkoop");
							}
						}
					}
					if(isset($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"])) {

						if(!$bijkomendekosten_header_getoond) {

							// $bijkomendekosten_header2.="<TABLE width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
							// $bijkomendekosten_header2.="<TR><TH colspan=\"2\">".html("bijkomendekosten","toonaccommodatie")."";

							$bijkomendekosten_header_getoond=true;
						}

						// $bijkomendekosten_table.="<tr><td>";
						$bijkomendekosten_table.="<table cellspacing=\"0\" border=\"0\" class=\"bijkomendekosten_table\">";
						$bijkomendekosten_table.="<tr>";
						$bijkomendekosten_table.="<td width=\"440\" class=\"nowrap".($db2->f("min_personen") ? " valigntop" : "")."\">".wt_he($db2->f("naam"));
						if($db2->f("omschrijving")) {
							$bijkomendekosten_table.="&nbsp;<a href=\"javascript:popwindow(500,0,'".$vars["path"]."popup.php?tid=".intval($this->type_id)."&id=bijkomendekosten&bkid=".$db2->f("bijkomendekosten_id")."');\">&#187;</a>";
						}
						$bijkomendekosten_table.="</td><td align=\"right\" class=\"nowrap\">";
						if($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"]==$bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"] or $bijkomendekosten[$db2->f("bijkomendekosten_id")]["exact"]<>0) {

							if($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"]<>$bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"]) {
								$bijkomendekosten_afhankelijkvandatum=true;
							}

							if($bijkomendekosten[$db2->f("bijkomendekosten_id")]["exact"]) {
								$bijkomendekosten_bedrag=$bijkomendekosten[$db2->f("bijkomendekosten_id")]["exact"];
							} else {
								$bijkomendekosten_bedrag=$bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"];
							}

							if($db2->f("perboekingpersoon")==2) {
								$bijkomendekosten_table.=html("perpersoonafk","toonaccommodatie");
							} else {
								$bijkomendekosten_table.="&nbsp;";
							}
							if($bijkomendekosten_bedrag<0) {
								$bijkomendekosten_table.="<td align=\"right\">".html("korting","vars")."</td>";
							} else {
								$bijkomendekosten_table.="<td>&nbsp;</td>";
							}
							$bijkomendekosten_table.="<td align=\"right\">&euro;</td>";
							$bijkomendekosten_table.="<td width=\"60\" align=\"right\">".number_format(abs($bijkomendekosten_bedrag),2,',','.')."</td>";
						} else {
							if(isset($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"]) and $bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"]>0 and $db2->f("min_personen")) {
								//
								// surcharge extra persons with variable prices
								//
								// $bijkomendekosten_table.="<td colspan=\"4\" align=\"right\" class=\"nowrap\">".html("maximaalXeuro","toonaccommodatie", array("v_bedragmin"=>number_format($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"], 2, ",", "."), "v_bedragmax"=>number_format($bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"], 2, ",", ".")))."<br/><span class=\"berekentotaalbedrag\">(<a href=\"".wt_he($vars["path"].txt("menu_calc").".php?tid=".$this->type_id."&back=".urlencode($_SERVER["REQUEST_URI"]."#prijsinformatie"))."\">".html("berekentotaalbedrag", "toonaccommodatie")."</a>)</span></td>";
								$bijkomendekosten_table.="<td colspan=\"4\" align=\"right\" class=\"nowrap\">".html("maximaalXeuro","toonaccommodatie", array("v_bedragmin"=>number_format($bijkomendekosten[$db2->f("bijkomendekosten_id")]["min"], 2, ",", "."), "v_bedragmax"=>number_format($bijkomendekosten[$db2->f("bijkomendekosten_id")]["max"], 2, ",", "."), "h_1"=>"<a href=\"javascript:popwindow(500,0,'".$vars["path"]."popup.php?tid=".intval($this->type_id)."&id=bijkomendekosten&bkid=".$db2->f("bijkomendekosten_id")."');\">", "h_2"=>"</a>"))."</td>";
							} else {
								$bijkomendekosten_table.="<td colspan=\"4\" align=\"right\">".html("afhankelijkvandatum","toonaccommodatie")."</td>";
							}
							$bijkomendekosten_afhankelijkvandatum=true;
						}
						$bijkomendekosten_table.="</tr>";
						$bijkomendekosten_table.="</table>";
						// $bijkomendekosten_table.="</td></tr>";
					}
				}
				if($bijkomendekosten_header_getoond) {
					// $bijkomendekosten_table.="</TABLE>";
				}
			}
		}
		if($bijkomendekosten_table) {
			if($NU_EVEN_NIET and $optie_pulldown_teller and $bijkomendekosten_afhankelijkvandatum) {
				// bijkomende kosten die afhankelijk zijn van datum: tijdelijk niet oproepbaar ($NU_EVEN_NIET) per exacte datum. Als er een datum is geselecteerd: dan worden de juist kosten al getoond
				$return.="<form name=\"bijkomendekostentabel2".$seizoenid."\" method=\"get\" action=\"".$_SERVER["PHP_SELF"]."#prijsinformatie\" style=\"padding:0px;\">";
				$return.="<h1>".html("bijkomendekosten","toonaccommodatie").":</h1><div class=\"toelichting_onderdeel\">";
				// $return.=$bijkomendekosten_header2;
				$return.="<input type=\"hidden\" name=\"selecttab\" value=\"tarieven\">\n";

				@reset($_GET);
				while(list($key2,$value2)=@each($_GET)) {
					if($key2<>"optie_datum" and $key2<>"selecttab" and !is_array($value2)) $return.="<input type=\"hidden\" name=\"".$key2."\" value=\"".wt_he($value2)."\">\n";
				}
				$return.=ereg_replace("optietabel".$seizoenid,"bijkomendekostentabel2".$seizoenid,ereg_replace(html("optietarieven","toonaccommodatie")." ","",$optie_pulldown_html));
			} else {
				$return.="<h1>".html("bijkomendekosten","toonaccommodatie").":</h1><div class=\"toelichting_onderdeel\">";
				// $return.=$bijkomendekosten_header2;
			}
			// $return.="</th></tr>";

			$return.=$bijkomendekosten_table;

			$return.="</div>";
		}

		return $return;
	}

	private function toelichting() {


		//
		// toon teksten "inclusief", "exclusief" en "bijkomende kosten"
		//

		global $vars, $isMobile;

		$db = new DB_sql;


		if($this->arrangement) {
			# Skipasgegevens uit database halen
			$db->query("SELECT s.website_omschrijving".$vars["ttv"]." AS website_omschrijving FROM skipas s, accommodatie a WHERE a.skipas_id=s.skipas_id AND a.accommodatie_id='".intval($this->accinfo["accommodatie_id"])."';");
			if($db->next_record()) {
				if($db->f("website_omschrijving")) $skipas_website_omschrijving=$db->f("website_omschrijving");
			}
		}


		if($vars["wederverkoop"]) {
			if($this->accinfo["inclusief"] or $this->accinfo["tinclusief"]) {
				$return.="<h1>".html("inclusief","toonaccommodatie").":</h1>";
				$return.="<div class=\"toelichting_onderdeel\">".$this->toon_tekst_acc_en_type($this->accinfo["inclusief"],$this->accinfo["tinclusief"])."</div>";
			}
		} else {
			if($this->accinfo["inclusief"] or $this->accinfo["tinclusief"] or $skipas_website_omschrijving) {
				$return.="<h1>".html("inclusief","toonaccommodatie").":</h1>";
				$return.="<div class=\"toelichting_onderdeel\">";
				if($skipas_website_omschrijving) $return.=$this->toon_tekst_acc_en_type($skipas_website_omschrijving);
				if($this->accinfo["inclusief"] or $this->accinfo["tinclusief"]) {
					if($skipas_website_omschrijving) $return.="<br><br>";
					$return.=trim($this->toon_tekst_acc_en_type($this->accinfo["inclusief"],$this->accinfo["tinclusief"]));
				}
				$return.="</div>";
			}
		}

		$this->accinfo["texclusief"]=trim($this->accinfo["texclusief"]."\n\n".txt("reserveringskosten", "vars")." (€ ".$vars["reserveringskosten"].",- ".txt("perboeking", "vars").").");

		if($this->accinfo["exclusief"] or $this->accinfo["texclusief"]) {
			$return.="<h1>".html("exclusief","toonaccommodatie").":</h1>";
			$return.="<div class=\"toelichting_onderdeel\">";
			$return.=$this->toon_tekst_acc_en_type($this->accinfo["exclusief"],$this->accinfo["texclusief"]);
			$return.="</div>";
		}

		// bijkomende kosten
		$return .= $this->bijkomende_kosten();

		// uitbreidingsmogelijkheden
		$return.="<h1>".html("uitbreidingsmogelijkheden","tarieventabel").":</h1>";
		$return.="<div class=\"toelichting_onderdeel\">";
		$return.=html("bekijkdeextraopties","tarieventabel",array("h_1"=>"<a href=\"#extraopties\">","h_2"=>" &raquo;</a>"));
		$return.="</div>";


		if(!$isMobile){
			$return .= "<div class=\"toelichting_bereken_totaalbedrag\">";
			if(!$vars["wederverkoop"]) {
					$return.="<a href=\"".$vars["path"]."calc.php?tid=".intval($this->type_id)."&ap=".wt_he($this->get_aantal_personen)."&d=".wt_he($_GET["d"])."&back=".urlencode($_SERVER["REQUEST_URI"])."\">".html("berekentotaalbedrag","tarieventabel")." &raquo;</a>";
			}
			$return .= "</div>"; # afsluiten .toelichting_bereken_totaalbedrag
		}

		return $return;
	}

	private function tarieven_uit_database() {

		if(!$this->tarieven_uit_database_done) {

			global $vars, $accinfo;

			$db = new DB_sql;
			$db2 = new DB_sql;

			if($this->toon_bijkomendekosten) {
				$bijkomendekosten = new bijkomendekosten;
			}



			# Accinfo
			if($accinfo) {
				$this->accinfo=$accinfo;
			} else {
				$this->accinfo=accinfo($this->type_id);
			}

			if($this->accinfo["toonper"]==3 or $vars["wederverkoop"]) {

			} else {
				$this->arrangement=true;
				$this->toon_accommodatie_per_persoon = false;
			}


			if($this->meerdere_valuta) {
				$db->query("SELECT wisselkoers_pond FROM diverse_instellingen WHERE diverse_instellingen_id=1;");
				if($db->next_record()) {
					$this->wisselkoers["gbp"]=$db->f("wisselkoers_pond");
				}
			}

			# Controle op vertrekdagaanpassing?
			$typeid=$this->type_id;
			include($vars["unixdir"]."content/vertrekdagaanpassing.html");

			// aflopen_allotment uit calculatiesjabloon halen
			$db2->query("SELECT week, aflopen_allotment FROM calculatiesjabloon_week WHERE seizoen_id IN (".$this->seizoen_id.") AND leverancier_id='".intval($this->accinfo["leverancierid"])."';");
			while($db2->next_record()) {
				if($db2->f("aflopen_allotment")<>"") $this->aflopen_allotment[$db2->f("week")]=$db2->f("aflopen_allotment");
			}

			// seizoensgegevens uit database halen
			// $db->query("SELECT MIN(UNIX_TIMESTAMP(s.begin)) AS begin, MAX(UNIX_TIMESTAMP(s.eind)) AS eind, s.naam".$vars["ttv"]." AS naam FROM seizoen s WHERE s.type='".$vars["seizoentype"]."' AND s.seizoen_id IN (".$this->seizoen_id.") AND s.tonen>1;");
			$db->query("SELECT s.seizoen_id, UNIX_TIMESTAMP(s.begin) AS begin, UNIX_TIMESTAMP(s.eind) AS eind, s.naam".$vars["ttv"]." AS naam FROM seizoen s WHERE s.type='".$vars["seizoentype"]."' AND s.seizoen_id IN (".$this->seizoen_id.") AND s.tonen>1 AND s.eind>NOW() ORDER BY s.begin, s.eind;");
			while($db->next_record()) {

				// seizoeninfo
				$this->seizoeninfo[$db->f("seizoen_id")]["naam"] = $db->f("naam");
				$this->seizoeninfo[$db->f("seizoen_id")]["begin"] = $db->f("begin");
				$this->seizoeninfo[$db->f("seizoen_id")]["eind"] = $db->f("eind");

				if(!$this->first_seizoen_id) {
					$this->first_seizoen_id = $db->f("seizoen_id");
				}
				$this->last_seizoen_id = $db->f("seizoen_id");
				$this->seizoen_counter ++;


				// begin, eind en binnen_seizoen bepalen
				$week=$db->f("begin");
				$kolomteller=0;
				while($week<=$db->f("eind")) {

					if($week>time()) {

						if(!$this->begin) $this->begin=$week;
						$this->eind=$week;

						$this->binnen_seizoen[date("Ym",$week)]=true;
					}

					$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
				}
			}


			// tarieven uit database halen
			if($this->arrangement) {


				//
				// arrangement
				//
				// gegevens uit database halen
				//

				$db->query("SELECT t.bruto, t.arrangementsprijs, t.beschikbaar, t.blokkeren_wederverkoop, tp.week, tp.personen, tp.prijs, t.voorraad_garantie, t.voorraad_allotment, t.voorraad_vervallen_allotment, t.voorraad_optie_leverancier, t.voorraad_xml, t.voorraad_request, t.voorraad_optie_klant, t.voorraad_bijwerken, t.aanbiedingskleur, t.aanbiedingskleur_korting, t.aflopen_allotment, t.toonexactekorting, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.aanbieding_skipas_percentage, t.aanbieding_skipas_euro, t.seizoen_id FROM tarief_personen tp, tarief t WHERE tp.seizoen_id IN(".$this->seizoen_id.") AND tp.type_id='".addslashes($this->type_id)."' AND t.type_id=tp.type_id AND t.seizoen_id=tp.seizoen_id AND t.week=tp.week AND tp.week>UNIX_TIMESTAMP((NOW()- INTERVAL 6 WEEK)) ORDER BY tp.week, tp.personen DESC;");
				if($db->num_rows()) {
					$this->tarieven_ingevoerd=true;
				}

				while($db->next_record()) {

					# seizoen_id bij een bepaalde week bepalen
					$this->week_seizoen_id[$db->f("week")]=$db->f("seizoen_id");

					if($this->toon_bijkomendekosten) {

						if(!$seizoen_cache_fetched[$db->f("seizoen_id")]) {
							$seizoen_cache_fetched[$db->f("seizoen_id")] = true;

							$bk_add_to_price = $bijkomendekosten->get_type_from_cache_all_persons($this->type_id, $vars["seizoentype"], $db->f("seizoen_id"), $this->accinfo["maxaantalpersonen"], true);

						}
					}


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

					// if(!$this->begin) $this->begin=$db->f("week");
					// $this->eind=$db->f("week");

					// $this->binnen_seizoen[date("Ym",$db->f("week"))]=true;

					if($db->f("week")>=time() and $db->f("bruto")>0) {
						$this->tarief_ingevoerd[$db->f("week")] = true;
					}

					if($db->f("week")>=time() and $db->f("prijs")>0 and $db->f("beschikbaar") and ($db->f("bruto")>0 or $db->f("arrangementsprijs")>0)) {

						$this->tarief[$db->f("personen")][$db->f("week")]=$db->f("prijs");

						if($this->toon_bijkomendekosten) {
							// add bijkomende kosten
							$this->tarief[$db->f("personen")][$db->f("week")] += $bk_add_to_price[$db->f("personen")];


							$this->tarief_exact[$db->f("personen")][$db->f("week")] = $this->tarief[$db->f("personen")][$db->f("week")];


							// round with ceil()
							$this->tarief[$db->f("personen")][$db->f("week")] = ceil($this->tarief[$db->f("personen")][$db->f("week")]);
						}

						$this->aantal_personen[$db->f("personen")]=true;

						// if($db->f("personen")>$max) $max=$db->f("personen");
						// if(!$min) $min=$db->f("personen");
						// if($db->f("personen")<$min) $min=$db->f("personen");

						if($db->f("week")>time()) {

							# Aanbiedingskleur
							// if($db->f("aanbiedingskleur")) {
							// 	$aanbiedingskleur[$db->f("week")]=true;
							// }

							if($db->f("aanbiedingskleur_korting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0 or $db->f("aanbieding_skipas_percentage")>0 or $db->f("aanbieding_skipas_euro")>0)) {
								// $aanbiedingskleur[$db->f("week")]=true;

								$this->toonkorting_3[$db->f("week")]=true;
								$this->aanbieding_actief=true;

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
					$db->query("SELECT t.c_bruto, t.bruto, t.beschikbaar, t.blokkeren_wederverkoop, t.wederverkoop_verkoopprijs, t.wederverkoop_commissie_agent, t.week, t.c_verkoop_site, t.voorraad_garantie, t.voorraad_allotment, t.voorraad_vervallen_allotment, t.voorraad_optie_leverancier, t.voorraad_xml, t.voorraad_request, t.voorraad_optie_klant, t.voorraad_bijwerken, t.aanbiedingskleur, t.aanbiedingskleur_korting, t.aflopen_allotment, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.toonexactekorting, t.seizoen_id FROM tarief t WHERE t.seizoen_id IN (".$this->seizoen_id.") AND t.type_id='".addslashes($this->type_id)."' AND t.week>UNIX_TIMESTAMP((NOW()- INTERVAL 6 WEEK)) ORDER BY t.week;");
					if($db->num_rows()) {
						$this->tarieven_ingevoerd=true;
					}
				} else {
					$db->query("SELECT t.c_bruto, t.beschikbaar, t.blokkeren_wederverkoop, t.week, t.c_verkoop_site, t.voorraad_garantie, t.voorraad_allotment, t.voorraad_vervallen_allotment, t.voorraad_optie_leverancier, t.voorraad_xml, t.voorraad_request, t.voorraad_optie_klant, t.voorraad_bijwerken, t.aanbiedingskleur, t.aanbiedingskleur_korting, t.aflopen_allotment, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.toonexactekorting, t.seizoen_id FROM tarief t WHERE t.seizoen_id IN (".$this->seizoen_id.") AND t.type_id='".addslashes($this->type_id)."' AND t.week>UNIX_TIMESTAMP((NOW()- INTERVAL 6 WEEK)) ORDER BY t.week;");
					if($db->num_rows()) {
						$this->tarieven_ingevoerd=true;
					}
				}
				// echo $db->lq;
				while($db->next_record()) {

					if($this->toon_bijkomendekosten) {

						if(!$seizoen_cache_fetched[$db->f("seizoen_id")]) {
							$seizoen_cache_fetched[$db->f("seizoen_id")] = true;

							$bk_add_to_price = $bijkomendekosten->get_type_from_cache_all_persons($this->type_id, $vars["seizoentype"], $db->f("seizoen_id"), $this->accinfo["maxaantalpersonen"], false);

							// toon losse accommodatie per persoon
							if($this->toon_accommodatie_per_persoon and !$this->aantal_personen and is_array($bk_add_to_price)) {
								foreach ($bk_add_to_price as $key => $value) {
									$this->aantal_personen[$key] = true;
								}
								krsort($this->aantal_personen);
							}
						}
					}

					if($db->f("week")>=time() and $db->f("c_bruto")>0) {
						$this->tarief_ingevoerd[$db->f("week")] = true;
					}

					# seizoen_id bij een bepaalde week bepalen
					$this->week_seizoen_id[$db->f("week")]=$db->f("seizoen_id");

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

					if($this->toon_interne_informatie and $db->f("c_bruto")>0 and ($db->f("c_verkoop_site")>0 or $db->f("wederverkoop_verkoopprijs")>0)) {
						# Korting bepalen om intern te kunnen tonen
						if($db->f("inkoopkorting_percentage")>0) $this->korting["inkoopkorting_percentage"][$db->f("week")]=$db->f("inkoopkorting_percentage");
						if($db->f("inkoopkorting_euro")>0) $this->korting["inkoopkorting_euro"][$db->f("week")]=$db->f("inkoopkorting_euro");
						if($db->f("aanbieding_acc_percentage")>0) $this->korting["aanbieding_acc_percentage"][$db->f("week")]=$db->f("aanbieding_acc_percentage");
						if($db->f("aanbieding_acc_euro")>0) $this->korting["aanbieding_acc_euro"][$db->f("week")]=$db->f("aanbieding_acc_euro");
					}

					unset($temp_beschikbaar, $temp_bruto, $temp_verkoop_site);
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

					// if(!$this->begin) $this->begin=$db->f("week");
					// $this->eind=$db->f("week");

					// $this->binnen_seizoen[date("Ym",$db->f("week"))]=true;

					if($db->f("week")>=time() and $temp_verkoop_site>0 and $temp_beschikbaar and $temp_bruto>0) {

						if($this->toon_accommodatie_per_persoon) {

							if(is_array($bk_add_to_price)) {
								foreach ($bk_add_to_price as $key => $value) {
									$this->tarief[$key][$db->f("week")] = ($temp_verkoop_site + $value ) / $key;

									$this->tarief_exact[$key][$db->f("week")] = $this->tarief[$key][$db->f("week")];

									// round with ceil()
									$this->tarief[$key][$db->f("week")] = ceil($this->tarief[$key][$db->f("week")]);

								}
							}
						} else {

							$this->tarief[$db->f("week")]=$temp_verkoop_site;

							if($this->toon_bijkomendekosten) {
								// add bijkomende kosten
								// $this->tarief[$db->f("week")] += $bk_add_to_price[($_GET["ap"] ? $_GET["ap"] : $accinfo["optimaalaantalpersonen"])];
								$this->tarief[$db->f("week")] += $bk_add_to_price[($_GET["ap"] ? $_GET["ap"] : 1)];

								// round with ceil()
								$this->tarief[$db->f("week")] = ceil($this->tarief[$db->f("week")]);
							}


							if($this->tarief[$db->f("week")]>0) {
								// $tarieventabel_tonen[$db->f("week")]=1;
								$this->commissie[$db->f("week")]=$db->f("wederverkoop_commissie_agent");
								if($vars["chalettour_aanpassing_commissie"]) {
									$this->commissie[$db->f("week")]=$this->commissie[$db->f("week")]+$vars["chalettour_aanpassing_commissie"];
								}
							}
						}

						// Voorraad bepalen t.b.v. ingelogde reisbureaus
						// 1 = beschikbaar (groen), 2 = op aanvraag (licht oranje), 3 = niet beschikbaar (grijs)
						if($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")-$db->f("voorraad_optie_klant")>=1) {
							$this->voorraad["wederverkoop"][$db->f("week")]=1;
						} elseif($db->f("voorraad_request")>=1 or $db->f("voorraad_optie_klant")>=1 or $db->f("voorraad_vervallen_allotment")>=1) {
							$this->voorraad["wederverkoop"][$db->f("week")]=2;
						} else {
							$this->voorraad["wederverkoop"][$db->f("week")]=3;
						}

						if($db->f("week")>time()) {

							// Aanbiedingskleur
							// if($db->f("aanbiedingskleur")) {
							// 	$aanbiedingskleur[$db->f("week")]=true;
							// }

							if($db->f("aanbiedingskleur_korting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0)) {
								// $aanbiedingskleur[$db->f("week")]=true;

								$this->toonkorting_3[$db->f("week")]=true;
								$this->aanbieding_actief=true;
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
						$this->voorraad["wederverkoop"][$db->f("week")]=3;
					}
				}
			}


			// Aantal keer geboekt uit database halen
			$db->query("SELECT aankomstdatum, aankomstdatum_exact, vertrekdatum_exact FROM boeking WHERE goedgekeurd=1 AND geannuleerd=0 AND type_id='".intval($this->type_id)."' AND seizoen_id IN (".$this->seizoen_id.");");
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
			$kolomteller=0;
			while($week<=$this->eind) {
				if($vertrekdag[$this->week_seizoen_id[$week]][date("dm",$week)] or $this->accinfo["aankomst_plusmin"]) {
					$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$this->week_seizoen_id[$week]][date("dm",$week)]+$this->accinfo["aankomst_plusmin"],date("Y",$week));
				} else {
					$aangepaste_unixtime=$week;
				}

				if($this->binnen_seizoen[date("Ym",$week)]) {

					$kolomteller++;

					$this->dag[$week]=date("d",$aangepaste_unixtime);
					$this->dag_van_de_week[$week]=strftime("%a",$aangepaste_unixtime);
					if(date("w",$aangepaste_unixtime)<>6) {
						$this->dag_van_de_week_afwijkend[$week]=true;
					}
					$this->maand[date("Y-m",$aangepaste_unixtime)]++;
				}
				$this->unixtime_week[$week] = $aangepaste_unixtime;

				if($_GET["d"] and $_GET["d"]==$week) $this->actieve_kolom=$kolomteller;


				if(!$_GET["d"] and !$this->scroll_first_monthyear) {
					if($this->tarief_ingevoerd[$week]) {
						$this->scroll_first_monthyear=date("Ym", $week);
					}
				}

				$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
			}

			# Aantal nachten bepalen
			$week=$this->begin;
			$eind=mktime(0,0,0,date("m",$this->eind),date("d",$this->eind)+7,date("Y",$this->eind));
			while($week<=$eind) {
				# Afwijkende vertrekdag
				$aantalnachten_afwijking[date("dm",$week)]+=$vertrekdag[$this->week_seizoen_id[$week]][date("dm",$week)];
				$vorigeweek=mktime(0,0,0,date("n",$week),date("j",$week)-7,date("Y",$week));
				$aantalnachten_afwijking[date("dm",$vorigeweek)]-=$vertrekdag[$this->week_seizoen_id[$week]][date("dm",$week)];

				# Afwijkende verblijfsduur
				$aantalnachten_afwijking[date("dm",$week)]=$aantalnachten_afwijking[date("dm",$week)]+$this->accinfo["aankomst_plusmin"]-$this->accinfo["vertrek_plusmin"];

				$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
			}

			$week=$this->begin;
			$eind=mktime(0,0,0,date("m",$this->eind),date("d",$this->eind)+7,date("Y",$this->eind));
			while($week<=$eind) {
				if($aantalnachten_afwijking[date("dm",$week)]) {
					$this->aantalnachten[$week]=7-$aantalnachten_afwijking[date("dm",$week)];
					$this->afwijkend_aantal_nachten = true;
				} else {
					$this->aantalnachten[$week]=7;
				}
				$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));

			}

			$this->tarieven_uit_database_done = true;

			return $return;
		}
	}

	private function tabel_bottom() {
		// $return .= "</td></tr></table>";
		return $return;
	}

	private function toon_tekst_acc_en_type($tekst1,$tekst2="") {

		$tekst1=trim($tekst1);
		$tekst2=trim($tekst2);

		if($tekst1 and $tekst2) {
			return nl2br(wt_htmlent($tekst1))."<br/><br/>".nl2br(wt_htmlent($tekst2));
		} elseif($tekst1) {
			return nl2br(wt_htmlent($tekst1));
		} else {
			return nl2br(wt_htmlent($tekst2));
		}
	}

	private function button_to_other_website($zomerwinterkoppeling_accommodatie_id) {
		//
		// Button to other website (summer/winter)
		//

		global $vars, $isMobile;

		$db = new DB_sql;

		if($zomerwinterkoppeling_accommodatie_id) {
			$db->query("SELECT t.websites FROM type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".intval($zomerwinterkoppeling_accommodatie_id)."' AND a.tonen=1 AND t.tonen=1;");
			if($db->next_record()) {
				if($vars["websitetype"]==1 or $vars["websitetype"]==4 or ($vars["websitetype"]==6 and $vars["seizoentype"]==1)) {
					# Op Chalet.nl/be link naar zomerhuisje.nl/eu
					if($vars["website"]=="C") {
						if(preg_match("/Z/",$db->f("websites"))) {
							$beginurl="https://www.zomerhuisje.nl";
						}
					} elseif($vars["website"]=="B") {
						if(preg_match("/N/",$db->f("websites"))) {
							$beginurl="https://www.zomerhuisje.nl";
						}
					} elseif($vars["website"]=="V") {
						if(preg_match("/V/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.nl";
						}
					} elseif($vars["website"]=="Q") {
						if(preg_match("/Q/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.com";
						}
					} elseif($vars["website"]=="T") {
						if(preg_match("/Z/",$db->f("websites"))) {
							$beginurl="https://www.zomerhuisje.nl";
							$beginurl_extra="?fromsite=chalettour";
						}
					}
					if($beginurl) {
						$return .= "<a href=\"".wt_he($beginurl.ereg_replace("[a-z]+/$","",$vars["path"]).txt("menu_accommodatie")."/".$this->accinfo["begincode"].$zomerwinterkoppeling_accommodatie_id."/".$beginurl_extra)."\" class=\"button_to_other_website analytics_track_external_click\" target=\"_blank\">";
						$return .= nl2br(html("accommodatieopanderesite_2","toonaccommodatie",array("h_1"=>"<b>","h_2"=>"</b>")));
						$return .= "</a>";
					}
				} elseif($vars["websitetype"]==3 or $vars["websitetype"]==5 or ($vars["websitetype"]==6 and $vars["seizoentype"]==2)) {
					# Op Zomerhuisje.nl/eu link naar winterpagina
					if($vars["website"]=="Z") {
						if($_GET["fromsite"]=="chalettour" or $_COOKIE["fromsite"]=="chalettour") {
							if(preg_match("/T/",$db->f("websites"))) {
								$beginurl="https://www.chalettour.nl";
							}
						} else {
							if(preg_match("/C/",$db->f("websites"))) {
								$beginurl="https://www.chalet.nl";
							}
						}
					} elseif($vars["website"]=="N") {
						if(preg_match("/B/",$db->f("websites"))) {
							$beginurl="https://www.chalet.be";
						}
					} elseif($vars["website"]=="V") {
						if(preg_match("/V/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.nl";
						}
					} elseif($vars["website"]=="Q") {
						if(preg_match("/Q/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.com";
						}
					}
					if($beginurl) {
						$return .= "<a href=\"".wt_he($beginurl.ereg_replace("[a-z]+/$","",$vars["path"]).txt("menu_accommodatie")."/".$this->accinfo["begincode"].$zomerwinterkoppeling_accommodatie_id."/")."\" class=\"button_to_other_website analytics_track_external_click\" target=\"_blank\">";
						if($vars["websitetype"]==6) {
							$return .= nl2br(html("accommodatieopanderesite_vallandry","toonaccommodatie"));
						} else {
							$return .= nl2br(html("accommodatieopanderesite_2","toonaccommodatie",array("h_1"=>"<b>","h_2"=>"</b>")));
						}
						$return .= "</a>";
					}
				}
			}
		}
		return $return;
	}


	public function css() {

			?>

			<style>


			</style>

			<?php

	}

}


?>