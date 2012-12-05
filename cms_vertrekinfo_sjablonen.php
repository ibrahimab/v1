<?php

$mustlogin=true;

include("admin/vars.php");

if(!$_GET["wzt"]) {
	if($_GET["54k0"]) {
		$db->query("SELECT wzt FROM vertrekinfo_sjabloon WHERE vertrekinfo_sjabloon_id='".addslashes($_GET["54k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

$cms->settings[54]["list"]["show_icon"]=false;
$cms->settings[54]["list"]["edit_icon"]=true;
$cms->settings[54]["list"]["delete_icon"]=true;

$cms->db[54]["where"]="wzt='".addslashes($_GET["wzt"])."'";
$cms->db[54]["set"]="wzt='".addslashes($_GET["wzt"])."'";

$vars["vertrekinfo_sjablonen_soorten"]=array(1=>"incheck-tekst");

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(54,"text","naam");
$cms->db_field(54,"select","soort","",array("selection"=>$vars["vertrekinfo_sjablonen_soorten"]));
$cms->db_field(54,"textarea","tekst");



# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(54,"naam","Naam");
$cms->list_field(54,"soort","Soort");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(54,1,"soort");
$cms->edit_field(54,1,"naam");
$cms->edit_field(54,0,"htmlcol","Beschikbare variabelen",array("html"=>"<table style=\"margin-top:15px;margin-bottom:15px;width:675px;\" class=\"tbl\" cellspacing=\"0\"><tr style=\"font-weight:bold;\"><th>variabele</th><th>omschrijving</th><th>voorbeeldwaarde</th></tr>

                 <tr><td>[inchecktijd]</td><td>tijdstip van inchecken</td><td>17:00</td></tr>
                 <tr><td>[uiterlijke_inchecktijd]</td><td>uiterlijke inchecktijd</td><td>19:00</td></tr>
                 <tr><td>[uitchecktijd]</td><td>tijdstip van uitchecken</td><td>09:00</td></tr>
                 <tr><td>[borgbedrag]</td><td>bedrag van de borg (valuta staat in de variabele)</td><td>&euro;&nbsp;150,-</td></tr>
                 <tr><td>[telefoonnummer]</td><td>telefoonnummer</td><td>0039 0437 72 38 05</td></tr>

                 </table>"));
$cms->edit_field(54,1,"tekst","","","",array("rows"=>20));


# Show
#$cms->show_name[54]="plaatsgegevens";
#$cms->show_mainfield[54]="naam";
#$cms->show_field(54,"naam","Naam");
#$cms->show_field(54,"pdfplattegrond","Plattegrond");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(54);
if($cms_form[54]->filled) {

}

# Na opslaan form de volgende actie uitvoeren
if($cms_form[54]->okay) {

}

function form_before_goto($form) {
	global $db0,$login,$vars;
}

# Controle op delete-opdracht
if($_GET["delete"]==54 and $_GET["54k0"]) {

}


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>