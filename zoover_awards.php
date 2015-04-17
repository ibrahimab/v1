<?php
// landingspagina zoover awards 2015

$laat_titel_weg      = true;
$breadcrumbs["last"] = 'Zoover awards 2015';
$robot_noindex       = true;

include 'admin/vars.php';


if (in_array($vars['website'], array('C', 'I'))) {
    
	$title['zoover_awards']="Zoover awards 2015";
	include 'content/opmaak.php';
    
} else {
    
	header('Location: /');
    exit;
}