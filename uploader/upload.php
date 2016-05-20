<?php

include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function ratio($width, $height) {
        return $width / $height;
    }

    function rounddown($f, $precision) {
        return floor($f * pow(10, $precision)) / pow(10, 2);
    }

    function jsonResponse($data = []) {

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    $headers       = getallheaders();
    $image         = imagecreatefromstring(file_get_contents('php://input'));
    $cropData      = json_decode($headers['X_CROP_DATA'], true);

    if (!is_array($cropData)) {

        jsonResponse([

            'type'    => 'error',
            'message' => 'crop data is not valid',
            'crop'    => $cropData,
        ]);
    }

    $cropData      = array_map('ceil', $cropData);
    $cropData      = array_map('intval', $cropData);
    $new           = imagecrop($image, $cropData);

    if (false === is_resource($new)) {

        jsonResponse([

            'type'    => 'error',
            'message' => 'Could not create new image resource',
            'crop'    => $cropData,
        ]);
    }

    $size          = ['width' => imagesx($new), 'height' => imagesy($new)];
    $size['ratio'] = rounddown(ratio($size['width'], $size['height']), 2);
    $minRatio      = 1.3300;
    $maxRatio      = 1.3399;

    if (false === ($size['ratio'] >= $minRatio && $size['ratio'] <= $maxRatio)) {

        jsonResponse([

            'type'    => 'error',
            'message' => 'ratio is not allowed',
            'ratio'   => $size['ratio'],
            'crop'    => $cropData,
        ]);
    }

    $fileinfo      = pathinfo($headers['X_FILENAME']);
    $rank          = intval($headers['X_RANK']);
    $directory     = $headers['X_COLLECTION'];
    $destination   = dirname(dirname(__FILE__)) . '/pic/cms/' . $directory;

    /* determine filename: check for available filenames */
    $photoCounter  = $rank;
    while (true) {

        $filename = $headers['X_FILE_ID'] . '-' . $photoCounter . '.' . strtolower($fileinfo['extension']);

        if (file_exists($destination . '/' . $filename)) {
            $photoCounter += 1;
        } else {
            break;
        }

    }

    imagejpeg($new, $destination . '/' . $filename, 100);
    imagedestroy($new);

    filesync::add_to_filesync_table('pic/cms/' . $directory . '/' . $filename);

    $mongodb = $vars['mongodb']['wrapper'];
    $data    = [

        'file_id'   => intval($headers['X_FILE_ID']),
        'rank'      => $rank,
        'label'     => $headers['X_LABEL'],
        'filename'  => $filename,
        'directory' => $directory,
        'width'     => $cropData['width'],
        'height'    => $cropData['height'],
        'under'     => false,
        'always'    => false,
        'type'      => 'normal',
    ];

    $mongodb->getCollection($headers['X_COLLECTION'])->insert($data);

    jsonResponse([

        'type'     => 'success',
        'message'  => 'Uploading file',
        'image'    => [

            'id'   => (string)$data['_id'],
            'path' => $directory . '/' . $filename,
        ],
    ]);

    exit;
}
