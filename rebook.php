<?php

include("admin/vars.php");

if($_GET["bid"] and $_GET["c"]==substr(sha1($_GET["bid"]."_WT_488439fk3"),0,8)) {

	$gegevens=get_boekinginfo($_GET["bid"]);
	nawcookie($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"],$gegevens["stap2"]["adres"],$gegevens["stap2"]["postcode"],$gegevens["stap2"]["plaats"],$gegevens["stap2"]["land"],$gegevens["stap2"]["telefoonnummer"],$gegevens["stap2"]["mobielwerk"],$gegevens["stap2"]["email"],$gegevens["stap2"]["geboortedatum"],0,$gegevens["stap2"]["geslacht"]);

	setcookie("rebook",$_GET["bid"]."_".$_GET["c"],0,"/");

	if($_GET["goto"]) {
		# Doorsturen naar ander onderdeel van de site?
		if($_GET["goto"]=="accommodatie") {
			if($gegevens["stap1"]["accinfo"]["url_zonderpad"]) {
				$goto=$vars["path"].$gegevens["stap1"]["accinfo"]["url_zonderpad"];
			}
		} elseif($_GET["goto"]=="contact") {
			$goto=$vars["path"].txt("menu_contact").".php";
		} elseif($_GET["goto"]=="zoekenboek") {
			$goto=$vars["path"].txt("menu_zoek-en-boek").".php";
		}
	}

	if(!$goto) $goto=$vars["path"];

	header("Location: ".$goto);
	exit;



	#
	# NIET MEER IN GEBRUIK (22-02-2013)
	#

	# Formulier
	$form=new form2("frm");
	$form->settings["fullname"]="Mailing bestaande klanten vorig seizoen";
	$form->settings["layout"]["css"]=false;
#	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	$form->settings["language"]=$vars["taal"];
	#$form->settings["target"]="_blank";

	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

	#_field: (obl),id,title,db,prevalue,options,layout

	$form->field_onlyinoutput("oud_boekingsnummer","Boekingsnummer vorig seizoen","",array("text"=>"<a href=\"http://www.chalet.nl/cms_boekingen.php?show=21&21k0=".$gegevens["stap1"]["boekingid"]."\">".$gegevens["stap1"]["boekingsnummer"]."</a>"));
	$form->field_htmlrow("",html("wanneerjenogniet","rebook"));
	$form->field_text(0,"contactmoment",txt("contactmoment","rebook"));
	$form->field_textarea(0,"opmerkingen",txt("overigevragen","rebook"),"","","",array("newline"=>true));
	$form->field_email(1,"email",txt("email","rebook"),"",array("text"=>$gegevens["stap2"]["email"]));

	$form->check_input();

	if($form->filled) {

	}

	if($form->okay) {
		$form->mail("info@chalet.nl","","Ingevuld formulier n.a.v. mailing bestaande klanten vorig seizoen");
		chalet_log("formulier ingevuld n.a.v. mailing bestaande klanten vorig seizoen",true,true);
	}
	$form->end_declaration();

	include "content/opmaak.php";
} else {
	header("Location: /");
	exit;
}

?>