<?php



class accommodatielijst {

	private $input;
	private $acc_sorteer;
	private $sorteer_teller;
	private $type_id_inquery;
	private $vanafprijs_12;
	private $vanafprijs_3;
	// private $type_key;
	// private $type_sorteer;
	private $types_alle_inhoud;
	private $min_max_per_accommodatie;

	public $sorteer_accommodaties;

	function __construct () {
		$this->settings["sorteer_accommodaties"] = false;
		$this->settings["groepeer_per_accommodatie"] = true;
		$this->settings["vanaf_prijzen_tonen"] = false;
	}

	public function type_toevoegen($input) {
		if($this->settings["vanaf_prijzen_tonen"]) {
			// type opslaan om vanaf-prijzen van op te vragen
			$this->type_id_inquery.=",".$input["type_id"];
		}

		$this->types_alle_inhoud[]=$input;
	}

	private function type_naar_array($input) {

		// kijken of er een tarief bekend is
		if($this->settings["vanaf_prijzen_tonen"]) {
			if($input["toonper"]==1 or $input["toonper"]==2) {
				if($this->vanafprijs_12[$input["type_id"]]>0) {
					$input["tarief_float"]=$this->vanafprijs_12[$input["type_id"]];
				}
			}

			if($input["toonper"]==3) {
				if($this->vanafprijs_3[$input["type_id"]]>0) {
					$input["tarief_float"]=$this->vanafprijs_3[$input["type_id"]];
				}
			}
		}


		// sorteren types binnen de accommodatie bepalen
		if($input["tarief_float"]>0) {
			$tarief_sorteer=$input["tarief_float"];
			$type_sorteer.="1";
		} else {
			$tarief_sorteer="99999999";
			$type_sorteer.="9";
		}

		if($this->settings["groepeer_per_accommodatie"]) {
			if($input["apart_tonen_in_zoekresultaten"]) {
				# dit type apart tonen
				$accid=$input["accommodatie_id"]."_".$input["type_id"];
			} else {
				$accid=$input["accommodatie_id"];
			}
		} else {
			// alle types apart toen
			$accid=$input["accommodatie_id"]."_".$input["type_id"];
		}

		// accommodaties sorteren?
		if($this->settings["sorteer_accommodaties"]) {
			$this->acc_sorteer[$input["sorteer_accommodatie"]]=$accid;
		} else {
			$this->sorteer_teller++;
			$this->acc_sorteer[$this->sorteer_teller]=$accid;
		}

		$type_sorteer.=substr("0000".$input["optimaalaantalpersonen"],-4)."_".substr("0000".$input["maxaantalpersonen"],-4)."_".substr("0000000000".number_format($tarief_sorteer,2,"",""),-10)."_".$input["type_id"];

		// echo $input["type_id"].": ".$type_sorteer." - ".$input["tarief_float"]."<br>";

		// // key en sorteer opslaan per type (voor gebruik in andere functies)
		// $this->type_key[$input["type_id"]]=$accid;
		// $this->type_sorteer[$input["type_id"]]=$type_sorteer;

		$this->input[$accid][$type_sorteer]["type_id"]=$input["type_id"];
		$this->input[$accid][$type_sorteer]["accommodatie_id"]=$input["accommodatie_id"];
		$this->input[$accid][$type_sorteer]["begincode"]=$input["begincode"];
		$this->input[$accid][$type_sorteer]["naam"]=$input["naam"];
		$this->input[$accid][$type_sorteer]["tnaam"]=$input["tnaam"];
		$this->input[$accid][$type_sorteer]["soortaccommodatie"]=$input["soortaccommodatie"];
		$this->input[$accid][$type_sorteer]["slaapkamers"]=$input["slaapkamers"];
		$this->input[$accid][$type_sorteer]["badkamers"]=$input["badkamers"];
		$this->input[$accid][$type_sorteer]["optimaalaantalpersonen"]=$input["optimaalaantalpersonen"];
		$this->input[$accid][$type_sorteer]["maxaantalpersonen"]=$input["maxaantalpersonen"];
		$this->input[$accid][$type_sorteer]["plaats"]=$input["plaats"];
		$this->input[$accid][$type_sorteer]["skigebied"]=$input["skigebied"];
		$this->input[$accid][$type_sorteer]["land"]=$input["land"];
		$this->input[$accid][$type_sorteer]["korteomschrijving"]=$input["korteomschrijving"];
		$this->input[$accid][$type_sorteer]["tkorteomschrijving"]=$input["tkorteomschrijving"];
		$this->input[$accid][$type_sorteer]["toonper"]=$input["toonper"];
		$this->input[$accid][$type_sorteer]["skigebied_id"]=$input["skigebied_id"];
		$this->input[$accid][$type_sorteer]["tarief"]=$input["tarief_float"];

		# aanbieding van toepassing?
		if($soort_aanbieding or ($_GET["aab"] and $_GET["faab"])) {
			$this->input[$accid][$type_sorteer]["aanbieding"]=true;
			$this->input[$accid][$type_sorteer]["type_id_aanbieding"]=true;
		}

		// if($trclass) {
		// 	$this->input[$accid][$type_sorteer]["type_id_trclass"]=trim($trclass);
		// }

		if($input["tarief_float"]>0) {
			if(!$this->min_max_per_accommodatie[$accid]["mintarief"] or $this->min_max_per_accommodatie[$accid]["mintarief"]>$input["tarief_float"]) $this->min_max_per_accommodatie[$accid]["mintarief"]=$input["tarief_float"];
			if($this->min_max_per_accommodatie[$accid]["maxtarief"]<$input["tarief_float"]) $this->min_max_per_accommodatie[$accid]["maxtarief"]=$input["tarief_float"];
		}

		if($input["tkwaliteit"]) {
			$kwaliteit=$input["tkwaliteit"];
		} else {
			$kwaliteit=$input["akwaliteit"];
		}

		if(!$this->min_max_per_accommodatie[$accid]["minkwaliteit"] or $this->min_max_per_accommodatie[$accid]["minkwaliteit"]>$kwaliteit) $this->min_max_per_accommodatie[$accid]["minkwaliteit"]=$kwaliteit;
		if($this->min_max_per_accommodatie[$accid]["maxkwaliteit"]<$kwaliteit) $this->min_max_per_accommodatie[$accid]["maxkwaliteit"]=$kwaliteit;

		if($this->input[$accid][$type_sorteer]["aanbieding"]) {
			# binnen deze accommodatie is een aanbieding actief
			$this->min_max_per_accommodatie[$accid]["aanbieding"]=true;

			if(is_array($soort_aanbieding["percentage"]) and count($soort_aanbieding["percentage"])==1 and !$soort_aanbieding["euro"]) {
				# aanbiedingspercentage per type bepalen
				if($this->input[$accid][$type_sorteer]["aanbieding_percentage"]<$soort_aanbieding["percentage"]) {
					$this->input[$accid][$type_sorteer]["aanbieding_percentage"]=array_shift($soort_aanbieding["percentage"]);
				}
			}
			if(is_array($soort_aanbieding["euro"]) and count($soort_aanbieding["euro"])==1 and !$soort_aanbieding["percentage"]) {
				# aanbiedingsbedrag per type bepalen
				if($this->input[$accid][$type_sorteer]["aanbieding_euro"]<$soort_aanbieding["euro"]) {
					$this->input[$accid][$type_sorteer]["aanbieding_euro"]=array_shift($soort_aanbieding["euro"]);
				}
			}
		}

	}

