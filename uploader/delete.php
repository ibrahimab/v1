<?php
include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['id'])) {

        $mongodb     = $vars['mongodb']['wrapper'];
        $collection  = $mongodb->getCollection($_POST['collection']);
        $id          = new MongoId($_POST['id']);
        $file        = $collection->findOne(['_id' => $id]);
        $destination = dirname(dirname(__FILE__)) . '/pic/cms/' . $file['directory'];
		$location	 = $destination . '/' . $file['filename'];

		if (file_exists($location) && is_file($location)) {
			
			unlink($location);
			filesync::add_to_filesync_table('pic/cms/' . $file['directory'] . '/' . $file['filename'], true);
		}
		
		$collection->remove(['_id' => $id]);
    }

    header('Content-type: application/json');
    exit(json_encode(['type' => 'success', 'message' => 'Successfully removed image']));
}