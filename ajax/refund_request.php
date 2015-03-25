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

$refundRequest = new RefundRequest($db);

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

    chalet_log(sprintf('retourstorting t.w.v. € %s gewijzigd', [$formatted_price]), false, true);
    cmslog_pagina_title('retourstorting gewijzigd');

    // appending refund request to internal comments
    $comment = sprintf("%s (%s) \n€ %s retourstorting gewijzigd", date('d/m/Y'), $login->vars['voornaam'], $formatted_price);

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

    chalet_log(sprintf('Verzoek retourbetaling t.w.v. € %s aangemaakt', [$formatted_price]), false, true);
    cmslog_pagina_title('Verzoek retourbetaling aangemaakt');

    // appending refund request to internal comments
    $comment = sprintf("%s (%s) \n€ %s Verzoek retourbetaling aangemaakt", date('d/m/Y'), $login->vars['voornaam'], $formatted_price);

    $db->query("UPDATE boeking SET opmerkingen_intern = CONCAT(opmerkingen_intern, '\n\n', '" . wt_as($comment) . "') WHERE boeking_id = " . intval($params['boeking_id']));
    cmslog_pagina_title('interne opmerkingen boeking gewijzigd');

    JsonResponse([

    	'type'    => 'success',
    	'message' => 'Refund request has successfully been created',
    ]);
}