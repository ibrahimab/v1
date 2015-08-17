<?php
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    include '../admin/vars.php';
    
	function JsonResponse($data = []) {
		
	    header('Content-Type: application/json');
	    echo json_encode($data);
	    exit;
	}
    
    $id         = new MongoId($_POST['id']);
    $type       = $_POST['type'];
    $collection = $_POST['collection'];
    $mongodb    = $vars['mongodb']['wrapper'];
    $bulk       = $mongodb->getBulkUpdater($collection);
    
	$bulk->add(['q' => ['_id'  => $id],
				'u' => ['$set' => ['type' => 'normal']]]);
    
    $bulk->execute();
    
    JsonResponse([
        
        'type'    => 'success',
        'message' => 'main image cleared',
    ]);
}