<?php

$laat_titel_weg      = true;
$breadcrumbs['last'] = 'Fotowedstrijd';
$robot_noindex       = true;

include 'admin/vars.php';


if ($vars['website'] === 'C') {
    
	$title['onderzoek'] = 'Onderzoek';
	include 'content/opmaak.php';
    
} else {
    
	header("Location: /");
}
