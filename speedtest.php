<?php

echo "<html><title>Speedtest</title>";
echo "<meta name=\"robots\" content=\"noindex,nofollow\" />";
echo "<SCRIPT type=\"text/javascript\"> <!--\n\nvar thisdate = new Date();\nvar begins = thisdate.getSeconds();\n\n-->\n</script>";
echo "<body onload=\"var thisdate = new Date();\nvar einds = thisdate.getSeconds();if(begins>einds) {\neinds=einds+60;\n}var laadtijd=einds-begins;alert('laadtijd: '+laadtijd+' sec.');\">";
echo "Pagina om de site-snelheid te controleren.<p>";
for($i=1;$i<=15;$i++) {
	echo "<img src=\"pic/speedtest.jpg?cache=".time()."&i=".$i."\" width=\"100\" height=\"66\">\n";
	if($i==5) echo "<p>";
	if($i==10) echo "<p>";
}

echo "</body></html>";


?>