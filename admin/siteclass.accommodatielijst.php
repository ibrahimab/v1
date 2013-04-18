<?php



class accommodatielijst {

	// public $kind;
	// private $user_data;
	private $input;
	private $acc_sorteer;
	private $sorteer_teller;

	public $sorteer_accommodaties;

	function __construct () {
		$this->sorteer_accommodaties=false;
	}

	public function type_toevoegen($input) {
		# sorteren types binnen de accommodatie bepalen
		if($input["tarief_float"]>0) {
			$tarief_sorteer=$input["tarief_float"];
			$type_sorteer.="1";
			$tarieven_tonen=true;
		} else {
			$tarief_sorteer="99999999";
			$type_sorteer.="9";
		}

		# wel/niet apart tonen in zoekresultaten
		if($input["apart_tonen_in_zoekresultaten"]) {
			$accid=$input["accommodatie_id"]."_".$input["type_id"];
		} else {
			$accid=$input["accommodatie_id"];
		}

		// accommodaties sorteren?
		if($this->sorteer_accommodaties) {
			$this->acc_sorteer[$input["sorteer_accommodatie"]]=$accid;
		} else {
			$this->sorteer_teller++;
			$this->acc_sorteer[$this->sorteer_teller]=$accid;
		}

		$type_sorteer.=substr("0000".$input["optimaalaantalpersonen"],-4)."_".substr("0000".$input["maxaantalpersonen"],-4)."_".substr("0000000000".number_format($tarief_sorteer,2,"",""),-10)."_".$input["type_id"];

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
			if(!$newresultsminmax[$accid]["mintarief"] or $newresultsminmax[$accid]["mintarief"]>$input["tarief_float"]) $newresultsminmax[$accid]["mintarief"]=$input["tarief_float"];
			if($newresultsminmax[$accid]["maxtarief"]<$input["tarief_float"]) $newresultsminmax[$accid]["maxtarief"]=$input["tarief_float"];
		}

		if($input["tkwaliteit"]) {
			$kwaliteit=$input["tkwaliteit"];
		} else {
			$kwaliteit=$input["akwaliteit"];
		}

		if(!$newresultsminmax[$accid]["minkwaliteit"] or $newresultsminmax[$accid]["minkwaliteit"]>$kwaliteit) $newresultsminmax[$accid]["minkwaliteit"]=$kwaliteit;
		if($newresultsminmax[$accid]["maxkwaliteit"]<$kwaliteit) $newresultsminmax[$accid]["maxkwaliteit"]=$kwaliteit;

		if($this->input[$accid][$type_sorteer]["aanbieding"]) {
			# binnen deze accommodatie is een aanbieding actief
			$newresultsminmax[$accid]["aanbieding"]=true;

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

		unset($input["multiple_types"]);
		if(count($this->input[$acc_id])>1) {
			$input["multiple_types"]=true;
		}

		ksort($this->input[$acc_id]);

		$return.="<div class=\"zoekresultaat_block boxshadow\">";
		$return.=$this->accommodatie_deel(current($this->input[$acc_id]));
		$return.=$this->types_deel(current($this->input[$acc_id]));
		$return.="</div>";

		return $return;
	}

