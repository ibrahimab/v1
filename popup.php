<?php

if(ereg("^cms_",$_GET["id"])) {
	$mustlogin=true;
}

include("admin/vars.php");




if($_GET["id"]=="cms_mailtekst_bewerken" and isset($_POST["mailtekst_opties"]) and $_GET["bid"]) {
	$db->query("UPDATE boeking SET mailtekst_opties='".addslashes($_POST["mailtekst_opties"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk".$_GET["bid"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
	exit;
}

if($_GET["id"]=="cms_mailtekst_aanmaningen_bewerken" and isset($_POST["mailtekst_opties"]) and $_GET["bid"]) {
	$db->query("UPDATE boeking SET aanmaning_tekst='".addslashes($_POST["mailtekst_opties"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk".$_GET["bid"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
	exit;
}

if($_GET["id"]=="cms_mail_klanten_vorig_seizoen_bewerken" and isset($_POST["mailtekst"]) and $_GET["bid"]) {
	$db->query("UPDATE boeking SET mailtekst_klanten_vorig_seizoen='".addslashes($_POST["mailtekst"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk".$_GET["bid"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
	exit;
}

#if($vars["websitetype"]==1) {
#	$bordercolor="#0D3E88";
#	$rood="#D3033A";
#	$hover="#0D3E88";
#	$hr="#D5E1F9";
#	$table="#0D3E88";
#	$font="Tahoma, Helvetica, sans-serif;";
#} else {
#	$bordercolor="#BAC5D6";
#	$rood="#CC0033";
#	$hover="#00cc00";
#	$hr="#BAC5D6";
#	$table="#BAC5D6";
#	$font="Arial, Helvetica, sans-serif;";
#}

if($_GET["typeid"]) {
	$accinfo=accinfo($_GET["typeid"]);
	$aanbieding[$_GET["sid"]]=aanbiedinginfo($_GET["typeid"],$_GET["sid"]);
}

if($_GET["gid"]) {
	$db->query("SELECT s.naam, s.omschrijving".$vars["ttv"]." AS omschrijving1, g.omschrijving".$vars["ttv"]." AS omschrijving2 FROM optie_soort s, optie_groep g WHERE g.optie_groep_id='".addslashes($_GET["gid"])."' AND g.optie_soort_id=s.optie_soort_id;");
	if($db->next_record()) {
		if($db->f("omschrijving1")) $omschrijving=nl2br(wt_htmlentities($db->f("omschrijving1"),true,true))."<br>";
		if($db->f("omschrijving2")) $omschrijving.=nl2br(wt_htmlentities($db->f("omschrijving2"),true,true));
	}
}

if($_GET["bkid"]) {
	$db->query("SELECT b.naam, b.omschrijving".$vars["ttv"]." AS omschrijving FROM bijkomendekosten b WHERE b.bijkomendekosten_id='".addslashes($_GET["bkid"])."';");
	if($db->next_record()) {
		$omschrijving=nl2br(wt_htmlentities($db->f("omschrijving"),true,true))."<br>";
	}	
}

$meertalig_array=array("annuleringsverzekering","schadeverzekering");

$title["soort"]=txt("popuptitle_soort");
$title["algemenevoorwaarden"]=txt("popuptitle_algemenevoorwaarden","",array("v_websitenaam"=>$vars["langewebsitenaam"]));
$title["bijkomendekosten"]=txt("popuptitle_bijkomendekosten");
$title["verzekeringen"]=txt("popuptitle_verzekeringen");
$title["catering"]=txt("popuptitle_catering");
$title["materiaalhuur1"]=txt("popuptitle_materiaalhuur1");
$title["materiaalhuur2"]=txt("popuptitle_materiaalhuur2");
$title["kwaliteit"]=txt("popuptitle_kwaliteit");
$title["bijkomendekosten"]=txt("popuptitle_bijkomendekosten");
$title["opties"]=txt("popuptitle_opties")." - ".$db->f("naam");
$title["tarieventabel"]=ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam"];
$title["zoektekst"]=txt("popuptitle_zoektekst");
$title["annuleringsverzekering"]=txt("popuptitle_annuleringsverzekering");
$title["stuurdoor"]=txt("popuptitle_stuurdoor");


$noprint["tarieventabel"]=true;
$noprint["stuurdoor"]=true;


if(ereg("(.*)\-query-(.*)",$_GET["id"],$regs)) {
	$_GET["id"]=$regs[1];
	$querystring=$regs[2];
}

echo "<HTML><HEAD>";
echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=8\">\n";
echo "<META NAME=\"robots\" CONTENT=\"noindex,follow\">";
echo "<TITLE>".htmlentities($title[$_GET["id"]])."</TITLE>";
echo "<link rel=\"stylesheet\" href=\"css/toonaccommodatie.css\">";
echo "<link REL=\"SHORTCUT ICON\" href=\"".$path;
if($vars["websitetype"]==2) {
	echo "favicon_wsa.ico";
} else {
	echo "favicon.ico";
}
echo "\">";
?>
<script type="text/javascript">
function validateEmail(email) {  
	var pattern = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
	return email.match(pattern);     
}  

function setVerzender(){
	//window.alert(document.getElementById("VerzenderNaam").value);
	//document.getElementById("Verzender").innerHTML = document.getElementById("VerzenderNaam").value;
}
function controlEnSend(){
	if(document.getElementById("VerzenderNaam").value == ""){
		//window.alert("OK");
		document.getElementById("Verzender").style.backgroundColor = 'red';
		document.getElementById("Verzender").style.color = 'white';
		document.getElementById("Verzender").style.padding = '10px';
		document.getElementById("Verzender").innerHTML = "U hebt uw naam niet ingevuld";
	}
	else if(document.getElementById("verzenderAdres").value == ""){
		//window.alert("OK");
		document.getElementById("Verzender").style.backgroundColor = 'red';
		document.getElementById("Verzender").style.color = 'white';
		document.getElementById("Verzender").style.padding = '10px';
		document.getElementById("Verzender").innerHTML = "U hebt uw email adres niet ingevuld";
	}
	else if(document.getElementById("EmailOntvanger").value == ""){
		//window.alert("OK");
		document.getElementById("Verzender").style.backgroundColor = 'red';
		document.getElementById("Verzender").style.color = 'white';
		document.getElementById("Verzender").style.padding = '10px';
		document.getElementById("Verzender").innerHTML = "U hebt de email adres(en) van de ontvanger(s) niet ingevuld";
	}
	else{
		var container = document.getElementById("EmailOntvanger").value;
		var emailAdressen = container.split(" ");
		for(var i =0; i < emailAdressen.length; i++){
			if(!validateEmail(emailAdressen[i])){
				window.alert(emailAdressen[i] + " is geen geldige email adres. Geef aub alleen maar geldige email adressen op.");
				document.getElementById("Verzender").style.backgroundColor = 'red';
				document.getElementById("Verzender").style.color = 'white';
				document.getElementById("Verzender").style.padding = '10px';
				document.getElementById("Verzender").innerHTML = "Vul A.u.b geldige email adres(sen) in";
				break;
			}
			else{
				document.getElementById("Verzender").innerHTML ="";
				document.forms["mailAcc"].submit();
				break;
			}
		}
	}
}
</script>
<style type="text/css">

html {
	overflow: -moz-scrollbars-vertical;
}

a {
	color: <?php echo $rood; ?>;
}

a:hover {
	color: <?php echo $hover; ?>;
}

a.venstersluiten {
	font-size: 1.0em;
	color: #FFFFFF;
	text-decoration: none;
}

a.venstersluiten:hover {
	color: #878481;
}

TD {
	font-family: <?php echo $font; ?>;
	font-size: 0.8em;
}

UL {
	margin-top: 7px;
	margin-bottom: 0px;
}

LI {
	padding-bottom: 5px;
}

.wtform_table {
	width: 500px;
	background-color: #FFFFFF;
	border: 2px solid <?php echo $table; ?>;
	font-family: <?php echo $font; ?>;
}

.wtform_input {
	font-family: <?php echo $font; ?>;
	font-size: 1.0em;
	width: 300px;
	border: 1px solid <?php echo $table; ?>;
	padding: 1px;
}

.wtform_input_narrow {
	font-size: 1.0em;
	border: 1px solid <?php echo $table; ?>;
}

.wtform_input:focus {
	background-color:#ebebeb;
}

.wtform_error {
	color: red;
}

.wtform_cell_left {
	width: 150px;
	padding: 6px;
	vertical-align:top;
	padding-top: 8px;
}

.wtform_cell_right {
	width: 300px;
	padding: 6px;
}

.wtform_cell_colspan {
	padding: 6px;
}

.wtform_img_tbl {
	border: solid <?php echo $table; ?> 1px;
}

.wtform_small {
	font-size: 0.8em;
}

.wtform input:focus,.wtform textarea:focus {
	background-color:#ebebeb;
}

.tarieventabel_header {
	padding-top: 10px;
	padding-bottom: 10px;
	font-weight: bold;
	margin-left: 22px;
}

<?php if($vars["websitetype"]==7) { ?>

.aanbieding {
	background-color: #661700 !important;
	color: #ffffff !important;
}

.aanbieding a {
	background-color: #661700 !important;
	color: #ffffff !important;
}

.aanbieding_td_aldoorgerekend {
	background-color: #661700 !important;
	color: #ffffff;
}

.aanbieding a:hover {
	background-color: yellow !important;
	color: #000000 !important;
}

<?php } else { ?>


.aanbieding {
	background-color: #fb6703;
}

.aanbieding_td_tarieventabel {
	background-color: #fb6703;
	font-size: 1.0em;
	font-weight: bold;
}

.aanbieding_td_aldoorgerekend {
	background-color: #fb6703;
	letter-spacing: 0.15em;
}
<?php } ?>

<?php if($vars["websitetype"]==3) { ?>
.tarieventabel .kop {
	background-color: #dacce1 !important;
}
<?php } ?>

<?php if($vars["websitetype"]==7) { ?>
.tarieventabel .kop {
	background-color: #e0d1cc !important;
}
<?php } ?>


</style>
<?php

if($_GET["id"]=="tarieventabel") {



	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_chalet.css?cache=".@filemtime("css/".$id."_chalet.css")."\" />\n";	

	if($vars["seizoentype"]==2) {
		echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_zomerhuisje.js?cache=".@filemtime("scripts/functions_zomerhuisje.js")."\" ></script>\n";
	}
}

?>
</HEAD>
<BODY bgcolor="<?php echo $bodybgcolor; ?>"><TABLE border="0" style="background-color:<?php echo $table; ?>;border: solid 2px <?php echo $table; ?>" width="100%" height="100%" cellspacing="0" cellpadding="5">
<?php 

echo "<TR><TD height=\"30\"><TABLE width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><TR><TD><FONT color=\"".$thfontcolor."\"><B>".htmlentities($title[$_GET["id"]])."&nbsp;</B></FONT></TD><TD width=\"20\">";
if($noprint[$_GET["id"]]) echo "&nbsp;"; else echo "<A HREF=\"javascript:window.print();\"><IMG SRC=\"pic/printer.gif\" border=\"0\" alt=\"".html("paginaafdrukken","popup")."\" width=\"20\" height=\"18\"></A>";
echo "</TD><TD width=\"75\" align=\"right\"><FONT SIZE=\"1\"><A HREF=\"javascript:self.close();\" class=\"venstersluiten\">".ereg_replace(" ","&nbsp;",html("venstersluiten"))."</A></FONT></TD></TR></TABLE></TD></TR>";
echo "<TR><TD bgcolor=\"#FFFFFF\" valign=\"top\" align=\"left\" class=\"content\">";
#echo "&nbsp;<BR><B>".htmlentities($title[$_GET["id"]])."</B><P>";
if(isset($_POST['VerzenderNaam'])){
	$emails = explode(" ", $_POST['verzenderAdres']);
	for($i = 0; $i<sizeof($emails); $i++){
		$mail=new wt_mail;
		$mail->fromname=$vars["websitenaam"];
		$mail->from=$_POST['verzenderAdres'];
		$mail->toname=$_POST[''];
		$mail->to=$emails[$i];
		$mail->subject=$_POST['bericht'];
	
		$mail->plaintext=""; # deze leeg laten bij een opmaak-mailtje
	
		$mail->html_top="";
		//$mail->html="<b>Hier de inhoud van de mail met opmaak via html</b>"; # Hier kun je al je html in kwijt
		$mail->html_bottom="";
		$mail->send(); # mailtje daadwerkelijk verzenden
	}
	echo "<b>Bedankt, uw email(s) zijn verzonden</b>";
}
if($_GET["id"]) {
	if(in_array($_GET["id"],$meertalig_array)) {
		include "content/_meertalig/popup-".$_GET["id"]."_".$vars["taal"].".html";
	} else {
		if(file_exists("content/popup-".$_GET["id"].".html")) {
			include("content/popup-".$_GET["id"].".html");
		}
	}
}

 ?></TD></TR></TABLE>
 </BODY>
</HTML>