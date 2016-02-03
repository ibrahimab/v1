<?php
require 'admin/vars.php';

use Chalet\Api\Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$request  = Request::createFromGlobals();
$api      = new Api($request);
$endpoint = $api->getEndpoint();
$result   = $endpoint->result();

$response = new JsonResponse();
$response->setData($result);

return $response->send();