	public function lijst() {

		if($this->settings["vanaf_prijzen_tonen"]) {
			$this->vanaf_prijzen_uit_database_halen();
		}

		// alle type-gegevens in array plaatsen
		foreach ($this->types_alle_inhoud as $key => $value) {
			$this->type_naar_array($value);
		}

		# hulpmiddeltje om hover-kleur door te geven aan jQuery
		$return.="<div id=\"hulp_zoekresultaat_hover\"></div>";

		ksort($this->acc_sorteer);

		while(list($key,$value)=each($this->acc_sorteer)) {

			if($this->acc_sorteer_gehad[$value]) {
				continue;
			}
			$this->acc_sorteer_gehad[$value]=true;
			$return.=$this->accommodatie($value);
		}
		return $return;
	}

	public function accommodatie($acc_id) {

		// bevat deze accommodaties meerdere types?
		if(count($this->input[$acc_id])>1) {
			$multiple_types=true;
		}

		$return.="<div class=\"zoekresultaat_block boxshadow\">";

		$return.=$this->accommodatie_deel($this->input[$acc_id],$acc_id,$multiple_types);
		$return.=$this->types_deel($this->input[$acc_id],$multiple_types);

		$return.="</div>";

		return $return;
	}

	private function accommodatie_deel($alle_types,$acc_id,$multiple_types) {

		global $vars;


		# gebruik de gegevens van het eerste type
		ksort($alle_types);
		$input=current($alle_types);

		# aanbieding?
		if($input["type_id_aanbieding"]) {
			$aanbieding_acc=true;
		} else {
			$aanbieding_acc=false;
		}



		# resultaat (gehele accommodatie)
		$return.="<a href=\"".wt_he($vars["path"].txt("menu_accommodatie")."/".$input["begincode"].$input["type_id"])."/".wt_he($querystring)."\" class=\"zoekresultaat\">";
			$return.="<div class=\"zoekresultaat_top\">";
				$return.="<div class=\"zoekresultaat_titel\">".wt_he(ucfirst($vars["soortaccommodatie"][$input["soortaccommodatie"]])." ".$input["naam"].(!$multiple_types&&$input["tnaam"] ? " ".$input["tnaam"] : ""))."</div>";
			$return.="</div>";
			$return.="<div>";
				# afbeelding bepalen
				$img="accommodaties/0.jpg";
				if($multiple_types) {
					if(file_exists("pic/cms/accommodaties/".$input["accommodatie_id"].".jpg")) {
						$img="accommodaties/".$input["accommodatie_id"].".jpg";
					} elseif(file_exists("pic/cms/types_specifiek/".$input["type_id"].".jpg")) {
						$img="types_specifiek/".$input["type_id"].".jpg";
					}
				} else {
					if(file_exists("pic/cms/types_specifiek/".$input["type_id"].".jpg")) {
						$img="types_specifiek/".$input["type_id"].".jpg";
					} elseif(file_exists("pic/cms/accommodaties/".$input["accommodatie_id"].".jpg")) {
						$img="accommodaties/".$input["accommodatie_id"].".jpg";
					}
				}
				$return.="<div class=\"zoekresultaat_img\"><img src=\"".wt_he($vars["path"]."pic/cms/".$img)."\"></div>";

				$return.="<div class=\"zoekresultaat_content\">";

					$return.="<div class=\"zoekresultaat_content_land\">".wt_he($input["land"])."</div>";
					$return.="<div>".wt_he($input["plaats"])."</div>";
					$return.="<div>".wt_he($input["skigebied"])."</div>";

					$return.="<div class=\"zoekresultaat_content_sterren\">";
					if($this->min_max_per_accommodatie[$acc_id]["minkwaliteit"]) {
						for($i=1;$i<=$this->min_max_per_accommodatie[$acc_id]["minkwaliteit"];$i++) {
							$return.="<img src=\"".$vars["path"]."pic/ster_".$vars["websitetype"].".png\">";
						}
						if($this->min_max_per_accommodatie[$acc_id]["maxkwaliteit"]>$this->min_max_per_accommodatie[$acc_id]["minkwaliteit"]) {
							$return.="<img src=\"".$vars["path"]."pic/ster-scheidingsteken.gif\">";
							for($i=1;$i<=$this->min_max_per_accommodatie[$acc_id]["maxkwaliteit"];$i++) {
								$return.="<img src=\"".$vars["path"]."pic/ster_".$vars["websitetype"].".png\">";
							}
						}
					}
					$return.="</div>";

					$return.="<div class=\"zoekresultaat_omschrijving"."\">".wt_he((!$multiple_types&&$input["tkorteomschrijving"] ? $input["tkorteomschrijving"] : $input["korteomschrijving"]))."</div>";

				if($this->min_max_per_accommodatie[$acc_id]["aanbieding"]) {
					$return.="<div class=\"zoekresultaat_aanbieding\"><img src=\"".$vars["path"]."pic/aanbieding_groot_".$vars["websitetype"].".gif\">".html("aanbieding","accommodaties")."</div>";
				}

				$return.="</div>";

				$return.="<div class=\"zoekresultaat_prijs\">";
					if($input["tarief"]) {
						unset($zoekresultaat_prijs_periode);
						$zoekresultaat_prijs_periode.="<div class=\"zoekresultaat_prijs_periode\">";
						if(preg_match("/^([0-9]+)n$/",$gekozen["fdu"],$regs)) {
							if($regs[1]==1) {
								$zoekresultaat_prijs_periode.="1 ".html("nacht","vars");
							} else {
								$zoekresultaat_prijs_periode.=intval($regs[1])." ".html("nachten","vars");
							}
						} elseif($gekozen["fdu"]>1) {
							$zoekresultaat_prijs_periode.=$gekozen["fdu"]." ".html("weken","vars");
						} else {
							$zoekresultaat_prijs_periode.="1 ".html("week","vars");
						}
						$zoekresultaat_prijs_periode.="</div>";

						if(($multiple_types and $this->min_max_per_accommodatie[$acc_id]["maxtarief"]>$this->min_max_per_accommodatie[$acc_id]["mintarief"]) or $this->settings["vanaf_prijzen_tonen"]) {
							$return.=$zoekresultaat_prijs_periode;
							$return.="<div class=\"zoekresultaat_prijs_vanaf\">".html("vanaf")."</div>";
						} else {
							$return.="<div class=\"zoekresultaat_prijs_vanaf\">&nbsp;</div>";
							$return.=$zoekresultaat_prijs_periode;
						}
						$return.="<div class=\"zoekresultaat_prijs_bedrag".($aanbieding_acc ? " zoekresultaat_prijs_bedrag_aanbieding" : "")."\">&euro;&nbsp;".number_format($this->min_max_per_accommodatie[$acc_id]["mintarief"],0,",",".")."</div>";
						$return.="<div class=\"zoekresultaat_prijs_per\">";
						if($input["toonper"]==3 or $vars["wederverkoop"]) {
							$return.=html("peraccommodatie","zoek-en-boek");
						} else {
							$return.=html("perpersoon","zoek-en-boek")."<br>".html("inclusiefskipas","zoek-en-boek");
						}
						$return.="</div>";
					}
				$return.="</div>";
				$return.="<div class=\"clear\"></div>";
			$return.="</div>";

		if($multiple_types) {
			$return.="</a>";
		}

		return $return;
	}

