<?php

$mustlogin=false;
include("admin/vars.php");

header("Content-Type: application/javascript");

if($login->logged_in) {

	$date = new Datetime('+1 year');

	echo "document.cookie = 'acceptance_user_id=".wt_he($login->user_id)."; expires=". $date->format(DateTime::COOKIE)."; path=/';\n";
	echo "document.cookie = 'acceptance_email=".wt_he($login->vars["email"])."; expires=". $date->format(DateTime::COOKIE)."; path=/';\n";
}

echo "var acceptance_cookie_set = true;";



?>