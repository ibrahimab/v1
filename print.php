<?php

if(!$_GET["id"]) {
	header("Location: /",true,301);
	exit;
}

#
# Alleen nog actief voor wsa.nl
#
if($vars["websitetype"]<>2) {
	header("Location: /",true,301);
	exit;
}

include_once "admin/vars.php";
$print=true;

?><HTML><HEAD><TITLE><?php echo wt_he($vars["websitenaam"]); ?></TITLE>
<meta name="robots" content="noindex,nofollow" />
<script type="text/javascript" language="JavaScript1.2" src="functions.js"></script>
<script type="text/javascript" language="JavaScript">
<!--hide this script from non-javascript-enabled browsers

function popwindow(xsize,ysize,url,align) {
	var wWidth, wHeight, wLeft, wTop;
	wWidth = xsize;
	if(ysize>0) {
		wHeight = ysize;
	} else {
		wHeight = wWidth*.75;
	}
	if(align=='center') {
		wLeft = (screen.width-wWidth)/2;
		wTop = (screen.height-wHeight)/2;
		var Scherm = window.open(<?php echo "'".$vars["basehref"]."'+"; ?>url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(<?php echo "'".$vars["basehref"]."'+"; ?>url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

// stop hiding -->
</script>
<link rel="stylesheet" type="text/css" href="css/opmaak_websites_en_cms.css.phpcache?type=<?php echo $vars["websitetype"]; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/opmaak_alle_sites.css.phpcache?type=<?php echo $vars["websitetype"]; ?>" />
<style type="text/css">

A:hover {
	color: #0D3E88;
}

A.venstersluiten {
	font-size: 1.0em;
	color: #FFFFFF;
	text-decoration: none;
}

A.venstersluiten:hover {
	color: #878481;
}

.tarieftabel {
	font-family: Tahoma, Helvetica, sans-serif;
	font-size: 0.97em;
}

TD {
	font-family: Tahoma, Helvetica, sans-serif;
	font-size: 0.8em;
}

UL {
	margin-top: 7px;
	margin-bottom: 0px;
}

LI {
	padding-bottom: 5px;
}

.tonen_verbergen {
	display: table-row;
}

.noprint {
	display: none;
}

.onlyprint {
	display: inline;
}

@media print {
	.noprint {
		display: none;
	}

	.onlyprint {
		display: inline;
	}

	A {
		text-decoration: none;
	}
}

</style>
</HEAD>
<BODY aonload="javascript:window.print();" bgcolor="#FFFFFF" text="#000000" link="#D3033A" vlink="#D3033A" alink="#D3033A">
<TABLE border="0" cellspacing="0" cellpadding="5"><TR><TD><?php

@include "content/".$_GET["id"].".html";

?></TD></TR><TR><TD align="center"><B><?php echo wt_he($vars["websitenaam"]); ?> - Wipmolenlaan 3 - 3447 GJ Woerden - <?php if($vars["websiteland"]<>"nl") echo html("nederland","contact")." - "; echo txt("telefoonnummer"); ?></B></TD></TR></TABLE></BODY>
</HTML>