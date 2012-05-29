<?php

include("admin/vars.php");

if($vars["websitetype"]<>3 and $vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm"); 
$form->settings["fullname"]="Naam";
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]="VERZENDEN";
#$form->settings["target"]="_blank";
 
# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

#_field: (obl),id,title,db,prevalue,options,layout

$vars["budgetindicatie_keuzes"]=array(1=>txt("budgetindicatie_1","vraagonsadvies"),2=>txt("budgetindicatie_2","vraagonsadvies"),3=>txt("budgetindicatie_3","vraagonsadvies"),4=>txt("budgetindicatie_4","vraagonsadvies"),5=>txt("budgetindicatie_5","vraagonsadvies"),6=>txt("budgetindicatie_6","vraagonsadvies"),7=>txt("budgetindicatie_7","vraagonsadvies"));
$vars["verblijfsduur"]["1"]="1 ".txt("week","vars");
$vars["verblijfsduur"]["2"]="2 ".txt("weken","vars");
$vars["verblijfsduur"]["3"]="3 ".txt("weken","vars");
$vars["verblijfsduur"]["4"]="4 ".txt("weken","vars");
$vars["verblijfsduur"]["1n"]="1 ".txt("nacht","vars");
for($i=2;$i<=$vars["flex_max_aantalnachten"];$i++) {
	$vars["verblijfsduur"][$i."n"]=$i." ".txt("nachten","vars");
}

unset($vars["aantalslaapkamers"][0]);

for($i=1;$i<=40;$i++) {
	$vars["aantalvolwassenen"][$i]=$i;
}

$vars["aantalkinderen"]["nul"]=0;
for($i=1;$i<=40;$i++) {
	$vars["aantalkinderen"][$i]=$i;
}
$vars["soortaccommodatie_keuzes"]=array(1=>txt("soortaccommodatie_1","vraagonsadvies"),2=>txt("soortaccommodatie_2","vraagonsadvies"),3=>txt("soortaccommodatie_3","vraagonsadvies"),4=>txt("soortaccommodatie_4","vraagonsadvies"),5=>txt("soortaccommodatie_5","vraagonsadvies"),6=>txt("soortaccommodatie_6","vraagonsadvies"));

$form->field_htmlrow("","<div style=\"width:650px;margin-bottom:15px;\"><b><i>".html("forminleiding","vraagonsadvies")."</i></b></div>");
$form->field_text(0,"bestemming",txt("bestemming","vraagonsadvies"),"","","",array("add_html_after_field"=>"<div style=\"margin-top:4px;font-size:0.8em;\">".html("bestemming_uitleg","vraagonsadvies")."</div>"));
$form->field_select(0,"verblijfsduur",txt("verblijfsduur","vraagonsadvies"),"","",array("selection"=>$vars["verblijfsduur"],"optgroup"=>array("1"=>"Aantal weken","1n"=>"Aantal nachten")));
#$form->field_htmlcol("","&nbsp;",array("html"=>"<i>".html("verblijf_tussen_uitleg","vraagonsadvies")."</i></b></div>"),"",array("title_html"=>true));
$form->field_htmlrow("","<div style=\"width:650px;\"><i>".html("verblijf_tussen_uitleg","vraagonsadvies")."</i></div>");
$form->field_date(0,"verblijf_tussen_van",txt("verblijf_tussen_van","vraagonsadvies"),"","",array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
$form->field_date(0,"verblijf_tussen_tot",txt("verblijf_tussen_tot","vraagonsadvies"),"","",array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
$form->field_select(0,"aantalvolwassenen",txt("aantalvolwassenen","vraagonsadvies"),"","",array("selection"=>$vars["aantalvolwassenen"]));
$form->field_select(0,"aantalkinderen",txt("aantalkinderen","vraagonsadvies"),"","",array("selection"=>$vars["aantalkinderen"]),array("add_html_after_title"=>"<div style=\"margin-top:4px;font-size:0.8em;\">(".html("totenmet12","vraagonsadvies").")</div>"));
$form->field_select(0,"aantalslaapkamers",txt("aantalslaapkamers","vraagonsadvies"),"","",array("selection"=>$vars["aantalslaapkamers"]));
$form->field_checkbox(0,"soortaccommodatie",txt("soortaccommodatie","vraagonsadvies"),"","",array("selection"=>$vars["soortaccommodatie_keuzes"]),array("one_per_line"=>true));
$form->field_select(0,"budgetindicatie",txt("budgetindicatie","vraagonsadvies"),"","",array("selection"=>$vars["budgetindicatie_keuzes"]));
$form->field_textarea(0,"toelichting",txt("toelichting","vraagonsadvies"),"","","",array("add_html_after_field"=>"<div style=\"margin-top:2px;margin-bottom:3px;font-size:0.8em;width:480px;\">".html("toelichting_uitleg","vraagonsadvies")."</div>"));
$form->field_text(0,"naam",txt("naam","vraagonsadvies"));
$form->field_email(1,"emailadres",txt("emailadres","vraagonsadvies"),"","","",array("add_html_after_field"=>"<div style=\"margin-top:4px;font-size:0.8em;width:480px;\">".html("ditmailadreszalniet","vraagonsadvies")."</div>"));
$form->field_text(0,"telefoonnummer",txt("telefoonnummer","vraagonsadvies"));

$form->check_input();

if($form->filled) {
	if($form->input["verblijf_tussen_van"]["unixtime"] and $form->input["verblijf_tussen_tot"]["unixtime"] and $form->input["verblijf_tussen_van"]["unixtime"]>$form->input["verblijf_tussen_tot"]["unixtime"]) $form->error("verblijf_tussen_tot","kies een datum die later ligt dan de eerste datum","","","Verblijf tussen (tweede datum)");
	if($form->input["verblijf_tussen_van"]["unixtime"] and $form->input["verblijf_tussen_van"]["unixtime"]<time()) $form->error("verblijf_tussen_van","kies een datum in de toekomst","","","Verblijf tussen (eerste datum)");
	if($form->input["verblijf_tussen_tot"]["unixtime"] and $form->input["verblijf_tussen_tot"]["unixtime"]<time()) $form->error("verblijf_tussen_tot","kies een datum in de toekomst","","","Verblijf tussen (tweede datum)");
}

if($form->okay) {
	# Gegevens mailen
	$form->mail($vars["email"],"","Vraag ons advies","","","",$vars["email"],"",array("replyto"=>$form->input["emailadres"]));
}
$form->end_declaration();

include "content/opmaak.php";

?>