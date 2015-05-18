<?php

#
#
# Verschil onlyinoutput / noedit :
# noedit wordt opgeslagen in database
# maar wordt 'ie ook goed getoond in een mailtje als 't een noedit met "selection" is?
#
# onlyinoutput: onduidelijk!
#
#
#
# Controle op "Content-Type" en "MIME-Version: 1.0" nog bouwen!!
#
# Nog doen:
# - minyear/maxyear verwerken in class.form_calendar.php (zodat niet beschikbare jaren niet te kiezen zijn)
# - NULL-fields vullen (checken of $this->value[$key] een waarde heeft bij check_input)
# - imagedelete alleen uitvoeren als een "picture" is gedeclareerd
# - geuploade bestanden aan mailtje koppelen
# - prevalue via $_GET["pv_..."] werkt nog niet voor een date-field
# - Handleiding schrijven
# - euro-teken testen in $this->input
# - prevalue werkt nog niet voor alle field-types
# - rare tekens wegfilteren (vreemde ' en "-tekens)
# - bij output_cell vullen: htmlentities toepassen
# - goede htmlentities vinden
# -
# - functie display_output_field afronden  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
# - display_output_field voorzien van htmlentities
# - bij functie check_input $this->outputtable_cell dmv functie display_output_field vullen <<<<<<<<<<
# - maxlength aan <input type=text> toevoegen
# - bij currency-fields euro-teken plaatsen
#
#

#
#
# _field: (obl),id,title,db,prevalue,options,layout
#


#
#
#
# Werkend voorbeeld
#
#
#


/*
# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Naam";
$form->settings["layout"]["css"]=false;
$form->settings["db"]["table"]="lid";
$form->settings["db"]["where"]="lid_id='".intval($_GET["lidid"])."'";
$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
$form->settings["prevent_csrf"] = true;
$form->settings["prevent_spambots"] = true;
#$form->settings["target"]="_blank";

# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

#_field: (obl),id,title,db,prevalue,options,layout

$form->field_text(1,"test","test",array("field"=>"test")); # (opslaan in databaseveld "test")
$form->field_text(1,"naam","Naam");
$form->field_htmlrow("","<b>Hier staat een stuk html-tekst over de hele regel</b>");
$form->field_email(1,"email","E-mailadres");
$form->field_url(1,"url","URL");
$form->field_password(1,"password","Kies een wachtwoord","","",array("new_password"=>true,"strong_password"=>true));
$form->field_password(1,"password2","Herhaal wachtwoord");
$form->field_textarea(1,"vraag","Vraag","","","",array("newline"=>true));
$form->field_htmlcol("","Waarde",array("html"=>"<b>hier html-tekst alleen in de rechterkolom</b>"));
$form->field_date(1,"eerstevoorkeur","Eerste voorkeur","","",array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
$form->field_yesno("verlengdconsult","Verlengd consult");
$form->field_yesno("standaardaan","Deze yesno-checkbox staat standaard aan","",array("selection"=>true));
$form->field_yesno("akkoord","Ik ga akkoord met de </label><a href=\"javascript:popwindowXY(500,400,'popup.php?c=algemenevoorwaarden',false);\">algemene voorwaarden</a>.","","","",array("title_html"=>true));
$form->field_textarea(1,"opmerkingen","Reden consult / klacht / eventuele opmerkingen","","","",array("newline"=>true));
$form->field_select(1,"sjabloon1","Opmaak","","",array("selection"=>$vars["sjablonen"]));
$form->field_checkbox(1,"sjabloon2","Opmaak-checkbox","","",array("selection"=>$vars["sjablonen"]));
$form->field_date(1,"geboortedatum","Geboortedatum","","",array("startyear"=>1900,"endyear"=>date("Y")));
$form->field_radio(1,"geslacht","Geslacht","","",array("selection"=>$vars["geslacht"]));
$form->field_upload(1,"afbeelding","Afbeelding","","",array("move_file_to"=>"pic/uploads/","must_be_filetype"=>"jpg","showfiletype"=>true,"rename_file_to"=>"1"),"");

$form->check_input();

if($form->filled) {
	if($form->input["naam"]=="hallo") $form->error("naam","Hallo is niet toegestaan");
	if($form->input["geboortedatum"]["unixtime"]>time()) $form->error("geboortedatum","geen datum in de toekomst mogelijk","","","Geboortedatum persoon 1");
	if($form->input["password"] and $form->input["password"]<>$form->input["password2"]) $form->error("password2","voer 2x hetzelfde wachtwoord in");
	if(!$form->input["akkoord"]) $form->error("akkoord","u dient akkoord te gaan met onze voorwaarden");
}

if($form->okay) {

	# Gegevens mailen
	$form->mail("jeroen@webtastic.nl","","Ingevuld formulier");

	# Gegevens opslaan in de database
	$form->save_db();
}
$form->end_declaration();

# Content
if($_GET["fo"]=="frm") {
	echo "Bedankt";
} else {
	$form->display_all();
}

*/

# ==================
# Externe variabelen
# ==================
#
# $this->input[$key] : wat is er ingevoerd?
# $this->db_insert_id : welk id heeft het zojuist ge-inserte database-record?
#
# ==================
# Interne variabelen
# ==================
# $this->value[$key] : $_POST["input"][$key]
#
# ==========================
# Opmerkingen field-functies
# ==========================
# field_currency: alles werkt met punten als scheidingsteken, behalve het invoerformulier en de outputtable
#
#
# ===================
# Instellingen fields
# ===================
#
# _field: (obl),id,title,db,prevalue,options,layout
#
# db:
#	- field
#	- table
#	- where
#	- not_unixtime
#
# prevalue:
#	- text
#	- force_text (altijd deze waarde gebruiken (ongeacht database-waarde): werkt alleen nog bij field_text-velden)
#	- html (bijv. bij htmlcol)
#	- selection
#	- year
#	- month
#	- day
#	- hour
#	- minute
#	- time
#
# options:
#	- selection
#	- optgroup (bij field_select): array met de optgroups
#	- subselection (subtekst bij selection; werkt alleen nog maar bij radio)
#	- startyear
#	- endyear
#	- min_jump (minutes jump bij datetime-pulldown)
#	- move_file_to
#	- rename_file_to
#	- img_width
#	- img_height
#	- img_maxwidth
#	- img_maxheight
#	- autoresize (op basis van img_width, img_height, img_maxwidth)
# 	- autoresize_cut (afbeeldingen snijden indien te groot?)
#	- mail_as_filename (naam van attachment bij mailen upload)
#	- must_be_filetype
#	- showfiletype
#	- maxlength
#	- strong_password (minimaal 6 tekens met cijfers)
#	- superstrong_password (minimaal 6 tekens met hoofd- en kleine letters en cijfers)
#	- new_password (invoer nieuw (of anders: reeds bestaand) wachtwoord)
#	- delete_password_input (vul wachtwoord niet automatisch in bij een niet-okay form)
#	- no_empty_first_selection (bij select: geen lege keuze bovenaan select-items)
#	- onfocus (werkt alleen nog maar bij textarea)
#	- day_onfocus
#	- day_onchange
#	- month_onfocus
#	- month_onchange
#	- year_onfocus
#	- year_onchange
#	- hour_onblur
#	- hide_year (bij date- en datetimefield)
#	- multiple (meerdere afbeeldingen uploaden)
#	- number_of_uploadbuttons (aantal uploadbuttons in geval van multiple)
#	- negative: bij float, integer en currency negatieve waarde toestaan
#	- empty_is_0: bij selectie-field: lege <option> krijgt value "0" mee
#	- dontsave: niet in database opslaan
#	- allow_0: een 0 als waarde toestaan bij een verplicht select-field
#	- show_at_other_field: geef veldnaam op waar dit veld getoond moet worden (bijv. checkboxen binnen een radio-keuze)
#	- htmlrow_in_output
#
# layout:
#	- title_html (titel bevat html)
#	- content_html (content bevat html (bijvoorbeeld de selection-array))
#	- title_style
#	- content_style
#	- calendar
#	- input_class (andere class voor input-veld)
#	- rows (bij textarea)
#	- cols (bij textarea)
#	- newline (bij textarea)
#	- one_per_line (bij radio en checkbox)
#	- onclick (bij yesno-checkbox, checkbox en radio al gemaakt, rest nog niet) =======>>>>> ADVIES: gebruik onclick ipv onchange!! (vanwege verschil IE/Firefox bij onchange)
#	- onchange (bij yesno-checkbox, date, select en radio al gemaakt, rest nog niet)
#	- tr_id (id van de <tr>)
#	- tr_class (class van de <tr>)
#	- tr_style (style van de <tr>)
#	- style (style van de textarea)
#	- indent (inspringen indien checkbox-keuze meerdere regels bevatten)
#	- notitle: toon geen title-cel in de linkerkolom (maar gebruik 1 kolom over de hele breedte)
#	- add_html_after_title: html die na het title-veld wordt getoond (binnen de td)
#	- add_html_after_field: html die na het input-veld wordt getoond (binnen de td)
#	- verberg_imgsize: bij uploadfield: imagesize niet tonen
# 	- verberg_afbeeldingwissen: bij uploadfield: keuze 'afbeelding wissen' niet tonen
#

#
# =====================
# Instellingen settings
# =====================
#
#	- bcc_mail_https: stuur bij een https-formulier toch een bcc naar track@webtastic.nl
# zie verder hieronder
#

class form2 {

	function form2($name="frm") {
		global $vars;

		$this->settings["formname"]=$name;

		$this->settings["language"]="nl";
		$this->settings["type"]="post";
		$this->settings["mail"]["send"]=false;
		$this->settings["layout"]["stars"]=true;
		$this->settings["layout"]["css"]=false;
		$this->settings["layout"]["top_submit_button"]=false;
		$this->settings["db"]["table"]="";
		$this->settings["db"]["where"]="";
		$this->settings["layout"]["goto_aname"]="wtform_".$this->settings["formname"];
		$this->settings["go_nowhere"]=false;
		$this->settings["path"]="";
		$this->settings["error"]["li_css_paddingleft"]=true;
		$this->settings["show_save_message"]=false;
		$this->settings["show_upload_message"]=false;
		$this->settings["bcc_mail_https"]=false;
		$this->settings["download_uploaded_files"]=true;

		$this->settings["add_to_filesync_table"] = false;
		$this->settings["add_to_filesync_table_source"] = 0;


		// Cross-site request forgery - http://css-tricks.com/serious-form-security/
		$this->settings["prevent_csrf"]=false;

		// Block spambots (via JavaScript after submit-click - allfunctions.js is necessary!)
		$this->settings["prevent_spambots"]=false;

		# Messages
		$this->settings["message"]["verplichtveld"]["nl"]="Verplicht veld";
		$this->settings["message"]["verplichtveld"]["en"]="Mandatory field";
		$this->settings["message"]["verplichtveld"]["de"]="Pflichtangaben";
		$this->settings["message"]["verplichtveld"]["fr"]="Champ obligatoire";

		$this->settings["message"]["submitbutton"]["nl"]="OK";
		$this->settings["message"]["submitbutton"]["en"]="OK";
		$this->settings["message"]["submitbutton"]["de"]="OK";
		$this->settings["message"]["submitbutton"]["fr"]="OK";

		$this->settings["message"]["kalender"]["nl"]="Kalender";
		$this->settings["message"]["kalender"]["en"]="Calendar";
		$this->settings["message"]["kalender"]["de"]="Kalender";
		$this->settings["message"]["kalender"]["fr"]="calendrier";

		$this->settings["message"]["u"]["nl"]="u";
		$this->settings["message"]["u"]["en"]="h";
		$this->settings["message"]["u"]["de"]="h";
		$this->settings["message"]["u"]["fr"]="h";

		$this->settings["message"]["ja"]["nl"]="ja";
		$this->settings["message"]["ja"]["en"]="yes";
		$this->settings["message"]["ja"]["de"]="ja";
		$this->settings["message"]["ja"]["fr"]="oui";

		$this->settings["message"]["nee"]["nl"]="nee";
		$this->settings["message"]["nee"]["en"]="no";
		$this->settings["message"]["nee"]["de"]="nein";
		$this->settings["message"]["nee"]["fr"]="non";

		$this->settings["message"]["volgendegegevens"]["nl"]="De volgende gegevens zijn via [URL] ingevoerd";
		$this->settings["message"]["volgendegegevens"]["en"]="The following data has been entered on [URL]";
		$this->settings["message"]["volgendegegevens"]["de"]="De volgende gegevens zijn via [URL] ingevoerd";
		$this->settings["message"]["volgendegegevens"]["fr"]="Les données suivantes ont été inscrites sur [URL]";

		$this->settings["message"]["invoer"]["nl"]="Invoer";
		$this->settings["message"]["invoer"]["en"]="Form";
		$this->settings["message"]["invoer"]["de"]="Eingang";
		$this->settings["message"]["invoer"]["fr"]="Forme";

		$this->settings["message"]["reeds_ontvangen"]["nl"]="Reeds ontvangen";
		$this->settings["message"]["reeds_ontvangen"]["en"]="File already received";
		$this->settings["message"]["reeds_ontvangen"]["de"]="Datei bereits erhalten";
		$this->settings["message"]["reeds_ontvangen"]["fr"]="Déjà reçu";

		$this->settings["message"]["nog_geen_gegevens"]["nl"]="nog geen gegevens in het systeem";
		$this->settings["message"]["nog_geen_gegevens"]["en"]="no data in system";
		$this->settings["message"]["nog_geen_gegevens"]["de"]="keine Daten im System";
		$this->settings["message"]["nog_geen_gegevens"]["fr"]="pas de données dans le système";

		$this->settings["message"]["pixels"]["nl"]="pixels";
		$this->settings["message"]["pixels"]["en"]="pixels";
		$this->settings["message"]["pixels"]["de"]="pixels";
		$this->settings["message"]["pixels"]["fr"]="pixels";

		$this->settings["message"]["imgsize_size"]["nl"]="_VAL1_x_VAL2_ pixels";
		$this->settings["message"]["imgsize_size"]["en"]="_VAL1_x_VAL2_ pixels";
		$this->settings["message"]["imgsize_size"]["de"]="_VAL1_x_VAL2_ pixels";
		$this->settings["message"]["imgsize_size"]["fr"]="_VAL1_x_VAL2_ pixels";

		$this->settings["message"]["imgsize_ratio"]["nl"]="verhouding _VAL1_:_VAL2_";
		$this->settings["message"]["imgsize_ratio"]["en"]="ratio _VAL1_:_VAL2_";
		$this->settings["message"]["imgsize_ratio"]["de"]="ratio _VAL1_:_VAL2_";
		$this->settings["message"]["imgsize_ratio"]["fr"]="ratio _VAL1_:_VAL2_";

		$this->settings["message"]["showfiletype"]["nl"]="_VAL1_-bestand";
		$this->settings["message"]["showfiletype"]["en"]="_VAL1_-file";
		$this->settings["message"]["showfiletype"]["de"]="_VAL1_-Datei";
		$this->settings["message"]["showfiletype"]["fr"]="_VAL1_-fichier";

		$this->settings["message"]["afbeeldingwissen"]["nl"]="afbeelding wissen";
		$this->settings["message"]["afbeeldingwissen"]["en"]="delete image";
		$this->settings["message"]["afbeeldingwissen"]["de"]="Bild löschen";
		$this->settings["message"]["afbeeldingwissen"]["fr"]="effacer image";

		$this->settings["message"]["afbeeldingvolgorde"]["nl"]="volgorde";
		$this->settings["message"]["afbeeldingvolgorde"]["en"]="order";
		$this->settings["message"]["afbeeldingvolgorde"]["de"]="Ordnung";
		$this->settings["message"]["afbeeldingvolgorde"]["fr"]="ordre";

		$this->settings["message"]["bestandwissen"]["nl"]="bestand wissen";
		$this->settings["message"]["bestandwissen"]["en"]="delete file";
		$this->settings["message"]["bestandwissen"]["de"]="Datei löschen";
		$this->settings["message"]["bestandwissen"]["fr"]="supprimer le fichier";

		$this->settings["message"]["zie_attachment"]["nl"]="zie attachment";
		$this->settings["message"]["zie_attachment"]["en"]="see attachment";
		$this->settings["message"]["zie_attachment"]["de"]="siehe beilage";
		$this->settings["message"]["zie_attachment"]["fr"]="voir fichier joint";

		# Error messages
		$this->settings["message"]["error_foutform"]["nl"]="U heeft niet alle velden correct ingevuld";
		$this->settings["message"]["error_foutform"]["en"]="Not all fields are filled out correctly";
		$this->settings["message"]["error_foutform"]["de"]="Nicht alle Felder sind korrekt ausgefüllt";
		$this->settings["message"]["error_foutform"]["fr"]="Vous n'avez pas rempli tous les champs correctement";

		$this->settings["message"]["error_verplicht"]["nl"]="Velden met een asterisk (*) zijn verplicht";
		$this->settings["message"]["error_verplicht"]["en"]="Fields marked with an asterisk are mandatory";
		$this->settings["message"]["error_verplicht"]["de"]="Sie haben nicht alle benötigten Felder ausgefüllt";
		$this->settings["message"]["error_verplicht"]["fr"]="Les champs suivis d'un astérisque (*) sont obligatoires";

		$this->settings["message"]["error_verplicht_veld"]["nl"]="verplicht veld";
		$this->settings["message"]["error_verplicht_veld"]["en"]="mandatory field";
		$this->settings["message"]["error_verplicht_veld"]["de"]="Pflichtangaben";
		$this->settings["message"]["error_verplicht_veld"]["fr"]="champ obligatoire";

		$this->settings["message"]["error_email"]["nl"]="geen geldig adres";
		$this->settings["message"]["error_email"]["en"]="not a valid address";
		$this->settings["message"]["error_email"]["de"]="falsche Adresse";
		$this->settings["message"]["error_email"]["fr"]="adresse email invalide";

		$this->settings["message"]["error_float"]["nl"]="alleen cijfers en een komma toegestaan";
		$this->settings["message"]["error_float"]["en"]="only digits and a comma allowed";

		$this->settings["message"]["error_float_toomany"]["nl"]="maximum aantal decimalen";
		$this->settings["message"]["error_float_toomany"]["en"]="maximum number of decimals";

		$this->settings["message"]["error_integer"]["nl"]="alleen cijfers toegestaan";
		$this->settings["message"]["error_integer"]["en"]="only digits allowed";

		$this->settings["message"]["error_currency"]["nl"]="voer de hele euro's, vervolgens een komma en dan de centen in";
		$this->settings["message"]["error_currency"]["en"]="enter euro's, a comma and then cents";

		$this->settings["message"]["error_url"]["nl"]="geen correcte url (inclusief 'http://')";
		$this->settings["message"]["error_url"]["en"]="not a valid url (including 'http://')";

		$this->settings["message"]["error_foutedatum"]["nl"]="onjuiste datum";
		$this->settings["message"]["error_foutedatum"]["en"]="wrong date";

		$this->settings["message"]["error_foutetijd"]["nl"]="onjuiste tijd";
		$this->settings["message"]["error_foutetijd"]["en"]="wrong time";

		$this->settings["message"]["error_onvolledigedatum"]["nl"]="onvolledige datum";
		$this->settings["message"]["error_onvolledigedatum"]["en"]="incomplete date";
		$this->settings["message"]["error_onvolledigedatum"]["fr"]="date incomplète";

		$this->settings["message"]["error_onvolledigedatumtijd"]["nl"]="onvolledige datum/tijd";
		$this->settings["message"]["error_onvolledigedatumtijd"]["en"]="incomplete date/time";

		$this->settings["message"]["error_img_size"]["nl"]="onjuiste afmetingen (moet _VAL1_ x _VAL2_ pixels zijn)";
		$this->settings["message"]["error_img_size"]["en"]="wrong size (must be _VAL1_ x _VAL2_ pixels)";

		$this->settings["message"]["error_img_size_width"]["nl"]="onjuiste afmetingen (moet _VAL1_ pixels breed zijn)";
		$this->settings["message"]["error_img_size_width"]["en"]="wrong size (width must be _VAL1_ pixels)";

		$this->settings["message"]["error_img_size_height"]["nl"]="onjuiste afmetingen (moet _VAL1_ pixels hoog zijn)";
		$this->settings["message"]["error_img_size_height"]["en"]="wrong size (height must be _VAL1_ pixels)";

		$this->settings["message"]["error_img_size_maxsize"]["nl"]="onjuiste afmetingen (mag maximaal _VAL1_ x _VAL2_ pixels zijn)";
		$this->settings["message"]["error_img_size_maxsize"]["en"]="wrong size (maximum size is _VAL1_ x _VAL2_ pixels)";

		$this->settings["message"]["error_img_size_maxwidth"]["nl"]="onjuiste afmetingen (mag maximaal _VAL1_ pixels breed zijn)";
		$this->settings["message"]["error_img_size_maxwidth"]["en"]="wrong size (maximum width is _VAL1_ pixels)";

		$this->settings["message"]["error_img_size_maxheight"]["nl"]="onjuiste afmetingen (mag maximaal _VAL1_ pixels hoog zijn)";
		$this->settings["message"]["error_img_size_maxheight"]["en"]="wrong size (maximum height is _VAL1_ pixels)";

		$this->settings["message"]["error_img_size_minsize"]["nl"]="onjuiste afmetingen (moet minimaal _VAL1_ x _VAL2_ pixels zijn)";
		$this->settings["message"]["error_img_size_minsize"]["en"]="wrong size (minimal size is _VAL1_ x _VAL2_ pixels)";

		$this->settings["message"]["error_img_size_minwidth"]["nl"]="onjuiste afmetingen (moet minimaal _VAL1_ pixels breed zijn)";
		$this->settings["message"]["error_img_size_minwidth"]["en"]="wrong size (minimal width is _VAL1_ pixels)";

		$this->settings["message"]["error_img_size_minheight"]["nl"]="onjuiste afmetingen (moet minimaal _VAL1_ pixels hoog zijn)";
		$this->settings["message"]["error_img_size_minheight"]["en"]="wrong size (minimal height is _VAL1_ pixels)";

		$this->settings["message"]["error_img_ratio"]["nl"]="onjuiste afmetingen (verhouding moet _VAL1_:_VAL2_ zijn)";
		$this->settings["message"]["error_img_ratio"]["en"]="wrong size (ratio must be _VAL1_:_VAL2_)";

		$this->settings["message"]["error_filetype"]["nl"]="onjuist bestandsformaat";
		$this->settings["message"]["error_filetype"]["en"]="wrong file format";

		$this->settings["message"]["error_filetype_jpg"]["nl"]="onjuist bestandsformaat (moet jpg zijn)";
		$this->settings["message"]["error_filetype_jpg"]["en"]="wrong file format (must be jpg)";

		$this->settings["message"]["error_filetype_gif"]["nl"]="onjuist bestandsformaat (moet gif zijn)";
		$this->settings["message"]["error_filetype_gif"]["en"]="wrong file format (must be gif)";

		$this->settings["message"]["error_filetype_png"]["nl"]="onjuist bestandsformaat (moet png zijn)";
		$this->settings["message"]["error_filetype_png"]["en"]="wrong file format (must be png)";

		$this->settings["message"]["error_password_strong"]["nl"]="minimaal 6 tekens met zowel cijfers als letters";
		$this->settings["message"]["error_password_strong"]["en"]="use at least 6 characters width both letters and numbers";
		$this->settings["message"]["error_password_strong"]["fr"]="minimum 6 signes avec un mélange de chiffres et de lettres";

		$this->settings["message"]["error_password_superstrong"]["nl"]="minimaal 6 tekens met cijfers, letters, hoofd- en kleine letters";
		$this->settings["message"]["error_password_superstrong"]["en"]="use at least 6 characters width both letters (lowercase and uppercase) and numbers";

		$this->settings["message"]["error_password_spaces"]["nl"]="spaties zijn niet toegestaan";
		$this->settings["message"]["error_password_spaces"]["en"]="spaces are not allowed";
		$this->settings["message"]["error_password_spaces"]["fr"]="espaces ne sont pas autorisés";

		$this->settings["message"]["error_csrf"]["nl"]="Verzendfout - probeer het opnieuw";
		$this->settings["message"]["error_csrf"]["en"]="Send error - please try again";
		$this->settings["message"]["error_csrf"]["fr"]="Envoyer erreur - s'il vous plaît essayer à nouveau";

		$this->settings["message"]["enable_javascript"]["nl"]="Verzendfout - uw browser ondersteunt geen JavaScript";
		$this->settings["message"]["enable_javascript"]["en"]="Send error - your browser doesn't support JavaScript";
		$this->settings["message"]["enable_javascript"]["fr"]="Envoyer erreur - votre navigateur ne supporte pas le JavaScript";


		if($_POST["pg"]) {
			if($_POST[$this->settings["formname"]."_filled"]==1) $this->filled=true;
			$this->value=$_POST["input"];
			$this->post_get_value=$_POST;
		} else {
			if($_GET[$this->settings["formname"]."_filled"]==1) $this->filled=true;
			$this->value=$_GET["input"];
			$this->post_get_value=$_GET;
		}
		return true;
	}

