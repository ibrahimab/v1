<?php
$html = '<html>';

$html .= '<head>';
// (used when customer click on the previous button of his browser)
$html .= '<script type="text/javascript">history.go(1);</script>';
$html .= '</head>';

/* check if the cluster already exists */
if ($paymentOrderExists) {

} else {

	$html . '<body>';

	/* temporary message */
	$html.= __('Redirecting to Docdata...');

	/* creating the form with action and hidden fields */
	$form = '<form id="docdata_checkout" name="docdata_checkout" method="GET" action="' . $webmenuUrl .'">';

	foreach($params as $key => $value) {
		$form .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
	}

	#$form .= '<input type="submit">';
	$form .= '</form>';

	$html.= $form;
        
	/* send the form */
	$html.= '<script type="text/javascript">document.getElementById("docdata_checkout").submit();</script>';
}

$html.= '</body></html>';

echo $html;