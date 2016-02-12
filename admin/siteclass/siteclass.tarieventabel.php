<?php

/**
 * class to show:
 *
 *  - the price table (tarieventabel) on the accommodation page
 *  - the additional costs (bijkomende kosten) below the price table
 *  - the slide-block with the total price of a booking (accommodation + additional costs)
 *
 * @author Jeroen Boschman (jeroen@webtastic.nl)
 * @since  2015-05-26 14:00
*/

class tarieventabel {

	public $voorraad;
	public $commissie;

	public $toon_interne_informatie;
	public $toon_beschikbaarheid;
	public $toon_commissie;

	private $actieve_kolom;
	private $bk_fetched;

	public $meerdere_valuta;

	private $config;

	/**
	 * whether the class is used by the new website (through the api)
	 *
	 * @var boolean
	 **/
	public $newWebsite = false;

	/**
	 * set default values
	 *
	 * @return void
	 */
	function __construct( $settings = array() )
	{

		$this->config = new Configuration;

		$this->toon_interne_informatie = false;
		$this->toon_beschikbaarheid    = false;
		$this->toon_commissie          = false;
		$this->meerdere_valuta         = false;
		$this->show_afwijkend_legenda  = false;



		if ( !$settings["ignore_get_values"] ) {
			//
			// use GET-values (override if necessary)
			//

			// number of persons
			if( isset( $_GET["ap"] ) ) {
				$this->aantalpersonen = $_GET["ap"];
			}

			// arrival date
			if( isset( $_GET["d"] ) ) {
				$this->aankomstdatum = $_GET["d"];
			}

			// length of stay
			if( isset( $_GET["fdu"] ) ) {
				$this->verblijfsduur = $_GET["fdu"];
			}

			// did the visitor enter from another Chalet-website? (e.g. from Chalettour to Zomerhuisje)
			if( isset( $_GET["fromsite"] ) ) {
				$this->fromsite = $_GET["fromsite"];
			}
		}


		$this->voorraad_doorlopen=array(
			"garantie"               => "Garantie",
			"aflopen_allotment"      => "Allotment loopt af op",
			"allotment"              => "Allotment",
			"vervallen_allotment"    => "Vervallen allotment",
			"optie_leverancier"      => "Optie leverancier",
			"xml"                    => "XML",
			"request"                => "Request",
			"optie_klant"            => "Optie klant",
			"totaal"                 => "Totaal",
			"voorraad_bijwerken"     => "Automatisch bijwerken",
			"blokkeren_wederverkoop" => "Blokkeren wederverkoop",
			"aantal_geboekt"         => "Aantal geboekt"
		);
	}


	/**
	 * show the price table
	 *
	 * @return string $return
	 */
	public function toontabel()
	{

		$db = new DB_sql;

		$this->tarieven_uit_database();


		// link to new season
		if($this->config->seizoentype==1) {
			if($this->seizoen_counter>1) {
				$return .= "<div class=\"tarieventabel_nextseason\"><a href=\"#\" class=\"tarieventabel_jump_jaarmaand\" data-jaarmaand=\"".date("Ym", $this->seizoeninfo[$this->last_seizoen_id]["begin"])."\">".html("nualteboeken", "tarieventabel", array("v_seizoennaam"=>$this->seizoeninfo[$this->last_seizoen_id]["naam"]))." &raquo;</a></div>";
			} else {
				// check for seasons without prices
				$db->query("SELECT seizoen_id, naam FROM seizoen WHERE show_newpricesmail=1 AND type='".intval($this->config->seizoentype)."' AND seizoen_id NOT IN (".$this->seizoen_id.") ORDER BY eind DESC LIMIT 0,1;");
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
					$this->mailmijvolgendseizoen_form .= "<img src=\"".$this->config->path."pic/icon_okay.png\" class=\"okay\"><img src=\"".$this->config->path."pic/icon_notokay.png\" class=\"notokay\">";
					$this->mailmijvolgendseizoen_form .= "</form>";
					$this->mailmijvolgendseizoen_form .= "</div>"; // close

					$return .= $this->mailmijvolgendseizoen_form;

				}
			}
		}

		if($this->config->websitetype<>4) {
			//
			// tekst "hulp bij online boeken" tonen
			//

			$kantoor_open = new kantoor_open;

			if($kantoor_open->is_het_kantoor_geopend()) {
				$return .= "<div class=\"tarieventabel_hulp_bij_online_boeken\">";
				if($this->config->isMobile) {
					if($this->config->livechat_code) {
						$return .= html("hulpbijonlineboeken","tarieventabel",array("h_1"=>"<a href=\"tel:".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."\"><b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b></a>","h_2"=>"<a onclick=\"LC_API.open_chat_window();return false;\" href=\"/\">","h_3"=>"</a>", "h_4"=>"<a href=\"".$this->config->path."veelgestelde-vragen\">", "h_5"=>"</a>"));
					} else {
						$return .= html("hulpbijonlineboeken_zonderchat","tarieventabel",array("h_1"=>"<a href=\"tel:".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."\"><b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b></a>", "h_4"=>"<a href=\"".$this->config->path."veelgestelde-vragen\">", "h_5"=>"</a>"));
					}
				} else {
					if($this->config->livechat_code) {
						$return .= html("hulpbijonlineboeken","tarieventabel",array("h_1"=>"<b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b>","h_2"=>"<span class=\"trigger_livechat_button\">","h_3"=>"</span>", "h_4"=>"<a href=\"".$this->config->path."veelgestelde-vragen\">", "h_5"=>"</a>"));
					} else {
						$return .= html("hulpbijonlineboeken_zonderchat","tarieventabel",array("h_1"=>"<b><i class=\"icon-phone\"></i>&nbsp;".preg_replace("@ @","&thinsp;",html("telefoonnummer_alleen"))."</b>", "h_4"=>"<a href=\"".$this->config->path."veelgestelde-vragen\">", "h_5"=>"</a>"));
					}
				}
				$return .= "</div>"; // afsluiten .tarieventabel_hulp_bij_online_boeken
			}
		}

		if($this->config->seizoentype==1 and !$this->aankomstdatum) {
			// winter: always scroll to december
			$this->scroll_first_monthyear = date("Ym", $this->seizoeninfo[$this->first_seizoen_id]["begin"]);
		}

		$return .= "<div class=\"tarieventabel_wrapper\" data-boek-url=\"".wt_he($this->config->path.txt("menu_boeken").".php?tid=".$this->type_id . (isset($this->verblijfsduur) ? '&fdu=' . $this->verblijfsduur : '') . (!$this->arrangement && $this->aantalpersonen ? "&ap=".intval($this->aantalpersonen) : ""))."\" data-actieve-kolom=\"".intval($this->actieve_kolom)."\" data-scroll_first_monthyear=\"".wt_he($this->scroll_first_monthyear)."\" data-type_id=\"".intval($this->type_id)."\" data-seizoen_id_inquery=\"".wt_he($this->seizoen_id)."\">";


		$return .= $this->tabel_top();
		$return .= $this->tabel_content();
		$return .= $this->tabel_bottom();

		$return .= $this->show_bijkomendekosten();

		$return .= "</div>"; // afsluiten .tarieventabel_wrapper

		if(is_array($this->datadiv)) {
			$return .= "<div class=\"tarieventabel_datadiv\"";
			foreach ($this->datadiv as $key => $value) {
				$return .= " data-".$key."=\"".wt_he($value)."\"";
			}
			$return .= "></div>";
		}

