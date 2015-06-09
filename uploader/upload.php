<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$headers = getallheaders();
    $image   = imagecreatefromstring(file_get_contents('php://input'));
    $new     = imagecrop($image, json_decode($headers['X_CROP_DATA'], true));

	imagejpeg($new, 'uploads/' . $headers['X_FILENAME'], 100);
    imagedestroy($new);

	header('Content-Type: application/json');
	echo json_encode([

		'type'    => 'success',
		'message' => 'Uploading file',
	]);

	exit;
}