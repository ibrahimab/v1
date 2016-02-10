<?php
// landingspagina zoover awards 2015

$laat_titel_weg      = true;
$breadcrumbs["last"] = 'Zoover awards 2016';
$robot_noindex       = true;

include 'admin/vars.php';


if (in_array($vars['website'], array_keys($vars['zoover']))) {

	$title['zoover_awards']="Zoover awards 2016";
	include 'content/opmaak.php';

} else {

	header('Location: /');
    exit;
}