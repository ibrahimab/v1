<?php
require 'admin/vars.php';

use Chalet\Api\Api;

$api      = new Api($_GET['endpoint'], $_GET['method']);
$endpoint = $api->getEndpoint($_GET);
$result   = $endpoint->result();

header('Content-type: application/json');
echo $result;
exit;