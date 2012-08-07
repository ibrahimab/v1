<?php

#$vars["verberg_linkerkolom"]=true;
#$robot_noindex=true;
$vars["jquery_fancybox"]=true;

include("admin/vars.php");

# Wintersport Magazine uitgeschakeld op verzoek van Bjorn (07-08-2012)
if($vars["website"]=="C") {
	header("Location: ".$vars["path"]);
	exit;
}


# Afbeeldingen resizen
$files[]="leeg.gif";
$d=dir("pic/folder/");
while(false!==($entry=$d->read())) {
	if($entry<>"." and $entry<>".." and ereg("folder_".$vars["seizoentype"]."_page",$entry)) {
		if(!file_exists("pic/folder_tn/".$entry)) {
			wt_create_thumbnail("pic/folder/".$entry,"pic/folder_tn/".$entry,350,"",false,"jpg",100);
		}
		$files[]=$entry;
	}
}
$d->close();

if(is_array($files)) {
	sort($files);
	if(count($files)&1) {
		$files[]="leeg.gif";
	}
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm"); 
$form->settings["fullname"]="Magazine-aanvraag";
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]="MAGAZINE AANVRAGEN";
#$form->settings["target"]="_blank";
 
# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

#_field: (obl),id,title,db,prevalue,options,layout

$form->field_htmlrow("","<i>Vul onderstaande gegevens in om ons magazine thuisgestuurd te krijgen:</i>");
$form->field_text(1,"naam","Naam");
$form->field_text(1,"adres","Adres");
$form->field_text(1,"postcode","Postcode");
$form->field_text(1,"plaats","Plaats");
$form->field_email(1,"email","E-mailadres");

$form->check_input();

if($form->filled) {

}

if($form->okay) {
	# Gegevens mailen
	$form->mail("info@chalet.nl","","Ingevulde magazine-aanvraag ".($vars["seizoentype"]==2 ? "zomer" : "winter"));
}

$form->end_declaration();

include "content/opmaak.php";

?>