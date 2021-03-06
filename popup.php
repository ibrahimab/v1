<?php

if(ereg("^cms_",$_GET["id"])) {
	$mustlogin=true;

	if ($_GET["id"]=="cms_mail_klanten_vorig_seizoen_bewerken") {
		$boeking_bepaalt_taal = true;
	}

	include("admin/vars.php");

} else {

	include("admin/vars.php");

	# session starten (nodig voor CAPTCHA van accommodatiemail)
	wt_session_start();
}


if($_GET["id"]=="cms_mailtekst_bewerken" and isset($_POST["mailtekst_opties"]) and $_GET["bid"]) {
	$db->query("UPDATE boeking SET mailtekst_opties='".addslashes($_POST["mailtekst_opties"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk".$_GET["bid"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
	exit;
}

if($_GET["id"]=="cms_mailtekst_aanmaningen_bewerken" and isset($_POST["mailtekst_opties"]) and $_GET["bid"]) {
	$db->query("UPDATE boeking SET aanmaning_tekst='".addslashes($_POST["mailtekst_opties"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
	boeking_log($_GET["bid"], "aanmaningstekst handmatig aangepast");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk".$_GET["bid"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
	exit;
}

if($_GET["id"]=="cms_mail_klanten_vorig_seizoen_bewerken" and isset($_POST["mailtekst"]) and $_GET["bid"]) {
	$db->query("UPDATE boeking SET mailtekst_klanten_vorig_seizoen='".addslashes($_POST["mailtekst"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk".$_GET["bid"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
	exit;
}

if($_GET["id"]=="cms_mail_klanten_vorig_seizoen_bewerken" and isset($_POST["mailtekst"]) and $_GET["newpricesmail_id"]) {
	$db->query("UPDATE newpricesmail SET mailtekst='".addslashes($_POST["mailtekst"])."' WHERE newpricesmail_id='".intval($_GET["newpricesmail_id"])."';");
	echo "<html><body onload=\"window.opener.document.getElementById('bewerk_np_".$_GET["newpricesmail_id"]."').style.display='inline';self.close();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</body></html>";
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
		if($db->f("omschrijving1")) $omschrijving=nl2br(wt_htmlent($db->f("omschrijving1"),true,true))."<br>";
		if($db->f("omschrijving2")) $omschrijving.=nl2br(wt_htmlent($db->f("omschrijving2"),true,true));
	}
}

if($_GET["bkid"] or $_GET["bksid"]) {
	if($_GET["bksid"]) {
		// get info from table bk_soort
		$db->query("SELECT b.naam".$vars["ttv"]." AS naam, b.toelichting".$vars["ttv"]." AS toelichting FROM bk_soort b WHERE b.bk_soort_id='".intval($_GET["bksid"])."';");
	} else {
		// get info from table bijkomendekosten
		$db->query("SELECT b.naam".$vars["ttv"]." AS naam, b.omschrijving".$vars["ttv"]." AS toelichting, b.min_personen, b.perboekingpersoon FROM bijkomendekosten b WHERE b.bijkomendekosten_id='".addslashes($_GET["bkid"])."';");
	}
	if($db->next_record()) {

		$naam = $db->f("naam");

		if($db->f("toelichting")) {
			$toelichting=nl2br(wt_htmlent($db->f("toelichting"),true,true))."<br>";
		}
		if($db->f("min_personen")) {
			$min_personen = $db->f("min_personen");
		}
		$perboekingpersoon = $db->f("perboekingpersoon");
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
#$title["stuurdoor"]=txt("popuptitle_stuurdoor");
$title["accommodatiemail"]=txt("popuptitle_stuurdoor");


$noprint["tarieventabel"]=true;
$noprint["stuurdoor"]=true;
$noprint["accommodatiemail"]=true;


if(ereg("(.*)\-query-(.*)",$_GET["id"],$regs)) {
	$_GET["id"]=$regs[1];
	$querystring=$regs[2];
}

echo "<html><head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">\n";
echo "<meta name=\"robots\" content=\"noindex,follow\">";
# JQuery
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jquery_url"])."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."\" ></script>\n";
echo "<title>".wt_he($title[$_GET["id"]])."</title>";
?>
<style type="text/css">

html {
	overflow: -moz-scrollbars-vertical;
	font-family: Verdana, Arial, Helvetica;
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

td {
	font-family: <?php echo $font; ?>;
	font-size: 0.8em;
}

ul {
	margin-top: 7px;
	margin-bottom: 0px;
}

li {
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


.toeslagtabel {
	border-spacing: 0px;
	border-collapse: separate;
	border: 1px solid #cccccc;
	margin-top: 20px;
	margin-bottom: 20px;
}

.toeslagtabel th {
	font-size: 0.8em;
}

.toeslagtabel td, .toeslagtabel th {
	border: 1px solid #cccccc;
	padding: 5px;
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

	if(file_exists("css/".$id)) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_chalet.css?cache=".@filemtime("css/".$id."_chalet.css")."\" />\n";
	}

	if($vars["seizoentype"]==2) {
		echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_zomerhuisje.js?cache=".@filemtime("scripts/functions_zomerhuisje.js")."\" ></script>\n";
	}
}

echo "</HEAD>";

if($_GET["fancybox"]) {
	echo "<body>";
	echo "<div style=\"padding:10px;\">";
} else {
	echo "<BODY bgcolor=\"".$bodybgcolor."\">";
	echo "<TABLE border=\"0\" style=\"background-color:".$table.";border: solid 2px ".$table."\" width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"5\">";
	echo "<TR><TD height=\"30\"><TABLE width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><TR><TD style=\"font-size:10pt;\"><FONT color=\"".$thfontcolor."\"><B>".wt_he($title[$_GET["id"]])."&nbsp;</B></FONT></TD><TD width=\"20\">";
	if($noprint[$_GET["id"]]) {
		echo "&nbsp;";
	} else {
		echo "<a href=\"javascript:window.print();\"><IMG SRC=\"pic/printer.gif\" border=\"0\" alt=\"".html("paginaafdrukken","popup")."\" width=\"20\" height=\"18\"></a>";
	}
	echo "</TD><TD width=\"75\" align=\"right\"><FONT SIZE=\"1\">&nbsp;<A HREF=\"javascript:self.close();\" class=\"venstersluiten\">".ereg_replace(" ","&nbsp;",html("venstersluiten"))."</A></FONT></TD></TR></TABLE></TD></TR>";
	echo "<TR><TD bgcolor=\"#FFFFFF\" valign=\"top\" align=\"left\" class=\"content\">";
	#echo "&nbsp;<BR><B>".wt_he($title[$_GET["id"]])."</B><P>";
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

if($_GET["fancybox"]) {
	echo "</div>";
} else {
	echo "</TD></TR></TABLE>";
}

 ?></BODY>
</HTML>