<?php


// http://legacy.chalet.nl/api.php?endpoint=3&token=0&type_id=15&season_id=27&method=1
// http://legacy-accept.chalet.nl/api.php?endpoint=3&token=0&type_id=15&season_id=27&method=1

$geen_tracker_cookie = true;
require 'admin/vars.php';

use Chalet\Api\Api;
use Chalet\Api\Auth\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    $response->setData([

        'type' => 'error',
        'message' => $e->getMessage(),
    ]);
}

return $response->send();