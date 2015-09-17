<?php

$file 	  = 'pdf/Interview-Ondernamen-Woerden-September-2015.pdf';
$filename = 'Interview-Ondernamen-Woerden-September-2015.pdf';

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo file_get_contents($file);
exit;