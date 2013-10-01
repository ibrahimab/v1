<?php
/**
 * Controller used to handle Docdata payment callbacks
 *
 */
class PaymentController extends Controller {

	protected $order;
	public $redirectPage = "/?docdata";

	public function __construct($controller, $action) {
		parent::__construct($controller, $action);

		if(isset($_SESSION["redirectPage"])) {
			$this->redirectPage = $_SESSION["redirectPage"];
		}
	}

	/**
	 * Retrieve the order
	 *
	 * @param boolean $forceReload Used to force a reload to get the latest order data
	 * @param object $request
	 *
	 * @return Model_Order instance
	 */
	protected function getOrder($forceReload = false, $request = null) {

		//reload order if needed
		if ($forceReload || $this->order === null) {
			$orderId = $this->getOrderId($request);

			if($orderId != null) {
				$this->order = App::get('model/order')->loadByIncrementId($orderId);

				if($this->order) {
					if($payment_type = $request->getParam("payment_type")) {
						$this->order->setPaymentType($payment_type);
					} elseif($cluster_key = $request->getParam('key')) {
						$this->order->setClusterKey($cluster_key);
					}
					if($pm_code = $request->getParam("pm_code")) {
						$this->order->setPmCode($pm_code);
					}
					if($country = $request->getParam("country")) {
						App::get('model/customer')->setCountry($country);
					}
				}
			}
		}
		return $this->order;
	}


	/**
	 * Get current order id
	 * @param object $request
	 *
	 * @return null|string
	 */
	protected function getOrderId($request = null) {

		$orderId = null;
		if ($this->order !== null) {
			return $this->order->getId();
		}

		//first check id in request params
		if ($request !== null) {
			$orderId = $request->getParam('order');
		}
                
		//if there is stil no id get by cluster key if isset
		if (empty($orderId)) {
			$cluster_key = $request->getParam('key');
			$orderId = App::get("model/payment")->getDocdataPaymentOrderId($cluster_key);
		}

		return $orderId;
	}


	/**
	 * Create a lock object, and try to get lock.
	 * This function blocks until the lock is aquired.
	 *
	 * @param string $orderNr the order increment id
	 *
	 * @return Model_Locking|False Returns locking object if lock is acquired otherwise False
	 */
	protected function getOrderLock($orderNr) {
		$lockName	= $orderNr;
		$orderInfo	= 'ordernr ['.$orderNr.'] pid ['.getmypid().'] lock ['.$lockName.']';
		$helper 	= App::get('helper/data');

		$helper->log(get_class().': Creating lock object. '.$orderInfo);

		$locking = new Model_Locking($lockName);

		//check if locking model successfully initialized
		if ($locking
			&& $locking instanceof Model_Locking
			&& $locking->initCheck()
		) {
			$helper->log(get_class().': Trying to lock object. '.$orderInfo);
			// Get the actual lock, this is blocking until lock is available.
			if (!$locking->lock()) {
				//cant get lock within timeout range
				$helper->log(get_class().': Acquiring lock unsuccessful. '.$orderInfo, App::ERR);
				$locking = false;
			} else {
				//lock acquired
				$helper->log(get_class().': lock acquired. '.$orderInfo);
			}
		} else {
			$helper->log(get_class().': Creating lock object failed. '.$orderInfo, App::WARN);
			$locking = false;
		}
		return $locking;
	}

	/**
	 * Release a lock object.
	 *
	 * @param Model_Locking $lock Locking object
	 *
	 * @return void
	 */
	protected function releaseOrderLock(Model_Locking $lock) {
		$helper = App::get('helper/data');

		// Release lock
		if ( $lock
			and $lock instanceof Model_Locking
			and $lock->initCheck()
		) {
			$orderInfo = 'pid ['.getmypid().'] lock ['.$lock->getLockCode().']';
			$helper->log(get_class().': Trying to unlock object. '.$orderInfo);
			$lock->unlock();
			$helper->log(get_class().': Unlock performed. '.$orderInfo);
		} else {
			$orderInfo = 'pid ['.getmypid().']';
			$helper->log(get_class().': No lock, so no unlocking needed. '.$orderInfo);
		}
	}

