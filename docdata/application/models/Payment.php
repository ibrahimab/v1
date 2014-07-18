<?php

class Payment extends Model {

	public $paymentId = null;
	public $orderId = null;
	public $clusterKey = null;
	public $code = null;

	private $table = "docdata_payments";
	private $paymentsTable = "boeking_betaling";

	function __construct($orderId = null) {

		if($orderId) {
			$sql = "SELECT * FROM `" . $this->table . "` WHERE docdata_payment_id = '" . mysql_real_escape_string($orderId) . "' LIMIT 1";
			$this->query($sql);

			$this->next_record();
			$this->paymentId = htmlspecialchars($this->f("id"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
		}
		parent::__construct();
	}


	/**
	 * Save the payment details when it is created
	 *
	 * @param string $cluster_key (md5)
	 * @param int $order_id
	 * @param string $payment_type
	 * @param float $amount
	 * @param string $order_reference
	 * @return bool
	 */
	public function createPayment($cluster_key, $order_id, $payment_type, $amount, $order_reference, $css_id) {

		$config = App::get('helper/config');
		$status = $config->getItem("new", $config::GROUP_STATUS);

		$sql  = "INSERT INTO `" . $this->table . "` ";
		$sql .= "SET cluster_key = '" . mysql_real_escape_string($cluster_key) . "', ";
		$sql .= "boeking_id = '" . mysql_real_escape_string($order_id) . "', status = '" . mysql_real_escape_string($status) . "', created_at = NOW(), ";
		$sql .= "css_id = '" . mysql_real_escape_string($css_id) . "', ";
                $sql .= "amount = '" . mysql_real_escape_string($amount) . "', type = '" . mysql_real_escape_string($payment_type) ."', reference = '" . mysql_real_escape_string($order_reference) . "' ;";

		$this->query($sql);

		return (bool)$this->num_rows();
	}

	/**
	 * Returns the payment code
	 *
	 * @return null
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Get Cluster Key based on order id and payment type
	 *
	 * @param $order_id
	 * @param string $payment_type (advance / full)
	 * @param string $status The payment status
	 * @return int
	 */
	public function getDocdataPaymentOrderKey($order_id, $payment_type, $status = NULL, $css_id = NULL) {

		$sql  = "SELECT cluster_key FROM `" . $this->table . "` ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($order_id) . "' AND type = '" . mysql_real_escape_string($payment_type) ."' ";
		if($status) {
			$sql .= "AND status='". $status ."' ";
		}
                if($css_id){
                        $sql .= "AND css_id='". $css_id . "' ";
                }
		$sql .= "ORDER BY id DESC LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

        $this->next_record();

        return htmlspecialchars($this->f("cluster_key"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

	/**
	 * Get Cluster Key based on docdata payment id and payment type
	 *
	 * @param $docdata_id
	 * @param string $payment_type (advance / full)
	 * @return int
	 */
	public function getDocdataPaymentClusterKey($docdata_id, $payment_type) {

		$sql  = "SELECT cluster_key FROM `" . $this->table . "` ";
		$sql .= "WHERE docdata_payment_id = '" . mysql_real_escape_string($docdata_id) . "' AND type = '" . mysql_real_escape_string($payment_type) ."' LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

                $this->next_record();

                return htmlspecialchars($this->f("cluster_key"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

        /**
         * Gets the CSS ID based on cluster key.
         *
         * @param type $cluster_key
         * @return boolean
         */
        public function getDocdataCssId($cluster_key){
            $sql = "SELECT css_id FROM `". $this->table . "` ";
            $sql.= "WHERE cluster_key ='". mysql_real_escape_string($cluster_key) . "' LIMIT 1";

            $this->query($sql);

            if((int)$this->num_rows() == 0) return false;

            $this->next_record();

            return $this->f('css_id');
        }

	/**
	 * Get Payment Type based on order id and Docdata cluster key
	 *
	 * @param $order_id
	 * @param string $cluster_key (md5)
	 * @return int
	 */
	public function getDocdataPaymentType($order_id, $cluster_key) {
		$sql  = "SELECT type FROM `" . $this->table . "` ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($order_id) . "' AND cluster_key = '" . mysql_real_escape_string($cluster_key) ."' LIMIT 1";

		$this->query($sql);

		if((int)$this->num_rows() == 0) return null;

		$this->next_record();


		return htmlspecialchars($this->f("type"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

	/**
	 * Get Order id based on Cluster key
	 *
	 * @param $cluster_key
	 * @return string
	 */
	public function getDocdataPaymentOrderId($cluster_key) {

		$sql  = "SELECT boeking_id FROM `" . $this->table . "` ";
		$sql .= "WHERE cluster_key = '" . mysql_real_escape_string($cluster_key) . "' LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

		$this->next_record();
		return htmlspecialchars($this->f("boeking_id"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

	/**
	 * Update payment status to cancel
	 *
	 * @param int $orderId
	 * @param string $clusterKey
	 * @param string $message
	 *
	 * @return int
	 */
	public function cancel($orderId, $clusterKey, $message) {

		$status = parent::STATE_CANCELED;

		$sql  = "UPDATE `" . $this->table . "` ";
		$sql .= "SET status = '" . mysql_real_escape_string($status) . "', message = '" . mysql_real_escape_string($message) ."', updated_at = NOW() ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($orderId) . "' AND cluster_key = '" . mysql_real_escape_string($clusterKey) ."' LIMIT 1";

		$this->query($sql);

		return $this->affected_rows();
	}

	/**
	 * Return class instance
	 *
	 * @return $this
	 */
	public function getMethodInstance() {
		return $this;
	}

	/**
	 * Return order status from payment table
	 *
	 * @param int $orderId
	 * @param string $clusterKey
	 * @return string
	 */
	public function getOrderStatus($orderId, $clusterKey) {
		$sql  = "SELECT status FROM `" . $this->table . "` ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($orderId) . "' AND cluster_key = '" . mysql_real_escape_string($clusterKey) ."' LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

		$this->next_record();
		return htmlspecialchars($this->f("status"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

	/**
	 * Set docdata payment id after a successful payment received from docdata
	 *
	 * @param bigint $id
	 * @param int $orderId
	 * @param string $clusterKey
	 * @return int
	 */
	public function setDocdataPaymentId($id, $orderId, $clusterKey) {

		$sql  = "UPDATE `" . $this->table . "` ";
		$sql .= "SET docdata_payment_id = '" . mysql_real_escape_string($id) . "', updated_at = NOW() ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($orderId) . "' AND cluster_key = '" . mysql_real_escape_string($clusterKey) . "' LIMIT 1";

		$this->query($sql);

		return $this->affected_rows();
	}

	/**
	 * Get the docdata payment id based on the cluster key
	 *
	 * @param string $clusterKey
	 * @return null|int
	 */
	public function getDocdataPaymentId($clusterKey) {

		$sql  = "SELECT docdata_payment_id FROM `" . $this->table . "` ";
		$sql .= "WHERE cluster_key = '" . mysql_real_escape_string($clusterKey) ."' LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

		$this->next_record();
		return htmlspecialchars($this->f("docdata_payment_id"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

	/**
	 * Get the payment method based on the cluster key
	 *
	 * @param string $clusterKey
	 * @return null|string
	 */
	public function getDocdataPaymentMethod($clusterKey) {

		$sql  = "SELECT payment_method FROM `" . $this->table . "` ";
		$sql .= "WHERE cluster_key = '" . mysql_real_escape_string($clusterKey) ."' LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

		$this->next_record();
		return htmlspecialchars($this->f("payment_method"), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	}

	/**
	 * Set docdata payment method after a successful payment received from docdata
	 *
	 * @param string $method
	 * @return int
	 */
	public function setMethod($method) {

		$sql  = "UPDATE `" . $this->table . "` ";
		$sql .= "SET payment_method = '" . mysql_real_escape_string($method) . "', updated_at = NOW() ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($this->orderId) . "' AND cluster_key = '" . mysql_real_escape_string($this->clusterKey) . "' LIMIT 1";

		$this->query($sql);

		return $this->affected_rows();
	}

	/**
	 * Save the payment into the boeking_betaling table
	 * It is executed only when the payment status is "paid"
	 *
	 * @param int $order_id
	 * @param int $payment_id
	 * @param string $type
	 * @param float $captured
	 * @return bool
	 */
	public function setInvoice($order_id, $payment_id, $type, $captured) {
		global $vars;

		$config = App::get('app/config')->getConfig();
		$payments_cfg = $config['payment'];
		$payment_type = $payments_cfg[$type]['type'];

		$sql  = "INSERT INTO `" . $this->paymentsTable . "` ";
		$sql .= "SET boeking_id = '" . mysql_real_escape_string($order_id) . "', ";
        $sql .= "bedrag = " . mysql_real_escape_string($captured) . ", datum = NOW(), ";
        $sql .= "type = '" . mysql_real_escape_string($payment_type) . "', ";
		$sql .= " docdata_payment_id = '" . mysql_real_escape_string($payment_id) ."' ;";

		$this->query($sql);

		$ok = $this->affected_rows();

		if($ok == 1) {
			// save booking log on success payment
			$text = $vars["boeking_betaling_type"][$payment_type].": € ".number_format($captured,2,",",".")." (id: ".$payment_id.")";
			boeking_log($order_id, $text);

			// send payment-receipt to client
			$paymentmail = new paymentmail;
			$paymentmail->send_mail($order_id, $captured, time());
		}

		return (bool)$ok;
	}

	/**
	 * Update payment status
	 *
	 * @param string $status
	 * @param string $msg
	 * @return int
	 */
	public function setStatus($status, $msg) {

		$sql  = "UPDATE `" . $this->table . "` ";
		$sql .= "SET status = '" . mysql_real_escape_string($status) . "', message = '" . mysql_real_escape_string($msg) . "', updated_at = NOW() ";
		$sql .= "WHERE boeking_id = '" . mysql_real_escape_string($this->orderId) . "' AND cluster_key = '" . mysql_real_escape_string($this->clusterKey) . "' LIMIT 1";

		$this->query($sql);

		return $this->affected_rows();
	}

	/**
	 * Get the total amount of the paid payments from boeking_betaling table
	 *
	 * @param int $orderId
	 * @return mixed
	 */
	public function getPayments($orderId) {
		$sql = "SELECT SUM(bedrag) AS total FROM `" . $this->paymentsTable ."` WHERE boeking_id = '" . mysql_real_escape_string($orderId) . "'";
		$this->query($sql);

		$this->next_record();
		return $this->f("total");
	}

	/**
	 * Get payment details by cluster key and order_id
	 *
	 * @param string $clusterKey
	 * @param int $orderId
	 * @return array|null
	 */
	public function loadByClusterKey($clusterKey, $orderId) {
		$sql  = "SELECT * FROM `" . $this->table . "` ";
		$sql .= "WHERE cluster_key = '" . mysql_real_escape_string($clusterKey) ."' ";
		$sql .= "AND boeking_id = '". $orderId ."' AND status='pending' AND docdata_payment_id = '0' LIMIT 1";

		$this->query($sql);
		if((int)$this->num_rows() == 0) return null;

		$this->next_record();

		return array(
			"amount" => $this->f("amount"),
			"type"	=> $this->f("type")
		);

	}
}