<?php

$mustlogin=true;

include("admin/vars.php");

if(!$_GET["websitetype"]) {
	if($_GET["44k0"]) {
		$db->query("SELECT websitetype FROM blog WHERE blog_id='".addslashes($_GET["44k0"])."';");
		if($db->next_record()) {
			$_GET["websitetype"]=$db->f("websitetype");
		}
	} else {
		$_GET["websitetype"]=1;
	}
}

$vars["wysiwyg_info"].="[rechts_afbeelding_1]: afbeelding 1 rechts\n\n[link_afbeelding_2]: afbeelding 2 links\n\n[centreer_afbeelding_3]: afbeelding 3 gecentreerd\n\n---------- = horizontale lijn\n\n----- = nieuwe regel forceren";

/*
# Gekoppelde plaatsen en regio's opslaan
if($_POST["toevoegen_filled"]) {
	if($_POST["plaatsen"]) {
		$db->query("INSERT INTO blog_plaats SET plaats_id='".addslashes($_POST["plaatsen"])."', blog_id='".addslashes($_GET["44k0"])."', adddatetime=NOW(), editdatetime=NOW();");
	}
	if($_POST["skigebieden"]) {
		$db->query("INSERT INTO blog_skigebied SET skigebied_id='".addslashes($_POST["skigebieden"])."', blog_id='".addslashes($_GET["44k0"])."', adddatetime=NOW(), editdatetime=NOW();");
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}
*/

$cms->db[44]["where"]="websitetype='".addslashes($_GET["websitetype"])."'";
$cms->db[44]["set"]="websitetype='".addslashes($_GET["websitetype"])."'";


$cms->settings[44]["list"]["show_icon"]=true;
$cms->settings[44]["list"]["edit_icon"]=false;
$cms->settings[44]["list"]["delete_icon"]=true;
$cms->settings[44]["show"]["goto_new_record"]=true;

