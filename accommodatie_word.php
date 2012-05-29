<?php

include("admin/vars.php");

if(($vars["chalettour_logged_in"] or $voorkant_cms) and $_GET["accid"]) {

	
	$db->query("SELECT begincode, type_id FROM view_accommodatie WHERE type_id='".addslashes($_GET["accid"])."';");
	if($db->next_record()) {
		
		$vars["accommodatie_word"]=true;
		
		# Word-bestand creëren
		include("admin/class.msoffice.php");
		$ms=new ms_office;
		$ms->author=$vars["websitenaam"];
		$ms->company=$vars["websitenaam"];
#		$ms->margin="0cm 0cm 0cm 0cm;";
#		$ms->landscape=true;
#		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $_SERVER["REMOTE_ADDR"]=="82.173.186.80NOT") $ms->test=true;
		$ms->filename=txt("menu_accommodatie")."_".$db->f("begincode").$db->f("type_id");
		$ms->css="body {
		background-color: white;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10pt;
	}

	h3 {
		margin: 0;
		padding: 0;
		margin-top: 1em;
	}
	
	#wrapper {
		width: 100%;
		margin: 0px;
		padding: 0px;
	}
	
	#menubalk {
		display: none;
	}

	#submenu {
		display: none;
	}

	.noprint {
		display: none;
		height: 0px;
	}

	.onlyprint {
		display: inline;
	}
	
	#menubalk_print {
		padding-top: 20px;
		float: left;
	}
	
	a {
		text-decoration: none;
	}
	#toonaccommodatie_content {
		float: left;
		width: 100%;
	}
	
	#toonaccommodatie_kopteksten {
		width: 400px;
	}
	
	#toonaccommodatie_kenmerken {
		width: 400px;	
	}

	#toonaccommodatie_kenmerken_2 {
		margin-left: 0px;
	}
	
	.ui-tabs .ui-tabs-panel {
		width:100%;
	}
	
	.toonacctabel {
		width: 100%;
	}
	
	td {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10pt;
	}
	
	th {
		text-align: left;
	}
	
	.optietabelclass {
		width: 100%;
	}
	
	.optietabelclass td {
		font-size: 8pt;
	}
	
	.tarieventabel {
		display: none;	
	}";

#		$ms->html.="hallo";		

		$typeid=$db->f("type_id");
		ob_start();
		include("content/toonaccommodatie_nieuw.html");
		$content=ob_get_contents();
		ob_end_clean();

		
#		$content=str_replace("<a href=\"#tabs_fotos\">",$ms->page_break(),$content);
		
		# tabs_indeling
		if(preg_match("/<div id=\"tabs_indeling\">(.*)<!-- afsluiten_tabs_indeling -->/s",$content,$regs)) {
			$tabs_indeling=$regs[1];
		}
		
		# tabs_fotos
