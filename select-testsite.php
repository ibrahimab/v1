<!DOCTYPE html>
<html>
<head>
	<title>Testsite selecteren</title>
</head>
<style>

html {
	overflow-y: none;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 0.8em;
}

h1 {
	font-size: 1.1em;
}

</style>

<body>
<h1>Testsite selecteren</h1>

<?php

include("admin/vars.php");


echo "<ul>";
foreach ($vars["websites_actief"] as $key => $value) {
	echo "<li><a href=\"".$vars["path"]."cms.php?testsite=".$key."\">".wt_he($value)."</a></li>";
}
echo "<ul>";


?></body>
</html>
