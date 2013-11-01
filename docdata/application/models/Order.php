<?php

class Order extends Model {

	// ADVANCE_PAYMENT and FULL_PAYMENT values are set as enum fields in Dataabse table docdata_payments
	const ADVANCE_PAYMENT = "advance";
	const FULL_PAYMENT = "full";

	public $increment_id = null;
	public static $customer_id = null;

	protected $customer;
	protected $cluster_key = null;
	protected $order_reference = null;

	private $payment_type = "advance";
	private $payment_amount = 0;
	private $table = "boeking"; // Database table name
        private $docdata_table = "docdata_payments"; // Database table name
	private $data = array();
	private $payment = null;

	public function loadByIncrementId($orderId = null) {
		$orderId = (int)$orderId;

		$data = get_boekinginfo($orderId);

		// Get approved (goedgekeurd) orders only
		if($data["stap1"]["goedgekeurd"] <> 1) return null;

		$this->increment_id = htmlspecialchars($data["stap1"]["boekingid"]);

		$this->data[$this->increment_id]['boekingsnummer'] = htmlspecialchars($data["stap1"]["boekingsnummer"]); // Booking number
		$this->data[$this->increment_id]['totale_reissom'] = htmlspecialchars($data["fin"]["totale_reissom"]); // Grand total
		$this->data[$this->increment_id]['aanbetaling'] = htmlspecialchars($data["fin"]["aanbetaling"]); // Advance payment #1
		$this->data[$this->increment_id]['bestelstatus'] = htmlspecialchars($data["stap1"]["bestelstatus"]); // Order status
		$acc_name = $data["stap1"]["accinfo"]["begincode"] . $data["stap1"]["accinfo"]["typeid"] . " - " . $data["stap1"]["accinfo"]["naam"];
		$this->data[$this->increment_id]['naam_accommodatie'] = htmlspecialchars($acc_name); // Accommodation name
		$this->data[$this->increment_id]['aanbetaling1'] = htmlspecialchars($data["stap1"]["aanbetaling1"]); // Advance payment #1
		$this->data[$this->increment_id]['aanbetaling2'] = htmlspecialchars($data["stap1"]["aanbetaling2"]); // Advance payment #2
		$this->data[$this->increment_id]['aanbetaling1_gewijzigd'] = htmlspecialchars($data["stap1"]["aanbetaling1_gewijzigd"]); // Advance payment manual entered
		$this->data[$this->increment_id]['aanbetaling2_datum'] = htmlspecialchars($data["stap1"]["aanbetaling2_datum"]); // Advance payment #2
		$this->data[$this->increment_id]['website'] = htmlspecialchars($data["stap1"]["website"]); // Booking website code
		$this->data[$this->increment_id]['taal'] = htmlspecialchars($data["stap1"]["taal"]); // Booking language

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

		$this->increment_id = htmlspecialchars($orderId);
		$this->cluster_key = htmlspecialchars($this->f("cluster_key"));

		$data = get_boekinginfo($orderId);

		$this->data[$this->increment_id]['bestelstatus'] = htmlspecialchars($data["stap1"]["bestelstatus"]); // Order status
		$this->data[$this->increment_id]['totale_reissom'] = htmlspecialchars($data["fin"]["totale_reissom"]); // Grand total

		return $this;
	}

	public function getRealOrderId() {
		$data = $this->data[$this->increment_id];
		return $data['boekingsnummer'];
	}

	public function getDocdataPaymentOrderKey($status = null) {
		return App::get("model/payment")->getDocdataPaymentOrderKey($this->increment_id, $this->payment_type, $status);
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

			$user_id = htmlspecialchars($this->f("user_id"));

			self::$customer_id = $user_id;
		}
		else {
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
	 * @return float
	 */
	public function getAdvancePaymentAmount() {

		$data = $this->data[$this->increment_id];

		$this->payment_type = self::ADVANCE_PAYMENT;
		$this->payment_amount = $data['aanbetaling'];

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
	 * return int;
	 */
	public function getShippingAmount() {
		return 0;
	}

	/**
	 * Get the order tax amount;
	 * For the moment is 0.
	 *
	 * return int;
	 */
	public function getTaxAmount() {
		return 0;
	}

	/**
	 * Get order shipping tax amount;
	 * For the moment is 0.
	 *
	 * return int;
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
		App::get('model/payment')->createPayment($payment_order_key, $this->increment_id, $this->payment_type, $this->payment_amount, $this->order_reference);
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
		// Select order total         
		$order_total = $this->data[$this->increment_id]['totale_reissom'];
		// Select payments totals
		$payments_total = App::get('model/payment')->getPayments($this->increment_id);
                $this->payment_amount = $order_total - $payments_total;
		$this->payment_type = self::FULL_PAYMENT;                

		return $this->payment_amount;
	}
        
	public function getBaseTotal() {
		// Select order total         
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
			case self::ADVANCE_PAYMENT:
				$gross_amount = $this->getAdvancePaymentAmount();
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

}