		return $return;

	}


	/**
	 * show the total price slider
	 *
	 * @param integer number of persons
	 * @param integer arrival date in unixtime
	 * @return string $return
	 */
	public function info_totaalprijs($aantalpersonen, $aankomstdatum)
	{

		$this->tarieven_uit_database();

		$return .= "<div class=\"tarieventabel_totaalprijs\">";

		$return .= "<div class=\"tarieventabel_totaalprijs_left\">".html("geselecteerde-aankomstdatum", "tarieventabel").":</div>";
		$return .= "<span class=\"tarieventabel_totaalprijs_right datum\">".datum("DAG D MAAND JJJJ", $this->unixtime_week[$aankomstdatum], $this->config->taal)."</span>";



		// per person
		$return .= "<div class=\"tarieventabel_totaalprijs_left\">";
		$return .= html("prijs-per-persoon","tarieventabel");
		$return .= ":</div>";
		$return .= "<span class=\"tarieventabel_totaalprijs_right\">&euro;&nbsp;".number_format($this->tarief_exact[$aantalpersonen][$aankomstdatum], 2, ",", ".")."</span>";

		// total
		$return .= "<div class=\"tarieventabel_totaalprijs_left\">";
		if($aantalpersonen==1) {
			$return .= html("totaalprijs-op-basis-1-persoon","tarieventabel");
		} else {
			$return .= html("totaalprijs-op-basis-x-personen","tarieventabel", array("v_aantal"=>$aantalpersonen));
		}

		if($this->arrangement) {
			$totaalbedrag_aleen_acc = $this->tarief_zonder_bk[$aantalpersonen][$aankomstdatum] * $aantalpersonen;
		} else {
			$totaalbedrag_aleen_acc = $this->tarief_zonder_bk[$aankomstdatum];
		}
		$totaalbedrag = $totaalbedrag_aleen_acc + $this->tarief_alleen_bk[$aantalpersonen][$aankomstdatum];

		if( $this->config->voorkant_cms ) {

			$email_text .= date("d/m/Y", $this->unixtime_week[$aankomstdatum]);
			$email_text .= " ";
			$email_text .= $this->accinfo["plaats"]." / ".$this->accinfo["accnaam"]." (".txt("mailcopy-max", "tarieventabel")." ".$this->accinfo["maxaantalpersonen"]." ".($this->accinfo["maxaantalpersonen"]==1 ? txt("persoon", "tarieventabel") : txt("personen", "tarieventabel")).")";
			$email_text .= "\n";
			$email_text .= txt("mailcopy-voor-omschrijving", "tarieventabel").": ".$this->accinfo["url"];
			$email_text .= "\n";
			if($this->accinfo["wzt"]==1) {
				if($aantalpersonen==1) {
					$email_text .= txt("mailcopy-bezetting-1-persoon","tarieventabel");
				} else {
					$email_text .= txt("mailcopy-bezetting-x-personen","tarieventabel", array("v_aantal"=>$aantalpersonen));
				}
				$email_text .= ": € ".number_format($this->tarief_exact[$aantalpersonen][$aankomstdatum], 2, ",", ".")." ".txt("perpersoon", "tarieventabel");
				$email_text .= ", ";
				if($this->arrangement) {
					$email_text .= txt("mailcopy-inclusief-x-daagse-skipas", "tarieventabel", array("v_skipasaantaldagen"=>$this->accinfo["skipas_aantaldagen"], "v_skipasnaam"=>$this->accinfo["skipas_naam"]));
				} else {
					$email_text .= txt("mailcopy-excl-skipassen", "tarieventabel");
				}
				$email_text .= "\n";
			}
			if($aantalpersonen==1) {
				$email_text .= txt("totaalprijs-op-basis-1-persoon","tarieventabel");
			} else {
				$email_text .= txt("totaalprijs-op-basis-x-personen","tarieventabel", array("v_aantal"=>$aantalpersonen));
			}
			$email_text .= ": € ".number_format($totaalbedrag, 2, ",", ".");
			$email_text .= " ".txt("mailcopy-per-week", "tarieventabel");
			$email_text .= "\n";

			$return .= "<span class=\"copy-to-clipboard-icon\" title=\"kopieer mailtekst naar klembord\"></span>";
			$return .= "<textarea class=\"copy-to-clipboard-text\">".wt_he($email_text)."</textarea>";
		}

		$return .= ":";
		$return .= "</div>";

		$return .= "<span class=\"tarieventabel_totaalprijs_right\">&euro;&nbsp;".number_format($totaalbedrag, 2, ",", ".")."</span>";

		// i-icon for specification popup
		$return .= "<div class=\"tarieventabel_totaalprijs_specificatie\" data-active=\"0\" data-aantalpersonen=\"".intval($aantalpersonen)."\"
		data-seizoen_id=\"".$this->week_seizoen_id[$aankomstdatum]."\" data-week=\"".intval($aankomstdatum)."\"></div>";

		// book-button
		$return .= "<button data-aantalpersonen=\"".$aantalpersonen."\" data-week=\"".$aankomstdatum."\">".html("boeknu", "toonaccommodatie")." &raquo;</button>";

		if($this->toon_commissie and $this->commissie[$aankomstdatum]>0) {

			// calculate commission
			$commissie_bedrag = round(($this->commissie[$aankomstdatum]/100) * $totaalbedrag_aleen_acc, 2);

			// show commission
			$return .= "<div class=\"tarieventabel_totaalprijs_commissie\">";
			$return .= "<div class=\"tarieventabel_totaalprijs_left\">";
			$return .= html("wederverkoop_commissie","tarieventabel");
			$return .= " (".number_format($this->commissie[$aankomstdatum],0,",","")."% x &euro;&nbsp;".number_format($totaalbedrag_aleen_acc, 2, ",", ".").")";

			$return .= ":</div>";
			$return .= "<span class=\"tarieventabel_totaalprijs_right\">&euro;&nbsp;".number_format($commissie_bedrag, 2, ",", ".")."</span>";
			$return .= "</div>"; // close .tarieventabel_totaalprijs_commissie

			// link to show/hide commission
			$return .= "<a href=\"#\" data-default=\"".html("wederverkoop_tooncommissie1","tarieventabel")."\" data-hide=\"".html("wederverkoop_tooncommissie2","tarieventabel")."\" class=\"tarieventabel_totaalprijs_toggle_commissie\">".html("wederverkoop_tooncommissie1","tarieventabel")."</a>";

		}

		$return .= "<div class=\"tarieventabel_totaalprijs_opmerking1\">".html("totaalprijs-opgebouwd", "tarieventabel");


		$return .= "</div>";
		$return .= "<div class=\"tarieventabel_totaalprijs_opmerking2\">".html("klik-op-datum-personen", "tarieventabel")."</div>";

		$return .= "</div>"; // close .tarieventabel_totaalprijs

		return $return;

	}

	/**
	 * show the specification of the total price
	 *
	 * @param integer number of persons
	 * @param integer arrival date in unixtime
	 * @return string $return
	 */
	public function specificatie_totaalprijs($aantalpersonen, $aankomstdatum)
	{
		//
		// get specification total amount
		//

		$this->tarieven_uit_database();

		$bijkomendekosten = new bijkomendekosten($this->type_id, "type");
		$bijkomendekosten->setRedis(new wt_redis);
		$bijkomendekosten->seizoen_id = $this->seizoen_id;
		$bijkomendekosten->pre_boeken = true;
		$bijkomendekosten->aantalpersonen = $aantalpersonen;
		$bijkomendekosten->accinfo = $this->accinfo;
		$bijkomendekosten->aankomstdatum = $aankomstdatum;

		$bk = $bijkomendekosten->get_booking_data($gegevens);

		$return .= "<table>";
		$return .= "<tr class=\"tarieventabel_totaalprijs_specificatie_popup_bold\"><td colspan=\"5\">".html("specificatie-totaalprijs", "bijkomendekosten")."</td></tr>";
		$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";

		if($this->arrangement) {
			$totaalbedrag_chalet_nl = $this->tarief_zonder_bk[$aantalpersonen][$aankomstdatum] * $aantalpersonen;
			$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">".$aantalpersonen."</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".html("accommodatieplusskipas", "bijkomendekosten")." (&euro;&nbsp;".$this->toonbedrag($this->tarief_zonder_bk[$aantalpersonen][$aankomstdatum])." ".html("pp", "bijkomendekosten").")</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_chalet_nl, 2, ",", ".")."</td></tr>";
		} else {
			$totaalbedrag_chalet_nl = $this->tarief_zonder_bk[$aankomstdatum];

			$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">1</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".html("accommodatie", "bijkomendekosten")."</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_chalet_nl, 2, ",", ".")."</td></tr>";
		}

		foreach ($bk["aan_chalet_nl"] as $key => $value) {
			if(!$value["bedragonbekend"] and $value["totaalbedrag"]>0) {
				$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">".$value["aantal"]."</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".wt_he($value["naam"]);
				if($value["toonbedrag"]) {
					$return .= " (".wt_he($value["toonbedrag"]).")";
				}
				$return .= "</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($value["totaalbedrag"], 2, ",", ".")."</td></tr>";
				$totaalbedrag_chalet_nl += $value["totaalbedrag"];
			}
		}


		$totaalbedrag += $totaalbedrag_chalet_nl;

		$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";
		$return .= "<tr class=\"tarieventabel_totaalprijs_specificatie_popup_bold\"><td colspan=\"3\">".html("totaal-aan-chalet-nl", "bijkomendekosten", array("v_websitenaam"=>$this->config->websitenaam))."</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_chalet_nl, 2, ",", ".")."</td></tr>";
		$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";


		if(is_array($bk["ter_plaatse"])) {
			foreach ($bk["ter_plaatse"] as $key => $value) {

				if(!$value["bedragonbekend"] and $value["totaalbedrag"]>0) {

					$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">".$value["aantal"]."</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".wt_he($value["naam"]." (".$value["toonbedrag"].")")."</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($value["totaalbedrag"], 2, ",", ".")."</td></tr>";
					$totaalbedrag_ter_plaatse += $value["totaalbedrag"];
				}
			}
			if($totaalbedrag_ter_plaatse>0) {
				$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";
				$return .= "<tr class=\"tarieventabel_totaalprijs_specificatie_popup_bold\"><td colspan=\"3\">".html("totaal-ter-plaatse", "bijkomendekosten")."</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_ter_plaatse, 2, ",", ".")."</td></tr>";
				$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";

				$totaalbedrag += $totaalbedrag_ter_plaatse;

			}
		}
		$return .= "<tr class=\"tarieventabel_totaalprijs_specificatie_popup_bold\"><td colspan=\"3\">";
		if($aantalpersonen==1) {
			$return .= html("totaalprijs-op-basis-1-persoon","bijkomendekosten");
		} else {
			$return .= html("totaalprijs-op-basis-x-personen","bijkomendekosten", array("v_aantal"=>$aantalpersonen));
		}
		$return .= "</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag, 2, ",", ".")."</td></tr>";

		$return .= "</table>";

		return $return;
	}

	/**
	 * show the specification of the total price to be shown below the pricetable
	 *
	 * @param integer number of persons
	 * @param integer arrival date in unixtime
	 * @return string $return
	 */
	public function specificatie_totaalprijs_below_pricetable($aantalpersonen, $aankomstdatum)
	{
		//
		// get specification total amount
		//

		global $isMobile;

		$this->tarieven_uit_database();

		$bijkomendekosten = new bijkomendekosten($this->type_id, "type");
		$bijkomendekosten->setRedis(new wt_redis);

		if (preg_match("@,@", $this->seizoen_id)) {
			// multiple seasons: get correct one
			$db = new DB_sql;
			$db->query("SELECT DISTINCT seizoen_id FROM tarief WHERE type_id='".intval($this->type_id)."' AND week='".intval($aankomstdatum)."';");
			if ($db->next_record()) {
				$seizoen_id = $db->f( "seizoen_id" );
			}
		} else {
			$seizoen_id = $this->seizoen_id;
		}

		$bijkomendekosten->seizoen_id = $seizoen_id;
		$bijkomendekosten->pre_boeken = true;
		$bijkomendekosten->aantalpersonen = $aantalpersonen;
		$bijkomendekosten->accinfo = $this->accinfo;
		$bijkomendekosten->aankomstdatum = $aankomstdatum;

		$bk = $bijkomendekosten->get_booking_data($gegevens);

		$return .= "<div class=\"tarieventabel_totaalprijs tarieventabel_totaalprijs_exclude_bkk\">";

		$return .= "<table>";
		$return .= "<tr class=\"\"><td colspan=\"5\">".ucfirst(datum("DAG D MAAND JJJJ", $this->unixtime_week[$aankomstdatum], $this->config->taal))." ";
		if ($aantalpersonen==1) {
			$return .= html("met-1-persoon", "tarieventabel");
		} else {
			$return .= html("met-x-personen", "tarieventabel", array("v_aantal"=>$aantalpersonen));
		}
		$return .= ":</td></tr>";
		$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";

		if($this->arrangement) {
			$totaalbedrag_chalet_nl = $this->tarief_zonder_bk[$aantalpersonen][$aankomstdatum] * $aantalpersonen;
			$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">".$aantalpersonen."</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".html("accommodatieplusskipas", "bijkomendekosten")." (&euro;&nbsp;".$this->toonbedrag($this->tarief_zonder_bk[$aantalpersonen][$aankomstdatum])." ".html("pp", "bijkomendekosten").")</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_chalet_nl, 2, ",", ".")."</td></tr>";
		} else {
			$totaalbedrag_chalet_nl = $this->tarief_zonder_bk[$aankomstdatum];

			$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">1</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".html("accommodatie", "bijkomendekosten")."</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_chalet_nl, 2, ",", ".")."</td></tr>";
		}

		$totaalbedrag_aleen_acc = $totaalbedrag_chalet_nl;

		foreach ($bk["aan_chalet_nl"] as $key => $value) {
			if(!$value["bedragonbekend"] and $value["totaalbedrag"]>0) {
				$return .= "<tr><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">".$value["aantal"]."</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".wt_he($value["naam"]);
				if($value["toonbedrag"]) {
					$return .= " (".wt_he($value["toonbedrag"]).")";
				}
				$return .= "</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($value["totaalbedrag"], 2, ",", ".")."</td></tr>";
				$totaalbedrag_chalet_nl += $value["totaalbedrag"];
			}
		}


		$totaalbedrag += $totaalbedrag_chalet_nl;

		$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";
		$return .= "<tr class=\"tarieventabel_totaalprijs_specificatie_popup_bold\"><td colspan=\"2\">&nbsp;</td><td>".html("totaal-aan-chalet-nl", "bijkomendekosten", array("v_websitenaam"=>$this->config->websitenaam));


		if( $this->config->voorkant_cms ) {

			$email_text .= date("d/m/Y", $this->unixtime_week[$aankomstdatum]);
			$email_text .= " ";
			$email_text .= $this->accinfo["plaats"]." / ".$this->accinfo["accnaam"]." (".txt("mailcopy-max", "tarieventabel")." ".$this->accinfo["maxaantalpersonen"]." ".($this->accinfo["maxaantalpersonen"]==1 ? txt("persoon", "tarieventabel") : txt("personen", "tarieventabel")).")";
			$email_text .= "\n";
			$email_text .= txt("mailcopy-voor-omschrijving", "tarieventabel").": ".$this->accinfo["url"];
			$email_text .= "\n";
			if($this->arrangement) {
				if($aantalpersonen==1) {
					$email_text .= txt("mailcopy-bezetting-1-persoon","tarieventabel");
				} else {
					$email_text .= txt("mailcopy-bezetting-x-personen","tarieventabel", array("v_aantal"=>$aantalpersonen));
				}
				$email_text .= ": € ".number_format($this->tarief_exact[$aantalpersonen][$aankomstdatum], 2, ",", ".")." ".txt("perpersoon", "tarieventabel");
				$email_text .= ", ";
				$email_text .= txt("mailcopy-inclusief-x-daagse-skipas", "tarieventabel", array("v_skipasaantaldagen"=>$this->accinfo["skipas_aantaldagen"], "v_skipasnaam"=>$this->accinfo["skipas_naam"]));
			} else {
				$email_text .= txt("mailcopy-huurprijs-acc", "tarieventabel", array("v_bedrag"=>number_format($totaalbedrag_aleen_acc, 2, ",", ".")));
			}
			$return .= "<span class=\"copy-to-clipboard-icon\" title=\"kopieer mailtekst naar klembord\"></span>";
			$return .= "<textarea class=\"copy-to-clipboard-text\">".wt_he($email_text)."</textarea>";
		}

		$return .= "</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($totaalbedrag_chalet_nl, 2, ",", ".")."</td></tr>";

		// book-button
		$return .= "<tr><td colspan=\"3\">&nbsp;</td><td colspan=\"2\"><button data-aantalpersonen=\"".$aantalpersonen."\" data-week=\"".$aankomstdatum."\">".html("boeknu", "toonaccommodatie")." &raquo;</button></td></tr>";

		if(!$isMobile) {
			$return .= "<tr><td colspan=\"5\" class=\"tarieventabel_totaalprijs_opmerking2\">".html("klik-op-datum-personen", "tarieventabel")."</td></tr>";
		}

		$terPlaatseNumerOfItems = 0;
		if(is_array($bk["ter_plaatse"])) {
			foreach ($bk["ter_plaatse"] as $key => $value) {

				if(!$value["bedragonbekend"] and $value["totaalbedrag"]>0) {

					if (!$isTerPlaatseHeaderShown) {
						$return .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";
						$return .= "<tr class=\"tarieventabel_totaalprijs_exclude_bkk_show_bkk\"><td colspan=\"5\">".html("verplichttevoldoen", "tarieventabel").":</td></tr>";
						$return .= "<tr style=\"height:6px;\"><td colspan=\"5\"></td></tr>";

						$isTerPlaatseHeaderShown = true;
					}

					$return .= "<tr class=\"tarieventabel_totaalprijs_exclude_bkk_show_bkk\"><td class=\"tarieventabel_totaalprijs_specificatie_aantal\">".$value["aantal"]."</td><td>x</td><td class=\"tarieventabel_totaalprijs_specificatie_naam\">".wt_he($value["naam"]." (".$value["toonbedrag"].")")."</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">".number_format($value["totaalbedrag"], 2, ",", ".")."</td></tr>";
					$totaalbedrag_ter_plaatse += $value["totaalbedrag"];

					$terPlaatseNumerOfItems ++;
				}
			}
			if($totaalbedrag_ter_plaatse>0 and $terPlaatseNumerOfItems>1) {

				$return .= "<tr style=\"height:6px;\"><td colspan=\"5\"></td></tr>";

				$totaal_ter_plaatse = "<tr class=\"tarieventabel_totaalprijs_exclude_bkk_show_bkk\"><td colspan=\"2\">&nbsp;</td><td>[b]".html("totaal-ter-plaatse", "bijkomendekosten")."[/b]</td><td>&nbsp;&euro;&nbsp;</td><td class=\"tarieventabel_totaalprijs_specificatie_popup_bedrag\">[b]".number_format($totaalbedrag_ter_plaatse, 2, ",", ".")."[/b]</td></tr>";

				$tags    = ['f' => ['[b]', '[/b]'], 'r' => ['<b>', '</b>']];
				$return .= ($this->config->website === 'D' ? str_replace($tags['f'], $tags['r'], $totaal_ter_plaatse) : str_replace($tags['f'], '', $totaal_ter_plaatse));

				if ($this->config->website === 'D') {

					$totaal_vakantie = number_format($totaalbedrag  + $totaalbedrag_ter_plaatse, 2, ',', '.');
					$return .= '<tr><td colspan="5">&nbsp;</td></tr>';
					$return .= '<tr class="tarieventabel_totaalprijs_exclude_bkk_show_bkk"><td colspan="2">&nbsp;</td><td>' . html('totaal-vakantie', 'bijkomendekosten') . ' (&euro; ' . number_format($totaalbedrag, 2, ',', '.') . ' + &euro; ' . number_format($totaalbedrag_ter_plaatse, 2, ',', '.') . ') </td><td>&nbsp;&euro;&nbsp;</td>';
					$return .= '<td class="tarieventabel_totaalprijs_specificatie_popup_bedrag">' . $totaal_vakantie . '</td></tr>';
				}

				$totaalbedrag += $totaalbedrag_ter_plaatse;
			}

			if ($totaalbedrag_ter_plaatse > 0) {

				if ($this->config->website === 'D' && !isset($totaal_vakantie)) {

					$totaal_vakantie = number_format($totaalbedrag  + $totaalbedrag_ter_plaatse, 2, ',', '.');
					$return .= '<tr><td colspan="5">&nbsp;</td></tr>';
					$return .= '<tr class="tarieventabel_totaalprijs_exclude_bkk_show_bkk"><td colspan="2">&nbsp;</td><td>' . html('totaal-vakantie', 'bijkomendekosten') . ' (&euro; ' . number_format($totaalbedrag, 2, ',', '.') . ' + &euro; ' . number_format($totaalbedrag_ter_plaatse, 2, ',', '.') . ') </td><td>&nbsp;&euro;&nbsp;</td>';
					$return .= '<td class="tarieventabel_totaalprijs_specificatie_popup_bedrag">' . $totaal_vakantie . '</td></tr>';
				}
			}
		}

		$return .= "</table>";


		if($this->toon_commissie and $this->commissie[$aankomstdatum]>0) {

			// calculate commission
			$commissie_bedrag = round(($this->commissie[$aankomstdatum]/100) * $totaalbedrag_aleen_acc, 2);

			// show commission
			$return .= "<br />";
			$return .= "<div class=\"tarieventabel_totaalprijs_commissie\">";
			$return .= "<div class=\"tarieventabel_totaalprijs_left\">";
			$return .= html("wederverkoop_commissie","tarieventabel");
			$return .= " (".number_format($this->commissie[$aankomstdatum],0,",","")."% x &euro;&nbsp;".number_format($totaalbedrag_aleen_acc, 2, ",", ".").")";

			$return .= ":</div>";
			$return .= "<span class=\"tarieventabel_totaalprijs_right\">&euro;&nbsp;".number_format($commissie_bedrag, 2, ",", ".")."</span>";
			$return .= "</div>"; // close .tarieventabel_totaalprijs_commissie

			// link to show/hide commission
			$return .= "<a href=\"#\" data-default=\"".html("wederverkoop_tooncommissie1","tarieventabel")."\" data-hide=\"".html("wederverkoop_tooncommissie2","tarieventabel")."\" class=\"tarieventabel_totaalprijs_toggle_commissie\">".html("wederverkoop_tooncommissie1","tarieventabel")."</a>";

		}



		$return .= "</div>"; // close .tarieventabel_totaalprijs

		return $return;
	}

	/**
	 * create html for the top of the price table
	 *
	 * @return string $return
	 */
	private function tabel_top()
	{

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
		if($this->arrangement) {
			$return .= ", ".html("perpersooninclskipas","tarieventabel");
		} else {
			$return .= ", ".html("perpersoon","tarieventabel");
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
			if ($this->config->acceptatie_testserver) {
				$return .= "http://test.chalet.nl";
			} elseif ($this->config->website<>"C" and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") {
				$return .= "https://www.chalet.nl";
			}
			if($this->config->lokale_testserver) {
				$return.="/chalet";
			}

			if(preg_match("@,@",$this->seizoen_id,$regs)) {
				$seizoenid_array=explode(",",$this->seizoen_id);
				$seizoenid=max($seizoenid_array);
			} else {
				$seizoenid=$this->seizoen_id;
			}

			$return.=ereg_replace("[a-z]+/$","",$this->config->path)."cms_tarieven.php?sid=".$seizoenid."&tid=".$this->type_id."&from=".urlencode("http".($_SERVER["HTTPS"]=="on" ? "s" : "")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."#prijsinformatie")."\" title=\"tarieven bewerken\">";
			$return.="<img src=\"".$this->config->path."pic/class.cms_edit.gif\" border=\"0\" alt=\"Tarieven bewerken\" width=\"14\" height=\"14\"></a>";
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
			$return.="<option value=\"euro\"".($this->actieve_valuta!="gbp" ? " selected" : "")." data-image=\"".$this->config->path."pic/flag-eu.png\">".html("euro","tarieventabel")."&nbsp;&nbsp;</option>";
			$return.="<option value=\"gbp\"".($this->actieve_valuta=="gbp" ? " selected" : "")." data-image=\"".$this->config->path."pic/flag-uk.png\">".html("gbp","tarieventabel")."&nbsp;&nbsp;</option>";
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

	/**
	 * create html for the middle content of the price table
	 *
	 * @return string $return
	 */
	private function tabel_content()
	{

		if($this->tarief) {

			//
			// kolomnamen
			//

			// bepalen welke aantallen personen direct zichtbaar moeten zijn

			$this->max_personen=max(array_keys($this->aantalpersonen_array));
			$this->min_personen=min(array_keys($this->aantalpersonen_array));

			if($this->aantalpersonen) {
				// valt het aantal personen binnen de capaciteit van deze accommodatie?
				if($this->aantalpersonen<$this->min_personen or $this->aantalpersonen>$this->max_personen) {
					// zo niet: aantal personen niet gebruiken bij tonen tarieventabel
					unset($this->aantalpersonen);
				}
			}

			if($this->aantalpersonen) {
				$this->max_personen_tonen=$this->aantalpersonen+2;
				$this->min_personen_tonen=$this->aantalpersonen-2;
			} else {
				$this->max_personen_tonen=max(array_keys($this->aantalpersonen_array));
				$this->min_personen_tonen=$this->max_personen_tonen-4;
			}


			$return.="<table cellspacing=\"0\" cellpadding=\"0\" class=\"tarieventabel_border tarieventabel_titels_links\">";
			$return.="<tr class=\"tarieventabel_maanden".($this->toon_interne_informatie ? "" : " tarieventabel_maanden_top")."\"><td class=\"tarieventabel_maanden_leeg\">&nbsp;</td></tr>";

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
							"inkoopkorting_percentage"     => "Inkoopkorting acc %",
							"aanbieding_acc_percentage"    => "Korting acc %",
							"aanbieding_skipas_percentage" => "Korting skipas %",
							"inkoopkorting_euro"           => "Inkoopkorting acc €",
							"aanbieding_acc_euro"          => "Korting acc €",
							"aanbieding_skipas_euro"       => "Korting skipas €"
						);
					} else {
						$this->korting_doorlopen=array(
							"inkoopkorting_percentage"  => "Inkoopkorting %",
							"aanbieding_acc_percentage" => "Korting %",
							"inkoopkorting_euro"        => "Inkoopkorting €",
							"aanbieding_acc_euro"       => "Korting €"
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
				if($this->arrangement) {
					$return.=html("korting","tarieventabel");
				} else {
					$return.=html("kortingaccommodatie","tarieventabel");
				}
				$return.="</td></tr>";
			}


			// regels met aantal personen tonen
			foreach ($this->aantalpersonen_array as $key => $value) {
				$return.="<tr class=\"".trim(($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? "tarieventabel_verbergen" : "").($this->aantalpersonen==$key && !$this->aankomstdatum ? " tarieventabel_tarieven_gekozen" : ""))."\"><td>".$key."&nbsp;".($key==1 ? html("persoon","tarieventabel") : html("personen","tarieventabel"))."</td></tr>";
			}


			$return.="</table>";

			$return .= $this->tabel_tarieven();

			// minder personen open/dichtklappen
			$return.="<div class=\"tarieventabel_toggle_toon_verberg\">";
			if($this->max_personen_tonen<$this->max_personen or $this->min_personen_tonen>$this->min_personen) {
				$return.="<a href=\"#\" data-default=\"".html("minderpersonen","tarieventabel")."\" data-hide=\"".html("minderpersonen_verbergen","tarieventabel")."\"><i class=\"icon-chevron-sign-down\"></i>&nbsp;<span>".html("minderpersonen","tarieventabel")."</span></a>";
			} else {
				$return.="&nbsp;";
			}
			$return.="</div>";


			if ( $this->bk_fetched ) {
				//
				// show "click for total"-message
				//
				if( !$this->config->isMobile ) {
					$return .= "<div class=\"tarieventabel_totaal_message\">";
					$return .= html("klik-voor-totaalbedrag", "tarieventabel");
					$return .= "</div>";
				}
			} else {
				//
				// redis down? Show error message
				//
				// $return .= "<div class=\"tarieventabel_redis_error_message\">";
				// $return .= html("melding-redis-down", "tarieventabel");
				// $return .= "</div>";

				// trigger_error( "no redis bijkomendekosten available",E_USER_NOTICE );
			}


			// legenda
			$return.="<div class=\"tarieventabel_legenda\">";

			$return .= $this->button_to_other_website($this->accinfo["zomerwinterkoppeling_accommodatie_id"]);

			if($this->aanbieding_actief) {
				$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_legenda_kleurenblokje_aanbieding tarieventabel_tarieven_aanbieding\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_aanbieding","tarieventabel")."</div>";
			}
			if($this->arrangement) {
				if($this->aantalpersonen and $this->aankomstdatum) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum_aantal_personen","tarieventabel")."</div>";
				} elseif($this->aantalpersonen and !$this->aankomstdatum) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_aantal_personen","tarieventabel")."</div>";
				} elseif($this->aankomstdatum and !$this->aantalpersonen) {
					$return.="<div><span class=\"tarieventabel_legenda_kleurenblokje tarieventabel_tarieven_gekozen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = ".html("legenda_gekozen_datum","tarieventabel")."</div>";
				}
			} else {
				if($this->aankomstdatum) {
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

				$aankomst_weekdag = mktime(0, 0, 0, 1, 3+$this->accinfo["aankomst_plusmin"], 2015); // = Saturday +- aankomst_plusmin
				$vertrek_weekdag = mktime(0, 0, 0, 1, 3+$this->accinfo["vertrek_plusmin"], 2015); // = Saturday +- vertrek_plusmin

				foreach ($this->aantalnachten as $key_datum => $value_nachten) {
					if($key_datum>time()) {

						$eind=mktime(0,0,0,date("m",$this->unixtime_week[$key_datum]),date("d",$this->unixtime_week[$key_datum])+$value_nachten,date("Y",$this->unixtime_week[$key_datum]));

						$include_row = false;
						$dag_van_de_week_afwijkend = false;
						if($this->accinfo["aankomst_plusmin"] or $this->accinfo["vertrek_plusmin"]) {
							if(date("w", $aankomst_weekdag)<>date("w", $this->unixtime_week[$key_datum]) or date("w", $vertrek_weekdag)<>date("w", $eind)) {
								$include_row = true;
								$dag_van_de_week_afwijkend = true;
							}
						} else {
							$include_row = true;
						}

						if($value_nachten and ($value_nachten<>7 or $this->dag_van_de_week_afwijkend[$key_datum] or $dag_van_de_week_afwijkend)) {
							if($this->unixtime_week[$key_datum]) {
								if($include_row) {
									$afwijkend .= "<div>".DATUM("DAG D MND JJJJ", $this->unixtime_week[$key_datum], $this->config->taal)." - ".DATUM("DAG D MND JJJJ", $eind, $this->config->taal)." (".$value_nachten." ".($value_nachten==1 ? html("nacht", "vars") : html("nachten", "vars")).")</div>";
								}
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

	/**
	 * create html for the bottom of the price table
	 *
	 * @return string $return
	 */
	private function tabel_bottom()
	{

		return $return;

	}


	/**
	 * check if an arrival date is at the start or end of a season
	 *
	 * @return boolean
	 */
	private function seizoenswissel($week, $begin=true)
	{
		// kijk of een week aan het begin of eind van een seizoen ligt
		if($begin) {
			$checkweek=mktime(0,0,0,date("m",$week),date("d",$week)-7,date("Y",$week));
		} else {
			$checkweek=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}

		if ($this->config->seizoentype==2) {
			// summer: seasons change 31 December / 1 January
			if($this->seizoen_counter>1 and date("Y", $week)<>date("Y", $checkweek)) {
				return true;
			} else {
				return false;
			}
		} else {

			if($this->seizoen_counter>1 and !$this->binnen_seizoen[date("Ym",$checkweek)] and (($begin and $checkweek>$this->begin) or (!$begin and $checkweek<$this->eind))) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * create html for the date row of the price table
	 *
	 * @param integer counter (is this the 1st or 2nd time the date row is shown?)
	 * @return string $return
	 */
	private function datum_headers($data_counter=0)
	{

		// regel met maanden
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
			$return .= DATUM("MAAND JJJJ",$unixtime,$this->config->taal);
			// $return .= " - ".$kolomteller."==".count($this->maand);

			$return .= "</td>";
		}
		$return.="</tr>";

		// regel met aankomstdatum
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
			// if($this->aankomstdatum and $this->aankomstdatum==$key) {
			// 	$class.=" tarieventabel_tarieven_kolom_actieve_datum";
			// }
			$return.="<td class=\"".trim($class)."\">".$value."</td>";
		}
		$return.="</tr>";

		// regel met dag van de week
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content tarieventabel_datumbalk_minder_opvallend\">";
		$kolomteller=0;
		foreach ($this->dag_van_de_week as $key => $value) {

			$kolomteller++;

			$class = "";
			$star = "";

			if($this->seizoenswissel($key,true)) {
				$class.=" tarieventabel_tarieven_kolom_begin_seizoen";
				$this->datadiv["begin_seizoen"] = $kolomteller;
				$this->datadiv["last_seizoen_id"] = $this->last_seizoen_id;
			}
			if($this->seizoenswissel($key,false)) {
				$class.=" tarieventabel_tarieven_kolom_eind_seizoen";
				$this->datadiv["eind_seizoen"] = $kolomteller;
				$this->datadiv["first_seizoen_id"] = $this->first_seizoen_id;
			}

			if($this->dag_van_de_week_afwijkend[$key] and $key>time()) {
				$class .= " tarieventabel_datumbalk_opvallend";
				if($this->show_afwijkend_legenda and $this->afwijkend_aantal_nachten) {
					$star = "*";
				}
			}

			$return.="<td class=\"".trim($class)."\">".$value.$star."</td>";
		}
		$return.="</tr>";

		// regel met aantal nachten
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

	/**
	 * create html for actual prices (in a <table>) of the price table
	 *
	 * @return string $return
	 */
	private function tabel_tarieven()
	{

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
							$return.="&nbsp;<img src=\"".$this->config->path."pic/vinkje.gif\">&nbsp;";
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

		// regel met korting
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

		//
		// tarieven-cellen tonen
		//

		foreach ($this->aantalpersonen_array as $key => $value) {

			$return.="<tr class=\"tarieventabel_tarieven".($key<$this->min_personen_tonen||$key>$this->max_personen_tonen ? " tarieventabel_verbergen" : "").($this->aantalpersonen==$key && !$this->aankomstdatum ? " tarieventabel_tarieven_gekozen" : "")."\" data-aantalpersonen=\"".$key."\">";
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

				if($this->aantalpersonen==$key and $this->aankomstdatum==$key2) {
					$class.=" tarieventabel_tarieven_gekozen";
				} elseif($this->aantalpersonen==$key and !$this->aankomstdatum) {
					$class.=" tarieventabel_tarieven_gekozen";
				} elseif($this->aankomstdatum==$key2 and !$this->aantalpersonen) {
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

					if($this->tarief[$key][$key2]>=10000) {
						$te_tonen_bedrag["euro"]=number_format($this->tarief[$key][$key2],0,",",".");
					} else {
						$te_tonen_bedrag["euro"]=number_format($this->tarief[$key][$key2],0,",","");
					}

					if($this->toonkorting_1[$key2] or $this->toonkorting_2[$key2] or $this->toonkorting_3[$key2]) {
						$return.="<div class=\"tarieventabel_tarieven_aanbieding\"";
					} else {
						$return.="<div";
					}

					if($this->meerdere_valuta) {
						$bedrag_andere_valuta["gbp"]=round($this->wisselkoers["gbp"]*$this->tarief[$key][$key2]);

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

					$this->tarieven_getoond[$this->week_seizoen_id[$key2]]=true;

					$return.="</div>";
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

	/**
	 * format amounts:
	 * - 2 decimals
	 * - comma as decimal point separator
	 * - dot as the thousands separator
	 * ",00" becomes ",-"
	 *
	 * @param float
	 * @return string $return
	 */
	private function toonbedrag($bedrag)
	{
		$return = number_format($bedrag, 2, ",", ".");
		$return = preg_replace("@,00$@", ",-", $return);
		return $return;
	}


	/**
	 * create html to show additional costs (bijkomendekosten) for all active seasons
	 *
	 * @return string $return
	 */
	public function show_bijkomendekosten()
	{

		if(!$this->first_seizoen_id) {
			// trigger_error("missing first_season_id",E_USER_NOTICE);
		}

		$bijkomendekosten = new bijkomendekosten($this->type_id, "type");
		$bijkomendekosten->setRedis(new wt_redis);
		$bijkomendekosten->arrangement = $this->arrangement;
		$bijkomendekosten->accinfo = $this->accinfo;
		if($this->seizoen_counter>1) {

			foreach ($this->seizoeninfo as $seizoen_id => $value) {
				// multiple seasons
				$bijkomendekosten->seizoen_id = $seizoen_id;
				$toelichting_season[$seizoen_id] = $bijkomendekosten->toon_type();

				if($check_for_doubles and $check_for_doubles==$toelichting_season[$seizoen_id]) {
					$both_season_are_the_same = true;
				}

				$check_for_doubles = $toelichting_season[$seizoen_id];
			}

			if($both_season_are_the_same) {
				$toelichting = $check_for_doubles;
			} else {

				$toelichting .= "<form><select class=\"tarieventabel_toelichting_switch_seasons\">";
				foreach ($toelichting_season as $seizoen_id => $value) {
					$season_counter++;
					$toelichting .= "<option value=\"".$seizoen_id."\"".($season_counter==1 ? " selected" : "").">&nbsp;".wt_he($this->seizoeninfo[$seizoen_id]["naam"])."&nbsp;</option>";
				}
				$toelichting .= "</select></form>";

				$toelichting .= "<div class=\"tarieventabel_toelichting_all_seasons\">";

				$toelichting .= "<div class=\"tarieventabel_toelichting_active_season\"></div>";

				foreach ($toelichting_season as $seizoen_id => $value) {

					$toelichting .= "<div class=\"tarieventabel_toelichting_one_season\" data-seizoen_id=\"".$seizoen_id."\">";
					$toelichting .= $value;
					$toelichting .= "</div>";

				}
				$toelichting .= "</div>";
			}


		} else {
			// one season
			$bijkomendekosten->seizoen_id = $this->first_seizoen_id;
			$toelichting = $bijkomendekosten->toon_type();
		}

		if($toelichting) {
			$return .= "<div class=\"tarieventabel_toelichting\">";

			// info totaalprijs
			$return .= "<div class=\"tarieventabel_totaalprijs_wrapper\">";
			if(constant("include_bkk")===true) {
				// include bkk in price
				if( $this->aantalpersonen and $this->aantalpersonen<=$this->accinfo["maxaantalpersonen"] and $this->aankomstdatum and $this->tarief[$this->aantalpersonen][$this->aankomstdatum]>0 ) {
					$return .= $this->info_totaalprijs($this->aantalpersonen, $this->aankomstdatum);
				}
			} else {
				// exclude bkk from price
				if ($this->aantalpersonen and $this->aantalpersonen<=$this->accinfo["maxaantalpersonen"]) {
					$specificatie_totaalprijs_aantalpersonen = $this->aantalpersonen;
				} else {
					$specificatie_totaalprijs_aantalpersonen = $this->accinfo["maxaantalpersonen"];
				}


				if ($this->aankomstdatum) {
					$specificatie_totaalprijs_aankomstdatum = $this->aankomstdatum;
				} else {
					if (is_array($this->tarief[$specificatie_totaalprijs_aantalpersonen])) {
						$cheapest_week = min(array_keys($this->tarief[$specificatie_totaalprijs_aantalpersonen], min($this->tarief[$specificatie_totaalprijs_aantalpersonen])));
						if ($cheapest_week>0) {
							$specificatie_totaalprijs_aankomstdatum = $cheapest_week;
						}
					}
				}
				if( $specificatie_totaalprijs_aantalpersonen and $specificatie_totaalprijs_aantalpersonen<=$this->accinfo["maxaantalpersonen"] and $specificatie_totaalprijs_aankomstdatum and $this->tarief[$specificatie_totaalprijs_aantalpersonen][$specificatie_totaalprijs_aankomstdatum]>0 ) {
					$return .= $this->specificatie_totaalprijs_below_pricetable($specificatie_totaalprijs_aantalpersonen, $specificatie_totaalprijs_aankomstdatum);
				}
			}
			$return .= "</div>";

			$return .= "<div class=\"tarieventabel_toelichting_inner\">";
			$return .= $toelichting;
			$return .= "</div>"; // afsluiten .tarieventabel_toelichting_inner

			$return .= "</div>"; // afsluiten .tarieventabel_toelichting
		}

		return $return;

	}

	/**
	 * get all prices from the database
	 *
	 * @return void
	 */
	private function tarieven_uit_database()
	{

		if(!$this->tarieven_uit_database_done) {

			global $accinfo;

			$db = new DB_sql;
			$db2 = new DB_sql;

			$bijkomendekosten = new bijkomendekosten;
			$bijkomendekosten->setRedis(new wt_redis);

			// Accinfo
			if($accinfo) {
				$this->accinfo=$accinfo;
			} else {
				$this->accinfo=accinfo($this->type_id);
			}

			// Chaletsinvallandry: correct seizoentype
			if($this->config->websitetype==6 and isset($this->accinfo["wzt"])) {
				$this->config->seizoentype=$this->accinfo["wzt"];
			}

			if($this->accinfo["toonper"]==3 or $this->config->wederverkoop) {

			} else {
				$this->arrangement=true;
			}


			if($this->meerdere_valuta) {
				$db->query("SELECT wisselkoers_pond FROM diverse_instellingen WHERE diverse_instellingen_id=1;");
				if($db->next_record()) {
					$this->wisselkoers["gbp"]=$db->f("wisselkoers_pond");
				}
			}

			// Controle op vertrekdagaanpassing?
			$vertrekdag_object = new vertrekdagaanpassing($this->type_id);
			$vertrekdag = $vertrekdag_object->get_dates()[$this->type_id];

			// aflopen_allotment uit calculatiesjabloon halen
			$db2->query("SELECT week, aflopen_allotment FROM calculatiesjabloon_week WHERE seizoen_id IN (".$this->seizoen_id.") AND leverancier_id='".intval($this->accinfo["leverancierid"])."';");
			while($db2->next_record()) {
				if($db2->f("aflopen_allotment")<>"") $this->aflopen_allotment[$db2->f("week")]=$db2->f("aflopen_allotment");
			}

			// seizoensgegevens uit database halen
			$db->query("SELECT s.seizoen_id, UNIX_TIMESTAMP(s.begin) AS begin, UNIX_TIMESTAMP(s.eind) AS eind, s.naam".$this->config->ttv." AS naam FROM seizoen s WHERE s.type='".$this->config->seizoentype."' AND s.seizoen_id IN (".$this->seizoen_id.") AND s.tonen>1 AND s.eind>NOW() ORDER BY s.begin, s.eind;");
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

					// seizoen_id bij een bepaalde week bepalen
					$this->week_seizoen_id[$db->f("week")]=$db->f("seizoen_id");


					if(!$seizoen_cache_fetched[$db->f("seizoen_id")]) {
						$seizoen_cache_fetched[$db->f("seizoen_id")] = true;

						$bk_add_to_price = $bijkomendekosten->get_type_from_cache_all_persons($this->type_id, $this->config->seizoentype, $db->f("seizoen_id"), $this->accinfo["maxaantalpersonen"], true);
						$bk_add_to_price_total = $bijkomendekosten->get_type_from_cache_all_persons($this->type_id, $this->config->seizoentype, $db->f("seizoen_id"), $this->accinfo["maxaantalpersonen"], false);

						if( is_array($bk_add_to_price) and max($bk_add_to_price)>0 ) {
							$this->bk_fetched = true;
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
						// Korting bepalen om intern te kunnen tonen
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

						$this->tarief_zonder_bk[$db->f("personen")][$db->f("week")]=$db->f("prijs");
						$this->tarief_alleen_bk[$db->f("personen")][$db->f("week")] = $bk_add_to_price_total[$db->f("personen")];

						// add bijkomende kosten
						$this->tarief[$db->f("personen")][$db->f("week")] += $bk_add_to_price[$db->f("personen")];

						$this->tarief_exact[$db->f("personen")][$db->f("week")] = $this->tarief[$db->f("personen")][$db->f("week")];

						// round with ceil()
						$this->tarief[$db->f("personen")][$db->f("week")] = ceil($this->tarief[$db->f("personen")][$db->f("week")]);

						$this->aantalpersonen_array[$db->f("personen")]=true;

						if($db->f("week")>time()) {

							if($db->f("aanbiedingskleur_korting") and ($db->f("aanbieding_acc_percentage")>0 or $db->f("aanbieding_acc_euro")>0 or $db->f("aanbieding_skipas_percentage")>0 or $db->f("aanbieding_skipas_euro")>0)) {

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

				if($this->config->wederverkoop) {
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

					if(!$seizoen_cache_fetched[$db->f("seizoen_id")]) {
						$seizoen_cache_fetched[$db->f("seizoen_id")] = true;

						$bk_add_to_price = $bijkomendekosten->get_type_from_cache_all_persons($this->type_id, $this->config->seizoentype, $db->f("seizoen_id"), $this->accinfo["maxaantalpersonen"], false);

						if( is_array($bk_add_to_price) and max($bk_add_to_price)>0 ) {
							$this->bk_fetched = true;
						}

						// surcharge extra persons
						$this->bk_add_to_price_per_person_per_week = $bijkomendekosten->get_type_from_cache_all_persons_all_weeks($this->type_id, $this->config->seizoentype);

						// toon losse accommodatie per persoon
						if(!$this->aantalpersonen_array and is_array($bk_add_to_price)) {
							foreach ($bk_add_to_price as $key => $value) {
								$this->aantalpersonen_array[$key] = true;
							}
							krsort($this->aantalpersonen_array);
						}
					}

					if($db->f("week")>=time() and $db->f("c_bruto")>0) {
						$this->tarief_ingevoerd[$db->f("week")] = true;
					}

					// seizoen_id bij een bepaalde week bepalen
					$this->week_seizoen_id[$db->f("week")]=$db->f("seizoen_id");

					// Voorraad bepalen t.b.v. Chalet-medewerkers
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
						// Korting bepalen om intern te kunnen tonen
						if($db->f("inkoopkorting_percentage")>0) $this->korting["inkoopkorting_percentage"][$db->f("week")]=$db->f("inkoopkorting_percentage");
						if($db->f("inkoopkorting_euro")>0) $this->korting["inkoopkorting_euro"][$db->f("week")]=$db->f("inkoopkorting_euro");
						if($db->f("aanbieding_acc_percentage")>0) $this->korting["aanbieding_acc_percentage"][$db->f("week")]=$db->f("aanbieding_acc_percentage");
						if($db->f("aanbieding_acc_euro")>0) $this->korting["aanbieding_acc_euro"][$db->f("week")]=$db->f("aanbieding_acc_euro");
					}

					unset($temp_beschikbaar, $temp_bruto, $temp_verkoop_site);
					if($this->config->wederverkoop) {
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
						// Korting bepalen om intern te kunnen tonen
						if($db->f("inkoopkorting_percentage")<>0) $korting["inkoopkorting_percentage"][$db->f("week")]=$db->f("inkoopkorting_percentage");
						if($db->f("inkoopkorting_euro")<>0) $korting["inkoopkorting_euro"][$db->f("week")]=$db->f("inkoopkorting_euro");
						if($db->f("aanbieding_acc_percentage")<>0) $korting["aanbieding_acc_percentage"][$db->f("week")]=$db->f("aanbieding_acc_percentage");
						if($db->f("aanbieding_acc_euro")<>0) $korting["aanbieding_acc_euro"][$db->f("week")]=$db->f("aanbieding_acc_euro");
					}

					if($db->f("week")>=time() and $temp_verkoop_site>0 and $temp_beschikbaar and $temp_bruto>0) {

						$this->tarief_zonder_bk[$db->f("week")]=$temp_verkoop_site;

						if(is_array($bk_add_to_price)) {
							foreach ($bk_add_to_price as $key => $value) {

								// surcharge extra persons
								if($this->bk_add_to_price_per_person_per_week[$key][$db->f("week")]) {
									$bk_add_to_price_total = $this->bk_add_to_price_per_person_per_week[$key][$db->f("week")];
								} else {
									$bk_add_to_price_total = 0;
								}

								$this->tarief[$key][$db->f("week")] = ($temp_verkoop_site + $value + $bk_add_to_price_total) / $key;

								$this->tarief_alleen_bk[$key][$db->f("week")] = $value + $bk_add_to_price_total;

								$this->tarief_exact[$key][$db->f("week")] = $this->tarief[$key][$db->f("week")];

								// round with ceil()
								$this->tarief[$key][$db->f("week")] = ceil($this->tarief[$key][$db->f("week")]);

								// commissie
								if($this->tarief[$key][$db->f("week")]>0) {
									$this->commissie[$db->f("week")] = $db->f("wederverkoop_commissie_agent");
									if($this->config->chalettour_aanpassing_commissie) {
										$this->commissie[$db->f("week")] = $this->commissie[$db->f("week")] + $this->config->chalettour_aanpassing_commissie;
									}
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

				if($this->aankomstdatum and $this->aankomstdatum==$week) $this->actieve_kolom=$kolomteller;


				if(!$this->aankomstdatum and !$this->scroll_first_monthyear) {
					if($this->tarief_ingevoerd[$week]) {
						$this->scroll_first_monthyear=date("Ym", $week);
					}
				}

				$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
			}

			// Aantal nachten bepalen
			$week=$this->begin;
			$eind=mktime(0,0,0,date("m",$this->eind),date("d",$this->eind)+7,date("Y",$this->eind));
			while($week<=$eind) {
				// Afwijkende vertrekdag
				$aantalnachten_afwijking[date("dm",$week)]+=$vertrekdag[$this->week_seizoen_id[$week]][date("dm",$week)];
				$vorigeweek=mktime(0,0,0,date("n",$week),date("j",$week)-7,date("Y",$week));
				$aantalnachten_afwijking[date("dm",$vorigeweek)]-=$vertrekdag[$this->week_seizoen_id[$week]][date("dm",$week)];

				// Afwijkende verblijfsduur
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

		}
	}

	/**
	 * create html for button "This accommodation in the summer/winter" ("Deze accommodatie in de zomer/winter")
	 *
	 * @param integer type_id of connected summer/winter accommodation
	 * @return string $return
	 */
	private function button_to_other_website($zomerwinterkoppeling_accommodatie_id)
	{

		$db = new DB_sql;

		if($zomerwinterkoppeling_accommodatie_id and !$this->config->isMobile) {
			$db->query("SELECT t.websites FROM type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".intval($zomerwinterkoppeling_accommodatie_id)."' AND a.tonen=1 AND t.tonen=1;");
			if($db->next_record()) {
				if($this->config->websitetype==1 or $this->config->websitetype==4 or ($this->config->websitetype==6 and $this->config->seizoentype==1)) {
					// Op Chalet.nl/be link naar zomerhuisje.nl/eu
					if($this->config->website=="C") {
						if(preg_match("/Z/",$db->f("websites"))) {
							$beginurl="https://www.zomerhuisje.nl";
						}
					} elseif($this->config->website=="B") {
						if(preg_match("/N/",$db->f("websites"))) {
							$beginurl="https://www.zomerhuisje.nl";
						}
					} elseif($this->config->website=="V") {
						if(preg_match("/V/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.nl";
						}
					} elseif($this->config->website=="Q") {
						if(preg_match("/Q/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.com";
						}
					} elseif($this->config->website=="T") {
						if(preg_match("/Z/",$db->f("websites"))) {
							$beginurl="https://www.zomerhuisje.nl";
							$beginurl_extra="?fromsite=chalettour";
						}
					}
					if($beginurl) {
						$return .= "<a href=\"".wt_he($beginurl.ereg_replace("[a-z]+/$","",$this->config->path).txt("menu_accommodatie")."/".$this->accinfo["begincode"].$zomerwinterkoppeling_accommodatie_id."/".$beginurl_extra)."\" class=\"button_to_other_website analytics_track_external_click\" target=\"_blank\">";
						$return .= nl2br(html("accommodatieopanderesite_2","toonaccommodatie",array("h_1"=>"<b>","h_2"=>"</b>")));
						$return .= "</a>";
					}
				} elseif($this->config->websitetype==3 or $this->config->websitetype==5 or ($this->config->websitetype==6 and $this->config->seizoentype==2)) {
					// Op Zomerhuisje.nl/eu link naar winterpagina
					if($this->config->website=="Z") {
						if($this->fromsite=="chalettour" or $_COOKIE["fromsite"]=="chalettour") {
							if(preg_match("/T/",$db->f("websites"))) {
								$beginurl="https://www.chalettour.nl";
							}
						} else {
							if(preg_match("/C/",$db->f("websites"))) {
								$beginurl="https://www.chalet.nl";
							}
						}
					} elseif($this->config->website=="N") {
						if(preg_match("/B/",$db->f("websites"))) {
							$beginurl="https://www.chalet.be";
						}
					} elseif($this->config->website=="V") {
						if(preg_match("/V/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.nl";
						}
					} elseif($this->config->website=="Q") {
						if(preg_match("/Q/",$db->f("websites"))) {
							$beginurl="https://www.chaletsinvallandry.com";
						}
					}
					if($beginurl) {
						$return .= "<a href=\"".wt_he($beginurl.ereg_replace("[a-z]+/$","",$this->config->path).txt("menu_accommodatie")."/".$this->accinfo["begincode"].$zomerwinterkoppeling_accommodatie_id."/")."\" class=\"button_to_other_website analytics_track_external_click\" target=\"_blank\">";
						if($this->config->websitetype==6) {
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
}


?>