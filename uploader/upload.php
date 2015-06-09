<?php
include 'admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$headers     = getallheaders();
    $image       = imagecreatefromstring(file_get_contents('php://input'));
    $cropData    = json_decode($headers['X_CROP_DATA'], true);
    $new         = imagecrop($image, $cropData);
    $fileinfo    = pathinfo($headers['X_FILENAME']);
    $rank        = intval($headers['X_RANK']);
    $filename    = time() . '-' . $rank . '.' . $fileinfo['extension'];
    $directory   = 'accommodations';
    $destination = dirname(dirname(__FILE__)) . '/pic/cms/' . $directory;

	imagejpeg($new, $destination . '/' . $filename, 100);
    imagedestroy($new);

    $mongodb = $vars['mongodb']['wrapper'];
    $data    = [

		'file_id'   => intval($headers['X_FILE_ID']),
		'rank'	    => $rank,
		'filename'  => $filename,
		'directory' => $directory,
		'width'     => $cropData['width'],
		'height'    => $cropData['height'],
	];

    $mongodb->getCollection($headers['X_COLLECTION'])->insert($data);

	header('Content-Type: application/json');
	echo json_encode([

		'type'    => 'success',
		'message' => 'Uploading file',
	]);

	exit;
}