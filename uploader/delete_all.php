<?php
include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mongodb     = $vars['mongodb']['wrapper'];
    $collection  = $mongodb->getCollection($_POST['collection']);
    $id          = intval($_POST['id']);
    $files       = $collection->find(['file_id' => $id]);
    $path        = dirname(dirname(__FILE__));

    foreach ($files as $file) {

        $delete = $path . '/pic/cms/' . $file['directory'] . '/' . $file['filename'];

        if (file_exists($delete)) {
            unlink($delete);
        }
    }

    $collection->remove(['file_id' => $id]);

    header('Content-type: application/json');
    exit(json_encode(['type' => 'success', 'message' => 'Successfully removed image']));
}