<?php

if(ereg("^cms_",$_GET["id"])) {
	$mustlogin=true;
} else {
	# session starten (nodig voor CAPTCHA van accommodatiemail)
	session_start();
}

include("admin/vars.php");

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

if($_GET["bkid"]) {
	$db->query("SELECT b.naam, b.omschrijving".$vars["ttv"]." AS omschrijving FROM bijkomendekosten b WHERE b.bijkomendekosten_id='".addslashes($_GET["bkid"])."';");
	if($db->next_record()) {
		$omschrijving=nl2br(wt_htmlent($db->f("omschrijving"),true,true))."<br>";
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

?>
<style type="text/css">
#fancybox-content a {
	color: <?php echo $rood; ?>;
}

#fancybox-content a:hover {
	color: <?php echo $hover; ?>;
}

#fancybox-content a.venstersluiten {
	font-size: 1.0em;
	color: #FFFFFF;
	text-decoration: none;
}

#fancybox-content a.venstersluiten:hover {
	color: #878481;
}

#fancybox-content TD {
	font-family: <?php echo $font; ?>;
	font-size: 0.9em;
}

#fancybox-content UL {
	margin-top: 7px;
	margin-bottom: 0px;
}

#fancybox-content LI {
	padding-bottom: 5px;
}

#fancybox-content .wtform_table {
	width: 500px;
	background-color: #FFFFFF;
	border: 2px solid <?php echo $table; ?>;
	font-family: <?php echo $font; ?>;
}

#fancybox-content .wtform_input {
	font-family: <?php echo $font; ?>;
	font-size: 1.0em;
	width: 300px;
	border: 1px solid <?php echo $table; ?>;
	padding: 1px;
}

#fancybox-content .wtform_input_narrow {
	font-size: 1.0em;
	border: 1px solid <?php echo $table; ?>;
}

#fancybox-content .wtform_input:focus {
	background-color:#ebebeb;
}

#fancybox-content .wtform_error {
	color: red;
}

#fancybox-content .wtform_cell_left {
	width: 150px;
	padding: 6px;
	vertical-align:top;
	padding-top: 8px;
}

#fancybox-content .wtform_cell_right {
	width: 300px;
	padding: 6px;
}

#fancybox-content .wtform_cell_colspan {
	padding: 6px;
}

#fancybox-content .wtform_img_tbl {
	border: solid <?php echo $table; ?> 1px;
}

#fancybox-content .wtform_small {
	font-size: 0.8em;
}

#fancybox-content .wtform input:focus,.wtform textarea:focus {
	background-color:#ebebeb;
}

#fancybox-content .tarieventabel_header {
	padding-top: 10px;
	padding-bottom: 10px;
	font-weight: bold;
	margin-left: 22px;
}

<?php if($vars["websitetype"]==7) { ?>

#fancybox-content .aanbieding {
	background-color: #661700 !important;
	color: #ffffff !important;
}

#fancybox-content .aanbieding a {
	background-color: #661700 !important;
	color: #ffffff !important;
}

#fancybox-content .aanbieding_td_aldoorgerekend {
	background-color: #661700 !important;
	color: #ffffff;
}

#fancybox-content .aanbieding a:hover {
	background-color: yellow !important;
	color: #000000 !important;
}

<?php } else { ?>


#fancybox-content .aanbieding {
	background-color: #fb6703;
}

#fancybox-content .aanbieding_td_tarieventabel {
	background-color: #fb6703;
	font-size: 1.0em;
	font-weight: bold;
}

#fancybox-content .aanbieding_td_aldoorgerekend {
	background-color: #fb6703;
	letter-spacing: 0.15em;
}
<?php } ?>

<?php if($vars["websitetype"]==3) { ?>
#fancybox-content .tarieventabel .kop {
	background-color: #dacce1 !important;
}
<?php } ?>

<?php if($vars["websitetype"]==7) { ?>
#fancybox-content .tarieventabel .kop {
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

?>
<div style="background-color: <?php echo $bodybgcolor; ?>">
<TABLE border="0" style="background-color:<?php echo $table; ?>;border: solid 2px <?php echo $table; ?>" width="100%" height="100%" cellspacing="0" cellpadding="5">
<?php
echo "<TR><TD height=\"30\">
<TABLE width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<TR>
<TD style=\"font-size:10pt;\"><FONT color=\"".$thfontcolor."\"><B>".htmlentities($title[$_GET["id"]])."&nbsp;</B></FONT></TD>
<TD width=\"20\"></TD>
</TR>
</TABLE></TD></TR>";
echo "<TR><TD bgcolor=\"#FFFFFF\" valign=\"top\" align=\"left\" class=\"content\">";

if($_GET["id"]) {
	if(in_array($_GET["id"],$meertalig_array)) {
		if(file_exists("content_mobile/_meertalig/popup-".$_GET["id"]."_".$vars["taal"].".html")) {
			include "content/_meertalig/popup-".$_GET["id"]."_".$vars["taal"].".html";
		} else {
			include "content/_meertalig/popup-".$_GET["id"]."_".$vars["taal"].".html";
		}
	} else {
		if(file_exists("content_mobile/popup-".$_GET["id"].".html")) {
			include("content_mobile/popup-".$_GET["id"].".html");
		} elseif(file_exists("content/popup-".$_GET["id"].".html")) {
			include("content/popup-".$_GET["id"].".html");
		}
	}
}

 ?></TD></TR></TABLE>
</div>