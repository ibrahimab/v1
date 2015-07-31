<?php
include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	function ratio($width, $height) {
		return ($width > $height ? ($width / $height) : ($height / $width));
	}
	
	function jsonResponse($data = []) {
		
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}

	$headers       = getallheaders();
    $image         = imagecreatefromstring(file_get_contents('php://input'));
	$cropData      = json_decode($headers['X_CROP_DATA'], true);
	$cropData	   = array_map('ceil', $cropData);
	$cropData	   = array_map('intval', $cropData);
    $new           = imagecrop($image, $cropData);
	
	$size		   = ['width' => imagesx($new), 'height' => imagesy($new)];
	$size['ratio'] = ratio($size['width'], $size['height']);
	$allowedRatio  = 4 / 3;

	if ($size['ratio'] !== $allowedRatio) {
		
		jsonResponse([
			
			'type'    => 'error',
			'message' => 'ratio is not allowed',
			'ratio'   => $size['ratio'],
			'crop'	  => $cropData,
		]);
	}

    $fileinfo      = pathinfo($headers['X_FILENAME']);
    $rank          = intval($headers['X_RANK']);
    $filename      = time() . '-' . $rank . '.' . $fileinfo['extension'];
    $directory     = 'accommodations';
    $destination   = dirname(dirname(__FILE__)) . '/pic/cms/' . $directory;

	imagejpeg($new, $destination . '/' . $filename, 100);
    imagedestroy($new);

    $mongodb = $vars['mongodb']['wrapper'];
    $data    = [

		'file_id'   => intval($headers['X_FILE_ID']),
		'rank'	    => $rank,
        'label'     => $headers['X_LABEL'],
		'filename'  => $filename,
		'directory' => $directory,
		'width'     => $cropData['width'],
		'height'    => $cropData['height'],
	];

    $mongodb->getCollection($headers['X_COLLECTION'])->insert($data);

	jsonResponse([

		'type'    => 'success',
		'message' => 'Uploading file',
	]);
	
	exit;
}