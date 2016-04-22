<?php

// landingspagina zoover awards 2015 / 2016

include 'admin/vars.php';

$laat_titel_weg      = true;

if (in_array($vars["website"], ['C', 'B'] )) {
    $year = '2016';
} else {
    $year = '2015';
}
$breadcrumbs["last"] = 'Zoover Awards ' . $year;

if (in_array($vars['website'], $vars['zoover'])) {

	$title['zoover-awards'] = 'Zoover Awards ' . $year;
	include 'content/opmaak.php';

} else {

	header('Location: /');
    exit;

}