	private function accommodatie_deel($input) {

		global $vars;

		# Sorteerscore gekoppelde skigebieden
		if($koppeling[$input["skigebied_id"]]) {
#					$sorteerscore_skigebied[$results_teller]=intval($koppeling[$input["skigebied_id"]]);
		}

		# aanbieding?
		if($input["type_id_aanbieding"]) {
			$aanbieding_acc=true;
		} else {
			$aanbieding_acc=false;
		}



		# resultaat (gehele accommodatie)
		$return.="<a href=\"".wt_he($vars["path"].txt("menu_accommodatie")."/".$input["begincode"].$input["type_id"])."/".wt_he($querystring)."\" class=\"zoekresultaat\">";
			$return.="<div class=\"zoekresultaat_top\">";
				$return.="<div class=\"zoekresultaat_titel\">".wt_he(ucfirst($vars["soortaccommodatie"][$input["soortaccommodatie"]])." ".$input["naam"].(!$input["multiple_types"]&&$input["tnaam"] ? " ".$input["tnaam"] : ""))."</div>";
			$return.="</div>";
			$return.="<div>";
				# afbeelding bepalen
				$img="accommodaties/0.jpg";
				if($input["multiple_types"]) {
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
					if($newresultsminmax[$value]["minkwaliteit"]) {
						for($i=1;$i<=$newresultsminmax[$value]["minkwaliteit"];$i++) {
							$return.="<img src=\"".$vars["path"]."pic/ster_".$vars["websitetype"].".png\">";
						}
						if($newresultsminmax[$value]["maxkwaliteit"]>$newresultsminmax[$value]["minkwaliteit"]) {
							$return.="<img src=\"".$vars["path"]."pic/ster-scheidingsteken.gif\">";
							for($i=1;$i<=$newresultsminmax[$value]["maxkwaliteit"];$i++) {
								$return.="<img src=\"".$vars["path"]."pic/ster_".$vars["websitetype"].".png\">";
							}
						}
					}
					$return.="</div>";

					$return.="<div class=\"zoekresultaat_omschrijving"."\">".wt_he((!$input["multiple_types"]&&$input["tkorteomschrijving"] ? $input["tkorteomschrijving"] : $input["korteomschrijving"]))."</div>";

				if($newresultsminmax[$value]["aanbieding"]) {
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

						if(($input["multiple_types"] and $newresultsminmax[$value]["maxtarief"]>$newresultsminmax[$value]["mintarief"]) or $vanaf_prijzen_tonen) {
							$return.=$zoekresultaat_prijs_periode;
							$return.="<div class=\"zoekresultaat_prijs_vanaf\">".html("vanaf")."</div>";
						} else {
							$return.="<div class=\"zoekresultaat_prijs_vanaf\">&nbsp;</div>";
							$return.=$zoekresultaat_prijs_periode;
						}
						$return.="<div class=\"zoekresultaat_prijs_bedrag".($aanbieding_acc ? " zoekresultaat_prijs_bedrag_aanbieding" : "")."\">&euro;&nbsp;".number_format($newresultsminmax[$value]["mintarief"],0,",",".")."</div>";
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

		if($input["multiple_types"]) {
			$return.="</a>";
		}

		return $return;
	}

	private function types_deel($input) {

		echo wt_dump($input);

		# resultaten type-regels
		reset($input);
		while(list($key2,$value2)=each($input)) {
			if($gekozen_skigebied) {
				if($input["skigebied_id"]==$gekozen_skigebied) {
					$toon_aantalresultaten_verschillende_types++;
				} else {
					$toon_aantalresultaten_anderskigebied++;
				}
			} else {
				$toon_aantalresultaten_verschillende_types++;
			}

			if($input["multiple_types"]) {
				$results[$results_teller].="<a href=\"".wt_he($vars["path"].txt("menu_accommodatie")."/".$input["begincode"].$input["type_id"])."/".wt_he($querystring)."\" class=\"zoekresultaat_type\">";
			} else {
				$results[$results_teller].="<div class=\"zoekresultaat_type_een_resultaat\">";
			}
			$results[$results_teller].="<div class=\"zoekresultaat_type_titel".($input["type_id_trclass"] ? " ".$input["type_id_trclass"] : "")."\">";
				$results[$results_teller].="<div class=\"zoekresultaat_type_personen\">".$input["optimaalaantalpersonen"].($input["maxaantalpersonen"]>$input["optimaalaantalpersonen"] ? " - ".$input["maxaantalpersonen"] : "")." ".($input["maxaantalpersonen"]==1 ? html("persoon") : html("personen"))."</div>";
				$results[$results_teller].="<div class=\"zoekresultaat_type_slaapkamers\">".$input["slaapkamers"]." ".($input["slaapkamers"]==1 ? html("slaapkamer") : html("slaapkamers"))."</div>";
				$results[$results_teller].="<div class=\"zoekresultaat_type_badkamers\">".$input["badkamers"]." ".($input["badkamers"]==1 ? html("badkamer") : html("badkamers"))."</div>";
				$results[$results_teller].="<div class=\"zoekresultaat_type_typenaam".($input["type_id_aanbieding"] ? " zoekresultaat_type_typenaam_aanbieding" : "")."\">".wt_he(($input["tnaam"] ? $input["tnaam"] : ""))."</div>";

				if($input["type_id_aanbieding"]) {
#							$results[$results_teller].="<div class=\"zoekresultaat_type_aanbieding\"><img src=\"".$vars["path"]."pic/aanbieding_klein_".$vars["websitetype"].".png\"></div>";
					$results[$results_teller].="<div class=\"zoekresultaat_type_aanbieding\">";
					if($input["tarief"]) {
						if(floatval($input["aanbieding_percentage"])>0 and !$input["aanbieding_euro"]) {
							$results[$results_teller].=floor($input["aanbieding_percentage"])."% ".html("korting","vars");
						} elseif(floatval($input["aanbieding_euro"])>0 and !$input["aanbieding_percentage"]) {
							$results[$results_teller].="&euro;&nbsp;".number_format(floor($input["aanbieding_euro"]),0,",",".")." ".html("korting","vars");
						} else {
							$results[$results_teller].=html("aanbieding","accommodaties");
						}
					}
					$results[$results_teller].="</div>";
#							$this->input[$accid][$type_sorteer]["aanbieding_percentage"]
				}

			$results[$results_teller].="</div>";
			$results[$results_teller].="<div class=\"zoekresultaat_type_prijs".($input["type_id_aanbieding"] ? " zoekresultaat_type_prijs_aanbieding" : "")."\">";
			if($input["tarief"]) {
				$results[$results_teller].="&euro;&nbsp;".number_format($input["tarief"],0,",",".");
				if($input["toonper"]==3 or $vars["wederverkoop"]) {

				} else {
					$results[$results_teller].=" ".html("pp");
				}
			} else {
				$results[$results_teller].="&nbsp;";
			}
			$results[$results_teller].="</div>";
			if(!$input["multiple_types"]) {
				$results[$results_teller].="</div>"; # afsluiten .zoekresultaat_type_een_resultaat
			}

			$results[$results_teller].="<div class=\"clear\"></div>";
			$results[$results_teller].="</a>";
		}
	}
}

?>