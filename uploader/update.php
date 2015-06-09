<?php
include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mongodb    = $vars['mongodb']['wrapper'];
    $collection = $_POST['collection'];
    $labels     = $_POST['label'];
    $ranks      = $_POST['rank'];
    $bulk       = $mongodb->getBulkUpdater($collection);

    foreach ($labels as $_id => $label) {

		$bulk->add(['q' => ['_id'  => new MongoId($_id)],
					'u' => ['$set' => ['label' => $label, 'rank' => $ranks[$_id]]]]);
    }

    $bulk->execute();

    header('Content-Type: application/json');
    echo json_encode(['type' => 'success', 'message' => 'Successfully updated!']);
    exit;
}