	private function types_deel($alle_types,$multiple_types) {

		global $vars;

		# sorteer alle types binnen deze accommodatie
		ksort($alle_types);

		# resultaten type-regels
		reset($alle_types);
		while(list($key2,$value2)=each($alle_types)) {
			$input=$value2;

			if($multiple_types) {
				$return.="<a href=\"".wt_he($vars["path"].txt("menu_accommodatie")."/".$input["begincode"].$input["type_id"])."/".wt_he($querystring)."\" class=\"zoekresultaat_type\">";
			} else {
				$return.="<div class=\"zoekresultaat_type_een_resultaat\">";
			}
			$return.="<div class=\"zoekresultaat_type_titel".($input["type_id_trclass"] ? " ".$input["type_id_trclass"] : "")."\">";
				$return.="<div class=\"zoekresultaat_type_personen\">".$input["optimaalaantalpersonen"].($input["maxaantalpersonen"]>$input["optimaalaantalpersonen"] ? " - ".$input["maxaantalpersonen"] : "")." ".($input["maxaantalpersonen"]==1 ? html("persoon") : html("personen"))."</div>";
				$return.="<div class=\"zoekresultaat_type_slaapkamers\">".$input["slaapkamers"]." ".($input["slaapkamers"]==1 ? html("slaapkamer") : html("slaapkamers"))."</div>";
				$return.="<div class=\"zoekresultaat_type_badkamers\">".$input["badkamers"]." ".($input["badkamers"]==1 ? html("badkamer") : html("badkamers"))."</div>";
				$return.="<div class=\"zoekresultaat_type_typenaam".($input["type_id_aanbieding"] ? " zoekresultaat_type_typenaam_aanbieding" : "")."\">".wt_he(($input["tnaam"] ? $input["tnaam"] : ""))."</div>";

				if($input["type_id_aanbieding"]) {
#							$return.="<div class=\"zoekresultaat_type_aanbieding\"><img src=\"".$vars["path"]."pic/aanbieding_klein_".$vars["websitetype"].".png\"></div>";
					$return.="<div class=\"zoekresultaat_type_aanbieding\">";
					if($input["tarief"]) {
						if(floatval($input["aanbieding_percentage"])>0 and !$input["aanbieding_euro"]) {
							$return.=floor($input["aanbieding_percentage"])."% ".html("korting","vars");
						} elseif(floatval($input["aanbieding_euro"])>0 and !$input["aanbieding_percentage"]) {
							$return.="&euro;&nbsp;".number_format(floor($input["aanbieding_euro"]),0,",",".")." ".html("korting","vars");
						} else {
							$return.=html("aanbieding","accommodaties");
						}
					}
					$return.="</div>";
				}

			$return.="</div>";
			$return.="<div class=\"zoekresultaat_type_prijs".($input["type_id_aanbieding"] ? " zoekresultaat_type_prijs_aanbieding" : "")."\">";
			if($input["tarief"]) {
				$return.="&euro;&nbsp;".number_format($input["tarief"],0,",",".");
				if($input["toonper"]==3 or $vars["wederverkoop"]) {

				} else {
					$return.=" ".html("pp");
				}
			} else {
				$return.="&nbsp;";
			}
			$return.="</div>";
			if(!$multiple_types) {
				$return.="</div>"; # afsluiten .zoekresultaat_type_een_resultaat
			}

			$return.="<div class=\"clear\"></div>";
			$return.="</a>";
		}
		return $return;
	}

	private function vanaf_prijzen_uit_database_halen() {
		$db = new DB_sql;
		if($this->type_id_inquery) {
			# toonper = 1 en 2 (arrangement)
			$db->query("SELECT tp.type_id, MIN(tp.prijs) AS prijs_per_persoon FROM tarief tr, tarief_personen tp, seizoen sz WHERE tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id IN (".substr($this->type_id_inquery,1).") GROUP BY tp.type_id;");
			while($db->next_record()) {
				$this->vanafprijs_12[$db->f("type_id")]=$db->f("prijs_per_persoon");
			}

			# toonper = 3 (accommodatie)
			$db->query("SELECT tr.type_id, MIN(tr.c_verkoop_site) AS c_verkoop_site FROM accommodatie a, type t, tarief tr, seizoen sz WHERE a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id IN (".substr($this->type_id_inquery,1).") GROUP BY tr.type_id;");
			while($db->next_record()) {
				$this->vanafprijs_3[$db->f("type_id")]=$db->f("c_verkoop_site");
			}
		}
	}
}

?>