	/**
	 * Cancels the last order for the current user.
	 *
	 * @param $cancelMsg Message to show the user after he has been redirected to the payment page
	 * @param $request
	 * @param $error
	 * @param bool $cancelDocdata
	 *
	 * @return void
	 */
	protected function cancelOrder($cancelMsg, $request = null, $error = null, $cancelDocdata = false) {

		$orderId = $this->getOrderId($request);
		$clusterKey = $request->getParam('key');
		
		if ($orderId != null) {

			if ($error) {
				App::get('helper/data')->log('Cancellation of order ' . $orderId . ', reason: ' .$error, App::ERR);
				App::get('helper/data')->dbLog('Cancellation of order ' . $orderId . ', reason: ' .$error, App::ERR, $orderId);
			} else {
				App::get('helper/data')->log('Cancellation of order ' . $orderId . ', message: ' . $cancelMsg);
			}

			//acquire lock
			//not checking result: if locked failed still continue since customer is waiting on this action
			$lock = $this->getOrderLock($orderId);

			//get the latest version of the order is retrieved after lock is acquired
			$order = $this->getOrder(true, $request);

			//in case order requires docdata cancel first the order in system might not need cancel (if docdata rejects)
			if($cancelDocdata) {
				$helper = App::get('helper/data');
				$response = App::get('model/docdata')->cancelCall($order);

				//regardless of outcome continue to cancel in system
				if ($response->hasError()) {
					$errorResponse = $response->getErrorMessage();

					$helper->log('Cancel action of an order failed: '.$order->getRealOrderId() . ". Message: " . $errorResponse, App::WARN);
					$helper->dbLog('Cancel action of an order failed: '.$order->getRealOrderId() . ". Message: " . $errorResponse, App::WARN, $order->getId());

					$errCode = 2;
					$redirect = "&error=" . $errCode;
				} else {
					$helper->log('Cancel of the order '.$order->getRealOrderId(). ' succeeded');
				}
			}

			/* Cancel the order so it isn't open anymore */
			$payment = $order->getPayment()->getMethodInstance();
			$payment->cancel($orderId, $clusterKey, $cancelMsg);

			/* Check for errors */
			if (!$error) {
				/* Set the given message to be shown on the next page load */
				$okCode = 2;
				$redirect = "&success=" . $okCode;
			} else {
				$errCode = 2;
				$redirect = "&error=" . $errCode;
			}

			$this->releaseOrderLock($lock);

		} else {
			$helper = App::get('helper/data');
			$helper->log('Order not found for the Cluster Key: '.$clusterKey, App::ERR);
			$helper->dbLog('Order not found for the Cluster Key: '.$clusterKey, App::ERR);

			//Order not found in the system
			$errCode = 1;
			$redirect = "&error=" .$errCode;
		}

		/* Redirect to the payment page*/
		$this->_redirect($this->redirectPage . $redirect);
	}

