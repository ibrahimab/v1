<?php

class Order extends Model {

	const ADVANCE_PAYMENT_1 = "advance1";
	const ADVANCE_PAYMENT_2 = "advance2";
	const FULL_PAYMENT = "full";

	public $increment_id = null;
	public static $customer_id = null;

	protected $customer;
	protected $cluster_key = null;
	protected $order_reference = null;
        protected $css_id = null;

	private $payment_type = "advance1";
	private $payment_amount = 0;
	private $table = "boeking"; // Bookings table name
	private $docdata_table = "docdata_payments"; // Payments table name
	private $data = array();
	private $payment = null;

	public function loadByIncrementId($orderId = null) {
		$orderId = (int)$orderId;

		$data = get_boekinginfo($orderId);

		// Get approved (goedgekeurd) orders only
		if($data["stap1"]["goedgekeurd"] <> 1) return null;

		$acc_name = $data["stap1"]["accinfo"]["begincode"] . $data["stap1"]["accinfo"]["typeid"] . " - " . $data["stap1"]["accinfo"]["naam"];

		$booking_payment = new booking_payment($data);
		$booking_payment->get_amounts();

		$this->increment_id = htmlspecialchars($data["stap1"]["boekingid"], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

		$this->data[$this->increment_id]['boekingsnummer'] = htmlspecialchars($data["stap1"]["boekingsnummer"], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'); // Booking number
		$this->data[$this->increment_id]['bestelstatus'] = htmlspecialchars($data["stap1"]["bestelstatus"], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'); // Order status
		$this->data[$this->increment_id]['naam_accommodatie'] = htmlspecialchars($acc_name, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'); // Accommodation name
		$this->data[$this->increment_id]['website'] = htmlspecialchars($data["stap1"]["website"], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'); // Booking website code
		$this->data[$this->increment_id]['taal'] = htmlspecialchars($data["stap1"]["taal"], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'); // Booking language

		$this->data[$this->increment_id]['totale_reissom'] = $booking_payment->amount["totaal"]; // Final total
		$this->data[$this->increment_id]['aanbetaling1'] = $booking_payment->amount["aanbetaling1"]; // Advance payment #1
		$this->data[$this->increment_id]['aanbetaling2'] = $booking_payment->amount["aanbetaling1"]+$booking_payment->amount["aanbetaling2"]; // Advance payment #2

		return $this;
	}

	public function loadByOrderReference($reference) {
		// Get approved (goedgekeurd) orders only
		$sql = "SELECT dp.boeking_id, dp.cluster_key FROM `" .  $this->docdata_table . "` dp, `" . $this->table ."` b ";
		$sql .= "WHERE dp.reference = '" . mysql_real_escape_string($reference) . "' AND b.goedgekeurd = 1 AND b.boeking_id = dp.boeking_id LIMIT 1";

		$this->query($sql);

		if($this->num_rows() == 0) return null;
		$this->next_record();

		$orderId = $this->f("boeking_id");

		$this->increment_id = htmlspecialchars($orderId, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
		$this->cluster_key = htmlspecialchars($this->f("cluster_key"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

		$data = get_boekinginfo($orderId);

		$booking_payment = new booking_payment($data);
		$booking_payment->get_amounts();

		$this->data[$this->increment_id]['bestelstatus'] = htmlspecialchars($data["stap1"]["bestelstatus"], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'); // Order status
		$this->data[$this->increment_id]['totale_reissom'] = $booking_payment->amount["totaal"]; // Final total

		return $this;
	}

	public function getRealOrderId() {
		$data = $this->data[$this->increment_id];
		return $data['boekingsnummer'];
	}

	public function getDocdataPaymentOrderKey($status = null, $css_id = NULL) {
		return App::get("model/payment")->getDocdataPaymentOrderKey($this->increment_id, $this->payment_type, $status, $css_id);
	}

        public function getDocdataCssId($cluster_key){
                return App::get("model/payment")->getDocdataCssId($cluster_key);
        }


	public function getDocdataPaymentClusterKey() {
		return App::get("model/payment")->getDocdataPaymentClusterKey($this->getDocdataPaymentId(), $this->getPaymentType());
	}

	/**
	 * Get the current order payment type
	 *
	 * @return string
	 *
	 */
	private function getPaymentType() {
		$this->payment_type = App::get("model/payment")->getDocdataPaymentType($this->increment_id, $this->cluster_key);

        return (!empty($this->payment_type)) ? $this->payment_type : self::FULL_PAYMENT;
	}

	/**
	 * Set order payment type (advance or full)
	 *
	 * @param $payment_type
	 */
	public function setPaymentType($payment_type) {
		$this->payment_type = (!empty($payment_type)) ? $payment_type : self::FULL_PAYMENT;
	}

	/**
	 * Set customer details based on order ID
	 *
	 * @param $order_id
	 * @return void
	 */
	private function getCustomer($order_id) {
		$sql = "SELECT bu.user_id FROM boeking b, boeking_persoon bp
				INNER JOIN boekinguser bu ON bp.email = bu.user
				WHERE bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.boeking_id = '" . mysql_real_escape_string($order_id) ."'";
		$this->query($sql);

		$this->customer = $this->next_record();
	}

	/**
	 * Returns the customer id for the current order
	 *
	 * @return null|string
	 */
	public function getCustomerId() {

		if(empty(self::$customer_id)) {
			$this->getCustomer($this->increment_id);
			$user_id = htmlspecialchars($this->f("user_id"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
			self::$customer_id = $user_id;
		} else {
			$user_id = self::$customer_id;
		}

		return $user_id;
	}

	/**
	 * Order Currency code; default is EUR
	 *
	 * @return string
	 */
	public function getOrderCurrencyCode() {
		return "EUR";
	}

	/**
	 * Amount of the advance payment
	 *
	 * @param int $type
	 * @return float
	 */
	public function getAdvancePaymentAmount($type = 1) {

		$data = $this->data[$this->increment_id];

		if($type == 1) {
			$this->payment_type = self::ADVANCE_PAYMENT_1;
		} elseif($type == 2) {
			$this->payment_type = self::ADVANCE_PAYMENT_2;
		}

		$this->payment_amount = $data['aanbetaling'.$type];

		return $this->payment_amount;
	}

	/**
	 * Get order grand total
	 *
	 * @return mixed
	 */
	public function getGrandTotal() {
		$data = $this->data[$this->increment_id];

		$this->payment_amount = $data['totale_reissom'];
		$this->payment_type = self::FULL_PAYMENT;

		return $this->payment_amount;
	}

	/**
	 * Get website code from the order info
	 *
	 * @return string
	 */
	public function getWebsiteCode() {
		$data = $this->data[$this->increment_id];

		return $data["website"];
	}

	/**
	 * Get order language that is used when redirecting to docdata webmenu
	 *
	 * @return string
	 */
	public function getLanguage() {
		$data = $this->data[$this->increment_id];

		return $data["taal"];
	}
	/**
	 * Get order shipping cost amount;
	 * For the moment is 0.
	 *
	 * @return int;
	 */
	public function getShippingAmount() {
		return 0;
	}

	/**
	 * Get the order tax amount;
	 * For the moment is 0.
	 *
	 * @return int;
	 */
	public function getTaxAmount() {
		return 0;
	}

	/**
	 * Get order shipping tax amount;
	 * For the moment is 0.
	 *
	 * @return int;
	 */
	public function getShippingTaxAmount() {
		return 0;
	}

	/**
	 * Get billing address from the Customer model
	 *
	 * @return Customer $address Address object
	 */
	public function getBillingAddress() {
		return App::get('model/customer')->load(self::$customer_id, $this->increment_id);
	}

	/**
	 * Shipping address is the same as Billing address
	 *
	 * @return Customer $address Address object
	 */
	public function getShippingAddress() {
		return $this->getBillingAddress();
	}

	/**
	 * Get all the booking items that will be include in the Docdata invoice
	 *
	 * return array
	 */
	public function getAllItems() {
		return array();
	}

	/**
	 * Fetch payment instance for the order
	 *
	 * @return Payment model
	 */
	public function getPayment() {
		if(!$this->payment) {
			$payment = App::get('model/payment');
			$this->payment = $payment;
		} else {
			$payment = $this->payment;
		}

		$payment->orderId = $this->increment_id;
		$payment->clusterKey = $this->cluster_key;

		return $payment;
	}

	/**
	 * Insert payment in the database with the cluster key
	 *
	 * @param $payment_order_key
	 *
	 * @return void
	 */
	public function setDocdataPaymentOrderKey($payment_order_key) {
		App::get('model/payment')->createPayment($payment_order_key, $this->increment_id, $this->payment_type, $this->payment_amount, $this->order_reference, $this->css_id);
	}

	/**
	 * Set payment code
	 *
	 * @param $pm_code
	 */
	public function setPmCode($pm_code) {
		$payment = $this->getPayment();
		$payment->code = $pm_code;
	}

	/**
	 * Get order status
	 *
	 * @return mixed
	 */
	public function getState() {
		$data = $this->data[$this->increment_id];
		return $data['bestelstatus'];
	}

	/**
	 * Can be used to handle different docdata payment profiles per website
	 * For the moment is not the case
	 *
	 * @return null
	 */
	public function getStoreId() {
		return null;
	}

	/**
	 * Fetch the name of the order accommodation
	 * @return mixed
	 */
	public function getAccommodationName() {
		$data = $this->data[$this->increment_id];
		return $data['naam_accommodatie'];
	}

	/**
	 * Get the payment status for the order
	 *
	 * @return mixed
	 */
	public function getStatus() {
		return App::get('model/payment')->getOrderStatus($this->increment_id, $this->cluster_key);
	}

	/**
	 * Get the cluster key of the order
	 *
	 * @return string
	 */
	public function getClusterKey() {
		return $this->cluster_key;
	}

	/**
	 * Register the payment into the boeking_betaling table
	 * This happens only when the order is paid
	 *
	 * @param $captured
	 */
	public function setInvoice($captured) {
		App::get('model/payment')->setInvoice($this->increment_id, $this->getDocdataPaymentId() , $this->getDocdataPaymentMethod(), $captured);
	}

	/**
	 * Update docdata payment with the Docdata Payment ID
	 *
	 * @param $id
	 */
	public function setDocdataPaymentId($id) {
		App::get('model/payment')->setDocdataPaymentId($id, $this->increment_id, $this->cluster_key);
	}

	/**
	 * Get the docdata_payment_id for the order
	 *
	 * @return int
	 */
	public function getDocdataPaymentId() {
            return App::get('model/payment')->getDocdataPaymentId($this->cluster_key);
	}

	public function getDocdataPaymentMethod() {
            return App::get('model/payment')->getDocdataPaymentMethod($this->cluster_key);
	}

	/**
	 * Function that returns the difference between order total amount and order payments
	 *
	 * @return int
	 */
	public function getBaseTotalDue() {
        $this->payment_amount = $this->data[$this->increment_id]['totale_reissom'];
		$this->payment_type = self::FULL_PAYMENT;

		return $this->payment_amount;
	}

	public function getBaseTotal() {
		// Select payments totals
		$payments_total = App::get('model/payment')->getPayments($this->increment_id);

		return $payments_total;
	}

	/**
	 * Set payment cluster key
	 *
	 * @param $key
	 */
	public function setClusterKey($key) {
		$this->cluster_key = $key;
	}

	/**
	 * Set order reference
	 *
	 * @param $key
	 */
	public function setOrderReference($key) {
		$this->order_reference = $key;
	}

        /**
         * Sets the current CSS.
         * @param $css_id
         */
        public function setCssId($css_id){
                $this->css_id = $css_id;
        }

	/**
	 * Get order id
	 * @return null
	 */
	public function getId() {
		return $this->increment_id;
	}

	/**
	 * Check if the payment is already registered in DocData to avoid creating multiple payments
	 *
	 * @param string $cluster_key
	 * @param Request $request
	 * @return bool
	 */
	public function matchBackOrder($cluster_key, $request) {
		if(empty($cluster_key)) return false;

		// Get the payment
		$existing_payment = App::get('model/payment')->loadByClusterKey($cluster_key, $this->getId());
		if(!$existing_payment) return false;

		switch($request->getParam('payment_type')) {
			case self::ADVANCE_PAYMENT_1:
				$gross_amount = $this->getAdvancePaymentAmount(1);
				break;
			case self::ADVANCE_PAYMENT_2:
				$gross_amount = $this->getAdvancePaymentAmount(2);
				break;
			case self::FULL_PAYMENT:
				$gross_amount = $this->getBaseTotalDue();
				break;
		}

		$_helper = App::get('helper/data');
		$total_gross_amount = $_helper->getAmountInMinorUnit($gross_amount, $this->getOrderCurrencyCode());
		$db_gross_amount = $_helper->getAmountInMinorUnit($existing_payment["amount"], $this->getOrderCurrencyCode());

		// Compare the amounts
		if($total_gross_amount != $db_gross_amount) return false;

		// Compare payment type
		if($this->payment_type != $existing_payment["type"]) return false;

		return true;
	}

        /**
         * Compare the existing cluster css with the config CSS.
         * @param type $cluster_key
         * @return boolean
         */
        public function compareCssId($cluster_key){
               if(empty($cluster_key)) return false;

               $helper = App::get('helper/api_create');
               $cssArray = $helper->getMenuPreference($this->getWebsiteCode());
               $css = $cssArray['css']['id'];
               $cssFromDb = $this->getDocdataCssId($cluster_key);
               if($css == $cssFromDb) return true;

               return false;
        }

}