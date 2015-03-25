<?php
/**
 * This Ajax request handles marking a refund as either paid or canceled.
 * It requires a post request method, a 'method' post param and an 'ids' array post param
 * It also checks to see if the request was actually sent with Ajax.
 */
$mustlogin = true;
include '../admin/vars.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {

	JsonResponse([

		'type'    => 'error',
		'message' => 'This method can only be accessed through AJAX',
		'request' => [

			'method' => $_SERVER['REQUEST_METHOD'],
			'with'   => $_SERVER['HTTP_X_REQUESTED_WITH'],
		],
	]);
}

$params = $_POST;
if (false === isset($params['method']) || false === isset($params['id'])) {

	JsonResponse([

		'type'    => 'error',
		'message' => 'Could not mark refund, request was invalid. Please provide method and id',
	]);
}

$refundRequest = new RefundRequest($db);
$id 		   = $params['id'];

if ($params['method'] === 'paid') {
	
	$refundRequest->markAsPaid($id);
	$message = 'Request was successfully marked as paid';

} else if ($params['method'] === 'cancel') {

	$refundRequest->markAsCancelled($id);
	$message = 'Request was successfully marked as cancelled';

} else {

	JsonResponse([

		'type'    => 'error',
		'message' => 'Refund request could not be marked, unknown method (' . $params['method'] . ')',
		'method'  => $params['method'],
	]);
}

/**
 * Now that refund marked, we log it to the database
 */
$refunds = $refundRequest->all(null, ['boeking_retour_id' => $params['id']]);
while ($refunds->next_record()) {

	$formatted_price = number_format($refunds->f('amount'), 2, ',', '.');

	if ($params['method'] === 'paid') {

		chalet_log(sprintf('retourstorting t.w.v. € %s afgerond', [$formatted_price]), false, true);
		cmslog_pagina_title('retourstorting afgerond');

		// appending refund request to internal comments
		$comment = sprintf("%s (%s):\n€ %s retour gestort", date('d/m/Y'), $login->vars['voornaam'], $formatted_price);

	} else {

		chalet_log(sprintf('retourstorting t.w.v. € %s geannuleerd', [$formatted_price]), false, true);
		cmslog_pagina_title('retourstorting geannuleerd');

		// appending refund request to internal comments
		$comment = sprintf("%s (%s):\nAanvraag retourstorting € %s teruggetrokken", date('d/m/Y'), $login->vars['voornaam'], $formatted_price);
	}

	$db->query("UPDATE boeking SET opmerkingen_intern = CONCAT(opmerkingen_intern, '\n\n', '" . wt_as($comment) . "') WHERE boeking_id = " . intval($refunds->f('boeking_id')));
	cmslog_pagina_title('interne opmerkingen boeking gewijzigd');
}

JsonResponse([

	'type'    => 'success',
	'message' => $message,
]);