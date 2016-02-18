<?php
$geen_tracker_cookie = true;
require 'admin/vars.php';

use Chalet\Api\Api;
use Chalet\Api\Auth\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$request = Request::createFromGlobals();

try {

    $auth = new Auth(['127.0.0.1']);
    $auth->authenticate($request->getClientIp());

    $api      = new Api($request);
    $endpoint = $api->getEndpoint();
    $result   = $endpoint->result();

    $response = new JsonResponse();
    $response->setData($result);

} catch (\Exception $e) {

    $response = new JsonResponse();
    $response->setData([

        'type' => 'error',
        'message' => $e->getMessage(),
    ]);
}

return $response->send();