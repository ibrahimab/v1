<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Wintersportaccommodaties.nl <?php 
	if($id=="index") {
		echo " - Wintersport";
	} elseif($title[$id]) {
		echo " - ".htmlentities($title[$id]);
	}
?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/opmaak_websites_en_cms.css.phpcache?cache=<?php echo @filemtime("css/opmaak_websites_en_cms.css.phpcache"); ?>&type=<?php echo $vars["websitetype"]; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/opmaak_alle_sites.css.phpcache?cache=<?php echo @filemtime("css/opmaak_alle_sites.css.phpcache"); ?>&type=<?php echo $vars["websitetype"]; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/opmaak_wsa.css?cache=3" />
	<link rel="shortcut icon" href="<?php echo $path; ?>favicon_wsa.ico" />
<?php
	
if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".htmlentities($vars["canonical"])."\" />";
}

# JQuery
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jquery_url"])."\" ></script>\n";

#if($id=="index") {
#	echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_wsa.js?cache=".@filemtime("scripts/functions_wsa.js")."\"></script>\n";
#}

# Chosen
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery.chosen.min.js\"></script>\n";

echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."\" ></script>\n";

?>	
	<meta name="robots" content="<?php if(!$vars["canonical"] and ($_GET["back"] or $_GET["backtypeid"] or $_GET["filled"] or $_GET["PHPSESSID"] or $id=="boeken" or $robot_noindex)) echo "no"; ?>index,follow" />
	<meta name="description" content="<?php echo wt_he($vars["websitenaam"])." - ".wt_he(txt("subtitel")); ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php

# Google Analytics
echo googleanalytics();

?>	
</head>
<body>
<div id="wrapper">
	<?php if($helemaalboven) echo $helemaalboven; ?>
	<div id="header">
	<table cellspacing="0" cellpadding="0" border="0" style="width:100%">
		<tr>
		<td style="width:100px;"><img src="<?php echo $path; ?>pic/chalet.jpg" alt="" height="100" width="100" border=0></td>
		<td style="width:100px;"><img src="<?php echo $path; ?>pic/leeg.gif" alt="" height="1" width="10" border=0></td>
		<td style="width:100px;"><img src="<?php echo $path; ?>pic/ski.jpg" alt="" height="100" width="100" border=0></td>
		<td style="width:100px;"><img src="<?php echo $path; ?>pic/leeg.gif" alt="" height="1" width="10" border=0></td>
		<td style="width:100px;"><img src="<?php echo $path; ?>pic/haardvuur.jpg" alt="" height="100" width="100" border=0></td>
		<td style="width:100px;"><img src="<?php echo $path; ?>pic/leeg.gif" alt="" height="1" width="10" border=0></td>
		<td align="right"><img src="<?php echo $path; ?>pic/logo_wintersportaccommodaties.gif" alt="Logo Wintersportaccommodaties.nl" border="0" height="100" width="420"></td>
		</tr>
	</table>
	</div>
	<div id="menu"><table width="770" cellspacing="0" cellpadding="0" style="background-color:#FFFFFF;font-weight: bolder;"><tr><td><img src="<?php echo $path; ?>pic/leeg.gif" width="35" height="1" alt=""></td><td align=center>
		<?php
		while(list($key,$value)=each($menu)) {
			echo "<img src=\"".$path."pic/blokje.gif\" alt=\"\" height=\"12\" width=\"12\">";
			if($key<>$id) {
				echo "<a href=\"".$path."";
				if($key=="index") {
				
				} else {
					echo strtolower(htmlentities($key)).".php";
				}
				echo "\" class=\"menu\">";
				echo strtoupper(nl2br(htmlentities($value)));
				echo "</a>&nbsp;&nbsp;";
			} else {
				echo "<font class=\"actiefmenu\">".strtoupper(nl2br(htmlentities($value)))."</font>&nbsp;&nbsp;";
			}
		}
		?></td><td><a href="<?php echo $path; ?>algemenevoorwaarden.php#sgr"><img src="<?php echo $path; ?>pic/sgr_wsa.gif" width="25" height="23" border="0" alt="Stichting Garantiefonds Reisgelden"></a></td><td><img src="<?php echo $path; ?>pic/leeg.gif" width="5" height="1" alt=""></td></tr></table>
	</div>
	<div id="content">
		<p class="onlyprint">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</p>
		<?php

		if($last_acc_wsa) {
			echo "<div style=\"margin-top:5px;margin-bottom:5px;text-align:right;\">".$last_acc_wsa."</div>";
		} else {
			echo "<div style=\"height:20px;\"></div>";
		}

		if(($id<>"index" and $id<>"toonaccommodatie") or $rechtsboven) {
			echo "<table border=\"0\" width=\"740\" cellspacing=\"0\" cellpadding=\"0\">";
			echo "<tr>";
			echo "<td>";
			if($id=="index") {
				echo "&nbsp;";
			} elseif(!$laat_titel_weg) {
				echo "<h1>".htmlentities(ucfirst($title[$id]))."</h1>";
			}
			echo "</td>";
			if($rechtsboven) {
				echo "<td align=\"right\" valign=\"top\">".$rechtsboven."<br>&nbsp;</td>";
			}
			echo "</tr></table>";
		} elseif($id<>"index" and $id<>"toonaccommodatie" and !$laat_titel_weg) {
			echo "<h1>".htmlentities(ucfirst($title[$id]))."</h1>";
		}
		if(file_exists("content/".$id."_wsa.html")) {
			include "content/".$id."_wsa.html";
		} else {
			if($language_content) {
				include "content/_meertalig/".$id."_".$vars["taal"].".html";
			} else {
				include "content/".$id.".html";
			}
#			include "content/".$id.".html";
		}
		if($id<>"index") echo "<br>&nbsp;<br>";
		?>
	</div><?php
	if($id=="index") {
	?>
		<div id="submenu">
		<?php
		while(list($key,$value)=each($submenu)) {
			echo "<img src=\"".$path."pic/subblokje.gif\" alt=\"\" height=\"6\" width=\"12\">";
			if($key<>$id) {
				echo "<a href=\"".$path."";
				echo strtolower(nl2br(htmlentities($key))).".php";
				echo "\" class=\"submenu\">";
				echo nl2br(htmlentities($value));
				echo "</a>&nbsp;&nbsp;";
			}
			else {
				echo "<font class=\"actiefsubmenu\">".nl2br(htmlentities($value))."</font>&nbsp;&nbsp;";
			}
		}
		?>
		</div><?php } ?>
	<div id="footer">Wintersportaccommodaties.nl - Lindenhof 5 - 3442 GT Woerden - <b><nobr>Telefoon 0348 - 43 46 49</nobr></b></div>
	 <div id="footer" class=\"noprint\" style="text-align:center;"> <a href="<?php echo $vars["path"] ?>disclaimer.php">Disclaimer</a> - <a href="<?php echo $vars["path"]?>privacy-statement.php">Privacy statement</a></div>
	</div>
	<div id="printlogo">
		<img src="<?php echo $path; ?>pic/logo_wintersportaccommodaties.gif" alt="Wintersportaccommodaties.nl" border="0" height="100" width="420">
</div>
</body>
</html>