#$cms->db[44]["where"]="wzt='".addslashes($_GET["wzt"])."'";
#$cms->db[44]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(44,"yesno","actief");
$cms->db_field(44,"text","titel");
$cms->db_field(44,"textarea","inleiding");
$cms->db_field(44,"textarea","inhoud");
$cms->db_field(44,"yesno","homepage_actief");
$cms->db_field(44,"text","homepage_titel");
$cms->db_field(44,"textarea","homepage_inleiding");
$cms->db_field(44,"text","accommodatiecodes");
$cms->db_field(44,"date","plaatsingsdatum");
$cms->db_field(44,"select","categorie","",array("selection"=>$vars["blogcategorie"][$_GET["websitetype"]]));
$cms->db_field(44,"picture","afbeelding","",array("savelocation"=>"pic/cms/blog/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(44,"picture","afbeelding_onderaan","",array("savelocation"=>"pic/cms/blog_onderaan/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(44,"picture","homepage_afbeelding","",array("savelocation"=>"pic/cms/blog_homepage/","filetype"=>"jpg","multiple"=>false));



# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[44]=array("plaatsingsdatum","titel");
$cms->list_sort_desc[44]=true;
$cms->list_field(44,"titel","Titel");
$cms->list_field(44,"actief","Actief");
$cms->list_field(44,"plaatsingsdatum","Tonen vanaf",array("date_format"=>"D MAAND JJJJ"));

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(44,0,"actief","Actief",array("selection"=>true));
$cms->edit_field(44,1,"plaatsingsdatum","Tonen vanaf",array("time"=>time()),"",array("calendar"=>true));
$cms->edit_field(44,1,"titel");
$cms->edit_field(44,1,"categorie","Categorie");
$cms->edit_field(44,1,"inleiding");
$cms->edit_field(44,1,"inhoud","Inhoud","","",array("rows"=>60,"info"=>$vars["wysiwyg_info"]));

$cms->edit_field(44,0,"accommodatiecodes","Accommodatiecodes (gescheiden door komma's)");
#$cms->edit_field(44,1,"afbeelding","Afbeelding","",array("autoresize"=>true,"img_maxwidth"=>"300"));
$cms->edit_field(44,0,"afbeelding","Afbeeldingen voor in artikel","",array("autoresize"=>false,"number_of_uploadbuttons"=>6));
$cms->edit_field(44,0,"htmlrow","<hr>");
$cms->edit_field(44,0,"afbeelding_onderaan","Afbeeldingen onderaan","",array("autoresize"=>false,"number_of_uploadbuttons"=>6));

$cms->edit_field(44,0,"htmlrow","<hr><b>Verwijzing op de homepage</b>");
$cms->edit_field(44,0,"homepage_actief","Tonen op de homepage",array("selection"=>true));
$cms->edit_field(44,0,"homepage_titel","Titel");
$cms->edit_field(44,0,"homepage_inleiding","Inleidende tekst");
$cms->edit_field(44,1,"homepage_afbeelding","Afbeelding","",array("autoresize"=>true,"img_width"=>"148","img_maxheight"=>"99"));



# Show
$cms->show_name[44]="blog";
$cms->show_mainfield[44]="titel";
$cms->show_field(44,"titel","Titel");
$cms->show_field(44,"homepage_afbeelding","Afbeelding homepage + overzicht");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(44);
if($cms_form[44]->filled) {
	if($cms_form[44]->input["homepage_actief"] and !$cms_form[44]->input["homepage_titel"]) {
		$cms_form[44]->error("homepage_titel","voer ook een homepage-titel in");
	}
	if($cms_form[44]->input["homepage_actief"] and !$cms_form[44]->input["homepage_inleiding"]) {
		$cms_form[44]->error("homepage_inleiding","voer ook een homepage-inleiding in");
	}
}

# Na opslaan form de volgende actie uitvoeren
function form_before_goto($form) {
	global $db0,$login;

}

# Controle op delete-opdracht
if($_GET["delete"]==44 and $_GET["44k0"]) {

}


#
# Koppeling met blog_plaats
#
$cms->settings[44]["connect"][]=45;
$cms->settings[45]["parent"]=44;

$cms->settings[45]["list"]["show_icon"]=false;
$cms->settings[45]["list"]["edit_icon"]=false;
$cms->settings[45]["list"]["delete_icon"]=true;
$cms->settings[45]["list"]["add_link"]=true;
$cms->settings[45]["list"]["delete_checkbox"]=true;

$cms->db[45]["where"]="blog_id='".addslashes($_GET["44k0"])."'";
$cms->db[45]["set"]="blog_id='".addslashes($_GET["44k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
if($_GET["websitetype"]==7) {
	// plaatsen Italissima
	$cms->db_field(45,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>"wzt=2 AND land_id=5"));
} else {
	// plaatsen Chalet
	$cms->db_field(45,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>"wzt=1"));
}

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[45]=array("plaats_id");
$cms->list_field(45,"plaats_id","Naam");


#
# Koppeling met blog_skigebied
#
$cms->settings[44]["connect"][]=46;
$cms->settings[46]["parent"]=44;

$cms->settings[46]["list"]["show_icon"]=false;
$cms->settings[46]["list"]["edit_icon"]=false;
$cms->settings[46]["list"]["delete_icon"]=true;
$cms->settings[46]["list"]["add_link"]=true;
$cms->settings[46]["list"]["delete_checkbox"]=true;

$cms->db[46]["where"]="blog_id='".addslashes($_GET["44k0"])."'";
$cms->db[46]["set"]="blog_id='".addslashes($_GET["44k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
if($_GET["websitetype"]==7) {
	// regio's Italissima
	$cms->db_field(46,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"wzt=2 AND skigebied_id IN (SELECT DISTINCT skigebied_id FROM plaats WHERE land_id=5)"));
} else {
	// skigebieden Chalet
	$cms->db_field(46,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"wzt=1"));
}

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[46]=array("skigebied_id");
$cms->list_field(46,"skigebied_id","Naam");



#
# Koppeling met blog_reactie
#
$cms->settings[44]["connect"][]=47;
$cms->settings[47]["parent"]=44;

$cms->settings[47]["list"]["show_icon"]=false;
$cms->settings[47]["list"]["edit_icon"]=true;
$cms->settings[47]["list"]["delete_icon"]=true;
$cms->settings[47]["list"]["add_link"]=false;
$cms->settings[47]["list"]["delete_checkbox"]=true;

$cms->db[47]["where"]="blog_id='".addslashes($_GET["44k0"])."'";
$cms->db[47]["set"]="blog_id='".addslashes($_GET["44k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(47,"yesno","actief");
$cms->db_field(47,"datetime","adddatetime");
$cms->db_field(47,"text","naam");
$cms->db_field(47,"email","email");

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[47]=array("adddatetime");
$cms->list_field(47,"adddatetime","Datum/tijd",array("date_format"=>"DD-MM-JJJJ UU:ZZ"));
$cms->list_field(47,"naam","Naam");
$cms->list_field(47,"email","E-mail");
$cms->list_field(47,"actief","Tonen");


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>