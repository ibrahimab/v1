<?php

namespace Chalet\Monitor;

/**
* Class to check if there is missing booking data in the database
*
* @author  Jeroen Boschman <jeroen@webtastic.nl>
* @package Chalet
*/
class MissingBookingDataChecker
{

	/**
	 * @var \DB_sql
	 */
	private $db;

	/**
	 * @var \Configuration
	 */
	private $config;

	/**
	 * @param \DB_sql $db
	 */
	function __construct(\DB_sql $db)
	{
		$this->db = $db;
		$this->config = new \Configuration;

	}

	/**
	 * check for missing personal data and send warning
	 *
	 * @return void
	 * @author Jeroen Boschman <jeroen@webtastic.nl>
	 **/
	public function CheckForBookingPersonMissing() {

		if ($this->IsBookingPersonMissing()) {

			$msg = 'Table `boeking_persoon` is leeg bij één of meerdere boekingen.';

			// send error to php-errorlog
			trigger_error( $msg, E_USER_NOTICE );

			// send email to developers
			$mail = new \wt_mail;
			$mail->fromname  = 'Chalet.nl Monitor';
			$mail->from      = 'noreply@chalet.nl';
			$mail->to        = $this->config->development_team_mail;
			$mail->subject   = $msg;
			$mail->plaintext = $msg . "\n\nControleer onmiddellijk of er meer boekingen met dit probleem kampen.";

			$mail->send();

		}
	}

	/**
	 * check if there are bookings without personal data in the table `boeking_persoon`
	 *
	 * @return boolean
	 * @author Jeroen Boschman <jeroen@webtastic.nl>
	 **/
	private function IsBookingPersonMissing()
	{

		$query = "	SELECT `boeking_id` FROM `boeking`
					WHERE boeking_id NOT IN (SELECT DISTINCT boeking_id FROM boeking_persoon)
					AND boekingsnummer<>'';";

		$this->db->query($query);

		if ($this->db->num_rows()) {
			return true;
		} else {
			return false;
		}

	}
}