	/**
	 * Redirect to a specified url
	 *
	 * @param $url
	 * @param $status
	 */
	private function _redirect($url, $status = 302) {
            //header("HTTP/1.1 301 Moved Permanently"); 
            $http = array (
                100 => "HTTP/1.1 100 Continue",
                101 => "HTTP/1.1 101 Switching Protocols",
                200 => "HTTP/1.1 200 OK",
                201 => "HTTP/1.1 201 Created",
                202 => "HTTP/1.1 202 Accepted",
                203 => "HTTP/1.1 203 Non-Authoritative Information",
                204 => "HTTP/1.1 204 No Content",
                205 => "HTTP/1.1 205 Reset Content",
                206 => "HTTP/1.1 206 Partial Content",
                300 => "HTTP/1.1 300 Multiple Choices",
                301 => "HTTP/1.1 301 Moved Permanently",
                302 => "HTTP/1.1 302 Found",
                303 => "HTTP/1.1 303 See Other",
                304 => "HTTP/1.1 304 Not Modified",
                305 => "HTTP/1.1 305 Use Proxy",
                307 => "HTTP/1.1 307 Temporary Redirect",
                400 => "HTTP/1.1 400 Bad Request",
                401 => "HTTP/1.1 401 Unauthorized",
                402 => "HTTP/1.1 402 Payment Required",
                403 => "HTTP/1.1 403 Forbidden",
                404 => "HTTP/1.1 404 Not Found",
                405 => "HTTP/1.1 405 Method Not Allowed",
                406 => "HTTP/1.1 406 Not Acceptable",
                407 => "HTTP/1.1 407 Proxy Authentication Required",
                408 => "HTTP/1.1 408 Request Time-out",
                409 => "HTTP/1.1 409 Conflict",
                410 => "HTTP/1.1 410 Gone",
                411 => "HTTP/1.1 411 Length Required",
                412 => "HTTP/1.1 412 Precondition Failed",
                413 => "HTTP/1.1 413 Request Entity Too Large",
                414 => "HTTP/1.1 414 Request-URI Too Large",
                415 => "HTTP/1.1 415 Unsupported Media Type",
                416 => "HTTP/1.1 416 Requested range not satisfiable",
                417 => "HTTP/1.1 417 Expectation Failed",
                500 => "HTTP/1.1 500 Internal Server Error",
                501 => "HTTP/1.1 501 Not Implemented",
                502 => "HTTP/1.1 502 Bad Gateway",
                503 => "HTTP/1.1 503 Service Unavailable",
                504 => "HTTP/1.1 504 Gateway Time-out"
            );

            header($http[$status]);
            header("Location:" . $url);
	}

	/**
	 * Handles the new order actions
	 *
	 * @param Order $order Order that is recently created
	 * @param Request $request object
	 *
	 * @return void
	 */
	private function _newOrder(Order $order, $request = null) {

		//acquire lock
		//not checking result: if locked failed still continue since customer is waiting on this action
		$lock = $this->getOrderLock($this->getOrderId($request));

		//get the latest version of the order is retrieved after lock is acquired
		$order = $this->getOrder(true, $request);
                
		//update order with latest data
		App::get('model/docdata')->statusCall($order, array());

		$this->releaseOrderLock($lock);

		//The Payment has been registered within system.
		$okCode = 1;

		//redirect customer to success page
		$this->_redirect($this->redirectPage . "&success=" . $okCode);
	}

	/**
	 * Action executed when Docdata notifies Chalet that there has been an update on an order
	 *
	 * @return void
	 */
	public function updateAction() {
		$error = false;

		$request = App::getRequest();
		$helper = App::get('helper/data');

		// Url should look somewhat like this: domain.tld/docdata/payment/update/id/1234
		$reference = $request->getParam('id');

		if ($reference === null) {
			// Error, id parameter not found, but was expected
			$helper->log('No id given in the current url', App::ERR);
			$error = true;
		}

		$position = strpos($reference, '_');
		$reference = ($position !== false ? substr($reference, 0, $position) : $reference);
		if (strlen($reference) === 0) {
			// Error, could not find underscore
			$helper->log(
				'Reference evaluated to nothing usable, anything after an underscore is stripped, maybe that caused this?',
				App::WARN
			);
			$error = true;
		}

		//acquire lock for reference
		$lock = $this->getOrderLock($reference);

		//retrieve the order to update
		$order = App::get("model/order")->loadByDocdataId($reference);
                
		
                if ($order === null || $order->getId() === null) {
			// Error, order by the given reference has not been found
			$helper->log(
				'Order not found by given reference, cannot proceed',
				App::ERR
			);
			$error = true;
		}

		if ($order === null || $order->getDocdataPaymentClusterKey() === null) {
			// Error, no payment order key found for the order, which is strange, because why would docdata notify us of an order which has never been made with docdata according to our system
			$helper->log(
				'Order does not have an order key, which means Docdata asked us to update something which isn\'t in their system according to our system...',
				App::ERR
			);
			$error = true;
		}

		if (!$error) {
			$result = App::get('model/docdata')->statusCall($order, array());
		}

		$this->releaseOrderLock($lock);

		if (isset($result) && $result->hasError()) {
			// Error, error during status update
			$helper->log(
				'An error occured during the update of the order, Docdata backend didn\'t response or data couldn\'t be parsed. Manual action might be required.',
				App::ERR
			);
			$helper->dbLog(
				'An error occured during the update of the order, Docdata backend didn\'t response or data couldn\'t be parsed. Manual action might be required.',
				App::ERR,
				$order->getId()
			);
			$error = true;
		} else {
			//all changes are kept to the order
		}

		if ($error) {
			header("HTTP/1.1 500 Internal Server Error");
			die('<h1>Server Error</h1><p>Required parameters could not be parsed.</p>');
		}
	}

