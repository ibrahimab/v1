<?php

$mustlogin=true;

include("admin/vars.php");


if($_GET["1k0"]) {
	# om welke leverancier gaat het?
	$db->query("SELECT leverancier_id FROM view_accommodatie WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
	if($db->next_record()) {
		if($db->f("leverancier_id")==131) {
			# Posarelli
			$bron_prevalue=2;
		}
	}
} elseif($_GET["48k0"]) {
	# 1k0 bepalen
	$db->query("SELECT accommodatie_id FROM accommodatie_review WHERE accommodatie_review_id='".addslashes($_GET["48k0"])."';");
	if($db->next_record()) {
		$_GET["1k0"]=$db->f("accommodatie_id");
	}
}

if(!$_GET["1k0"]) {
	echo "Fout<br><br>Bijbehorende accommodatie niet bekend.";
	exit;
}

$cms->settings[48]["list"]["show_icon"]=false;
$cms->settings[48]["list"]["edit_icon"]=true;
$cms->settings[48]["list"]["delete_icon"]=true;
$cms->settings[48]["list"]["add_link"]=true;
#$cms->settings[48]["list"]["delete_checkbox"]=true;

$cms->db[48]["where"]="accommodatie_id='".addslashes($_GET["1k0"])."'";
$cms->db[48]["set"]="accommodatie_id='".addslashes($_GET["1k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(48,"yesno","actief");
$cms->db_field(48,"select","bron","",array("selection"=>$vars["accommodatie_review_bron"]));
$cms->db_field(48,"date","datum");
$cms->db_field(48,"text","naam");
$cms->db_field(48,"textarea","tekst");
$cms->db_field(48,"yesno","afbreken");

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[48]=array("datum");
$cms->list_field(48,"datum","Datum",array("date_format"=>"DD-MM-JJJJ"));
$cms->list_field(48,"bron","Bron");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(48,1,"actief","Tonen op de website (op dit moment nog niet van toepassing)",array("selection"=>true));
$cms->edit_field(48,1,"bron","Bron van de review",array("selection"=>$bron_prevalue));
$cms->edit_field(48,1,"datum","Datum van de review","",array("startyear"=>2005,"endyear"=>date("Y")),array("calendar"=>true));
$cms->edit_field(48,0,"naam","Naam schrijver van de review");
$cms->edit_field(48,1,"tekst","Inhoud van de review");
$cms->edit_field(48,1,"afbreken","Inhoud na 4 regels afbreken (via openklap-functie)",array("selection"=>true));


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>