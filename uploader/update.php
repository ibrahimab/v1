<?php
include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	function JsonResponse($data = []) {
		
	    header('Content-Type: application/json');
	    echo json_encode($data);
	    exit;
	}

	if (!isset($_POST['label'])) {
		JsonResponse(['type' => 'error', 'could not update']);
	}

    $mongodb    = $vars['mongodb']['wrapper'];
    $collection = $_POST['collection'];
    $labels     = $_POST['label'];
    $ranks      = $_POST['rank'];
    $under      = $_POST['under'];
	$always		= $_POST['always'];
    $bulk       = $mongodb->getBulkUpdater($collection);
    $mainImages = $_POST['main_images'];

    foreach ($labels as $_id => $label) {

		$bulk->add(['q' => ['_id'  => new MongoId($_id)],
					'u' => ['$set' => ['label'  => $label, 
									   'under'  => (isset($under[$_id])), 
									   'always' => (isset($always[$_id])),
									   'type'	=> 'normal',
									   'rank'   => intval($ranks[$_id])]]]);
    }

	$bulk->execute();
	
	$bulk = $mongodb->getBulkUpdater($collection);

    foreach ($mainImages as $type => $_id) {

        if (trim($_id) === '') {
            continue;
        }

        $bulk->add(['q' => ['_id' => new MongoId($_id)],
                    'u' => ['$set' => ['type' => $type]]]);
    }

    $bulk->execute();

    JsonResponse(['type' => 'success', 'message' => 'Successfully updated!', 'main_images' => $_POST['main_images']]);
}