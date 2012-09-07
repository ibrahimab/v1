<?php

$mustlogin=true;
include("admin/vars.php");

$gegevens=get_boekinginfo($_GET["bid"]);


if($_GET["controleren"]) {
	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm"); 
	$form->settings["fullname"]="beoordelingen";
	$form->settings["layout"]["css"]=false;
	$form->settings["db"]["table"]="boeking_enquete";
	$form->settings["db"]["where"]="boeking_id='".intval($_GET["bid"])."'";
	$form->settings["layout"]["stars"]=false;
	
	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	#$form->settings["target"]="_blank";
	 
	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen
	
	#_field: (obl),id,title,db,prevalue,options,layout
	
	$form->field_htmlrow("","<b>Bekijk onderstaande enqu&ecirc;te en vul de status in.</b>");
	$form->field_select(1,"beoordeeld","Status",array("field"=>"beoordeeld"),"",array("selection"=>$vars["enquetestatus"],"allow_0"=>true));
	$db->query("SELECT websitetekst FROM boeking_enquete WHERE boeking_id='".addslashes($_GET["bid"])."';");
	if($db->next_record() and $db->f("websitetekst")<>"") {
		$form->field_textarea(0,"websitetekst_gewijzigd","Totaaloordeel",array("field"=>"websitetekst_gewijzigd"),"",array("newline"=>true));
	} else {
		$form->field_htmlcol("","Totaaloordeel",array("html"=>"<i>niet ingevuld</i>"));
	}
	
	$form->check_input();
	
	if($form->filled) {

	}
	
	if($form->okay) {
	
		# Gegevens opslaan in de database
		$form->save_db();
		
		if($_GET["from"]) {
			header("Location: ".$_GET["from"]);
			exit;
		} else {
			header("Location: ".$vars["path"]."cms_overzichten_overig.php?t=11");
			exit;
		}
	}
	$form->end_declaration();
}

$layout->display_all($cms->page_title);

?>