#		if(preg_match("/<div id=\"tabs_fotos\">(.*)<!-- afsluiten_tabs_fotos -->/s",$content,$regs)) {
		if($foto_table) {
			$tabs_fotos.="<h3>".html("fotos","vars")."</h3>";
			$tabs_fotos.="<table cellspacing=\"0\" cellpadding=\"2\">";
			$tabs_fotos.=$foto_table;
			$tabs_fotos.="</table>";
			$tabs_fotos.="<i>".html("fotosindicatief","imagetable")."</i>";

#			$tabs_fotos=str_replace("foto_doorklik.png","leeg.gif",$tabs_fotos);
		}

		# tabs_omgeving
		if(preg_match("/<div id=\"tabs_omgeving\">(.*)<!-- afsluiten_tabs_omgeving -->/s",$content,$regs)) {
			$tabs_omgeving=$regs[1];
		}

		# tabs_opties
		if(preg_match("/<div id=\"tabs_opties\">(.*)<!-- afsluiten_tabs_opties -->/s",$content,$regs)) {
			$tabs_opties=$regs[1];

			$tabs_opties=str_replace("<TABLE width=\"660\" border=\"0\" class=\"toonacctabel optietabelclass\" cellspacing=\"0\">","<TABLE width=\"100%\" border=\"0\" class=\"toonacctabel optietabelclass\" cellspacing=\"0\">",$tabs_opties);
			$tabs_opties=str_replace("<TD width=\"350\">","<TD width=\"290\">",$tabs_opties);
			$tabs_opties=str_replace("width=\"460\"","width=\"400\"",$tabs_opties);


#			$tabs_opties=preg_replace("/width=\"[0-9]+\"/"," ",$tabs_opties);

		}


		# Logo reisagent
		if(is_object($login_rb)) {

			$file="pic/cms/reisagent_logo/".$login_rb->user_id.".jpg";
			if(!file_exists($file)) {
				$file="pic/cms/reisbureau_logo/".$login_rb->vars["reisbureau_id"].".jpg";
			}
			if(file_exists($file)) {
				$size=getimagesize($file);
				if($size[0] and $size[1]) {
					if($size[0]>$size[1]) {
						# afbeelding is breed
						$height=round(200*$size[1]/$size[0]);
						if($height) {
							$ms->html.="<p><center><img src=\"".$vars["basehref"].$file."\" width=\"200\" height=\"".$height."\"></center></p>";
						}
					} else {
						# afbeelding is hoog
						$width=round(150*$size[0]/$size[1]);
						if($width) {
							$ms->html.="<p><center><img src=\"".$vars["basehref"].$file."\" width=\"".$height."\" height=\"150\"></center></p>";
						}
					}
				}
			}
		}
		
		# Koptekst
		$ms->html.="<h3>".htmlentities(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " - ".$db->f("tnaam") : ""))."</h3>";
		
		$ms->html.="<br><b><i>".htmlentities($db->f("land"))." - ".htmlentities($db->f("skigebied"))." - ".htmlentities($db->f("plaats"))."</i></b><br><br>";
		
		# Kenmerken
		$ms->html.="<table><tr><td><img src=\"".$vars["path"]."thumbnail.php?file=".urlencode($afbeelding.".jpg")."&w=170&h=127\" alt=\"".htmlentities($db->f("naam"))."\" width=\"170\" height=\"127\" style=\"".($afbeeldingborder ? "border: 1px solid #000000;" : "")."\"></td><td><ul>";
		
		# aantal personen
		$ms->html.=$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? " - ".$db->f("maxaantalpersonen") : "")." ";
		if($db->f("maxaantalpersonen")==1) {
			$ms->html.=html("persoon");
		} else {
			$ms->html.=html("personen");
		}
		$ms->html.="<br>";
		
		# Aantal slaapkamers
		if($db->f("slaapkamers")) {
			$ms->html.=$db->f("slaapkamers")." ";
			if($db->f("slaapkamers")==1) {
				$ms->html.=html("slaapkamer");
			} else {
				$ms->html.=html("slaapkamers");
			}
			if($db->f("slaapkamersextra")) {
				$ms->html.=" (".htmlentities($db->f("slaapkamersextra")).")";
			}
			$ms->html.="<br>";
		}
	
		# Aantal badkamers
		if($db->f("badkamers")) {
			$ms->html.=$db->f("badkamers")." ";
			if($db->f("badkamers")==1) {
				$ms->html.=html("badkamer");
			} else {
				$ms->html.=html("badkamers");
			}
			if($db->f("badkamersextra")) {
				$ms->html.=" (".htmlentities($db->f("badkamersextra")).")";
			}
			$ms->html.="<br>";
		}
		if($db->f("oppervlakte")) {
			$ms->html.=$db->f("oppervlakte")." m&#178;".($db->f("oppervlakteextra") ? " - ".htmlentities($db->f("oppervlakteextra")) : "")."<br>";
		}
		
		if($db->f("akwaliteit") or $db->f("kwaliteit")) {
			if($db->f("kwaliteit")) {
				$kwaliteit=$db->f("kwaliteit");
			} else {
				$kwaliteit=$db->f("akwaliteit");
			}
			for($i=1;$i<=$kwaliteit;$i++) {
				$ms->html.="<img src=\"".$vars["path"]."pic/ster.gif?cache=1\" width=\"19\" height=\"17\">";
			}
			$ms->html.="<br>";
		}
		$ms->html.="</ul></td></tr></table>";

		# omschrijving
		if($db->f("omschrijving") or $db->f("tomschrijving")) {
			$ms->html.="<p>".toon_tekst_acc_en_type($db->f("omschrijving"),$db->f("tomschrijving"))."</p>";
		}

		$ms->html.=$tabs_indeling;
		$ms->html.=$tabs_fotos;
		$ms->html.=$tabs_omgeving;
		$ms->html.="<br>".$tabs_opties;


		# "img src=" aanpassen
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$ms->html=preg_replace("/src=\"\/chalet\/pic\//","src=\"".$vars["basehref"]."pic/",$ms->html);
			$ms->html=preg_replace("/src=\"\/chalet\/thumbnail\.php/","src=\"".$vars["basehref"]."thumbnail.php",$ms->html);
		} else {
			$ms->html=preg_replace("/src=\"\/pic\//","src=\"".$vars["basehref"]."pic/",$ms->html);
			$ms->html=preg_replace("/src=\"\/thumbnail\.php/","src=\"".$vars["basehref"]."thumbnail.php",$ms->html);
		}

		# "a href" weghalen 
		$ms->html=preg_replace("/<a href=[^>]+>/i","",$ms->html);
		$ms->html=preg_replace("/<\/a>/i","",$ms->html);

		# "<br>&nbsp;<br>" weghalen
		$ms->html=str_replace("<br>&nbsp;<br>","",$ms->html);
		$ms->create_word_document();
	}
}

if(!is_object($ms)) {
	header("Location: ".$vars["path"]);
}

?>