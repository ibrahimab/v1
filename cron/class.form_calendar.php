<?php

if($_GET["lang"]=="en") {
	setlocale(LC_ALL,'en_EN');
	$text["title"]="Calendar";
	$text["klik"]="Click on the day of your choice";
} else {
#	setlocale(LC_ALL,"nl_NL.ISO_8859-1");
	setlocale(LC_ALL,"nl_NL.ISO8859-1");
	$text["title"]="Kalender";
	$text["klik"]="Klik op de gewenste dag";
}

$month=date("n");
$year=date("Y");

if($_GET["month"]) {
	$month=$_GET["month"];
	if($_GET["year"]) $year=$_GET["year"];
}

$activemonth=mktime(0,0,0,$month,1,$year);
function day($day,$plusminmonth=0) {
	global $cols,$month,$year;
	$time=mktime(0,0,0,$month,$day,$year);
	if(!$cols) {
		$weeknummer=date("W",mktime(0,0,0,date("n",mktime(0,0,0,$month+$plusminmonth,1,$year)),$day+6,date("Y",mktime(0,0,0,$month+$plusminmonth,1,$year))));
		if($weeknummer==0) $weeknummer=53;
		echo "<TR><TD class=\"weekcell\" align=\"center\"><FONT COLOR=\"#FFFFFF\"><I>".$weeknummer."</I></FONT></TD>";
	}
	$cols++;
	echo "<TD class=\"bcolor\" width=\"30\" align=\"center\" style=\"cursor:pointer\" onmouseover=\"this.style.backgroundColor='#FFC77E';\" onmouseout=\"this.style.backgroundColor='#".($cols==6 || $cols==7 ? "EBEBEB" : "FFFFFF")."';\" onclick=\"getdate(".$day.",".date("n",mktime(0,0,0,$month+$plusminmonth,1,$year)).",".date("Y",mktime(0,0,0,$month+$plusminmonth,1,$year)).");\"";
	if($cols==6 or $cols==7) echo " bgcolor=\"#ebebeb\"";
	echo ">";
	if($plusminmonth<>0) echo "<FONT COLOR=\"#878481\">";
	if($day==date("j") and $month==date("n") and $year==date("Y")) echo "<B>";
	echo $day;
	if($plusminmonth<>0) echo "</FONT>";
	echo "</A></TD>";
	if($cols==7) {
		$cols=0;
		echo "</TR>\n";
	}

}

if($_GET["justonevar"]) {
	$inputprefix="";
} else {
	$inputprefix="input";
}

?><HTML><HEAD>
<TITLE><?php echo $text["title"]; ?></TITLE>
<SCRIPT Language="JavaScript">
	function getdate(day,month,year) {<?php
		if($_GET["justonevar"]) {
			echo "window.opener.document.".htmlentities($_GET["nm"]).".elements['".htmlentities($_GET["input"])."_d'].value = day;\n";
			echo "window.opener.document.".htmlentities($_GET["nm"]).".elements['".htmlentities($_GET["input"])."_m'].value = month;\n";
			echo "window.opener.document.".htmlentities($_GET["nm"]).".elements['".htmlentities($_GET["input"])."_y'].value = year;\n";
		} else {
			echo "window.opener.document.".htmlentities($_GET["nm"]).".elements['input[".htmlentities($_GET["input"])."][day]'].value = day;\n";
			echo "window.opener.document.".htmlentities($_GET["nm"]).".elements['input[".htmlentities($_GET["input"])."][month]'].value = month;\n";
			echo "window.opener.document.".htmlentities($_GET["nm"]).".elements['input[".htmlentities($_GET["input"])."][year]'].value = year;\n";
		}

				#
				# Kijken of "Goedkeuring benodigd: vraag om goedkeuring/ondertekening door de klant" uitgezet moet worden
				#
				if($_GET["input"]=="factuur_ondertekendatum") {
			?>
				// window.opener.document.getElementById("yesnoondertekenen").checked=false;
				window.opener.goedkeuringen_benodigd_uitzetten();

		<?php }

	?>
		window.close();
	}
</SCRIPT>
<STYLE type="text/css">
<!--
	A:hover {
		color : #CC3333;
	}

	TD {
		font-family: Verdana, Arial, Helvetica;
		font-size: 12px;
	}

	.bcolor {
		border-style : solid;
		border-width : 1px;
		border-color : #878481;
	}

	.weekcell {
		border-style : solid;
		border-width : 1px;
		border-color : #878481;
		background-color: #878481;
		padding: 0px;
		width: 30px;
	}

--></STYLE>

</HEAD><BODY text="#000000" link="#000000" vlink="#000000" alink="#000000">
<?php
echo "<TABLE align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">";
echo "<TR><TD align=\"left\"><B><A HREF=\"class.form_calendar.php?nm=".htmlentities($_GET["nm"])."&lang=".htmlentities($_GET["lang"])."&justonevar=".htmlentities($_GET["justonevar"])."&input=".htmlentities($_GET["input"])."&month=".date("n",mktime(0,0,0,$month-1,1,$year))."&year=".date("Y",mktime(0,0,0,$month-1,1,$year))."\">&lt;&lt;</A></B></TD><TD align=\"center\"><B>".strftime("%B",$activemonth)." ".$year."</B></TD><TD align=\"right\"><B><A HREF=\"class.form_calendar.php?nm=".htmlentities($_GET["nm"])."&lang=".htmlentities($_GET["lang"])."&justonevar=".htmlentities($_GET["justonevar"])."&input=".htmlentities($_GET["input"])."&month=".date("n",mktime(0,0,0,$month+1,1,$year))."&year=".date("Y",mktime(0,0,0,$month+1,1,$year))."\">&gt;&gt;</A></B></TD></TR>";
echo "<TR><TD colspan=\"3\"><TABLE border=1 cellspacing=0 cellpadding=5 class=\"bcolor\">";
echo "<TR><TD class=\"bcolor\" bgcolor=\"#878481\">&nbsp;</TD>";
for($i=0;$i<7;$i++) {
	$weekday[]=strftime("%a",mktime(0,0,0,9,13+$i,2004));
}
#$weekday=array("ma","di","wo","do","vr","za","zo");
while(list($key,$value)=each($weekday)) {
	echo "<TD class=\"bcolor\" align=\"center\" bgcolor=\"#878481\"><FONT COLOR=\"#FFFFFF\"><B>".$value."</B></FONT></TD>";
}

$firstday=strftime("%u",$activemonth);
$lastday=strftime("%u",mktime(0,0,0,$month,date("t",$activemonth),$year));

if(($firstday-1)) {
	for($i=date("j",$activemonth-(($firstday-1)*86400));$i<=date("t",$activemonth-(($firstday-1)*86400));$i++) {
		day($i,-1);
	}
}

for($i=1;$i<=date("t",$activemonth);$i++) {
	day($i);
}

if($lastday<7) {
	for($i=1;$i<(8-$lastday);$i++) {
		day($i,1);
	}
}

echo "</TABLE></TD></TR><TR><TD colspan=\"3\">".$text["klik"]."</TD></TR></TABLE>";

?></BODY></HTML>