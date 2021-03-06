<?php
/**
 * This script creates a new refund request.
 */
// login is required, to get name employee
$mustlogin = true;
include '../admin/vars.php';

if (false === in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {

	JsonResponse([

		'type'    => 'error',
		'message' => 'Refunds can only be requested from within the CMS',
		'request' => [

			'method' => $_SERVER['REQUEST_METHOD'],
			'with'	 => $_SERVER['HTTP_X_REQUESTED_WITH'],
		],
	]);
}

$params = [];
switch ($_SERVER['REQUEST_METHOD']) {

    case 'POST':

        $params = $_POST;
        break;

    case 'PUT':

        parse_str(file_get_contents('php://input'), $params);
        break;
}

$refundRequest    = new RefundRequest($db);

// convert all params to windows-1252 (source is ajax utf-8)
foreach ($params as $key => $value) {
    $params[$key] = wt_utf8_decode($value);
}

$params['amount'] = floatval(str_replace(',', '.', $params['amount']));

// removes spaces from iban
$params['iban']   = str_replace(' ', '', $params['iban']);

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // updating refund request
    $refundRequest->update([

    	'name'		  => $params['name'],
    	'iban'		  => $params['iban'],
    	'bic'		  => $params['bic'],
    	'description' => $params['description'],
    	'amount'	  => $params['amount'],

    ], $params['boeking_retour_id']);

    // formatting price for logs and comment
    $formatted_price = number_format(abs($params['amount']), 2, ',', '.');

    boeking_log($params['boeking_id'], sprintf('verzoek retourbetaling t.w.v. � %s gewijzigd', $formatted_price));
    cmslog_pagina_title('verzoek retourbetaling gewijzigd');

    // appending refund request to internal comments
    $comment = sprintf("%s (%s) \n� %s verzoek retourbetaling gewijzigd", date('d/m/Y'), $login->vars['voornaam'], $formatted_price);

    $db->query("UPDATE boeking SET opmerkingen_intern = CONCAT(opmerkingen_intern, '\n\n', '" . wt_as($comment) . "') WHERE boeking_id = " . intval($params['boeking_id']));
    cmslog_pagina_title('interne opmerkingen boeking gewijzigd');

    JsonResponse([

    	'type'    => 'success',
    	'message' => 'Refund request has successfully been updated',
    ]);

} else {

    // creating refund request
    $refundRequest->create([

    	'boeking_id'  => $params['boeking_id'],
    	'name'		  => $params['name'],
    	'iban'		  => $params['iban'],
    	'bic'		  => $params['bic'],
    	'description' => $params['description'],
    	'amount'	  => $params['amount'],
    ]);

    // formatting price for logs and comment
    $formatted_price = number_format(abs($params['amount']), 2, ',', '.');

    boeking_log($params['boeking_id'], sprintf('verzoek retourbetaling t.w.v. � %s aangemaakt', $formatted_price));
    cmslog_pagina_title('Verzoek retourbetaling aangemaakt');

    // appending refund request to internal comments
    $comment = sprintf("%s (%s) \n� %s Verzoek retourbetaling aangemaakt", date('d/m/Y'), $login->vars['voornaam'], $formatted_price);

    $db->query("UPDATE boeking SET opmerkingen_intern = CONCAT(opmerkingen_intern, '\n\n', '" . wt_as($comment) . "') WHERE boeking_id = " . intval($params['boeking_id']));
    cmslog_pagina_title('interne opmerkingen boeking gewijzigd');

    JsonResponse([

    	'type'    => 'success',
    	'message' => 'Refund request has successfully been created',
    ]);
}
