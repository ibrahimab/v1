<?php

$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_breadcrumbs"]=true;
$robot_noindex=true;

include("admin/vars.php");

if($_GET["bid"] and ($_GET["c"]==substr(sha1("ldlklKDKLk".$_GET["bid"]."JJJdkkk4uah!"),0,8) or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html")) {
	$gegevens=get_boekinginfo(intval($_GET["bid"]));

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="verzendmethode";
	$form->settings["layout"]["css"]=false;
	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	#$form->settings["target"]="_blank";
	$form->settings["layout"]["stars"]=false;

	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

	#_field: (obl),id,title,db,prevalue,options,layout
	$form->field_htmlrow("","".html("inleiding1","verzendmethode")."<br><br>".html("inleiding2","verzendmethode")."");
	$form->field_htmlrow("","&nbsp;");
	$form->field_select(1,"verzendmethode_reisdocumenten",txt("keuze","verzendmethode"),"",$gegevens["stap1"]["verzendmethode_reisdocumenten"],array("selection"=>$vars["verzendmethode_reisdocumenten"]),array("title_style"=>"width:300px;"));
	$form->field_htmlrow("","&nbsp;");

	$form->check_input();

	if($form->filled) {

	}

	if($form->okay) {
		$db->query("UPDATE boeking SET verzendmethode_reisdocumenten='".addslashes($form->input["verzendmethode_reisdocumenten"])."' WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");
		chalet_log("verzendmethode reisdocumenten: ".$vars["verzendmethode_reisdocumenten_inclusief_nvt"][$form->input["verzendmethode_reisdocumenten"]],true,true);
	}
	$form->end_declaration();
}

include "content/opmaak.php";

?>