<?php

$mustlogin=true;
include("admin/vars.php");

?><!DOCTYPE html>
<html>

<head>
<title>Sneltoetsen &amp; extensies</title>
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
	<caption><h3>Algemene sneltoetsen</h3></caption>
	<tr>
		<th>Combinatie</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td>Ctrl + A</td>
		<td>Alles selecteren.</td>
	</tr>
	<tr>
		<td>Ctrl + C</td>
		<td>Selectie kopiëren.</td>
	</tr>
	<tr>
		<td>Ctrl + X</td>
		<td>Selectie knippen.</td>
	</tr>
	<tr>
		<td>Ctrl + V</td>
		<td>Selectie plakken.</td>
	</tr>
	<tr>
		<td>Ctrl + Z</td>
		<td>Laatste actie ongedaan maken.</td>
	</tr>
	<tr>
		<td>Ctrl + Y</td>
		<td>Laatste ongedane actie opnieuw uitvoeren.</td>
	</tr>
	<tr>
		<td>Ctrl + P</td>
		<td>Printopdracht geven.</td>
	</tr>
	<tr>
		<td>Ctrl + S</td>
		<td>Document opslaan.</td>
	</tr>
	<tr>
		<td>Win + E</td>
		<td>Computer openen.</td>
	</tr>
	<tr>
		<td>Win + D</td>
		<td>Naar bureaublad.</td>
	</tr>
	<tr>
		<td>Alt + Tab</td>
		<td>Wisselen tussen geopende programma's.</td>
	</tr>

</table>

<br>

<table style="width:100%">
	<caption><h3>Sneltoetsen voor Chrome</h3></caption>
	<tr>
		<th>Combinatie</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td>Ctrl + N</td>
		<td>Nieuw venster openen.</td>
	</tr>
	<tr>
		<td>Ctrl + T</td>
		<td>Nieuw tabblad openen.</td>
	</tr>
	<tr>
		<td>Ctrl + Shift + T</td>
		<td>Laatst gesloten tabblad opnieuw openen.</td>
	<tr>
		<td>Ctrl + W / Middelste muisknop op tabblad</td>
		<td>Tabblad sluiten.</td>
	</tr>
	<tr>
		<td>Ctrl + linker muisknop / Middelste muisknop op een link</td>
		<td>Link openen in nieuw tabblad.</td>
	</tr>
	<tr>
		<td>Ctrl + Tab</td>
		<td>Naar het volgende tabblad navigeren.</td>
	</tr>
	<tr>
		<td>Ctrl + Shift + Tab</td>
		<td>Naar het vorige tabblad navigeren.</td>
	</tr>
	<tr>
		<td>Ctrl + [nummer]</td>
		<td>Naar specifiek tabblad navigeren.</td>
	</tr>
	<tr>
		<td>Ctrl + L</td>
		<td>Cursor naar adresbalk (url invoeren).</td>
	</tr>
	<tr>
		<td>Ctrl + D</td>
		<td>Favoriet toevoegen.</td>
	</tr>
	<tr>
		<td>Ctrl + Shift + B</td>
		<td>Favorietenbalk uitklappen.</td>
	</tr>

</table>

<br>

<table style="width:100%">
	<caption><h3>Sneltoetsen voor Outlook</h3></caption>
	<tr>
		<th>Combinatie</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td>Ctrl + N</td>
		<td>Nieuwe e-mail.</td>
	</tr>
	<tr>
		<td>Ctrl + O</td>
		<td>E-mail openen.</td>
	</tr>
	<tr>
		<td>Ctrl + R</td>
		<td>E-mail beantwoorden.</td>
	</tr>
	<tr>
		<td>Ctrl + Shift + R</td>
		<td>Allen beantwoorden.</td>
	</tr>
	<tr>
		<td>Ctrl + F</td>
		<td>E-mail doorsturen.</td>
	</tr>
	<tr>
		<td>Shift + F3</td>
		<td>Hoofdletters (van selectie) wijzigen.</td>
	</tr>
	<tr>
		<td>Ctrl + Spatie</td>
		<td>Opmaak (van selectie) wissen.</td>
	</tr>
	<tr>
		<td>Ctrl + Enter</td>
		<td>Opgestelde e-mail verzenden.</td>
	</tr>
	<tr>
		<td>Ctrl + D</td>
		<td>E-mail verwijderen.</td>
	</tr>


</table>

<br>

<table style="width:100%">
	<caption><h3>Extensies voor Chrome</h3></caption>
	<tr>
		<th>Extensie</th>
		<th>Omschrijving</th>
	</tr>
	<tr>
		<td><a href="https://chrome.google.com/webstore/detail/onetab/chphlpgkkbolifaimnlloiipkdnihall">OneTab</a></td>
		<td>Alle openstaande tabbladen op één pagina weergeven.</td>
	</tr>
	<tr>
		<td><a href="https://chrome.google.com/webstore/detail/linkclump/lfpjkncokllnfokkgpkobnkbkmelfefj?hl=en">Linkclump</a></td>
		<td>Stelt je in staat om snel meerdere linkjes te openen in nieuwe tabbladen.</td>
	</tr>
	<tr>
		<td><a href="https://chrome.google.com/webstore/detail/full-page-screen-capture/fdpohaocaechififmbbbbbknoalclacl">Full Page Screen Capture</a></td>
		<td>Snel een schermafbeelding maken van het openstaande venster.</td>
	</tr>

</table>
</body>
</html>