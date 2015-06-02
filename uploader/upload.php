<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$headers = getallheaders();
	file_put_contents('uploads/' . $headers['X_FILENAME'], file_get_contents('php://input'));
	
	header('Content-Type: application/json');
	echo json_encode([
		
		'type'    => 'success',
		'message' => 'Uploading file',
	]);

	exit;
}