	/**
	 * Executed when a user returns from Docdata after canceling the payment themselves
	 *
	 * @return void
	 */
	public function cancelAction() {
		//log external trigger of the cancel action, details are logged in cancelOrder function
		App::get('helper/data')->log('Cancel Action');

		$cancelMsg = __('Your payment was cancelled upon your request. You can still place your order again later.');

		$this->cancelOrder($cancelMsg, App::getRequest(), null, true);
	}

	/**
	 * Executed when a user returns from Docdata after canceling the payment themselves
	 *
	 * @return void
	 */
	public function abortAction() {
		//log external trigger of the abort action, details are logged in cancelOrder function
		App::get('helper/data')->log('Abort Action');

		$cancelMsg = __('Your payment was cancelled upon your request.');

		$this->cancelOrder($cancelMsg, App::getRequest(), null, true);
	}

	/**
	 * Action executed at successfull creation of an order
	 *
	 * @return void
	 */
	public function successAction() {
		$request = App::getRequest();

		// retrieve the order
		$order = $this->getOrder(false, $request);
                             
		if ($order != null) {
			$order->setClusterKey($request->getParam("key"));

			App::get('helper/data')->log('Success Action for the order '.$order->getId());
			//handle new order
			$this->_newOrder($order, $request);
		} else {
			//no order found in session or via URL
			$errCode = 1;
			$this->_redirect($this->redirectPage . "&error=" .$errCode);
		}
	}

	/**
	 * Action executed when accessing the docdata/payment/index url
	 * It will display a empty page with message
	 *
	 */
	public function indexAction() {
		// We display an error message in this case
		die("Invalid Payment Request");
	}


