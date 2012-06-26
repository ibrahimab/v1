<?php
$unixdir="../";
include("../admin/vars.php");
$db->query("SELECT DISTINCT land_id, land FROM view_accommodatie WHERE atonen = '1' AND ttonen= '1' AND websites LIKE '%".$vars["website"]."%' AND wzt='".addslashes($vars["seizoentype"])."'");
if($db->num_rows()){
	$xmloutput=header("Content-Type: text/xml; charset=utf-8");
	$xmloutput="";
	$xmloutput.="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xmloutput.="<LocatieInfos>\n";
	$xmloutput.="<landen>\n";
	while($db->next_record()){
		$xmloutput.="<land>\n";
		$xmloutput.="<landID>";
		$xmloutput.=xml_text($db->f("land_id"));
		$xmloutput.="</landID>\n";
		$xmloutput.="<landNaam>";
		$xmloutput.=xml_text($db->f("land"));
		$xmloutput.="</landNaam>\n";
		$xmloutput.="<landUrl>";
		$xmloutput.=$vars["basehref"].txt("menu_land")."/".wt_convert2url($db->f("land"))."/";
		$xmloutput.="</landUrl>\n";
		$xmloutput.="</land>\n"; 
	}
	$xmloutput.="</landen>\n";
	$db->query("SELECT DISTINCT skigebied, skigebied_id FROM view_accommodatie WHERE atonen = '1' AND ttonen= '1' AND websites LIKE '%".$vars["website"]."%' AND wzt='".addslashes($vars["seizoentype"])."'");
	if($db->num_rows()){
		$xmloutput.="<skigebieden>\n";
		while($db->next_record()){
			$xmloutput.="<skigebied>\n";
			$xmloutput.="<skigebiedID>";
			$xmloutput.=xml_text($db->f("skigebied_id"));
			$xmloutput.="</skigebiedID>\n";
			$xmloutput.="<skigebiedNaam>";
			$xmloutput.=xml_text($db->f("skigebied"));
			$xmloutput.="</skigebiedNaam>\n";
			$xmloutput.="<skigebiedUrl>";
			$xmloutput.=$vars["basehref"].txt("menu_skigebied")."/".wt_convert2url($db->f("skigebied"))."/";
			$xmloutput.="</skigebiedUrl>\n";
			$xmloutput.="</skigebied>\n";
		}
		$xmloutput.="</skigebieden>\n";
		$db->query("SELECT DISTINCT plaats_id, plaats FROM view_accommodatie WHERE atonen = '1' AND ttonen= '1' AND websites LIKE '%".$vars["website"]."%' AND wzt='".addslashes($vars["seizoentype"])."'");
		if($db->num_rows()){
			$xmloutput.="<plaatsen>\n";
			while($db->next_record()){
				$xmloutput.="<plaats>\n";
				$xmloutput.="<plaatsID>";
				$xmloutput.=xml_text($db->f("plaats_id"));
				$xmloutput.="</plaatsID>\n";
				$xmloutput.="<plaatsNaam>";
				$xmloutput.=xml_text($db->f("plaats"));
				$xmloutput.="</plaatsNaam>\n";
				$xmloutput.="<plaatsUrl>";
				$xmloutput.=$vars["basehref"].txt("menu_plaats")."/".wt_convert2url($db->f("plaats"))."/";
				$xmloutput.="</plaatsUrl>\n";
				$xmloutput.="</plaats>\n";
			}
			$xmloutput.="</plaatsen>\n";
		}
	}
	$xmloutput.="</LocatieInfos>\n";
	print($xmloutput);
	
}
?>