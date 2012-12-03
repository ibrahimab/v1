<?php

#
# Beschikbare datums uit database halen
#

$geen_tracker_cookie=true;

set_time_limit(30);
ignore_user_abort(false);

$unixdir="../../../";
include("../../../admin/vars.php");

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: text/javascript; charset=UTF-8');

$return["data"]=true;

echo "function init2() {\n";

$db->query("SELECT UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE type='".$vars["seizoentype"]."' AND eind>NOW() AND tonen=3 ORDER BY begin;");
while($db->next_record()) {
	$timeteller=$db->f("begin");
	while($timeteller<=$db->f("eind")) {
		if($timeteller>=time()+21600) {
			echo "$('#vertrekdatum').append($(\"<option></option>\").attr(\"value\",".$timeteller.").text(\"".DATUM("D MND JJJJ",$timeteller)."\"));\n";
		}
		$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
	}
}

echo "\n}\n";

?>