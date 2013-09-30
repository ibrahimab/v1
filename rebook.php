<?php

include("admin/vars.php");

#
# Systeem voor plaatsen cookie na klikken op link "mail n.a.v. boeking vorig seizoen"
#
# Dit cookie geeft de bezoeker (deze sessie) de mogelijkheid een langere optie af te sluiten
#
if($_GET["bid"] and $_GET["c"]==substr(sha1($_GET["bid"]."_WT_488439fk3"),0,8)) {

	$gegevens=get_boekinginfo($_GET["bid"]);
	nawcookie($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"],$gegevens["stap2"]["adres"],$gegevens["stap2"]["postcode"],$gegevens["stap2"]["plaats"],$gegevens["stap2"]["land"],$gegevens["stap2"]["telefoonnummer"],$gegevens["stap2"]["mobielwerk"],$gegevens["stap2"]["email"],$gegevens["stap2"]["geboortedatum"],0,$gegevens["stap2"]["geslacht"],$gegevens["stap2"]["houseno"]);

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

} else {
	header("Location: /");
	exit;
}

?>