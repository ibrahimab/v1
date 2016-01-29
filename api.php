<?php
require 'admin/vars.php';

use Chalet\Api\Api;
use Symfony\Component\HttpFoundation\Request;

$request  = Request::createFromGlobals();
$api      = new Api($request);
$endpoint = $api->getEndpoint();
$result   = $endpoint->result();

header('Content-type: application/json');
echo $result;
exit;