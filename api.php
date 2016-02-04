<?php
require 'admin/vars.php';

use Chalet\Api\Api;
use Chalet\Api\Auth\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$request  = Request::createFromGlobals();

try {

    $auth = new Auth(new wt_redis);
    $auth->authenticate($request->get('token'));

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