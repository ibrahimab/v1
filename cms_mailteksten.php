<?php

# select DISTINCT mailtekst_id from accommodatie where mailtekst_id>0 and wzt=2



#
#
#

$mustlogin=true;

include("admin/vars.php");

# wzt opvragen indien niet meegegeven met query_string
if(!$_GET["wzt"]) {
	if($_GET["32k0"]) {
		$db->query("SELECT wzt FROM mailtekst WHERE mailtekst_id='".addslashes($_GET["32k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

# Seizoenen laden
$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='".addslashes($_GET["wzt"])."' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
while($db->next_record()) {
	$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
	$vars["seizoengoedgekeurd_eind"][$db->f("seizoen_id")]=$db->f("eind");
}

# Goedgekeurd t/m bepalen
$db->query("SELECT mailtekst_id, seizoengoedgekeurd FROM mailtekst;");
while($db->next_record()) {
	unset($seizoengoedgekeurd_eind);
	$seizoengoedgekeurd_array=split(",",$db->f("seizoengoedgekeurd"));
	while(list($key,$value)=@each($seizoengoedgekeurd_array)) {
		if($vars["seizoengoedgekeurd_eind"][$value]>$seizoengoedgekeurd_eind) {
			$seizoengoedgekeurd_eind=$vars["seizoengoedgekeurd_eind"][$value];
			if($vars["seizoengoedgekeurd"][$value]) {
				$seizoengoedgekeurd[$db->f("mailtekst_id")]=$vars["seizoengoedgekeurd"][$value];
			}
		}
	}
}

# Aantal gekoppelde actieve accommodaties bepalen
$db->query("SELECT a.mailtekst_id, COUNT(a.accommodatie_id) AS aantal FROM accommodatie a WHERE a.archief=0 AND a.tonen=1 GROUP BY a.mailtekst_id;");
while($db->next_record()) {
	$aantal_gekoppelde_actieve_accommodaties[$db->f("mailtekst_id")]=$db->f("aantal");
}

$cms->settings[32]["list"]["show_icon"]=false;
$cms->settings[32]["list"]["edit_icon"]=true;
$cms->settings[32]["list"]["delete_icon"]=true;

$cms->settings[32]["show"]["goto_new_record"]=false;

$cms->db[32]["where"]="wzt='".addslashes($_GET["wzt"])."'";
$cms->db[32]["set"]="wzt='".addslashes($_GET["wzt"])."'";


# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(32,"text","naam");
$cms->db_field(32,"textarea","tekst");
if($vars["cmstaal"]) $cms->db_field(32,"textarea","tekst_".$vars["cmstaal"]);
$cms->db_field(32,"checkbox","seizoengoedgekeurd","",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(32,"select","laatsteseizoen","mailtekst_id",array("selection"=>$seizoengoedgekeurd));
$cms->db_field(32,"select","aantal_gekoppelde_actieve_accommodaties","mailtekst_id",array("selection"=>$aantal_gekoppelde_actieve_accommodaties));




# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[32]=array("naam");
$cms->list_field(32,"naam","Naam");
$cms->list_field(32,"laatsteseizoen","Goedgekeurd t/m");
$cms->list_field(32,"aantal_gekoppelde_actieve_accommodaties","Aantal accommodaties");


# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(32,1,"naam","Naam");



$cms->edit_field(32,0,"seizoengoedgekeurd","Tekst is goedgekeurd voor seizoen","","",array("one_per_line"=>true));

$cms->edit_field(32,0,"htmlrow","<hr><i>".nl2br(html("mailopties_wzt1_1","vars"))."</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(32,0,"tekst","Mailtekst (tussenstuk) NL","",array("noedit"=>true));
	$cms->edit_field(32,1,"tekst_".$vars["cmstaal"],"Mailtekst (tussenstuk)".strtoupper($vars["cmstaal"]),"","",array("rows"=>35));
} else {
	$cms->edit_field(32,1,"tekst","Mailtekst (tussenstuk)","","",array("rows"=>35));
}

if(!$vars["cmstaal"]) {
	$cms->edit_field(32,0,"htmlrow","<div style=\"width:900px;text-align:right;\"><a href=\"#\" onclick=\"printField(document.frm.elements['input[tekst]']);return false;\">print bovenstaand tekstvak</a></div>");
}

$cms->edit_field(32,0,"htmlrow","<i>".nl2br(html("mailopties_wzt1_2","vars"))."</i><hr>Te gebruiken variabelen in de tekst:<br><table><tr><td>[LINK]</td><td>&nbsp;</td><td>Link naar &quot;Mijn boeking&quot;</td></tr><tr><td>[NAAM]</td><td>&nbsp;</td><td>Voornaam hoofdboeker</td></tr><tr><td>[PLAATS]</td><td>&nbsp;</td><td>Plaats bestemming</td></tr><tr><td>[DATUM]</td><td>&nbsp;</td><td>Aankomstdatum (voluit geschreven)</td></tr><tr><td>[VERZEKERINGLINK]</td><td>&nbsp;</td><td>Link naar verzekeringspagina website, bijvoorbeeld <i>https://www.chalet.nl/verzekeringen.php</i></td></tr><tr><td>[WEBSITE]</td><td>&nbsp;</td><td>Naam website</td></tr></table>");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(32);
if($cms_form[32]->filled) {

}


# Controle op delete-opdracht
if($_GET["delete"]==32 and $_GET["32k0"]) {
	$db->query("SELECT accommodatie_id FROM accommodatie WHERE mailtekst_id='".addslashes($_GET["32k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(32,"Deze mailtekst is nog gekoppeld aan accommodaties");
	}
}

#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(32)) {

}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);


?>