	/**
	 * Action executed to redirect the customer to the Docdata Webmenu page
	 *
	 * @throws Model_System_Exception
	 *
	 * @return void
	 */
	public function redirectAction() {

		$request = App::getRequest();

		// Instantiate the return page
		$this->setReturnPage();

		/* retrieve the order */
		$order = $this->getOrder(false, $request);
                
		if(!$order) {
			if($orderId = $request->getParam("order")) {
				App::get('helper/data')->log('Order not found with code: '.$orderId, App::ERR);
				App::get('helper/data')->dbLog('Order not found with code: '.$orderId, App::ERR, $orderId);
			} else {
				App::get('helper/data')->log('Order not found because order code was not provided', App::ERR);
			}
			$this->render = 0;

			//Order not found or the order is not approved
			$errCode = 1;
			$this->_redirect($this->redirectPage . "&error=" . $errCode);
			return;
		}

		// Get order customer id
		$customer_id = $order->getCustomerId();

		if($_SESSION['LOGIN']) {
			// Get logged in user id
			$user_id = $_SESSION['LOGIN']['chaletboekingwijzigen']['user_id'];

			// Check if the order is belongs to the currently logged in user
			if($user_id != $customer_id) {
				App::get('helper/data')->log('The order with the id: ' . $order->getId() .' does not belong to the user with id: ' .$user_id, App::ERR);
				App::get('helper/data')->dbLog('The order with the id: ' . $order->getId() .' does not belong to the user with id: ' .$user_id, App::ERR, $order->getId());

				// Order does not belong to the logged in user
				$errCode = 3;
				$this->_redirect($this->redirectPage . "&error=" . $errCode);
				return;
			}
		} else {
			App::get('helper/data')->log('Customer not logged in when accessing redirect action.', App::ERR);

			// User must login
			$errCode = 4;
			$this->_redirect($this->redirectPage . "&error=" . $errCode);
			return;
		}

		App::get('helper/data')->log('Redirect Action for the order '.$order->getRealOrderId());
                        
		//make sure order still needs to be placed with Docdata
		$payment_order_key = $order->getDocdataPaymentOrderKey($status="pending");
		$checkOtherParams = $order->matchBackOrder($payment_order_key, $request);

		if ($payment_order_key === null || $checkOtherParams == false) {

			// Get payment code from the $_POST request parameters
			$pm_code = $request->getParam('pm_code');

			// Can set extra parameters if needed
			$extra_params = array();
			unset($extra_params['pm_code']);

                        // add extra parameters for the creation of the payment order
			$extra_paramsCO = App::get('helper/data')->removePrefix(
				Model_Method_Abstract::PREFIX_CREATE,
				$extra_params
			);

			$errorMsg = false;

			try {
				// creation of the payment order
				$result = App::get('model/docdata')->createCall($order, $extra_paramsCO);
			} catch (Model_System_Exception $exception) {
				$errorMsg = $exception->getMessage();
			}

			if ($result->hasError()) {
				if (!$errorMsg) {
					$errorMsg = __('We\'re sorry but an error occurred trying to create your order.');
				}

				//error has been seen, cancel order.
				$this->cancelOrder(
					$errorMsg,
					$request,
					$result->getErrorMessage()
				);
				$this->render = 0;
				return;
			}
		}

		if(!isset($extra_params)) $extra_params = array();
		if(!isset($pm_code)) $pm_code = "docdata_idl";

		$payment_order_key = $order->getDocdataPaymentOrderKey($status="pending");

		$extra_paramsSO = App::get('helper/data')->removePrefix(
			Model_Method_Abstract::PREFIX_SHOW,
			$extra_params
		);

		$params = App::get('helper/api_webmenu')->getParams($order, $pm_code, $payment_order_key, $extra_paramsSO);
		$webmenu_url = App::get('helper/config')->getWebmenuUri();

		// creation of the block that will do the redirection with POST values
		$this->set('params', $params);
		$this->set('webmenuUrl', $webmenu_url);
		$this->set('paymentOrderExists', false);
	}

	/**
	 * Action executed when error occurs during handling of an order in the Docdata webmenu
	 *
	 * @return void
	 */
	public function errorAction() {
		$request = App::getRequest();
		// retrieve the order
		$order = $this->getOrder(false, $request);

		if ($order != null) {
			$order->setClusterKey($request->getParam("key"));
			App::get('helper/data')->log('Error Action for the order '.$order->getRealOrderId(), App::ERR);
		}

		$this->cancelOrder(
			__('An error occurred during the payment process. You may try again or come back later.'),
			$request,
			__('Docdata returned an error during the payment process.')
		);
	}

	/**
	 * Action executed at successfully creation of an order into the pending state
	 *
	 * @return void
	 */
	public function pendingAction() {
		$request = App::getRequest();
		// retrieve the order
		$order = $this->getOrder(false, $request);

		if ($order != null) {
			$order->setClusterKey($request->getParam("key"));
			App::get('helper/data')->log('Pending Action for the order '.$order->getRealOrderId());
			//handle new order
			$this->_newOrder($order, $request);
		} else {
			//no order found in session or via URL
			$errCode = 1;
			$this->_redirect($this->redirectPage . "&error=" . $errCode);
		}
	}

	/**
	 * Set the return page to user account
	 * The function is called only on redirect action
	 *
	 * @return void
	 */
	private function setReturnPage() {
		if(!empty($_SERVER['HTTP_REFERER'])) {

			$url = $_SERVER["HTTP_REFERER"];
			$arr = parse_url($url);

			parse_str($arr["query"], $str);

			// remove success and error codes from the redirect url
			unset($str["success"]);
			unset($str["error"]);

			$arr["query"] = http_build_query($str);

			$this->redirectPage = rtrim(SITE_URL, "/") . $arr["path"] . "?" . $arr["query"];
			$_SESSION["redirectPage"] = $this->redirectPage;
		}
	}
}