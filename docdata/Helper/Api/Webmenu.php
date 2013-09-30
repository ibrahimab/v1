<?php
/**
 * Helper for webmenu communication
 *
 */
class Helper_Api_Webmenu {

	const RETURN_URL_SUCCESS = 'docdata/payment/success/key/',
		RETURN_URL_CANCELED = 'docdata/payment/cancel/key/',
		RETURN_URL_ERROR = 'docdata/payment/error/key/',
		RETURN_URL_PENDING = 'docdata/payment/pending/key/';

	/**
	 * Extracts parameters needed for the Webmenu
	 *
	 * @param Order $order Order to handle in the Webmenu
	 * @param string $pm_code Payment method code
	 * @param string $payment_order_key Reference for the payment order to be used in the Webmenu
	 * @param array $extra_params Additional parameters to be used in the Webmenu
	 *
	 * @return array Array of parameters
	 */
	public function getParams(Order $order, $pm_code, $payment_order_key, array $extra_params)
	{
		$helper = App::get('helper/config');
		// get values to send
		$lang = $order->getLanguage();
		$merchant = $helper->getMerchant();
		//convert pm_code to command
		$pm_command = $helper->getPaymentMethodItem($pm_code, 'command');

		$result = array(
			'payment_cluster_key' => $payment_order_key,
			'merchant_name' => $merchant['username'],
			'client_language' => $lang,
			'default_pm' => $pm_command
		);

		//send urls to allow return urls per store (docdata backend currently only supports 1 return url for a shop)
		$result['return_url_success'] = App::getUrl(SITE_URL . self::RETURN_URL_SUCCESS . $payment_order_key, array('_secure'=>true));
		$result['return_url_canceled'] = App::getUrl(SITE_URL . self::RETURN_URL_CANCELED . $payment_order_key, array('_secure'=>true));
		$result['return_url_error'] = App::getUrl(SITE_URL . self::RETURN_URL_ERROR . $payment_order_key, array('_secure'=>true));
		$result['return_url_pending'] = App::getUrl(SITE_URL . self::RETURN_URL_PENDING . $payment_order_key, array('_secure'=>true));

		return array_merge($result, $extra_params);
	}
}