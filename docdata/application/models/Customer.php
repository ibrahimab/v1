<?php

class Customer extends Model {

	private $customer = array();

	public function load($id, $order_id) {
		if($id != null) {
			$sql = "SELECT bp.* FROM boeking b, boeking_persoon bp
				INNER JOIN boekinguser bu ON bp.email = bu.user
				WHERE bp.boeking_id=b.boeking_id
				AND bp.persoonnummer=1
				AND bu.user_id = '" . mysql_real_escape_string($id) ."'
				AND bp.boeking_id = '" . mysql_real_escape_string($order_id) ."'";

			$this->query($sql);

			$this->next_record();

			$this->customer['email'] = $this->f('email');
			$this->customer['gender'] = $this->f('geslacht');
			$this->customer['firstname'] = $this->f('voornaam');
			$this->customer['initials'] = $this->f('tussenvoegsel');
			$this->customer['lastname'] = $this->f('achternaam');
			$this->customer['address']	= $this->f('adres');
			$this->customer['postcode']	= $this->f('postcode');
			$this->customer['city']	= $this->f('plaats');
			#$this->customer['country']	= $this->f('land');
			$this->customer['dob'] = $this->f('geboortedatum'); // Date of Birth

			return $this;
		}

		return null;
	}

	public function getEmail() {
		return $this->customer['email'];
	}

	public function getGender() {
		return $this->customer['gender'];
	}

	public function getFirstname() {
		return $this->customer['firstname'];
	}

	public function getInitials() {
		return $this->customer['initials'];
	}

	public function getLastname() {
		return $this->customer['lastname'];
	}

	public function getDob() {
		if(!empty($this->customer['dob']))
			return date("Y-m-d", $this->customer['dob']);
		else
			return null;
	}

	/**
	 * Currently the users do not have a Company field, but we add it for future implementations
	 *
	 * @return null
	 */
	public function getCompany() {
		return null;
	}

	public function getStreetFull() {
		return $this->customer['address'];
	}

	public function getPostCode() {
		return $this->customer['postcode'];
	}

	public function getCity() {
		return $this->customer['city'];
	}

	public function getCountryId() {
		return (!empty($this->customer['country'])) ? $this->customer['country'] : "NL";
	}

	public function setCountry($country) {
		$this->customer["country"] = $country;
	}
}