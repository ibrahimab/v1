<?php
require 'admin/vars.php';

use Chalet\Api\Api;

$api    = new Api($_GET['endpoint'], $_GET['method']);
$result = $api->getEndpoint($_GET)->result();

header('Content-type: application/json');
echo $result;
exit;