	#
	# Interne functies
	#
	function message($title,$html=true,$value_array="") {
		global $vars;
		if(!$this->settings["message"][$title][$this->settings["language"]]) {
			trigger_error("WT-Error: no ".$this->settings["language"]." translation for ".$title,E_USER_NOTICE);

			$this->settings["message"][$title][$this->settings["language"]] = $this->settings["message"][$title]["nl"];
		}
		$return=$this->settings["message"][$title][$this->settings["language"]];
		while(list($key,$value)=@each($value_array)) {
			$value=strval($value);
			$return=ereg_replace("_VAL".$key."_",$value,$return);
		}
		if($vars["wt_htmlentities_utf8"]) {
			$return = iconv("CP1252", "UTF-8", $return);
		}

		if($html) {
			if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
				$return=wt_he($return);
			} else {
				$return=htmlentities($return,ENT_QUOTES,"iso-8859-15");
			}
		}
		@reset($value_array);
		while(list($key,$value)=@each($value_array)) {
			$return=ereg_replace("_HTML".$key."_",$value,$return);
		}
		return $return;
	}

	function insert_form_tokens() {

		// form security: csrf
		if($this->settings["prevent_csrf"]) {

			$token1 = sha1(uniqid(microtime(), true));
			$token2 = sha1(uniqid(microtime(), true));
			$_SESSION["form_csrf"][$token2] = $token1;

			$return.="<input type=\"hidden\" name=\"".$this->settings["formname"]."_csrf_token\" value=\"".wt_he($token1)."\">";
			$return.="<input type=\"hidden\" name=\"".$this->settings["formname"]."_csrf_token_name\" value=\"".wt_he($token2)."\">";
		}

		return $return;
	}

	function newfield($type,$checktype,$obl,$id,$title,$db,$prevalue,$options,$layout) {
		global $vars;

		# Initialiseren
		if(!$this->init) {
			if($this->settings["language"]=="nl") {
				setlocale(LC_TIME,"nl_NL.ISO_8859-1");
				setlocale(LC_MONETARY,"nl_NL.ISO_8859-1");

				if(!session_id()) {
					# session starten
					if (function_exists("wt_session_start")) {
						wt_session_start();
					} else {
						session_start();
					}
				}
			}

			$this->init=true;
		}
		if(!ereg("^[a-zA-Z0-9_]+$",$id)) {
			trigger_error("WT-Error: Wrong table-id '".$id."'",E_USER_ERROR);
		} elseif($this->fields["type"][$id]) {
			trigger_error("WT-Error: Double table-id '".$id."'",E_USER_ERROR);
		} else {
			$this->counter["fields"]++;
			$this->fields["count"][$id]=$this->counter["fields"];

			$this->fields["type"][$id]=$type;
			$this->fields["checktype"][$id]=$checktype;
			if($obl) $this->fields["obl"][$id]=$obl;
			$this->fields["title"][$id]=$title;
			if($db) $this->fields["db"][$id]=$db;
			if($prevalue) $this->fields["prevalue"][$id]=$prevalue;
			if($options) $this->fields["options"][$id]=$options;
			if($layout) $this->fields["layout"][$id]=$layout;
		}
		# Database-instellingen in $this->db plaatsen
		if(is_array($db)) {
			if($db["table"]) $table=$db["table"]; else $table=$this->settings["db"]["table"];
			if($db["where"]) $this->db[$table]["where"]=$db["where"]; else $this->db[$table]["where"]=$this->settings["db"]["where"];
			$this->db[$table]["fields"][$id]=$db["field"];
			if($this->db[$table]["selectquery"]) $this->db[$table]["selectquery"].=", ".$db["field"]; else $this->db[$table]["selectquery"]=$db["field"];
		}
	}

	#
	#
	# Field-functies (alfabetisch)
	#
	#

	function field_checkbox($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("checkbox","checkbox",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_currency($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("text","currency",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_date($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("datetime","date",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_datetime($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("datetime","datetime",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_email($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("text","email",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_float($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("text","float",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_hidden($id,$value,$options="") {
		$this->hidden[$id]["value"]=$value;
		$this->hidden[$id]["options"]=$options;
	}

	function field_htmlcol($id,$title,$prevalue,$options="",$layout="") {
		if(!$id) {
			$this->htmlcol_counter++;
			$id="htmlcol_".$this->htmlcol_counter;
		}
		$this->newfield("htmlcol","htmlcol",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_htmlrow($id,$html,$options="",$layout="") {
		if(!$id) {
			$this->htmlrow_counter++;
			$id="htmlrow_".$this->htmlrow_counter;
		}
		$this->newfield("htmlrow","htmlrow",$obl,$id,$html,$db,$prevalue,$options,$layout);
	}

	function field_integer($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("text","integer",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_multiradio($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("multiradio","multiradio",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_noedit($id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("noedit","noedit",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_onlyinoutput($id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("onlyinoutput","onlyinoutput",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_password($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("password","password",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_radio($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("radio","radio",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_select($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("select","select",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_text($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("text","text",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_textarea($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("textarea","textarea",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_upload($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("upload","upload",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_mongodb_upload($obl, $id, $title, $options) {
		$this->newfield('mongodb_upload', 'mongodb_upload', $obl, $id, $title, '', '', $options, '');
	}

	function field_url($obl,$id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("text","url",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	function field_yesno($id,$title,$db="",$prevalue="",$options="",$layout="") {
		$this->newfield("yesno","yesno",$obl,$id,$title,$db,$prevalue,$options,$layout);
	}

	#
	#
	# Display-functies (chronologisch)
	#
	#

	function display_css() {
		global $vars;

		ob_start();
		?>
		<style type="text/css"><!--
		.wtform_table {
			width: 100%;
			font-family:  Verdana, Helvetica, Arial, sans-serif;
			background-color: #FFFFFF;
			border: 2px solid #878481;
		}

		.wtform_input {
			font-family:  Verdana, Helvetica, Arial, sans-serif;
			font-size: 1.0em;
			width: 100%;
			border: 2px solid #878481;
		}

		.wtform_input_narrow {
			font-family:  Verdana, Helvetica, Arial, sans-serif;
			font-size: 1.0em;
			border: 2px solid #878481;
		}

		.wtform_error {
			color: red;
		}

		.wtform_cell_left {
			width: 150px;
			padding: 6px;
		}

		.wtform_cell_right {
			width: 350px;
			padding: 6px;
		}

		.wtform_cell_colspan {
			padding: 6px;
		}

		.wtform_img_tbl {
			border: solid #878481 2px;
		}

		.wtform_small {
			font-size: 0.8em;
		}

		--></style>
		<?php
		$return=ob_get_clean();
		return $return;
	}

	function display_openform() {
		global $vars;

		$return.="<form class=\"wtform".($this->settings["prevent_spambots"] ? " wtform_prevent_spambots" : "").($this->settings["form_css_class"] ? " ".$this->settings["form_css_class"] : "")."\" method=\"".$this->settings["type"]."\" action=\"";
		if($_SERVER["REQUEST_URI"]) {
			if($_SERVER["QUERY_STRING"]) {
#				$return.=str_replace("\?".$_SERVER["QUERY_STRING"],"",$_SERVER["REQUEST_URI"]);
				$return.=ereg_replace("\?.*","",$_SERVER["REQUEST_URI"]);
			} else {
				$return.=$_SERVER["REQUEST_URI"];
			}
		} else {
			$return.=$_SERVER["PHP_SELF"];
		}
		if(is_array($_GET)) {
			if($this->settings["type"]=="get") {
				# Get waarden die vooraf bestaan in hidden values zetten
				reset($_GET);
				while(list($key,$value)=each($_GET)) {
					if(!is_array($value)) {
						$hidden.="<input type=\"hidden\" name=\"".$key."\" value=\"".wt_he($value)."\">\n";
					}
				}
			} else {
				$return.="?";
				reset($_GET);
				while(list($key,$value)=each($_GET)) {
					if($multiple) $return.="&amp;";
					if(!is_array($value) and $key<>"fo" and $value<>$this->settings["formname"]) {
						$return.=urlencode($key)."=".urlencode($value);
						$multiple=true;
					}
				}
			}
		}
		if(substr($return,-1)=="?") $return=substr($return,0,-1);
		if($this->settings["layout"]["goto_aname"]) $return.="#".$this->settings["layout"]["goto_aname"];
		$return.="\" name=\"".$this->settings["formname"]."\"";
#		.($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "")."#wtform_".$this->settings["formname"]."\" name=\"".$this->settings["formname"]."\"";
		if(@in_array("upload",$this->fields["type"])) $return.=" enctype=\"multipart/form-data\"";
		if($this->settings["target"]) $return.=" target=\"".$this->settings["target"]."\"";
		$return.=">";
#		$return.="<a name=\"wtform_".$this->settings["formname"]."\"></a>";
		$return.="<div id=\"wtform_".$this->settings["formname"]."\"></div>";
		$return.=$hidden."<input type=\"hidden\" name=\"".$this->settings["formname"]."_filled\" value=\"1\"><input type=\"hidden\" name=\"pg\" value=\"1\">";

		// prevent csrf
		$return.=$this->insert_form_tokens();

		if($this->settings["prevent_spambots"]) {
			$return.="<input type=\"hidden\" name=\"wtform_botcheck\" value=\"1\">";
		}

		if(is_array($this->hidden)) {
			while(list($key,$value)=each($this->hidden)) {
				$return.="<input type=\"hidden\" name=\"".$key."\" value=\"".wt_he($value["value"])."\">\n";
			}
		}
		return $return;
	}

	function display_error() {
		global $vars;

		# Toon foutmeldingen

		# Eerst foutmeldingen van de veldnamen tonen en vervolgens extra foutmeldingen

		if(is_array($this->error)) {
			$return="<span class=\"wtform_error\">".$this->message("error_foutform").":</span><ul class=\"wtform_error\" style=\"padding-left:1.0em;margin-top:0px;margin-bottom:0px;margin-left:5px;\">";
			if($this->settings["layout"]["stars"] and in_array("obl",$this->error)) {
				$return.="<li style=\"".($this->settings["error"]["li_css_paddingleft"] ? "padding-left:0.8em;" : "")."\">".$this->message("error_verplicht");
				$counter++;
			}
			# Doorloop en toon veldnaam-foutmeldingen
			while(list($key,$value)=each($this->fields["title"])) {
				if($this->error[$key] and (!$this->settings["layout"]["stars"] or ($this->settings["layout"]["stars"] and $this->error[$key]<>"obl"))) {
					if($counter) $return.=";";
					$counter++;
					$return.="<li style=\"".($this->settings["error"]["li_css_paddingleft"] ? "padding-left:0.8em;" : "")."\">";
					if($this->fields["type"][$key]<>"yesno") {
						if($this->error_other_fieldname[$key]) {
							$return.=$this->error_other_fieldname[$key];
						} else {
							$return.=$this->fields["title"][$key];
						}
						$return.=": ";
					}
					if($this->error[$key]=="obl") {
						$return.=$this->message("error_verplicht_veld");
					} else {
						$return.=$this->error[$key];
					}
				}
			}
			# Doorloop en toon extra foutmeldingen
			while(list($key,$value)=@each($this->error["extra"])) {
				if($counter) $return.=";";
				$return.="<li style=\"".($this->settings["error"]["li_css_paddingleft"] ? "padding-left:0.8em;" : "")."\">".$value;
			}
			$return.=".</ul>";
			return $return;
		} else {
			return false;
		}
	}

	function display_title($id) {
		global $vars;
		if($this->error[$id]) $return.="<span class=\"wtform_error\">";
		if($this->fields["title"][$id]) {
#			if($this->fields["checktype"][$id]=="currency") $return.="<span style=\"float:left;\">";
			if($this->fields["layout"][$id]["title_html"]) {
				$return.=$this->fields["title"][$id];
			} else {
				if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
					$return.=wt_he($this->fields["title"][$id]);
				} else {
					$return.=htmlentities($this->fields["title"][$id],ENT_QUOTES,"iso-8859-15");
				}
			}
			if($this->settings["layout"]["stars"] and $this->fields["obl"][$id] and substr($return,-1)<>"*" and !$this->fields["layout"][$id]["hide_asterisk"]) $return.="*";
			if($this->error[$id]) $return.="</span>";
			if($this->fields["layout"][$id]["add_html_after_title"]) $return.=$this->fields["layout"][$id]["add_html_after_title"];
#			if($this->fields["checktype"][$id]=="currency") $return.="</span><span style=\"float:right;\">&euro;</span>";
			return $return;
		} else {
			return false;
		}
	}

	function display_input($id,$infobox="") {
		global $vars;
		if($this->fields["type"][$id]=="checkbox") {
			# Checkbox
			if(!$this->filled) {
				# Prevalue bepalen
				if($this->fields["prevalue"][$id]["selection"]) {
					$this->fields["prevalue"][$id]["selection"]=split(",",$this->fields["prevalue"][$id]["selection"]);
					while(list($key,$value)=each($this->fields["prevalue"][$id]["selection"])) {
						$this->value[$id][$value]="on";
					}
				} elseif($_GET["pv_".$id]) {
					unset($checkbox_prevalue);
					$checkbox_prevalue=split(",",$_GET["pv_".$id]);
					while(list($key,$value)=each($checkbox_prevalue)) {
						$this->value[$id][$value]="on";
					}
				}
			}
			if(is_array($this->fields["options"][$id]["selection"])) {
				while(list($key,$value)=each($this->fields["options"][$id]["selection"])) {

					if($this->fields["layout"][$id]["indent"]) {
						# inspringen indien keuze meerdere regels bevat
						$return.="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"vertical-align:top;\">";
					}
					$return.="<input type=\"checkbox\" id=\"checkbox".$id.$key."\" name=\"input[".$id."][".wt_he($key)."]\" ".($this->value[$id][$key]=="on" ? "checked " : "");
					if($this->fields["layout"][$id]["onchange"]) {
						$return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
					}
					if($this->fields["layout"][$id]["onclick"]) {
						$return.=" onclick=\"".$this->fields["layout"][$id]["onclick"]."\"";
					}
					$return.=">";
					if($this->fields["layout"][$id]["indent"]) {
						# inspringen indien keuze meerdere regels bevat
						$return.="</td><td>&nbsp;</td><td style=\"vertical-align:top;\"><label for=\"checkbox".$id.$key."\">";
					} else {
						$return.="<label for=\"checkbox".$id.$key."\">&nbsp;";
					}
					if($this->fields["layout"][$id]["content_html"]) {
						$return.=$value;
					} else {
						$return.=wt_he($value);
					}
					$return.="</label>";
					if($this->fields["layout"][$id]["indent"]) {
						# inspringen indien keuze meerdere regels bevat
						$return.="</td></tr></table>";
					} else {
						if($this->fields["layout"][$id]["one_per_line"]) {
							$return.="<br>";
						} else {
							$return.="&nbsp;&nbsp;";
						}
					}
				}
			} else {
				if($this->fields["obl"][$id]) {
					$this->disable_form=true;
				}
				$return.="(".$this->message("nog_geen_gegevens").")";
			}
		} elseif($this->fields["type"][$id]=="datetime") {
			# Datetime-field

			# Prevalue
			if(!$this->filled and $this->fields["prevalue"][$id]) {
				if($this->fields["prevalue"][$id]["time"]<>"") {
					$this->value[$id]["day"]=adodb_date("j",$this->fields["prevalue"][$id]["time"]);
					$this->value[$id]["month"]=adodb_date("n",$this->fields["prevalue"][$id]["time"]);
					$this->value[$id]["year"]=adodb_date("Y",$this->fields["prevalue"][$id]["time"]);
					$this->value[$id]["hour"]=adodb_date("H",$this->fields["prevalue"][$id]["time"]);
					if($this->fields["options"][$id]["min_jump"]>1) {
						$this->value[$id]["minute"]=ceil(adodb_date("i",$this->fields["prevalue"][$id]["time"])/$this->fields["options"][$id]["min_jump"])*$this->fields["options"][$id]["min_jump"];
						if($this->value[$id]["minute"]==60) {
							$this->value[$id]["minute"]=0;
							$this->value[$id]["hour"]++;
							if($this->value[$id]["hour"]==24) $this->value[$id]["hour"]=0;
						}
					} else {
						$this->value[$id]["minute"]=adodb_date("i",$this->fields["prevalue"][$id]["time"]);
					}
				} else {
					$this->value[$id]["day"]=$this->fields["prevalue"][$id]["day"];
					$this->value[$id]["month"]=$this->fields["prevalue"][$id]["month"];
					$this->value[$id]["year"]=$this->fields["prevalue"][$id]["year"];
					$this->value[$id]["hour"]=$this->fields["prevalue"][$id]["hour"];
					$this->value[$id]["minute"]=$this->fields["prevalue"][$id]["minute"];
				}
			}

			if(!$this->fields["options"][$id]["startyear"]) $this->fields["options"][$id]["startyear"]=adodb_date("Y");
			if(!$this->fields["options"][$id]["endyear"]) $this->fields["options"][$id]["endyear"]=adodb_date("Y")+4;

			if($this->fields["layout"][$id]["calendar"]) {
				$return.="<img src=\"".$this->settings["path"]."pic/class.form_calendar.gif\" border=\"0\" onClick=\"window.open('".$this->settings["path"]."class.form_calendar.php?nm=".urlencode($this->settings["formname"])."&lang=".$this->settings["language"]."&input=".$id."&month='+document.".$this->settings["formname"].".elements['input[".$id."][month]'].value+'&year='+document.".$this->settings["formname"].".elements['input[".$id."][year]'].value, '_blank', 'scrollbars=no,width=345,height=250,left=0,top=0');\" width=\"16\" height=\"15\" alt=\"".$this->message("kalender")."\" class=\"wtform_calendar_img\" style=\"cursor: pointer\">&nbsp;";
			}

			# Dag
			$return.="<select name=\"input[".$id."][day]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\"";
			if($this->fields["options"][$id]["day_onfocus"]) $return.=" onfocus=\"".$this->fields["options"][$id]["day_onfocus"]."\"";
			if($this->fields["options"][$id]["day_onchange"]) {
				$return.=" onchange=\"".$this->fields["options"][$id]["day_onchange"]."\"";
			} elseif($this->fields["layout"][$id]["onchange"]) {
				$return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
			}

			$return.="><option> </option>";
			for($i=1;$i<=31;$i++) {
				$return.="<option value=\"".$i."\"";
				if($this->value[$id]["day"]==$i) $return.=" selected";
				$return.=">".$i."&nbsp;</option>\n";
			}
			$return.="</select>&nbsp;";

			# Maand
			$return.="<select name=\"input[".$id."][month]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\"";
			if($this->fields["options"][$id]["month_onfocus"]) $return.=" onfocus=\"".$this->fields["options"][$id]["month_onfocus"]."\"";
			if($this->fields["options"][$id]["month_onchange"]) {
				$return.=" onchange=\"".$this->fields["options"][$id]["month_onchange"]."\"";
			} elseif($this->fields["layout"][$id]["onchange"]) {
				$return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
			}

			$return.="><option> </option>";
			for($i=1;$i<=12;$i++) {
				$return.="<option value=\"".$i."\"";
				if($this->value[$id]["month"]==$i) $return.=" selected";
				$return.=">".strftime("%B",mktime(0,0,0,$i,1,2004))."&nbsp;</option>\n";
			}
			$return.="</select>&nbsp;";

			# Jaar
			if(!$this->fields["options"][$id]["hide_year"]) {
				# Alleen tonen als hide_year false is
				# kijken of startyear/endyear binnen de waarde vallen
				if($this->value[$id]["year"] and $this->value[$id]["year"]<>"0000" and $this->value[$id]["year"]<$this->fields["options"][$id]["startyear"] and $this->fields["options"][$id]["startyear"]<$this->fields["options"][$id]["endyear"]) $this->fields["options"][$id]["startyear"]=$this->value[$id]["year"];
				if($this->value[$id]["year"]>$this->fields["options"][$id]["endyear"] and $this->fields["options"][$id]["endyear"]>$this->fields["options"][$id]["startyear"]) $this->fields["options"][$id]["endyear"]=$this->value[$id]["year"];
				$return.="<select name=\"input[".$id."][year]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\"";
				if($this->fields["options"][$id]["year_onfocus"]) $return.=" onfocus=\"".$this->fields["options"][$id]["year_onfocus"]."\"";
				if($this->fields["options"][$id]["year_onchange"]) {
					$return.=" onchange=\"".$this->fields["options"][$id]["year_onchange"]."\"";
				} elseif($this->fields["layout"][$id]["onchange"]) {
					$return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
				}

				$return.="><option> </option>";
				if($this->fields["options"][$id]["startyear"]<$this->fields["options"][$id]["endyear"]) {
					for($i=$this->fields["options"][$id]["startyear"];$i<=$this->fields["options"][$id]["endyear"];$i++) {
						$return.="<option value=\"".$i."\"";
						if($this->value[$id]["year"]==$i) $return.=" selected";
						$return.=">".$i."&nbsp;</option>\n";
					}
				} else {
					for($i=$this->fields["options"][$id]["startyear"];$i>=$this->fields["options"][$id]["endyear"];$i-=1) {
						$return.="<option value=\"".$i."\"";
						if($this->value[$id]["year"]==$i) $return.=" selected";
						$return.=">".$i."&nbsp;</option>\n";
					}
				}
				$return.="</select>";
			}

			if($this->fields["checktype"][$id]=="datetime") {
				# Tijd
				$return.="&nbsp;-&nbsp;";
				if(!$this->fields["layout"][$id]["time_text"]) {
					# Tijd met select-fields
					$return.="<select name=\"input[".$id."][hour]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\"";
					if($this->fields["options"][$id]["hour_onblur"]) $return.=" onblur=\"".$this->fields["options"][$id]["hour_onblur"]."\"";
					if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
					$return.="><option> </option>";
					for($i=0;$i<=23;$i++) {
						$return.="<option value=\"".strftime("%H",mktime($i,0,0,1,1,2004))."\"";
						if($this->value[$id]["hour"]==strftime("%H",mktime($i,0,0,1,1,2004))) $return.=" selected";
						$return.=">".strftime("%H",mktime($i,0,0,1,1,2004))."&nbsp;</option>\n";
					}
					$return.="</select>:";

					$return.="<select name=\"input[".$id."][minute]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\"";
					if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
					$return.="><option> </option>";
					if(!$this->fields["options"][$id]["min_jump"]) $this->fields["options"][$id]["min_jump"]=1;
					for($i=0;$i<=59;$i=$i+$this->fields["options"][$id]["min_jump"]) {
						$return.="<option value=\"".strftime("%M",mktime(1,$i,0,1,1,2004))."\"";
						if($this->value[$id]["minute"]==strftime("%M",mktime(1,$i,0,1,1,2004))) $return.=" selected";
						$return.=">".strftime("%M",mktime(1,$i,0,1,1,2004))."&nbsp;</option>\n";
					}
					$return.="</select>";
				} else {
					# Tijd met input-fields
					$return.="<input type=\"text\" size=\"2\" maxlength=\"2\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\" name=\"input[".$id."][hour]\"".($this->value[$id]["hour"]<>"" ? " value=\"".$this->value[$id]["hour"]."\"" : "");
					if($this->fields["options"][$id]["hour_onblur"]) $return.=" onblur=\"".$this->fields["options"][$id]["hour_onblur"]."\"";
					if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";

					$return.=">:";
					$return.="<input type=\"text\" size=\"2\" maxlength=\"2\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\" name=\"input[".$id."][minute]\"".($this->value[$id]["minute"]<>"" ? " value=\"".$this->value[$id]["minute"]."\"" : "");
					if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
					$return.=">";
				}
				$return.=$this->message("u").".";
			}

		} elseif($this->fields["type"][$id]=="htmlcol") {
			# Htmlcol
			if($this->fields["prevalue"][$id]["text"]) {
				$return=wt_he($this->fields["prevalue"][$id]["text"]);
			} elseif($this->fields["prevalue"][$id]["html"]) {
				$return=$this->fields["prevalue"][$id]["html"];
			}
		} elseif($this->fields["type"][$id]=="htmlrow") {
			# Htmlrow
			# Niks doen
		} elseif($this->fields["type"][$id]=="multiradio") {
			# multiradio

			if(!$this->filled) {


#echo wt_dump($this->fields["prevalue"][$id]["multiselection"]);
#exit;
				# Prevalue bepalen
				if(is_array($this->fields["prevalue"][$id]["multiselection"])) {
					while(list($key,$value)=each($this->fields["prevalue"][$id]["multiselection"])) {
						$tempsplit=@split(",",$value);
						while(list($key2,$value2)=@each($tempsplit)) {
							if($value2) {
								$this->value[$id][$value2]=$key;
							}
						}
					}
				}

#				# Prevalue bepalen
#				if($this->fields["prevalue"][$id]["multiselection"]) {
#					$this->value[$id]=$this->fields["prevalue"][$id]["multiselection"];
#				} elseif($_GET["pv_".$id]) {
#					$this->value[$id]=$_GET["pv_".$id];
#				}
			}
#echo wt_dump($this->value[$id]);
#exit;
#$this->fields["prevalue"][$key2]["multiselection"][$key3]

			$return.="<table cellspacing=\"0\" cellpadding=\"4\" class=\"wtform_multiradio_tbl\"><tr><td>&nbsp;</td>";
			reset($this->fields["options"][$id]["multiselection"]);
			while(list($key,$value)=each($this->fields["options"][$id]["multiselection"])) {
				$return.="<td style=\"text-align:center;\"><label for=\"wtform_multiradio_check_all_".wt_he($id."_".$value)."\">";
				$return.=wt_he($value);
				$return.="<input type=\"radio\" name=\"1\" id=\"wtform_multiradio_check_all_".wt_he($id."_".$value)."\" class=\"wtform_multiradio_check_all\" data-id=\"".wt_he($id)."\" data-value=\"".wt_he($key)."\">";
				$return.="</label>";
				$return.="</td>";
			}
			$return.="</tr>";
			reset($this->fields["options"][$id]["selection"]);
			while(list($key,$value)=each($this->fields["options"][$id]["selection"])) {
				$return.="<tr><td style=\"vertical-align:top;\">";
				$return.=wt_he($value);
				$return.="</td>";
				reset($this->fields["options"][$id]["multiselection"]);
				while(list($key2,$value2)=each($this->fields["options"][$id]["multiselection"])) {
					$return.="<td style=\"text-align:center;vertical-align:top;\">";
					$return.="<input type=\"radio\" class=\"wtform_multiradio_fields_".$id."\" id=\"multiradio".$id."_".$key."_".$key2."\" name=\"input[".$id."][".$key."]\" ".($this->value[$id][$key]==$key2 ? "checked " : "")."value=\"".wt_he($key2)."\"";
					if($this->fields["layout"][$id]["onchange"]) {
						$return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
					}
					if($this->fields["layout"][$id]["onclick"]) {
						$return.=" onclick=\"".$this->fields["layout"][$id]["onclick"]."\"";
					}
					$return.=">";
					$return.="</td>";
				}
				$return.="</tr>";
			}
			$return.="</table>";
		} elseif($this->fields["type"][$id]=="noedit") {
			# Noedit
			if(is_array($this->fields["options"][$id]["selection"])) {
				# Prevalue bepalen
				if($this->fields["prevalue"][$id]["selection"]) {
					$this->value[$id]=$this->fields["prevalue"][$id]["selection"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
				$return=$this->fields["options"][$id]["selection"][$this->value[$id]];
			} else {
				if($this->fields["prevalue"][$id]["html"]) {
					$return.=$this->fields["prevalue"][$id]["html"];
				} else {
					if($this->fields["layout"][$id]["html"]) {
						$return.=$this->fields["prevalue"][$id]["text"];
					} else {
						if($this->fields["options"][$id]["date_format"]) {
							if($this->fields["prevalue"][$id]["text"]<>"") {
								$return.=datum($this->fields["options"][$id]["date_format"],$this->fields["prevalue"][$id]["text"],$this->settings["language"]);
							} else {
								$return.="&nbsp;";
							}
						} else {
							$return.=nl2br(wt_he($this->fields["prevalue"][$id]["text"]));
						}
					}
				}
			}
		} elseif($this->fields["type"][$id]=="onlyinoutput") {
			# Onlyinoutput
		} elseif($this->fields["type"][$id]=="password") {
			# Password-field
			if(!$this->filled) {
				# Prevalue bepalen
				if(isset($this->fields["prevalue"][$id]["text"])) {
					$this->value[$id]=$this->fields["prevalue"][$id]["text"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
			}
			$return.="<input type=\"password\" name=\"input[".$id."]\" autocomplete=\"off\" value=\"";
			if(isset($this->value[$id]) and !$this->fields["options"][$id]["delete_password_input"]) {
				if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
					$return.=wt_he($this->value[$id]);
				} else {
					$return.=htmlentities($this->value[$id],ENT_QUOTES,"iso-8859-15");
				}
			}
			$return.="\"";
			if($this->fields["options"][$id]["maxlength"]) $return.=" maxlength=\"".$this->fields["options"][$id]["maxlength"]."\"";
			if($this->fields["options"][$id]["onkeyup"]) $return.=" onkeyup=\"".$this->fields["options"][$id]["onkeyup"]."\"";
			$return.=" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input")."\">";
		} elseif($this->fields["type"][$id]=="radio") {
			# Radio
			if(!$this->filled) {
				# Prevalue bepalen
				if($this->fields["prevalue"][$id]["selection"]) {
					$this->value[$id]=$this->fields["prevalue"][$id]["selection"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
			}
			while(list($key,$value)=each($this->fields["options"][$id]["selection"])) {
				if($this->fields["layout"][$id]["one_per_line"]) {
					$return.="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"vertical-align:top;\">";
				}
				$return.="<input type=\"radio\" id=\"radio".$id.$key."\" name=\"input[".$id."]\" ".($this->value[$id]==$key ? "checked " : "")."value=\"".wt_he($key)."\"";
				if($this->fields["layout"][$id]["onchange"]) {
					$return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
				}
				if($this->fields["layout"][$id]["onclick"]) {
					$return.=" onclick=\"".$this->fields["layout"][$id]["onclick"]."\"";
				}
				$return.=">";
				if($this->fields["layout"][$id]["one_per_line"]) {
					$return.="</td><td>&nbsp;</td><td style=\"vertical-align:top;\">";
				}
				$return.="<label for=\"radio".$id.$key."\">";
				if($this->fields["layout"][$id]["one_per_line"]) {

				} else {
					$return.="&nbsp;";
				}
				unset($otherfield_toshow);
				if(preg_match("/_show_at_other_field_/",$value)) {
					$value=preg_replace("/_show_at_other_field_/","",$value);
					if($this->show_at_other_field[$id]) {
						$otherfield_toshow=$this->show_at_other_field[$id];
					}
				}
				if($this->fields["layout"][$id]["content_html"]) {
					$return.=$value;
				} else {
					$return.=wt_he($value);
				}
#				$return.=wt_he($value);
				$return.="</label>".($this->fields["options"][$id]["subselection"][$key] ? "<div style=\"\">".$this->fields["options"][$id]["subselection"][$key]."</div>" : "").($otherfield_toshow ? "<div style=\"\">".$otherfield_toshow."</div>" : "").($this->fields["layout"][$id]["one_per_line"] ? "<br>" : "&nbsp;&nbsp;");
				if($this->fields["layout"][$id]["one_per_line"]) {
					$return.="</td></tr></table>";
				}
			}
		} elseif($this->fields["type"][$id]=="select") {
			# Select
			if(!$this->filled) {
				# Prevalue bepalen
				if($this->fields["prevalue"][$id]["selection"]) {
					$this->value[$id]=$this->fields["prevalue"][$id]["selection"];
				} elseif($this->fields["options"][$id]["allow_0"] and $this->fields["prevalue"][$id]["selection"]=="0") {
					$this->value[$id]=$this->fields["prevalue"][$id]["selection"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
			}
			if(is_array($this->fields["options"][$id]["selection"])) {
				$return.="<select name=\"input[".$id."]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input")."\"";
				if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
				$return.=">";
				if(!$this->fields["options"][$id]["no_empty_first_selection"]) {
					$return.="<option".($this->fields["options"][$id]["empty_is_0"] ? " value=\"0\"" : "")."> </option>";
				}
				reset($this->fields["options"][$id]["selection"]);
				unset($optgroup_open);
				while(list($key,$value)=each($this->fields["options"][$id]["selection"])) {
					if($this->fields["options"][$id]["optgroup"][$key]) {
						if($optgroup_open) $return.="</optgroup>";
						$return.="<optgroup label=\"".wt_he($this->fields["options"][$id]["optgroup"][$key])."\">";
						$optgroup_open=true;
					}
					$return.="<option";
					if(strval($this->value[$id])==strval($key)) {
						$return.=" selected";
					}
					$return.=" value=\"".wt_he($key)."\">".wt_he($value)."&nbsp;</option>\n";
				}
				if($optgroup_open) $return.="</optgroup>";
				$return.="</select>";
			} else {
				if($this->fields["obl"][$id]) {
					$this->disable_form=true;
				}
				$return.="(".$this->message("nog_geen_gegevens").")";
			}
		} elseif($this->fields["type"][$id]=="text") {
			# Text-field
			if(!$this->filled) {
				# Prevalue bepalen
				if(isset($this->fields["prevalue"][$id]["force_text"])) {
					# Force_text: altijd deze waarde gebruiken (ongeacht database-waarde)
					$this->value[$id]=$this->fields["prevalue"][$id]["force_text"];
				} elseif(isset($this->fields["prevalue"][$id]["text"])) {
					$this->value[$id]=$this->fields["prevalue"][$id]["text"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
			}
			$return.="<input type=\"text\" name=\"input[".$id."]\" value=\"";
			if(isset($this->value[$id])) {
				if(($this->fields["checktype"][$id]=="currency" or $this->fields["checktype"][$id]=="float")and !$this->filled) {
					# Indien currency/float: punt in komma veranderen
					$return.=ereg_replace("\.",",",$this->value[$id]);
				} else {
					if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
						$return.=wt_he($this->value[$id]);
					} else {
						$return.=htmlentities($this->value[$id],ENT_QUOTES,"iso-8859-15");
					}
				}
			} elseif($this->fields["checktype"][$id]=="url") {
				$return.="http://";
			}
			$return.="\"";
			if($this->fields["layout"][$id]["placeholder"]) {
				$return .= " placeholder=\"".wt_he($this->fields["layout"][$id]["placeholder"])."\"";
			}
			if($this->fields["options"][$id]["maxlength"]) $return.=" maxlength=\"".$this->fields["options"][$id]["maxlength"]."\"";
			if($this->fields["options"][$id]["onkeyup"]) $return.=" onkeyup=\"".$this->fields["options"][$id]["onkeyup"]."\"";
			if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";

			if(is_array($this->fields["options"][$id]["data_field"])) {
				foreach ($this->fields["options"][$id]["data_field"] as $key => $value) {
					$return.=" data-".$key."=\"".wt_he($value)."\"";
				}
			}

			$return.=" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input")."\">";
		} elseif($this->fields["type"][$id]=="textarea") {
			# Textarea
			if(!$this->filled) {
				# Prevalue bepalen
				if($this->fields["prevalue"][$id]["text"]) {
					$this->value[$id]=$this->fields["prevalue"][$id]["text"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
			}

			if(!$this->fields["layout"][$id]["rows"]) $this->fields["layout"][$id]["rows"]=5;
			if(!$this->fields["layout"][$id]["cols"]) $this->fields["layout"][$id]["cols"]=40;
#			if($this->fields["options"][$id]["year_onfocus"]) $return.=" onfocus=\"".$this->fields["options"][$id]["year_onfocus"]."\"";

			$return.="<textarea class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input wtform_textarea")."\" name=\"input[".$id."]\" rows=\"".$this->fields["layout"][$id]["rows"]."\" cols=\"".$this->fields["layout"][$id]["cols"]."\"".($this->fields["layout"][$id]["style"] ? " style=\"".$this->fields["layout"][$id]["style"]."\"" : "").($this->fields["options"][$id]["onfocus"] ? " onfocus=\"".$this->fields["options"][$id]["onfocus"]."\"" : "").">";
			if($this->value[$id]) {
				if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
					$return.=wt_he($this->value[$id]);
				} else {
					$return.=htmlentities($this->value[$id],ENT_QUOTES,"iso-8859-15");
				}
			}
			$return.="</textarea>";
		} elseif($this->fields["type"][$id]=="upload") {
			# Upload
			if (isset($this->upload[$id])) {
				foreach($this->upload[$id] as $file_upload_info) {
					if ($file_upload_info["tmp_name"]) $temp["upload_ontvangen"]=true;
				}
			}

			if($this->filled and !$this->error[$id] and $temp["upload_ontvangen"]) {
				$return.=$this->message("reeds_ontvangen");
				foreach($this->upload[$id] as $i => $file_upload_info) {
					if($file_upload_info["tmp_name"] and $file_upload_info["name"]) {
						$return.="<input type=\"hidden\" name=\"input[".$id."][".$i."][tmp_name]\" value=\"".$file_upload_info["tmp_name"]."\">";
						$return.="<input type=\"hidden\" name=\"input[".$id."][".$i."][name]\" value=\"".$file_upload_info["name"]."\">";
					}
				}
			} else {
				unset($temp["filename"]);
				if($this->fields["options"][$id]["multiple"]) {
					$d=dir($this->fields["options"][$id]["move_file_to"]);
					while($entry=$d->read()) {
						if($entry!="." and $entry!=".." and ereg("^".$this->fields["options"][$id]["rename_file_to"]."-([0-9]+)\.".$this->fields["options"][$id]["must_be_filetype"]."$",$entry,$regs)) {
							$regs[1]=substr("000000000000000000000".$regs[1],-20);
							$temp["filename"][$regs[1]]=$this->fields["options"][$id]["move_file_to"].$entry;
#							$temp["filename"][]=$this->fields["options"][$id]["rename_file_to"].".".$this->fields["options"][$id]["must_be_filetype"];
						}
					}
					$d->close();
				} else {
					if(preg_match("@,@", $this->fields["options"][$id]["must_be_filetype"])) {
						$file_extensions = preg_split("@,@", $this->fields["options"][$id]["must_be_filetype"]);
						foreach ($file_extensions as $key5 => $value5) {
							$temp_filename = $this->fields["options"][$id]["move_file_to"].$this->fields["options"][$id]["rename_file_to"].".".$value5;
							if(file_exists($temp_filename)) {
								$temp["filename"][1] = $temp_filename;
								break;
							}
						}
						if(!$temp["filename"][1]) {
							$temp["filename"][1] = $this->fields["options"][$id]["move_file_to"].$this->fields["options"][$id]["rename_file_to"].".".$file_extensions[0];
						}
					} else {
						$temp["filename"][1]=$this->fields["options"][$id]["move_file_to"].$this->fields["options"][$id]["rename_file_to"].".".$this->fields["options"][$id]["must_be_filetype"];
					}
				}
				@ksort($temp["filename"]);
				unset($afbeeldingteller);
				while(list($key,$value)=@each($temp["filename"])) {
					if(file_exists($value)) {
						$afbeeldingteller++;
						$key=ereg_replace("^0+","",$key);
						$temp["filesize"]=@getimagesize($value);
						if($temp["filesize"][0]>300) {
							$temp["width"]=300;
							$temp["height"]=round(($temp["filesize"][1]*$temp["width"])/$temp["filesize"][0]);
						} else {
							$temp["width"]=$temp["filesize"][0];
							$temp["height"]=$temp["filesize"][1];
						}
						$ext=strtolower(substr($value,strrpos($value,".")+1,strlen($value)-strrpos($value,".")-1));
						if($ext=="jpg" or $ext=="gif" or $ext=="png") {
							$return.="<table class=\"wtform_img_tbl\">";
							$return.="<tr><td style=\"text-align:center;\"><img src=\"".($this->fields["options"][$id]["requestfilevia"] ? $this->fields["options"][$id]["requestfilevia"] : $value).(strpos($this->fields["options"][$id]["requestfilevia"],"?")===false ? "?" : "&amp;")."anticache=".time()."\"";
							if(!$this->fields["layout"][$id]["show_without_width_height"]) {
								$return.=" width=\"".$temp["width"]."\" height=\"".$temp["height"]."\"";
							}
							$return.=" border=\"0\" alt=\"".($this->fields["options"][$id]["hide_location"] ? "" : wt_he($value))."\" title=\"".($this->fields["options"][$id]["hide_location"] ? "" : wt_he($value))."\"><br>";
							if(!$this->fields["layout"][$id]["verberg_imgsize"]) $return.="<span style=\"font-size:0.8em;\">".$temp["filesize"][0]." x ".$temp["filesize"][1]." ".$this->message("pixels")."</span><br>";
							if(!$this->fields["obl"][$id] and !$this->fields["layout"][$id]["verberg_afbeeldingwissen"]) $return.="<input type=\"checkbox\" name=\"imagedelete[".$id."]".($this->fields["options"][$id]["multiple"] ? "[".$key."]" : "")."\" id=\"imagedelete".$id.($this->fields["options"][$id]["multiple"] ? "_".$key : "")."\"><label for=\"imagedelete".$id.($this->fields["options"][$id]["multiple"] ? "_".$key : "")."\">&nbsp;".$this->message("afbeeldingwissen")."</label>";
							if($this->fields["options"][$id]["multiple"] and @count($temp["filename"])>1 and !$this->fields["options"][$id]["blokkeer_volgorde"]) {
								$return.="<br>".$this->message("afbeeldingvolgorde").":&nbsp;<input type=\"text\" name=\"imgorder[".$id."][".wt_he(basename($value))."]\" value=\"".($afbeeldingteller*10)."\" style=\"width:40px;\">";
								$return.="<input type=\"hidden\" name=\"orgimgorder[".$id."][".wt_he(basename($value))."]\" value=\"".($afbeeldingteller*10)."\">";
							}
							$return.="</td></tr>";
							$return.="</table><br>";
						} elseif($ext=="pdf" or $ext=="doc" or $ext=="docx" or $ext=="pps") {
							$return.="<table class=\"wtform_img_tbl\">";
							$return.="<tr><td style=\"text-align:center;\">";
							if($this->settings["download_uploaded_files"]) {
								$return.="<a href=\"".wt_he(($this->fields["options"][$id]["requestfilevia"] ? $this->fields["options"][$id]["requestfilevia"] : $value)).(preg_match("@\?@", $this->fields["options"][$id]["requestfilevia"]) ? "&" : "?")."c=".@filemtime($value)."\" target=\"_blank\">";
							}
							$return.="<img src=\"".$this->settings["path"]."pic/class.form_".$ext."_icon.gif\" width=\"20\" height=\"20\" border=\"0\" alt=\"".wt_he($value)."\" title=\"".wt_he($value)."\">";
							if($this->settings["download_uploaded_files"]) $return.="</a>";
							$return.="<br>";
							if(!$this->fields["obl"][$id]) $return.="<input type=\"checkbox\" name=\"imagedelete[".$id."]".($this->fields["options"][$id]["multiple"] ? "[".$key."]" : "")."\" id=\"imagedelete".$id.($this->fields["options"][$id]["multiple"] ? "_".$key : "")."\"><label for=\"imagedelete".$id.($this->fields["options"][$id]["multiple"] ? "_".$key : "")."\">".$this->message("bestandwissen")."</label>";
							$return.="</td></tr>";
							$return.="</table><br>";
						}
					}
				}

				$return.="<input type=\"file\" name=\"input[".$id."][]\" class=\"".($this->fields["layout"][$id]["input_class"] ? $this->fields["layout"][$id]["input_class"] : "wtform_input_narrow")."\"";
				if($this->fields["options"][$id]["must_be_filetype"]=="jpg") {
					$return.=" accept=\"image/jpeg\"";
				} elseif($this->fields["options"][$id]["accept_element"]) {
					$return.=" accept=\"".$this->fields["options"][$id]["accept_element"]."\"";
				}
				if($this->fields["options"][$id]["multiple"]) {
					$return.=" multiple=\"multiple\"";
				}
				$return.=">";

				# Indien afbeelding: evt. opmerkingen weergeven
				if(!$this->fields["layout"][$id]["hide_imginfo"]) {
					if(($this->fields["options"][$id]["img_ratio_width"] and $this->fields["options"][$id]["img_ratio_height"]) or ($this->fields["options"][$id]["img_width"] and $this->fields["options"][$id]["img_height"]) or $this->fields["options"][$id]["showfiletype"]) {
						$return.="<span class=\"wtform_small\">&nbsp;(";
						if($this->fields["options"][$id]["showfiletype"]) {
							$return.=$this->message("showfiletype","",array(1=>$this->fields["options"][$id]["must_be_filetype"]));
							$spatie=true;
						}
						if($this->fields["options"][$id]["img_ratio_width"] and $this->fields["options"][$id]["img_ratio_height"]) {
							if($spatie) $return.=" ";
							$return.=$this->message("imgsize_ratio","",array(1=>$this->fields["options"][$id]["img_ratio_width"],2=>$this->fields["options"][$id]["img_ratio_height"]));
						} elseif($this->fields["options"][$id]["img_width"] and $this->fields["options"][$id]["img_height"]) {
							if($spatie) $return.=" ";
							$return.=$this->message("imgsize_size","",array(1=>$this->fields["options"][$id]["img_width"],2=>$this->fields["options"][$id]["img_height"]));
						}
						$return.=")</span>";
					}
				}
			}

		} elseif ($this->fields['type'][$id] === 'mongodb_upload') {

			/**
			 * This is a new form class type called 'mongodb_upload'
			 *
			 * This allows one to upload files to mongodb using just one required options (collection)
			 * If you also provide a file_id, the files will be grouped by this ID.
			 * If you don't, the form class will update the uploaded files with the db_insert_id when known.
			 */

			$mongodb  	 = $vars['mongodb']['wrapper'];
			$fileId		 = $this->fields['options'][$id]['file_id'];
			$collection  = $this->fields['options'][$id]['collection'];
			$collections = array_flip($vars['mongodb']['collections']);
			$limit       = $this->fields['options'][$id]['limit'];

			if (intval($fileId) > 0) {

				$files = $mongodb->getFiles($collection, $fileId);

				if ($files > 0) {

					$template  = '<div class="image-grid-3">
							   		<img src="' . $vars['path'] . 'gridfs.php?c={collection}&fid={fileId}&r={rank}" /> <br />
							   		<input type="checkbox" name="delete_mongodb[{id}][{_id}]" /> afbeelding wissen <br />
							   		volgorde: <input type="text" name="rank_mongodb[{id}][{_id}]" value="{rankField}" style="width: 40px;" /> <br />
							   		label: <input type="text" name="label_mongodb[{id}][{_id}]" value="{label}" style="width: 170px;" /> <br />
							   	 ';

					if (isset($this->fields['options'][$id]['kinds'])) {

						$template .= 'soort: <select name="kind_mongodb[{id}][{_id}]">';
						foreach ($this->fields['options'][$id]['kinds'] as $identifier => $title) {
							$template .= '<option value="' . $identifier . '"{' . $identifier . 'KindSelected}>' . $title . '</option>';
						}

						$template .= '</select>';
					}

					$template .= '</div>';

					$return .= '<div class="image-grid">';

					foreach ($files as $file) {

						$image = str_replace(['{collection}', '{fileId}', '{id}', '{rank}', '{rankField}', '{label}', '{_id}'],
						  				     [$collections[$collection], $fileId, $id, $file['metadata']['rank'], ($rank += 10), $file['metadata']['label'], $file['_id']],
											 $template);

						if (isset($this->fields['options'][$id]['kinds'])) {

							foreach ($this->fields['options'][$id]['kinds'] as $identifier => $title) {

								if ($file['metadata']['kind'] === $identifier) {
									$image = str_replace('{' . $identifier . 'KindSelected}', ' selected="selected"', $image);
								} else {
									$image = str_replace('{' . $identifier . 'KindSelected}', '', $image);
								}
							}
						}

						$return .= $image;
					}

					$return .= '</div>';
				}
			}

			$return .= '<input type="file" name="input[' . $id . '][]" class="wtform_input_narrow"';

			if ($this->fields['options'][$id]['must_be_filetype'] === 'jpg') {
				$return .= ' accept="image/jpeg"';
			} elseif ($this->fields['options'][$id]['accept_element']) {
				$return .= ' accept="' . $this->fields['options'][$id]['accept_element'] . '"';
			}

			if ($limit > 1) {
				$return .= ' multiple="multiple"';
			}

			$return .= ' />';

			if (!$this->fields['layout'][$id]['hide_imginfo']) {

				if (($this->fields['options'][$id]['img_ratio_width'] and $this->fields['options'][$id]['img_ratio_height']) or ($this->fields['options'][$id]['img_width'] and $this->fields['options'][$id]['img_height']) or $this->fields['options'][$id]['showfiletype']) {

					$return .= '<span class="wtform_small">&nbsp;(';
					if ($this->fields['options'][$id]['showfiletype']) {

						$return .= $this->message('showfiletype', '', array(1 => $this->fields['options'][$id]['must_be_filetype']));
						$spatie  = true;
					}

					if ($this->fields['options'][$id]['img_ratio_width'] and $this->fields['options'][$id]['img_ratio_height']) {

						if ($spatie) $return .= ' ';
						$return .= $this->message('imgsize_ratio', '', array(1 => $this->fields['options'][$id]['img_ratio_width'], 2 => $this->fields['options'][$id]['img_ratio_height']));

					} elseif ($this->fields['options'][$id]['img_width'] and $this->fields['options'][$id]['img_height']) {

						if ($spatie) $return .= ' ';
						$return .= $this->message('imgsize_size', '', array(1 => $this->fields['options'][$id]['img_width'], 2 => $this->fields['options'][$id]['img_height']));
					}

					$return .= ')</span>';
				}
			}

		} elseif($this->fields["type"][$id]=="yesno") {
			# Yesno
			if(!$this->filled) {
				# Prevalue bepalen
				if(is_array($this->fields["prevalue"][$id]) and $this->fields["prevalue"][$id]["selection"]) {
					$this->value[$id]=$this->fields["prevalue"][$id]["selection"];
				} elseif($_GET["pv_".$id]) {
					$this->value[$id]=$_GET["pv_".$id];
				}
			}
			# 1=ja / of andere getal?
			if($this->fields["options"][$id]["selection"][1]) {
				$waarde=$this->fields["options"][$id]["selection"][1];
			} else {
				$waarde=1;
			}
			$return.="<div style=\"float:left;margin-right:10px;\"><input type=\"checkbox\" name=\"input[".$id."]\" id=\"yesno".$id."\" ".($this->value[$id]==$waarde ? "checked " : "")."value=\"".wt_he($waarde)."\"";
			if($this->fields["layout"][$id]["onchange"]) $return.=" onchange=\"".$this->fields["layout"][$id]["onchange"]."\"";
			if($this->fields["layout"][$id]["onclick"]) $return.=" onclick=\"".$this->fields["layout"][$id]["onclick"]."\"";
			$return.="></div><div style=\"float:left;width:90%;\"><label for=\"yesno".$id."\">";
			if($this->fields["layout"][$id]["title_html"]) {
				$return.=$this->fields["title"][$id];
			} else {
				if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
					$return.=wt_he($this->fields["title"][$id]);
				} else {
					$return.=htmlentities($this->fields["title"][$id],ENT_QUOTES,"iso-8859-15");
				}
			}
			$return.="</label>".$infobox."</div>";
		}
		if($this->fields["layout"][$id]["add_html_after_field"]) $return.=$this->fields["layout"][$id]["add_html_after_field"];
		return $return;
	}

	function display_submitbutton() {
		global $vars;

		if($this->disable_form) {

		} else {
			$this->counter["submitbutton"]++;
			if($this->settings["submitbutton"]["button_element"]) {
				$return.="<button class=\"".($this->settings["submitbutton"]["class"] ? $this->settings["submitbutton"]["class"] : "wtform_submitbutton")."\" type=\"submit\">".$this->message("submitbutton")."</button>";
			} else {
				$return.="<input type=\"submit\" value=\" ".$this->message("submitbutton")." \" id=\"submit".$this->counter["submitbutton"].$this->settings["formname"]."\"";
				if(!$this->settings["submitbutton"]["no_action"]) {
					$return.=" onclick=\"document.".$this->settings["formname"].".submit".$this->counter["submitbutton"].$this->settings["formname"].".disabled=1;document.".$this->settings["formname"].".submit();\"";
				}
				$return.=" class=\"".($this->settings["submitbutton"]["class"] ? $this->settings["submitbutton"]["class"] : "wtform_submitbutton")."\">";
			}
		}
		return $return;
	}

	function display_annuleerbutton() {
		global $vars;

		if($this->disable_form) {

		} else {
			$this->counter["annuleerbutton"]++;
			$return.="<input type=\"button\" value=\" ".$this->message("annuleerbutton")." \" id=\"annuleer".$this->counter["annuleerbutton"].$this->settings["formname"]."\" onclick=\"document.".$this->settings["formname"].".annuleer".$this->counter["annuleerbutton"].$this->settings["formname"].".disabled=1;document.location.href='".$this->settings["annuleerbutton_url"]."';\" class=\"wtform_submitbutton\">";
		}
		return $return;
	}

	function display_closeform() {
		global $vars;

		$return.="</form>";
		return $return;
	}

	function display_all() {
		global $vars;

		if(!$this->okay or $this->settings["type"]=="get" or $this->settings["alwaysshowform"]) {
			if(!is_array($this->fields["type"])) {
				trigger_error("WT-Error: this form has no fields",E_USER_NOTICE);
			}
			if(@count($this->fields["type"])<2) $this->settings["layout"]["stars"]=false;

			# Toon complete tabel
			if($this->settings["layout"]["css"]) echo $this->display_css();
			echo "<a data-name=\"wtform_".$this->settings["formname"]."\"></a>\n";
			echo $this->display_openform()."\n";
			echo "\n<table class=\"wtform_table".($this->settings["table_class"] ? " ".$this->settings["table_class"] : "")."\"".($this->settings["table_style"] ? " style=\"".$this->settings["table_style"]."\"" : "").">";
			if($this->settings["html_after_open_table"]) echo $this->settings["html_after_open_table"];
			if($this->settings["htmlheader"]) echo "<tr><td class=\"wtform_cell_colspan\" colspan=\"2\">".$this->settings["htmlheader"]."</td></tr>\n";

			# Extra submitbutton bovenaan formulier?
			if($this->settings["layout"]["top_submit_button"]) {
				echo "<tr><td class=\"wtform_cell_colspan\" colspan=\"2\" style=\"text-align:center;\">".$this->display_submitbutton();
				if($this->settings["annuleerbutton"] and $this->settings["annuleerbutton_url"]) {
					echo "&nbsp;&nbsp;&nbsp;".$this->display_annuleerbutton();
				}
				echo "</td></tr>";
			}

			if($_GET["wttest"]) echo "<tr><td class=\"wtform_cell_colspan\" colspan=\"2\"><strong>Testversie</strong></td></tr>\n";
			if($this->error) echo "<tr><td class=\"wtform_cell_colspan\" colspan=\"2\">".$this->display_error()."</td></tr>\n";
			@reset($this->fields["type"]);
			while(list($key,$value)=@each($this->fields["type"])) {
				if($this->fields["options"][$key]["show_at_other_field"]) {
					$this->show_at_other_field[$this->fields["options"][$key]["show_at_other_field"]]=$this->display_input($key);
				} else {
					$tr="<tr".($this->fields["layout"][$key]["tr_id"] ? " id=\"".$this->fields["layout"][$key]["tr_id"]."\"" : "").($this->fields["layout"][$key]["tr_class"] ? " class=\"".$this->fields["layout"][$key]["tr_class"]."\"" : "").($this->fields["layout"][$key]["tr_style"] ? " style=\"".$this->fields["layout"][$key]["tr_style"]."\"" : "").">";

					unset($infobox);
					if($this->fields["layout"][$key]["info"] or $this->fields["layout"][$key]["info_html"]) {
						$infobox.="&nbsp;&nbsp;<a href=\"#\" onclick=\"return false;\" class=\"opm\"><span class=\"balloon_small\">";
						if($this->fields["layout"][$key]["info"]) {
							$infobox.=nl2br(wt_he($this->fields["layout"][$key]["info"]));
						} else {
							$infobox.=$this->fields["layout"][$key]["info_html"];
						}
						$infobox.="</span><img src=\"".$this->settings["path"]."pic/class.form_info_icon.gif\" width=\"13\" height=\"13\" border=\"0\" style=\"margin-bottom:-1px;\">";
					}

					if($value=="yesno") {
						echo $tr."<td class=\"wtform_cell_colspan\" colspan=\"2\">".$this->display_input($key,$infobox)."</td></tr>\n";
					} elseif($value=="onlyinoutput") {

					} elseif($value=="htmlrow") {
						echo $tr."<td class=\"wtform_cell_colspan\" colspan=\"2\">".$this->fields["title"][$key]."</td></tr>\n";
					} else {
						if($this->fields["layout"][$key]["notitle"]) {
							echo $tr."<td class=\"wtform_cell_colspan\" colspan=\"2\">".$this->display_input($key)."</td></tr>\n";
						} elseif($this->fields["layout"][$key]["newline"]) {
							echo $tr."<td ".($this->fields["layout"][$key]["title_style"] ? "style=\"".$this->fields["layout"][$key]["title_style"]."\" " : "")."class=\"wtform_cell_colspan\" colspan=\"2\">".$this->display_title($key).$infobox."</td></tr><tr".($this->fields["layout"][$key]["tr_class"] ? " class=\"".$this->fields["layout"][$key]["tr_class"]."\"" : "")."><td class=\"wtform_cell_colspan\" colspan=\"2\">".$this->display_input($key)."</td></tr>\n";
						} else {
							echo $tr."<td ".($this->fields["layout"][$key]["title_style"] ? "style=\"".$this->fields["layout"][$key]["title_style"]."\" " : "")."class=\"wtform_cell_left\">".$this->display_title($key).$infobox."</td><td class=\"".($this->fields["layout"][$key]["td_cell_right_class"] ? $this->fields["layout"][$key]["td_cell_right_class"] : "wtform_cell_right")."\">".$this->display_input($key)."</td></tr>\n";
						}
					}
				}
			}
			if($this->settings["layout"]["stars"] and is_array($this->fields["obl"])) echo "<tr><td class=\"wtform_cell_colspan\" colspan=\"2\">* = ".$this->message("verplichtveld")."</td></tr>";
			echo "<tr><td class=\"wtform_cell_colspan\" colspan=\"2\" style=\"text-align:center;\">".$this->display_submitbutton();
			if($this->settings["annuleerbutton"] and $this->settings["annuleerbutton_url"]) {
				echo "&nbsp;&nbsp;&nbsp;".$this->display_annuleerbutton();
			}
			echo "</td></tr>";
			echo "</table>\n";
			echo $this->display_closeform();
		} else {
			# Toon okay-melding(en)
#			echo "<h1>== OKAY ==</h1>";
		}
#		echo wt_dump($this->show_at_other_field);
	}

	#
	# Overige functies
	#

	function error($id,$message,$overrule=false,$extra=false,$other_fieldname="") {
		global $vars;

		# id = id van het form-field
		# bericht (evt. incl. html)
		# overrule = overschrijf reeds eerder aangemaakte error-message
		# extra = melding over niet aanwezige id's (worden getoond ná gewone foutmeldingen)
		# other_fieldname = toon bij de melding van een andere veldnaam
		if($this->fields["type"][$id]) {
			if($this->filled) {
				$this->okay=false;
				if(!$this->error[$id] or $overrule) {
					$this->error[$id]=$message;
					$this->error_other_fieldname[$id]=$other_fieldname;
				}
			}
		} elseif($extra) {
			if($this->filled) {
				$this->okay=false;
				if(!$this->error[$id] or $overrule) $this->error["extra"][$id]=$message;
			}
		} else {
			trigger_error("WT-Error: Uknown field-id '".$id."' in function 'error'",E_USER_ERROR);
		}
	}

	function save_db() {
		global $vars;

		# Gegevens opslaan in database
		global $db0;
		reset($this->db);
		while(list($key,$value)=each($this->db)) {
			unset($setquery);
			while(list($key2,$value2)=each($value["fields"])) {
				unset($dont_save_value);
				if($this->fields["type"][$key2]=="datetime") {
					if($this->fields["db"][$key2]["not_unixtime"]) {
						if($this->input[$key2]["unixtime"]<>"") {
							if($this->input[$key2]["unixtime"]<0) {
								$savevalue="'".date('Y-m-d H:i:s',$this->input[$key2]["unixtime"])."'";
							} else {
								$savevalue="FROM_UNIXTIME(".$this->input[$key2]["unixtime"].")";
							}
						} elseif($this->fields["db"][$key2]["null"]) {
							$savevalue="NULL";
						} else {
							$savevalue="''";
						}
					} else {
						if($this->input[$key2]["unixtime"]<>"") {
							$savevalue="'".$this->input[$key2]["unixtime"]."'";
						} elseif($this->fields["db"][$key2]["null"]) {
							$savevalue="NULL";
						} else {
							$savevalue="''";
						}
					}
				} elseif($this->fields["type"][$key2]=="noedit") {
					if($this->fields["prevalue"][$key2]["selection"]) {
						$savevalue="'".addslashes($this->fields["prevalue"][$key2]["selection"])."'";
					} elseif($_GET["pv_".$key2]) {
						$savevalue="'".addslashes($_GET["pv_".$key2])."'";
					} else {
						$savevalue="'".addslashes($this->fields["prevalue"][$key2]["text"])."'";
					}
				} elseif($this->fields["type"][$key2]=="multiradio") {
					if(!is_array($this->fields["options"][$key2]["selection"])) {
						# indien de selectie-array niet beschikbaar is, ook niet opslaan
						$dont_save_value=true;
					} else {
						# query's bepalen gebeurt hieronder (vanwege meerdere velden)
					}
				} elseif($this->fields["type"][$key2]=="password") {
					if($this->input[$key2]) {
						if($this->fields["options"][$key2]["salt"]) {
							$savevalue="'".wt_complex_password_hash($this->input[$key2],$this->fields["options"][$key2]["salt"])."'";
						} else {
							$savevalue="'".md5($this->input[$key2])."'";
						}
					} else {
						# niet ingevuld wachtwoord niet opslaan
						$dont_save_value=true;
					}
				} elseif($this->fields["type"][$key2]=="select") {
					if(!is_array($this->fields["options"][$key2]["selection"])) {
						# indien de selectie-array niet beschikbaar is, ook niet opslaan
						$dont_save_value=true;
					} else {
						if($this->input[$key2]=="" and $this->fields["db"][$key2]["null"]) {
							# Indien waarde leeg en het een NULL-field betreft
							$savevalue="NULL";
						} else {
							$savevalue="'".addslashes($this->input[$key2])."'";
						}
					}
				} else {
					if($this->input[$key2]=="" and $this->fields["db"][$key2]["null"]) {
						# Indien waarde leeg en het een NULL-field betreft
						$savevalue="NULL";
					} else {
						if($this->fields["db"][$key2]["encode"]) {
							# Coderen met ENCODE
							$savevalue="ENCODE('".addslashes($this->input[$key2])."','".md5($this->fields["db"][$key2]["encode"])."')";
						} else {
							$savevalue="'".addslashes($this->input[$key2])."'";
						}
					}
				}
				if($this->fields["options"][$key2]["dontsave"]) {
					$dont_save_value=true;
				}
				if(!$dont_save_value) {
					if($this->fields["type"][$key2]=="multiradio") {
#						if(is_array($this->input[$key2])) {
#							while(list($key3,$value3)=each($this->input[$key2])) {
#								if($this->fields["options"][$key2]["multiselectionfields"][$key3]) {
#									if($setquery) $setquery.=", ".$this->fields["options"][$key2]["multiselectionfields"][$key3]."='".$value3."'"; else $setquery=$this->fields["options"][$key2]["multiselectionfields"][$key3]."='".$value3."'";
#								}
#							}
#						}
						if(is_array($this->fields["options"][$key2]["multiselectionfields"])) {
							reset($this->fields["options"][$key2]["multiselectionfields"]);
							while(list($key3,$value3)=each($this->fields["options"][$key2]["multiselectionfields"])) {
								if($setquery) $setquery.=", ".$this->fields["options"][$key2]["multiselectionfields"][$key3]."='".$this->input[$key2][$key3]."'"; else $setquery=$this->fields["options"][$key2]["multiselectionfields"][$key3]."='".$this->input[$key2][$key3]."'";
							}
						}
#						echo $setquery."<br>";
#						exit;
					} else {
						# Alleen in SET-query opnemen als $dont_save_value niet true is
						if($setquery) $setquery.=", ".$value2."=".$savevalue; else $setquery=$value2."=".$savevalue;
					}
				}
			}
			if($value["where"]) {
				if($this->db[$key]["editdatetime"]) {
					if($setquery) $setquery.=", editdatetime=NOW()"; else $setquery="editdatetime=NOW()";
				}
				if($this->db[$key]["editdatetime_bigint"]) {
					if($setquery) $setquery.=", editdatetime='".time()."'"; else $setquery="editdatetime='".time()."'";
				}
				$query="UPDATE ".$key." SET ".$setquery." WHERE ".$value["where"].";";

				$db0->query($query);
				if($db0->Errno) {
#					$_SESSION["wt_popupmsg"]="LET OP: gegevens zijn <b>niet</b> correct gewijzigd";
					trigger_error("WT-Error: mysql-fout ".$db0->Errno." bij UPDATE",E_USER_NOTICE);
				} else {
					if($this->settings["show_save_message"]) {
						$_SESSION["wt_popupmsg"]="gegevens zijn correct opgeslagen";
					}
				}
			} else {
				if($this->db[$key]["adddatetime"]) {
					if($setquery) $setquery.=", adddatetime=NOW(), editdatetime=NOW()"; else $setquery="adddatetime=NOW(), editdatetime=NOW()";
				}
				if($this->db[$key]["adddatetime_bigint"]) {
					if($setquery) $setquery.=", adddatetime='".time()."', editdatetime='".time()."'"; else $setquery="adddatetime='".time()."', editdatetime='".time()."'";
				}
				$query="INSERT INTO ".$key." SET ".$setquery.($this->settings["db"]["set"] ? ", ".$this->settings["db"]["set"] : "").";";
				$db0->query($query);
				$this->db_insert_id=$db0->insert_id();
				if($db0->Errno) {
#					$_SESSION["wt_popupmsg"]="LET OP: gegevens zijn <b>niet</b> correct gewijzigd";
					trigger_error("WT-Error: mysql-fout ".$db0->Errno." bij INSERT: ".$query,E_USER_NOTICE);
				} else {
					if($this->settings["show_save_message"]) {
						$_SESSION["wt_popupmsg"]="gegevens zijn correct opgeslagen";
					}
				}
			}
		}
	}

	function get_db() {
		global $vars;

		if(is_array($this->fields["db"]) and !$this->get_db) {
			global $db0;
			reset($this->db);
			while(list($key,$value)=each($this->db)) {

				# Veld-types uit database halen
				if($key) {
					$db0->query("SHOW COLUMNS FROM ".$key.";");
					while($db0->next_record()) {
						unset($id,$maxlength);
						if(ereg("char\(([0-9]+)\)",$db0->f("Type"),$regs)) {
							$maxlength=$regs[1];
						}

						# adddatetime en editdatetime?
						if($db0->f("Field")=="adddatetime" and $db0->f("Type")=="datetime") $this->db[$key]["adddatetime"]=true;
						if($db0->f("Field")=="adddatetime" and preg_match("/bigint/",$db0->f("Type"))) $this->db[$key]["adddatetime_bigint"]=true;

						if($db0->f("Field")=="editdatetime" and $db0->f("Type")=="datetime") $this->db[$key]["editdatetime"]=true;
						if($db0->f("Field")=="editdatetime" and preg_match("/bigint/",$db0->f("Type"))) $this->db[$key]["editdatetime_bigint"]=true;

						$id=array_search($db0->f("Field"),$this->db[$key]["fields"]);
						if($id) {
#							echo $id." ".$db0->f("Type");
							if($db0->f("Null")=="YES") $this->fields["db"][$id]["null"]=true;
							if($this->fields["type"][$id]=="text" and $maxlength) {
								if(!$this->fields["options"][$id]["maxlength"] or $this->fields["options"][$id]["maxlength"]>$maxlength) $this->fields["options"][$id]["maxlength"]=$maxlength;
							} elseif(($this->fields["type"][$id]=="datetime" or $this->fields["type"][$id]=="noedit") and ($db0->f("Type")=="date" or $db0->f("Type")=="datetime")) {
								if(!isset($this->fields["db"][$id]["not_unixtime"])) {
									$this->fields["db"][$id]["not_unixtime"]=true;
#									echo "ja";
								}
							}
#							echo " ".$this->fields["type"][$id];
#							echo "<br>";
						}
					}
					if($value["where"]) {
						# Selectquery bepalen
						unset($selectquery);
						reset($this->db[$key]["fields"]);
						while(list($key2,$value2)=each($this->db[$key]["fields"])) {
							if($value2) {
								if($this->fields["db"][$key2]["not_unixtime"]) {
#									$selectquery.=", UNIX_TIMESTAMP(".$value2.") AS ".$value2;
									$selectquery.=", ".$value2;
								} elseif($this->fields["db"][$key2]["encode"]) {
									$selectquery.=", DECODE(".$value2.",'".md5($this->fields["db"][$key2]["encode"])."') AS ".$value2;
								} else {
									if(is_array($this->fields["options"][$key2]["multiselectionfields"])) {
										while(list($key3,$value3)=each($this->fields["options"][$key2]["multiselectionfields"])) {
											if($value3) {
												$selectquery.=", ".$value3;
											}
										}
									} else {
										$selectquery.=", ".$value2;
									}
								}
							}
						}
#						echo $selectquery;
						$selectquery=substr($selectquery,2);
						# Record uit database halen
						$db0->query("SELECT ".$selectquery." FROM ".$key." WHERE ".$value["where"].($this->settings["db"]["set"] ? " AND ".$this->settings["db"]["set"] : "").";");
#						echo $db0->lastquery;
						if($db0->num_rows()==1) {
							$db0->next_record();
							while(list($key2,$value2)=each($value["fields"])) {

								unset($database_value);
								if($this->fields["db"][$key2]["not_unixtime"]) {
									if(substr($db0->f($value2),0,5)=="0000-") {
										# datum-veld is leeg
									} else {
										# non-unixtime-waarde omzetten naar unixtime
										$database_value=strtotime($db0->f($value2));
									}
								} else {
									$database_value=$db0->f($value2);
								}

								if($this->fields["type"][$key2]=="checkbox") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["selection"]=$database_value;
									$this->fields["previous"][$key2]["selection"]=$database_value;
								} elseif($this->fields["type"][$key2]=="datetime") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["time"]=$database_value;
									$this->fields["previous"][$key2]["time"]=$database_value;
								} elseif($this->fields["type"][$key2]=="htmlcol") {

								} elseif($this->fields["type"][$key2]=="htmlrow") {

								} elseif($this->fields["type"][$key2]=="multiradio") {
									if(is_array($this->fields["options"][$key2]["multiselectionfields"])) {
										reset($this->fields["options"][$key2]["multiselectionfields"]);
										while(list($key3,$value3)=each($this->fields["options"][$key2]["multiselectionfields"])) {
											if(!$this->filled) $this->fields["prevalue"][$key2]["multiselection"][$key3]=$db0->f($value3);
											$this->fields["previous"][$key2]["multiselection"][$key3]=$db0->f($value3);
										}
									}
#									echo wt_dump($this->fields["prevalue"][$key2]);
#									exit;
								} elseif($this->fields["type"][$key2]=="noedit") {
									if($this->fields["options"][$key2]["selection"]) {
										$this->fields["prevalue"][$key2]["selection"]=$database_value;
										$this->fields["previous"][$key2]["selection"]=$database_value;
# De volgende regel staat er i.v.m. mogelijke compatibiliteitsproblemen: alle sites nalopen met "noedit"-fields en kijken of er een prevalue wordt opgevraagd met "text" (moet "selection" worden als het een "selection"-prevalue is) (opmerking geplaatst in 2009)
										$this->fields["prevalue"][$key2]["text"]=$database_value;
										$this->fields["previous"][$key2]["text"]=$database_value;
									} else {
										$this->fields["prevalue"][$key2]["text"]=$database_value;
										$this->fields["previous"][$key2]["text"]=$database_value;
									}
								} elseif($this->fields["type"][$key2]=="onlyinoutput") {

								} elseif($this->fields["type"][$key2]=="password") {

								} elseif($this->fields["type"][$key2]=="radio") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["selection"]=$database_value;
									$this->fields["previous"][$key2]["selection"]=$database_value;
								} elseif($this->fields["type"][$key2]=="select") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["selection"]=$database_value;
									$this->fields["previous"][$key2]["selection"]=$database_value;
								} elseif($this->fields["type"][$key2]=="text") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["text"]=$database_value;
									$this->fields["previous"][$key2]["text"]=$database_value;
								} elseif($this->fields["type"][$key2]=="textarea") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["text"]=$database_value;
									$this->fields["previous"][$key2]["text"]=$database_value;
								} elseif($this->fields["type"][$key2]=="upload") {

								} elseif($this->fields["type"][$key2]=="yesno") {
									if(!$this->filled) $this->fields["prevalue"][$key2]["selection"]=$database_value;
									$this->fields["previous"][$key2]["selection"]=$database_value;
								} else {
									trigger_error("WT-Error: Unknown type '".$this->fields["type"][$key2]."' in function get_db",E_USER_ERROR);
								}
							}
						} else {
							if($db0->num_rows()>1) {
								if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") trigger_error("WT-Error: field '".$key."': query has more than 1 result (".$db0->lastquery.")",E_USER_ERROR);
							} else {
								if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") trigger_error("WT-Error: field '".$key."': query has less than 1 result (".$db0->lastquery.")",E_USER_ERROR);
							}
						}
					}
				}
			}
			$this->get_db=true;
		}
	}

	function display_output_field($id,$layout="") {
		global $vars;

		if($this->fields["checktype"][$id]=="currency") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
		} elseif($this->fields["checktype"][$id]=="date") {
			if($this->input[$id]) $value=$this->input[$id]["unixtime"]; else $value=$this->fields["prevalue"][$id]["time"];
			$return=datum("D MAAND JJJJ",$value,$this->settings["language"]);
		} elseif($this->fields["checktype"][$id]=="datetime") {
			if($this->input[$id]) $value=$this->input[$id]["unixtime"]; else $value=$this->fields["prevalue"][$id]["time"];
			$return=datum("D MAAND JJJJ, UU:ZZ",$value,$this->settings["language"]).$this->message("u").".";
		} elseif($this->fields["checktype"][$id]=="email") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
		} elseif($this->fields["checktype"][$id]=="float") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
		} elseif($this->fields["checktype"][$id]=="integer") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
		} elseif($this->fields["checktype"][$id]=="noedit") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
			if(is_array($this->fields["options"][$id]["selection"])) {
				$return=$this->fields["options"][$id]["selection"][$value];
			} else {
				$return=$value;
			}
		} elseif($this->fields["checktype"][$id]=="onlyinoutput") {
			if($this->input[$id]) $value=$this->input[$id]["unixtime"]; else $value=$this->fields["prevalue"][$id]["time"];
		} elseif($this->fields["checktype"][$id]=="select") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["selection"];
			$return=$this->fields["options"][$id]["selection"][$value];
		} elseif($this->fields["checktype"][$id]=="text") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
			$return=$value;
		} elseif($this->fields["checktype"][$id]=="textarea") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
			$return=$value;
		} elseif($this->fields["checktype"][$id]=="upload") {

		} elseif($this->fields["checktype"][$id]=="url") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["text"];
		} elseif($this->fields["checktype"][$id]=="yesno") {
			if($this->input[$id]) $value=$this->input[$id]; else $value=$this->fields["prevalue"][$id]["selection"];
			$return=($value ? $this->message("ja") : $this->message("nee"));
		} else {
			trigger_error("WT-Error: Unknown checktype '".$this->fields["checktype"][$id]."' (".$id.") in function display_output_field",E_USER_ERROR);
		}
		return $return;
	}


	function check_input() {
		global $vars;

		# - Foutcontrole (alle velden goed ingevuld?)
		# - Output-table vullen
		$this->check_input=true;
		if($this->filled and $_GET["fo"]<>$this->settings["formname"]) {
			$this->get_db();

			// check for csrf
			if($this->settings["prevent_csrf"]) {

				if($_POST[$this->settings["formname"]."_csrf_token"] and $_SESSION["form_csrf"][$_POST[$this->settings["formname"]."_csrf_token_name"] ] == $_POST[$this->settings["formname"]."_csrf_token"]) {

				} else {
					$this->error["extra"][]=$this->message("error_csrf");
					trigger_error("_notice: csrf-fout",E_USER_NOTICE);
				}
				// unset($_SESSION["form_csrf"][$_POST[$this->settings["formname"]."_csrf_token_name"] ]);
			}

			if($this->settings["prevent_spambots"]) {
				if($_POST["wtform_botcheck"]<>"checked3283847") {
					$this->error["extra"][]=$this->message("enable_javascript");
					trigger_error("_notice: spambot-fout",E_USER_NOTICE);
				}
			}

			reset($this->fields["checktype"]);
			while(list($key,$value)=each($this->fields["checktype"])) {
				if($value=="checkbox") {
					if(is_array($this->value[$key])) {
						while(list($key2,$value2)=each($this->value[$key])) {
							if($this->input[$key]) $this->input[$key].=",".$key2; else $this->input[$key]=$key2;
							if($this->fields["layout"][$key]["content_html"]) {
								$checkbox_value=strip_tags($this->fields["options"][$key]["selection"][$key2],"<a><b><i><u><strong>");
							} else {
								$checkbox_value=wt_he($this->fields["options"][$key]["selection"][$key2]);
							}
							if($this->outputtable_cell[$key]) $this->outputtable_cell[$key].="<br>".$checkbox_value; else $this->outputtable_cell[$key]=$checkbox_value;
						}
					} elseif($this->fields["obl"][$key]) {
						$this->error[$key]="obl";
					}
				} elseif($value=="currency") {
					if(ereg("^-?[0-9]+$",$this->value[$key])) $this->value[$key].=",00";
					if(ereg("^-?[0-9]+,[0-9]$",$this->value[$key])) $this->value[$key].="0";

					# Punten ook toestaan (omzetten in komma)
					$this->value[$key]=ereg_replace("\.",",",$this->value[$key]);

					# Om te kunnen gebruiken als float: komma naar punt omzetten
					$this->input[$key]=ereg_replace(",",".",$this->value[$key]);

					if($this->value[$key]=="-0,00") $this->value[$key]="0,00";
					$this->outputtable_cell[$key]=$this->value[$key];
					if($this->value[$key] and !eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+$",$this->value[$key]) and !eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+,[0-9]{1,2}$",$this->value[$key])) $this->error[$key]=$this->message("error_currency");
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="date") {

					if(!is_array($this->value[$key])) {
						$this->value[$key] = array();
					}

					# Indien hide_year: year=2004 (schrikkeljaar, dus 29 februari is mogelijk om in te voeren)
					if($this->fields["options"][$key]["hide_year"] and $this->value[$key]["day"] and $this->value[$key]["month"]) $this->value[$key]["year"]=2004;

					$this->input[$key]["day"]=$this->value[$key]["day"];
					$this->input[$key]["month"]=$this->value[$key]["month"];
					$this->input[$key]["year"]=$this->value[$key]["year"];
					if($this->value[$key]["day"] and $this->value[$key]["month"] and $this->value[$key]["year"]) {
						if(@checkdate($this->value[$key]["month"],$this->value[$key]["day"],$this->value[$key]["year"])) {
							$this->input[$key]["unixtime"]=adodb_mktime(0,0,0,$this->value[$key]["month"],$this->value[$key]["day"],$this->value[$key]["year"]);
						} else {
							$this->error[$key]=$this->message("error_foutedatum");
						}
						$this->outputtable_cell[$key]=datum("D MAAND JJJJ",$this->input[$key]["unixtime"],$this->settings["language"]);
					} elseif($this->value[$key]["day"] or $this->value[$key]["month"] or $this->value[$key]["year"]) {
						$this->error[$key]=$this->message("error_onvolledigedatum");
						$this->outputtable_cell[$key]="";
					} else {
						$this->outputtable_cell[$key]="";
					}
					if($this->fields["obl"][$key] and (!$this->value[$key]["day"] or !$this->value[$key]["month"] or !$this->value[$key]["year"])) $this->error[$key]="obl";
				} elseif($value=="datetime") {
					# Indien hide_year: year=2004 (schrikkeljaar, dus 29 februari is mogelijk om in te voeren)
					if($this->fields["options"][$key]["hide_year"] and $this->value[$key]["day"] and $this->value[$key]["month"]) $this->value[$key]["year"]=2004;

					$this->input[$key]["hour"]=$this->value[$key]["hour"];
					$this->input[$key]["minute"]=$this->value[$key]["minute"];
					$this->input[$key]["day"]=$this->value[$key]["day"];
					$this->input[$key]["month"]=$this->value[$key]["month"];
					$this->input[$key]["year"]=$this->value[$key]["year"];
					if($this->value[$key]["day"] and $this->value[$key]["month"] and $this->value[$key]["year"] and $this->value[$key]["hour"]<>"" and $this->value[$key]["minute"]<>"") {
						if($this->value[$key]["hour"]>23 or $this->value[$key]["minute"]>59 or !ereg("^[0-9]{1,2}$",$this->value[$key]["hour"]) or !ereg("^[0-9]{1,2}$",$this->value[$key]["minute"])) {
							$this->error[$key]=$this->message("error_foutetijd");
						} elseif(@checkdate($this->value[$key]["month"],$this->value[$key]["day"],$this->value[$key]["year"])) {
							$this->input[$key]["unixtime"]=adodb_mktime($this->value[$key]["hour"],$this->value[$key]["minute"],0,$this->value[$key]["month"],$this->value[$key]["day"],$this->value[$key]["year"]);
						} else {
							$this->error[$key]=$this->message("error_foutedatum");
						}
						$this->outputtable_cell[$key]=datum("D MAAND JJJJ",$this->input[$key]["unixtime"],$this->settings["language"]);
					} elseif($this->value[$key]["day"] or $this->value[$key]["month"] or $this->value[$key]["year"] or $this->value[$key]["hour"] or $this->value[$key]["minute"]) {
						$this->error[$key]=$this->message("error_onvolledigedatumtijd");
						$this->outputtable_cell[$key]="";
					} else {
						$this->outputtable_cell[$key]="";
					}
					if($this->fields["obl"][$key] and !$this->error[$key]) {
						if(($this->value[$key]["hour"]=="0" or $this->value[$key]["hour"]=="00" or $this->value[$key]["hour"]>0) and ($this->value[$key]["minute"]=="0" or $this->value[$key]["minute"]=="00" or $this->value[$key]["minute"]>0)) {
							if(!isset($this->value[$key]["hour"]) or !isset($this->value[$key]["minute"]) or !$this->value[$key]["day"] or !$this->value[$key]["month"] or !$this->value[$key]["year"]) $this->error[$key]="obl";
						} else {
							$this->error[$key]="obl";
						}
					}
				} elseif($value=="email") {
					$this->input[$key]=strtolower($this->value[$key]);
					$this->outputtable_cell[$key]="<a href=\"mailto:".wt_he($this->value[$key])."\">".wt_he($this->value[$key])."</a>";
					if($this->value[$key] and !wt_validmail($this->value[$key])) $this->error[$key]=$this->message("error_email");
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="float") {
					if(ereg("^-?[0-9]+$",$this->value[$key])) $this->value[$key].=",00";
					if(ereg("^-?[0-9]+,[0-9]$",$this->value[$key])) $this->value[$key].="0";
					$this->input[$key]=ereg_replace(",",".",$this->value[$key]);
					if($this->value[$key]=="-0,00") $this->value[$key]="0,00";
					$this->outputtable_cell[$key]=$this->value[$key];
					if($this->fields["options"][$key]["decimals"]) {
						$temp_decimals=$this->fields["options"][$key]["decimals"];
					} else {
						$temp_decimals=2;
					}
					if($this->value[$key]) {
						if(!eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+$",$this->value[$key]) and !eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+,[0-9]{1,".$temp_decimals."}$",$this->value[$key])) {
							$this->error[$key]=$this->message("error_float");
						}
						if(eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+,[0-9]{1,}$",$this->value[$key]) and !eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+,[0-9]{1,".$temp_decimals."}$",$this->value[$key])) {
							$this->error[$key]=$this->message("error_float_toomany").": ".$temp_decimals;
						}
					}
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="htmlcol") {

				} elseif($value=="htmlrow") {
					if($this->fields["options"][$key]["htmlrow_in_output"]) {
						$this->outputtable_row[$key]=$this->fields["title"][$key];
					}
				} elseif($value=="integer") {
					$this->input[$key]=$this->value[$key];
					$this->outputtable_cell[$key]=$this->value[$key];
					if($this->value[$key] and !eregi("^".($this->fields["options"][$key]["negative"] ? "-?" : "")."[0-9]+$",$this->value[$key])) $this->error[$key]=$this->message("error_integer");
					if($this->fields["obl"][$key] and !$this->value[$key] and $this->value[$key]<>"0") $this->error[$key]="obl";
				} elseif($value=="multiradio") {
#					$this->input[$key]=$this->value[$key];
					if($this->fields["layout"][$key]["content_html"]) {
#						$this->outputtable_cell[$key]=$this->fields["options"][$key]["selection"][$this->value[$key]];
					} else {
#						$this->outputtable_cell[$key]=wt_he($this->fields["options"][$key]["selection"][$this->value[$key]]);
					}
					reset($this->fields["options"][$key]["selection"]);
					while(list($key2,$value2)=each($this->fields["options"][$key]["selection"])) {
						reset($this->fields["options"][$key]["multiselection"]);
						while(list($key3,$value3)=each($this->fields["options"][$key]["multiselection"])) {
							if(!$this->value[$key][$key2]) {
								if($this->fields["obl"][$key]) {
									$this->error[$key]="obl";
								}
							}
						}
						if($this->value[$key][$key2]) {
							if($this->input[$key][$this->value[$key][$key2]]) $this->input[$key][$this->value[$key][$key2]].=",".$key2; else $this->input[$key][$this->value[$key][$key2]]=$key2;
						}
					}
#					echo $this->error[$key];
#					echo wt_dump($this->input[$key]);
#					echo wt_dump($_POST);
#					exit;
				} elseif($value=="noedit") {
					$this->input[$key]=$this->fields["prevalue"][$key]["text"];
					$this->outputtable_cell[$key]=$this->fields["prevalue"][$key]["text"];
				} elseif($value=="onlyinoutput") {
					if($this->fields["prevalue"][$key]["text"]) {
						$this->input[$key]=$this->fields["prevalue"][$key]["text"];
						$this->outputtable_cell[$key]=$this->fields["prevalue"][$key]["text"];
					} elseif($_GET["pv_".$key]) {
						# Indien _GET["pv"] waarde in onlyinoutput plaatsen
						$this->input[$key]=$_GET["pv_".$key];
						$this->outputtable_cell[$key]=wt_he($_GET["pv_".$key]);
					}
				} elseif($value=="password") {
					$this->input[$key]=$this->value[$key];
#					$this->outputtable_cell[$key]=$this->value[$key];

					# Is het een nieuw wachtwoord (of de invoer van een bestaand wachtwoord)?
					if($this->fields["options"][$key]["new_password"]) {
						if($this->fields["options"][$key]["strong_password"]) {
							if($this->value[$key] and (strlen($this->value[$key])<6 or !ereg("[0-9]",$this->value[$key]) or !eregi("[a-z]",$this->value[$key]))) $this->error[$key]=$this->message("error_password_strong");
						} elseif($this->fields["options"][$key]["superstrong_password"]) {
							if($this->value[$key] and (strlen($this->value[$key])<6 or !ereg("[0-9]",$this->value[$key]) or !ereg("[a-z]",$this->value[$key]) or !ereg("[A-Z]",$this->value[$key]))) $this->error[$key]=$this->message("error_password_superstrong");
						}
						if($this->value[$key] and ereg(" ",$this->value[$key])) $this->error[$key]=$this->message("error_password_spaces");
					}
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="radio") {
					$this->input[$key]=$this->value[$key];
					if($this->fields["layout"][$key]["content_html"]) {
						$this->outputtable_cell[$key]=$this->fields["options"][$key]["selection"][$this->value[$key]];
					} else {
						$this->outputtable_cell[$key]=wt_he($this->fields["options"][$key]["selection"][$this->value[$key]]);
					}
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="select") {
					$this->input[$key]=$this->value[$key];
					if($this->fields["layout"][$key]["content_html"]) {
						$this->outputtable_cell[$key]=$this->fields["options"][$key]["selection"][$this->value[$key]];
					} else {
						$this->outputtable_cell[$key]=wt_he($this->fields["options"][$key]["selection"][$this->value[$key]]);
					}
					if($this->fields["options"][$key]["allow_0"]) {
						if($this->fields["obl"][$key] and !$this->value[$key] and $this->value[$key]<>"0") $this->error[$key]="obl";
					} else {
						if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
					}
				} elseif($value=="text") {
					$this->input[$key]=$this->value[$key];
					$this->outputtable_cell[$key]=wt_he($this->value[$key]);
					if($this->fields["obl"][$key] and $this->value[$key]=="") $this->error[$key]="obl";
				} elseif($value=="textarea") {
					$this->input[$key]=$this->value[$key];
					$this->outputtable_cell[$key]=nl2br(wt_he($this->value[$key]));
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="upload") {
					$file_upload[$key]=false;
					unset($upload_error);
					if(is_array($_FILES["input"]["tmp_name"][$key])) {
						while(list($key2,$value2)=each($_FILES["input"]["tmp_name"][$key])) {
							if($_FILES["input"]["tmp_name"][$key][$key2] and $_FILES["input"]["tmp_name"][$key][$key2]<>"none") {
								$file_upload[$key]=true;
								unset($temp,$resize_image);
								if(file_exists("tmp/")) {
									if(is_writable("tmp/")) {
										# Kijken of upload voldoet aan size

										# Kijken of upload voldoet aan afmetingen
										if($this->fields["options"][$key]["img_width"] or $this->fields["options"][$key]["img_height"] or $this->fields["options"][$key]["img_maxwidth"] or $this->fields["options"][$key]["img_maxheight"] or ($this->fields["options"][$key]["img_ratio_width"] and $this->fields["options"][$key]["img_ratio_height"])) {
											$temp["img"]["getimagesize"]=@getimagesize($_FILES["input"]["tmp_name"][$key][$key2]);

											# Width checken
											if($this->fields["options"][$key]["img_width"] and $temp["img"]["getimagesize"][0]<>$this->fields["options"][$key]["img_width"]) {
												if($this->fields["options"][$key]["autoresize"]) {
													if($temp["img"]["getimagesize"][0]>$this->fields["options"][$key]["img_width"]) {
														# alleen resizen als afbeelding groter is dan img_width
														$resize_image["width"]=$this->fields["options"][$key]["img_width"];
													}
												} else {
													if($this->fields["options"][$key]["img_height"]) {
														$upload_error[$key2]=$this->message("error_img_size","",array(1=>$this->fields["options"][$key]["img_width"],2=>$this->fields["options"][$key]["img_height"]));
													} else {
														$upload_error[$key2]=$this->message("error_img_size_width","",array(1=>$this->fields["options"][$key]["img_width"]));
													}
												}
											}

											# Height checken
											if($this->fields["options"][$key]["img_height"] and $temp["img"]["getimagesize"][1]<>$this->fields["options"][$key]["img_height"]) {
												if($this->fields["options"][$key]["autoresize"]) {
													if($temp["img"]["getimagesize"][1]>$this->fields["options"][$key]["img_height"]) {
														# alleen resizen als afbeelding groter is dan img_height
														$resize_image["height"]=$this->fields["options"][$key]["img_height"];
													}
												} else {
													if($this->fields["options"][$key]["img_width"]) {
														$upload_error[$key2]=$this->message("error_img_size","",array(1=>$this->fields["options"][$key]["img_width"],2=>$this->fields["options"][$key]["img_height"]));
													} else {
														$upload_error[$key2]=$this->message("error_img_size_height","",array(1=>$this->fields["options"][$key]["img_height"]));
													}
												}
											}

											# Maxwidth checken
											if($this->fields["options"][$key]["img_maxwidth"] and $temp["img"]["getimagesize"][0]>$this->fields["options"][$key]["img_maxwidth"]) {
												if($this->fields["options"][$key]["autoresize"]) {
													$resize_image["width"]=$this->fields["options"][$key]["img_maxwidth"];
												} else {
													if($this->fields["options"][$key]["img_maxheight"]) {
														$upload_error[$key2]=$this->message("error_img_size_maxsize","",array(1=>$this->fields["options"][$key]["img_maxwidth"],2=>$this->fields["options"][$key]["img_maxheight"]));
													} else {
														$upload_error[$key2]=$this->message("error_img_size_maxwidth","",array(1=>$this->fields["options"][$key]["img_maxwidth"]));
													}
												}
											}



											# Maxheight checken
											if($this->fields["options"][$key]["img_maxheight"] and $temp["img"]["getimagesize"][1]>$this->fields["options"][$key]["img_maxheight"]) {
												if($this->fields["options"][$key]["autoresize"]) {
													$resize_image["height"]=$this->fields["options"][$key]["img_maxheight"];
												} else {
													if($this->fields["options"][$key]["img_maxwidth"]) {
														$upload_error[$key2]=$this->message("error_img_size_maxsize","",array(1=>$this->fields["options"][$key]["img_maxwidth"],2=>$this->fields["options"][$key]["img_maxheight"]));
													} else {
														$upload_error[$key2]=$this->message("error_img_size_maxheight","",array(1=>$this->fields["options"][$key]["img_maxheight"]));
													}
												}
											}

											# Minwidth checken
											if($this->fields["options"][$key]["img_minwidth"] and $temp["img"]["getimagesize"][0]<$this->fields["options"][$key]["img_minwidth"]) {
												if($this->fields["options"][$key]["img_minheight"]) {
													$upload_error[$key2]=$this->message("error_img_size_minsize","",array(1=>$this->fields["options"][$key]["img_minwidth"],2=>$this->fields["options"][$key]["img_minheight"]));
												} else {
													$upload_error[$key2]=$this->message("error_img_size_minwidth","",array(1=>$this->fields["options"][$key]["img_minwidth"]));
												}
											}

											# Minheight checken
											if($this->fields["options"][$key]["img_minheight"] and $temp["img"]["getimagesize"][1]<$this->fields["options"][$key]["img_img_minheight"]) {
												if($this->fields["options"][$key]["img_minwidth"]) {
													$upload_error[$key2]=$this->message("error_img_size_minsize","",array(1=>$this->fields["options"][$key]["img_minwidth"],2=>$this->fields["options"][$key]["img_minheight"]));
												} else {
													$upload_error[$key2]=$this->message("error_img_size_img_minheight","",array(1=>$this->fields["options"][$key]["img_img_minheight"]));
												}
											}

											# Ratio checken
											if($this->fields["options"][$key]["img_ratio_width"] and $this->fields["options"][$key]["img_ratio_height"] and $temp["img"]["getimagesize"][0] and $temp["img"]["getimagesize"][1]) {
												if(round($this->fields["options"][$key]["img_ratio_width"]/$this->fields["options"][$key]["img_ratio_height"],2)<>round($temp["img"]["getimagesize"][0]/$temp["img"]["getimagesize"][1],2)) {
													$upload_error[$key2]=$this->message("error_img_ratio","",array(1=>$this->fields["options"][$key]["img_ratio_width"],2=>$this->fields["options"][$key]["img_ratio_height"]));
												}
											}
										}

										# Kijk of upload voldoet aan filetype
										$temp["extension"]=strtolower(ereg_replace("^.*\.([a-z0-9A-Z]{1,8})$","\\1",$_FILES["input"]["name"][$key][$key2]));
										$temp["filetype_array"]=split(",",$this->fields["options"][$key]["must_be_filetype"]);
										if(!$temp["extension"] or ($this->fields["options"][$key]["must_be_filetype"] and !in_array($temp["extension"],$temp["filetype_array"]))) {
											$upload_error[$key2]=$this->message("error_filetype");
										}
										if($temp["img"]["getimagesize"]["mime"]) {
											if($this->fields["options"][$key]["must_be_filetype"]=="jpg" and $temp["img"]["getimagesize"]["mime"]<>"image/jpeg") {
												$upload_error[$key2]=$this->message("error_filetype_jpg");
											}
											if($this->fields["options"][$key]["must_be_filetype"]=="gif" and $temp["img"]["getimagesize"]["mime"]<>"image/gif") {
												$upload_error[$key2]=$this->message("error_filetype_gif");
											}
											if($this->fields["options"][$key]["must_be_filetype"]=="png" and $temp["img"]["getimagesize"]["mime"]<>"image/png") {
												$upload_error[$key2]=$this->message("error_filetype_png");
											}
										}
										if(!$upload_error[$key2]) {
											# Bestand verplaatsen
											$this->upload[$key][$key2]["tmp_name"]="phpupload".md5(uniqid(rand()));
											$this->upload[$key][$key2]["name"]=$_FILES["input"]["name"][$key][$key2];
											$this->file_uploaded[$key]=true;
											move_uploaded_file($_FILES["input"]["tmp_name"][$key][$key2],"tmp/".$this->upload[$key][$key2]["tmp_name"]);

											if($resize_image) {
												if($this->fields["options"][$key]["must_be_filetype"]=="jpg" or $this->fields["options"][$key]["must_be_filetype"]=="gif" or $this->fields["options"][$key]["must_be_filetype"]=="png") {
													if($this->fields["options"][$key]["autoresize_cut"]) {
														$autoresize_cut=true;
													} else {
														$autoresize_cut=false;
													}
													if($resize_image["width"] or $resize_image["height"]) {
														wt_create_thumbnail("tmp/".$this->upload[$key][$key2]["tmp_name"],"tmp/".$this->upload[$key][$key2]["tmp_name"],$resize_image["width"],$resize_image["height"],$autoresize_cut,$this->fields["options"][$key]["must_be_filetype"]);
													}
												}
											}
										}
									} else {
										trigger_error("WT-Error: Directory ".dirname($_SERVER["PHP_SELF"])."/tmp/ is not writable",E_USER_ERROR);
									}
								} else {
									trigger_error("WT-Error: Directory ".dirname($_SERVER["PHP_SELF"])."/tmp/ does not exist",E_USER_ERROR);
								}
							}
						}
						if(is_array($upload_error)) {
							while(list($key2,$value2)=each($upload_error)) {
								$upload_error_counter++;
								$this->error[$key].=$value2;
								if($upload_error_counter<count($upload_error)) $this->error[$key].=" - ";
							}
						}
					}
					if(!$file_upload[$key]) {
						if(is_array($this->value[$key])) {
							while(list($key2,$value2)=each($this->value[$key])) {
								if(ereg("^phpupload",$this->value[$key][$key2]["tmp_name"])) {
									$temp["file_ontvangen"][$key]=true;
									$this->upload[$key][$key2]["tmp_name"]=basename($this->value[$key][$key2]["tmp_name"]);
									$this->upload[$key][$key2]["name"]=basename($this->value[$key][$key2]["name"]);
									$this->file_uploaded[$key]=true;
								}
							}
						}

						if(!$temp["file_ontvangen"][$key] and $this->fields["obl"][$key]) {
							$temp["filename"]=$this->fields["options"][$key]["move_file_to"].$this->fields["options"][$key]["rename_file_to"].".".$this->fields["options"][$key]["must_be_filetype"];
							if(!file_exists($temp["filename"])) {
								if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
									# geen foutmelding bij testen
								} else {
									$this->error[$key]="obl";
								}
							}
						}
					}
					if($this->file_uploaded[$key]) {
						$this->outputtable_cell[$key]=$this->message("zie_attachment");
					}

				} elseif ($value === 'mongodb_upload') {

					/**
					 * Processing mongodb uploads
					 *
					 * This section processes the selected files to be uploaded
					 * to mongodb. It also keeps track of the files uploaded in case
					 * a file_id is not provided in the options so the form class will
					 * update these files with the correct one once it has been generated.
					 *
					 * It will ignore all uploads once it exceeded the provided limit and shows
					 * an error.
					 */
					$files = $_FILES['input']['tmp_name'][$key];
					if (is_array($files)) {

						$limit   	        = $this->fields['options'][$key]['limit'];
						$collection         = $this->fields['options'][$key]['collection'];
						$fileId		        = $this->fields['options'][$key]['file_id'];
						$sizeOptions		= [];

						if ($this->fields['options'])
						$sizeOptions		= [

							'width'       => $this->fields['options'][$key]['img_width'],
							'height'      => $this->fields['options'][$key]['img_height'],
							'max_width'	  => $this->fields['options'][$key]['img_maxwidth'],
							'max_height'  => $this->fields['options'][$key]['img_maxheight'],
							'min_width'   => $this->fields['options'][$key]['img_minwidth'],
							'min_height'  => $this->fields['options'][$key]['img_minheight'],
							'ratio_width' => $this->fields['options'][$key]['img_ratio_width'],
							'ratio_width' => $this->fields['options'][$key]['img_ratio_height'],
						];

						$mongodb 	        = $vars['mongodb']['wrapper'];
						$maxRank 	        = $mongodb->maxRank($collection, $fileId);
						$this->mongo_upload = [$key => []];

						foreach ($files as $i => $file) {

							if (!$file) {
								continue;
							}

							list($width, $height) = getimagesize($file);

							$id = $mongodb->storeFile($collection, $file, [

								'file_id'  => intval($fileId),
								'rank'	   => ++$maxRank,
								'filename' => $_FILES['input']['name'][$key][$i],
								'kind'	   => $this->fields['options'][$key]['default_kind'],
								'width'    => $width,
								'height'   => $height,
							]);

							$this->mongo_upload[$key][$i] = $id;

							if ($maxRank === $limit) {
								break;
							}
						}
					}

				} elseif($value=="url") {
					if($this->value[$key]=="http://") $this->value[$key]="";
					# Trailing slash toevoegen
					if(ereg("^https?://(.*)",$this->value[$key],$regs) and !ereg("/",$regs[1])) $this->value[$key].="/";
					$this->input[$key]=$this->value[$key];
					$this->outputtable_cell[$key]="<a href=\"".wt_he($this->value[$key])."\">".wt_he($this->value[$key])."</a>";
					if($this->value[$key] and !ereg("^https?://",$this->value[$key])) $this->error[$key]=$this->message("error_url");
					if($this->fields["obl"][$key] and !$this->value[$key]) $this->error[$key]="obl";
				} elseif($value=="yesno") {
					$this->input[$key]=$this->value[$key];
					$this->outputtable_cell[$key]=($this->value[$key] ? $this->message("ja") : $this->message("nee"));
				} else {
					trigger_error("WT-Error: Unknown checktype '".$value."' in function check_input",E_USER_ERROR);
				}
				if(!$this->error[$key]) $this->checked_input[$key]=$this->input[$key];
			}
			if(!is_array($this->error)) {
				$this->okay=true;
			}
		}
	}

	function outputtable($complete=true) {
		global $vars;

		$return="<table class=\"wtform_table\">";
		if($complete) {
			$return.="<tr><td class=\"wtform_cell_left\">Formulier</td><td class=\"wtform_cell_right\">".wt_he($this->settings["fullname"])."</td></tr>";
			$return.="<tr><td class=\"wtform_cell_left\">Ingevuld op</td><td class=\"wtform_cell_right\">".strftime("%e %B %Y, %T")."</td></tr>";
		}
		reset($this->fields["title"]);
		while(list($key,$value)=each($this->fields["title"])) {
			if(isset($this->outputtable_cell[$key])) {
				$title = $value;
				if($this->fields["layout"][$key]["title_html"]) {
					$title = strip_tags($title);
				}
				$title = ereg_replace("\*$","",$title);
				$title = wt_he($title);
				$return.="<tr><td style=\"text-align:left;vertical-align:top;\" class=\"wtform_cell_left\">".$title."</td><td style=\"text-align:left;vertical-align:top;\" class=\"wtform_cell_right\">".(isset($this->outputtable_cell[$key]) ? $this->outputtable_cell[$key] : "&nbsp;")."</td></tr>";
			}
			if(isset($this->outputtable_row[$key])) $return.="<tr><td style=\"text-align:left;vertical-align:top;\" colspan=\"2\">".wt_he($value)."</td></tr>";
		}
		$return.="</td></tr>";
		if($this->outputtable_tr) {
			$return.=$this->outputtable_tr;
		}
		$return.="</table>";

		# _show_at_other_field_ eruit filteren
		$return=preg_replace("/_show_at_other_field_/","",$return);

		return $return;
	}

	function add_to_filesync_table($file, $delete=false) {
		// save file to table `filesync`

		if( $this->settings["add_to_filesync_table"] ) {
			$db=new DB_sql;

			if(!$this->settings["add_to_filesync_table_source"] and defined("wt_server_id")) {
				$this->settings["add_to_filesync_table_source"] = wt_server_id;
			}

			$db->query("INSERT INTO `filesync` SET `source`='".intval($this->settings["add_to_filesync_table_source"])."', `file`='".wt_as($file)."', `delete`='".intval($delete)."', `added`=NOW();");
		}
	}


	function mail($to,$toname,$subject,$fullbody="",$topbody="",$bottombody="",$from="formmail@webtastic.nl",$fromname="WebTastic FormMail",$special_settings=array("")) {
		global $vars;

		$mail=new wt_mail;
		if($this->settings["bcc_mail_https"]) $mail->send_bcc=true;
		$mail->fromname=$fromname;
		$mail->from=$from;
		$mail->toname=$toname;
		if($vars["wt_htmlentities_utf8"]) {
			$mail->settings["plaintext_utf8"]=true;
		}

		if($_GET["wttest"]) {
			$mail->to="jeroen@webtastic.nl";
		} else {
			$mail->to=$to;
		}
		if($subject) {
			$mail->subject=$subject;
		} else {
			$mail->subject=$this->message("invoer",false)." ".$this->settings["fullname"];
		}
		$mail->html="<html><head>".$this->mail_css()."</head>\n<body><table><tr><td>\n";
		if($fullbody) {
			$mail->html.=$fullbody;
		} else {
			$mail->html.=$topbody;
			$url="http".($_SERVER["HTTPS"]=="on" ? "s" : "")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
			$mail->html.="<p>".ereg_replace("\[URL\]","<a href=\"".$url."\">".wt_he($url)."</a>",$this->message("volgendegegevens")).":<p>".$this->outputtable()."<p>";
			$mail->html.=$bottombody;
		}
		$mail->html.="</td></tr></table></body></html>";

		# Uploads als attachments toevoegen
		while(list($key,$value)=@each($this->upload)) {
			if($this->fields["options"][$key]["multiple"] and $this->fields["options"][$key]["number_of_uploadbuttons"]) {
				$temp["aantal_uploadbuttons"]=$this->fields["options"][$key]["number_of_uploadbuttons"];
			} else {
				$temp["aantal_uploadbuttons"]=1;
			}
			for($i=1;$i<=$temp["aantal_uploadbuttons"];$i++) {
				if(file_exists("tmp/".$this->upload[$key][$i]["tmp_name"]) and $this->fields["options"][$key]["mail_as_filename"]) {
					$mail->attachment("tmp/".$this->upload[$key][$i]["tmp_name"],"",false,$this->fields["options"][$key]["mail_as_filename"]);
				}
			}
		}

		# Andere reply-to?
		if(isset($special_settings["replyto"]) and $special_settings["replyto"]) {
			$mail->replyto=$special_settings["replyto"];
		}

		# Mail verzenden
		$mail->send();
	}

	function mail_css() {
		global $vars;

		if(file_exists("class.form.css")) {
			$return="<style type=\"text/css\"><!--\n".file_get_contents("class.form.css")."\n--></style>";
		} else {
			ob_start();
			?>
			<style type="text/css"><!--

			body {
				background-color: #EBEBEB;
				font-family: Verdana, Helvetica, Arial, sans-serif;
				font-size: 12px;
			}

			a:link,a:active,a:visited {
				color:#0000FF;
			}

			a:visited:hover,a:hover {
				color:#878481;
			}

			td {
				font-family: Verdana, Helvetica, Arial, sans-serif;
				font-size: 12px;
			}

			.wtform_table {
				width: 600;
				background-color: #FFFFFF;
				border: 2px solid #878481;
				border-collapse: collapse;
			}

			.wtform_cell_left {
				width: 150px;
				border: 2px solid #878481;
				color: #0000FF;
				font-weight: bold;
				padding: 4px;
			}

			.wtform_cell_right {
				width: 450px;
				border: 2px solid #878481;
				padding: 4px;
			}

			.wtform_cell_colspan {
				padding: 4px;
			}

			--></style>
			<?php
			$return=ob_get_clean();
		}
		return $return;
	}

	function end_declaration() {
		global $vars;

		if(!$this->settings["fullname"]) {
			trigger_error("WT-Error: Form has no fullname",E_USER_ERROR);
		} elseif(!$this->check_input) {
			trigger_error("WT-Error: Missing check_input function",E_USER_ERROR);
		} else {

			# Eventueel: afbeeldingen wissen
			@reset($_POST["imagedelete"]);
			while(list($key,$value)=@each($_POST["imagedelete"])) {
				if(is_array($value)) {
					while(list($key2,$value2)=each($value)) {
						if($value2=="on") {
							$temp["filename"]=$this->fields["options"][$key]["move_file_to"].$this->fields["options"][$key]["rename_file_to"]."-".basename($key2).".".$this->fields["options"][$key]["must_be_filetype"];
							if($this->fields["options"][$key]["move_file_to"] and file_exists($temp["filename"])) {
								unlink($temp["filename"]);
								$this->add_to_filesync_table($temp["filename"], true);
								$this->deleted_images[$temp["filename"]]=true;
							}
						}
					}
				} else {
					if($value=="on") {
						if(preg_match("@,@", $this->fields["options"][$key]["must_be_filetype"])) {
							// multiple filetypes: delete all extensions
							$file_extensions = preg_split("@,@", $this->fields["options"][$key]["must_be_filetype"]);
							foreach ($file_extensions as $key5 => $value5) {
								$temp["filename"]=$this->fields["options"][$key]["move_file_to"].$this->fields["options"][$key]["rename_file_to"].".".$value5;
								if($this->fields["options"][$key]["move_file_to"] and file_exists($temp["filename"])) {
									unlink($temp["filename"]);
									$this->add_to_filesync_table($temp["filename"], true);
									$this->deleted_images[$temp["filename"]]=true;
								}
							}
						} else {
							$temp["filename"]=$this->fields["options"][$key]["move_file_to"].$this->fields["options"][$key]["rename_file_to"].".".$this->fields["options"][$key]["must_be_filetype"];
							if($this->fields["options"][$key]["move_file_to"] and file_exists($temp["filename"])) {
								unlink($temp["filename"]);
								$this->add_to_filesync_table($temp["filename"], true);
								$this->deleted_images[$temp["filename"]]=true;
							}
						}
					}
				}
			}

			if (isset($_POST['delete_mongodb'])) {

				$mongodb = $vars['mongodb']['wrapper'];
				foreach ($_POST['delete_mongodb'] as $key => $files) {

					$collection = $this->fields['options'][$key]['collection'];
					foreach ($files as $_id => $val) {
						$mongodb->removeFile($collection, $_id);
					}
				}
			}

			# Eventueel: form_after_imagedelte-functie runnen
			if(function_exists(form_after_imagedelete)) {
				form_after_imagedelete($this);
			}

			# Eventueel: afbeeldingen van volgorde veranderen
			@reset($_POST["orgimgorder"]);
			while(list($key,$value)=@each($_POST["orgimgorder"])) {
				if(is_array($value)) {
					unset($volgorde_gewijzigd,$nieuwevolgorde,$nieuwevolgorde_teller,$nieuwevolgorde_nieuwenaam);
					while(list($key2,$value2)=each($value)) {
						if($value2<>$_POST["imgorder"][$key][$key2]) {
							$volgorde_gewijzigd=true;
						}
						$nieuwevolgorde[$key2]=$_POST["imgorder"][$key][$key2];
					}
					asort($nieuwevolgorde);
					if($volgorde_gewijzigd) {
						while(list($key2,$value2)=@each($nieuwevolgorde)) {
							$nieuwevolgorde_teller++;
							$nieuwevolgorde_nieuwenaam[$key2]=ereg_replace("-([0-9]+)\.","-".$nieuwevolgorde_teller.".",$key2);
						}
						@reset($nieuwevolgorde_nieuwenaam);
						while(list($key2,$value2)=@each($nieuwevolgorde_nieuwenaam)) {
							if(file_exists($this->fields["options"][$key]["move_file_to"].$key2)) {
								rename($this->fields["options"][$key]["move_file_to"].$key2,$this->fields["options"][$key]["move_file_to"]."_temp_".$key2);
								$this->add_to_filesync_table($this->fields["options"][$key]["move_file_to"].$key2, true);
							}
						}
						@reset($nieuwevolgorde_nieuwenaam);
						while(list($key2,$value2)=@each($nieuwevolgorde_nieuwenaam)) {
							if(file_exists($this->fields["options"][$key]["move_file_to"]."_temp_".$key2)) {
								rename($this->fields["options"][$key]["move_file_to"]."_temp_".$key2,$this->fields["options"][$key]["move_file_to"].$value2);
								touch($this->fields["options"][$key]["move_file_to"].$value2);
								$this->add_to_filesync_table($this->fields["options"][$key]["move_file_to"].$value2);
							}
						}
					}
				}
			}

			if (isset($_POST['kind_mongodb'])) {

				$mongodb = $vars['mongodb']['wrapper'];
				foreach ($_POST['kind_mongodb'] as $key => $kinds) {

					$collection = $this->fields['options'][$key]['collection'];
					$bulk		= $mongodb->getBulkUpdater($collection);

					foreach ($kinds as $_id => $kind) {

						$bulk->add(['q' => ['_id'  => new MongoId($_id)],
									'u' => ['$set' => ['metadata.kind' => $kind]]]);
					}

					$bulk->execute();
				}
			}

			if (isset($_POST['label_mongodb'])) {

				$mongodb = $vars['mongodb']['wrapper'];
				foreach ($_POST['label_mongodb'] as $key => $labels) {

					$collection = $this->fields['options'][$key]['collection'];
					$bulk		= $mongodb->getBulkUpdater($collection);
					foreach ($labels as $_id => $label) {

						$bulk->add(['q' => ['_id'  => new MongoId($_id)],
									'u' => ['$set' => ['metadata.label' => $label]]]);
					}

					$bulk->execute();
				}
			}

			if (isset($_POST['rank_mongodb'])) {

				$mongodb = $vars['mongodb']['wrapper'];

				foreach ($_POST['rank_mongodb'] as $key => $ranks) {

					// sorting
					asort($ranks, SORT_NUMERIC);

					$i			  = 1;
					$collection   = $this->fields['options'][$key]['collection'];
					$bulk		  = $mongodb->getBulkUpdater($collection);

					foreach ($ranks as $_id => $rank) {

						$bulk->add(['q' => ['_id'  => new MongoId($_id)],
									'u' => ['$set' => ['metadata.rank' => $i++]]]);
					}

					$bulk->execute();
				}
			}

			if(!is_array($this->error) and !$_GET["fo"] and $this->filled) {
				# Eventuele uploads verplaatsen
				if(is_array($this->upload)) {
					reset($this->upload);
					while(list($key,$value)=each($this->upload)) {
						while(list($key2,$value2)=each($value)) {
							unset($temp);
							if($this->fields["options"][$key]["move_file_to"]) {
								if(ereg("^(.*)\.([a-z0-9A-Z]{1,8})$",$value2["name"],$regs)) {
									$temp["filename_we"]=basename($regs[1]);
									$temp["extension"]=strtolower($regs[2]);
								}
								if(isset($this->fields["options"][$key]["rename_file_to"])) {
									$temp["filename"]=$this->fields["options"][$key]["rename_file_to"];
								} else {
									$temp["filename"]=$temp["filename_we"];
								}
								if($this->db_insert_id) $temp["filename"].=$this->db_insert_id;
								if($this->fields["options"][$key]["multiple"]) {
									$d=dir($this->fields["options"][$key]["move_file_to"]);
									while($entry=$d->read()) {
										if($entry!="." and $entry!=".." and ereg("^".$temp["filename"]."-([0-9]+)\.".$this->fields["options"][$key]["must_be_filetype"]."$",$entry,$regs)) {
											if($temp["max_filenumber"]<$regs[1]) $temp["max_filenumber"]=$regs[1];
										}
									}
									$d->close();
									$temp["max_filenumber"]++;
									$temp["filename"].="-".$temp["max_filenumber"];
								}

								$temp["newfilename"]=$this->fields["options"][$key]["move_file_to"].$temp["filename"].($temp["extension"] ? ".".$temp["extension"] : "");
								copy("tmp/".$value2["tmp_name"],$temp["newfilename"]);
								@chmod($temp["newfilename"],0666);

								if(file_exists($temp["newfilename"])) {
									$this->upload_okay[$key]=true;
									$this->upload_filename[$temp["newfilename"]]=true;

									$this->add_to_filesync_table($temp["newfilename"]);

									if($this->settings["show_upload_message"]) {
										$_SESSION["wt_popupmsg"]="bestand(en) correct ontvangen";
									}
								}
							}

							# tmp-bestand wissen
							unlink("tmp/".$value2["tmp_name"]);

							# Oude tmp-bestanden wissen
							$d=dir("tmp/");
							while($entry=$d->read()) {
								if(ereg("^phpupload[a-z0-9]{32}$",$entry,$regs)) {
									if(filemtime("tmp/".$entry)<(time()-86400)) unlink("tmp/".$entry);
								}
							}
							$d->close();
						}
					}
				}

				/**
				 * This part will update the mongodb entries when new item has been added (unknown id when uploaded)
				 * to the now known id.
				 */
				if (is_array($this->mongo_upload) && count($this->mongo_upload) > 0 && $this->db_insert_id) {

					$mongodb = $vars['mongodb']['wrapper'];
					foreach ($this->mongo_upload as $key => $files) {

						$collection = $this->fields['options'][$key]['collection'];
						foreach ($files as $i => $id) {
							$file = $mongodb->updateFileId($collection, $id, $this->db_insert_id);
						}
					}
				}

				if(function_exists(form_before_goto)) {
					form_before_goto($this);
				}

				# Ga (na correct invullen) naar $this->settings["goto"] of naar PHP_SELF met ?fo=1
				if($this->settings["type"]=="get" and !$this->settings["goto"]) {

				} elseif($this->settings["go_nowhere"]) {

				} else {
					if($_SERVER["REQUEST_URI"]) {
						if($_SERVER["QUERY_STRING"]) {
							$goto=ereg_replace("\?.*","",$_SERVER["REQUEST_URI"]);
						} else {
							$goto=$_SERVER["REQUEST_URI"];
						}
					} else {
						$goto=$_SERVER["PHP_SELF"];
					}
					if(substr($goto,-1)=="?") $goto=substr($goto,0,-1);
					if($_POST["PHPSESSID"] and !ereg("PHPSESSID=",$_SERVER["QUERY_STRING"])) {
						$phpsessid="&PHPSESSID=".$_POST["PHPSESSID"];
					} else {
						$phpsessid="";
					}
					header("Location: ".($this->settings["goto"] ? $this->settings["goto"] : $goto."?fo=".$this->settings["formname"].($_SERVER["QUERY_STRING"] ? "&".$_SERVER["QUERY_STRING"] : "").$phpsessid));
					exit;
				}
			}
			# Gegevens uit database halen
			$this->get_db();
		}
	}
}

?>
