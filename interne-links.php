<?php

$mustlogin=true;
include("admin/vars.php");

?><!DOCTYPE html>
<html>

<head>
<title>Interne links - Chalet.nl</title>
<style>

html {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12pt;
}

body {
	max-width: 800px;
	margin: 0 auto;
	padding: 0 0 30px 0;
}

table, th, td {
	border: 1px solid #003366;
	border-collapse: collapse;
}

th, td {
	padding: 5px;
}

th {
	background-color: #d5e1f9;
}

a:hover {
	color: #888888;
}

</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>

$(document).ready(function() {

	$(this).find("a").attr("target", "_blank");

});


</script>


</head>

<body>

<table style="width:100%">
	<caption><h3>Handige URLs</h3></caption>
	<tr>
		<th>URL</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td><a href="https://www.chalet.nl/cms.php">www.chalet.nl/cms.php</a></td>
		<td>CMS</td>
	</tr>
	<tr>
		<td><a href="http://test.chalet.nl/cms.php">test.chalet.nl/cms.php</a></td>
		<td>Test-CMS</td>
	</tr>
	<tr>
		<td><a href="http://www2.chalet.nl">www2.chalet.nl</a></td>
		<td>Backup-server</td>
	</tr>
	<tr>
		<td><a href="http://docs.chalet.nl">docs.chalet.nl</a></td>
		<td>Interne Wiki</td>
	</tr>
	<tr>
		<td><a href="https://www.chalet.nl/webkleuren.php">www.chalet.nl/webkleuren.php</a></td>
		<td>Gebruikte kleurencombinaties</td>
	</tr>
	<tr>
		<td><a href="https://www.chalet.nl/pic/banners/htmlbanners/overzicht.php">www.chalet.nl/pic/banners/htmlbanners/overzicht.php</a></td>
		<td>Gebruikte HTML-banners</td>
	</tr>
	<tr>
		<td><a href="https://chaletnl.atlassian.net">chaletnl.atlassian.net</a></td>
		<td>JIRA Agile account</td>
	</tr>
	<tr>
		<td><a href="https://chaletnl.hipchat.com">chaletnl.hipchat.com</a></td>
		<td>HipChat</td>
	</tr>
	<tr>
		<td><a href="https://github.com/orgs/Chalet/dashboard">github.com/orgs/Chalet/dashboard</a></td>
		<td>Github dashboard</td>
	</tr>
	<tr>
		<td><a href="https://github.com/Chalet/Website-Chalet.nl/wiki">github.com/Chalet/Website-Chalet.nl/wiki</a></td>
		<td>Github Wiki</td>
	</tr>
	<tr>
		<td><a href="http://www2.chalet.nl/cms_error_log.php?show=0">http://www2.chalet.nl/cms_error_log.php?show=0</a></td>
		<td>Interne PHP-errorlog</td>
	</tr>
	<tr>
		<td><a href="http://www.google.com/analytics">www.google.com/analytics</a></td>
		<td>Google Analytics</td>
	</tr>
	<tr>
		<td><a href="sneltoetsen">www.chalet.nl/sneltoetsen</a></td>
		<td>Sneltoetsen</td>
	</tr>

</table>

<br>

<table style="width:100%">
	<caption><h3>Inloggegevens</h3></caption>
	<tr>
		<th>Inlog</th>
		<th>Password</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td>Chaletnl</td>
		<td>Zomerhuisje</td>
		<td>Wi-Fi netwerk</td>
	</tr>
	<tr>
		<td>Chaletnl2</td>
		<td>Zomerhuisje</td>
		<td>Wi-Fi netwerk</td>
	</tr>
	<tr>
		<td><a href="https://accounts.google.com/ServiceLogin">chaletmailbackup@gmail.com</a></td>
		<td></td>
		<td>Backup van alle automatische e-mail</td>
	</tr>

</table>

<br>

<table style="width:100%">
	<caption><h3>Websites</h3></caption>
	<tr>
		<th>Merknaam</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td><a href="https://www.chalet.nl">www.chalet.nl</a></td>
		<td>Winteraanbod</td>
	</tr>
	<tr>
		<td><a href="https://www.chalet.eu">www.chalet.eu</a></td>
		<td>Winteraanbod Engelstalig</td>
	</tr>
	<tr>
		<td><a href="https://www.chalettour.nl">www.chalettour.nl</a></td>
		<td>Wederverkoop winter</td>
	</tr>
	<tr>
		<td><a href="https://www.chalet.be">www.chalet.be</a></td>
		<td>Winteraanbod Belgische markt</td>
	</tr>
	<tr>
		<td><a href="https://www.chaletonline.de">www.chaletonline.de</a></td>
		<td>Winteraanbod Duitse markt</td>
	</tr>
	<tr>
		<td><a href="https://www.chaletsinvallandry.nl">www.chaletsinvallandry.nl</a></td>
		<td>Aanbod chalets in Vallandry</td>
	</tr>
	<tr>
		<td><a href="https://www.chaletsinvallandry.com">www.chaletsinvallandry.com</a></td>
		<td>Aanbod chalets in Vallandry Engelstalig</td>
	</tr>
	<tr>
		<td><a href="https://www.venturasol.nl">www.venturasol.nl</a></td>
		<td>Winteraanbod via Venturasol</td>
	</tr>
	<tr>
		<td><a href="https://www.venturasolvacances.nl">www.venturasolvacances.nl</a></td>
		<td>Wederverkoop via Venturasol</td>
	</tr>
	<tr>
		<td><a href="https://www.zomerhuisje.nl">www.zomerhuisje.nl</a></td>
		<td>Aanbod zomervakantie in de bergen</td>
	</tr>
	<tr>
		<td><a href="https://www.italissima.nl">www.italissima.nl</a></td>
		<td>Aanbod Italië</td>
	</tr>
	<tr>
		<td><a href="https://www.italissima.be">www.italissima.be</a></td>
		<td>Aanbod Italië Belgische markt</td>
	</tr>
	<tr>
		<td><a href="https://www.italyhomes.eu">www.italyhomes.eu</a></td>
		<td>Aanbod Italië Engelstalig</td>
	</tr>
</table>

<br />

<p><i>Versie 01/02/2016</i></p>

</body>

</html>