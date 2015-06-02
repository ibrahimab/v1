<?php

/**
* XML-export for affiliate-provider HomeAway
*
* @author: Jeroen Boschman (jeroen@webtastic.nl)
* @since: 2015-06-02 15:11
*/

class xmlHomeAway extends xmlExport
{

	/**  name of the export (used in the filename for caching)  */
	protected $name = "homeaway";


	/**  type of feed (rates of reservations)  */
	public $feedtype = "rates";

	/**
	 * call the parent constructor
	 *
	 * @return void
	 */
	function __construct()
	{

		parent::__construct();

		$this->use_wederverkoop_prices = true;

		$this->must_be_available = false;
	}

	/**
	 * create XML according to HomeAway-structure
	 * and store data in parent object $this->x (XMLWriter)
	 *
	 * @return void
	 */
	protected function createSpecificXML()
	{

		if( $this->feedtype == "rates" ) {
			$this->x->startElement('ratePeriodsBatch');
		} else {
			$this->x->startElement('reservationBatch');
		}
		$this->x->writeElement('documentVersion', '1.0');


		$this->x->startElement('advertisers');
		$this->x->startElement('advertiser');
		$this->x->writeElement('assignedId', 'CHALETWINTERSPORTS');

		ksort($this->type_data);

		foreach ($this->type_data as $type_id => $type_data) {

			if( is_array($this->type_price[$type_id]) ) {

				if( $this->feedtype == "rates" ) {
					//
					// feed rates
					//

					$this->x->startElement('advertiserRatePeriods');

					$this->x->writeElement('listingExternalId', $this->type_codes[$type_id]);
					$this->x->writeElement('unitExternalId', $this->type_codes[$type_id]);

					$this->x->startElement('ratePeriods');
					foreach ($this->type_price[$type_id] as $key => $value) {

						$this->x->startElement('ratePeriod');

						$this->x->startElement('dateRange');
						$this->x->writeElement('beginDate', date("Y-m-d", $this->type_arrival[$type_id][$key]));

						// HomeAway uses "nights" and thus wants a week to end on Friday (Saturday-Friday): use -1
						$end_date = new DateTime( date("Y-m-d", $this->type_departure[$type_id][$key] ) );
						$end_date->modify('-1 day');
						$this->x->writeElement('endDate', $end_date->format('Y-m-d') );
						$this->x->endElement(); // close dateRange

						$this->x->writeElement('minimumStay', "ONE_WEEK");

						$this->x->startElement('name');
						$this->x->startElement('texts');
						$this->x->startElement('text');
						$this->x->writeAttribute('locale', 'en');

						// text "Winter" or "Summer"
						$this->x->writeElement('textValue', txt($this->config->seizoentype_namen[ $this->config->seizoentype ], "xml"));

						$this->x->endElement(); // close text
						$this->x->endElement(); // close texts
						$this->x->endElement(); // close name

						$this->x->startElement('rates');
						$this->x->startElement('rate');
						$this->x->writeAttribute('rateType', 'WEEKLY');

						$this->x->startElement('amount');
						$this->x->writeAttribute('currency', 'EUR');
						$this->x->text(number_format($this->type_price[$type_id][$key], 2, ".", ""));
						$this->x->endElement(); // close amount

						$this->x->endElement(); // close rate
						$this->x->endElement(); // close rates
						$this->x->endElement(); // close ratePeriod

					}

					$this->x->endElement(); // close ratePeriods

					$this->x->endElement(); // close advertiserRatePeriods

				} elseif( $this->feedtype == "reservations") {
					//
					// feed reservations
					//
					$this->x->startElement('advertiserReservations');

					$this->x->writeElement('listingExternalId', $this->type_codes[$type_id]);
					$this->x->writeElement('unitExternalId', $this->type_codes[$type_id]);

					$this->x->startElement('reservations');

					unset( $available_days );
					ksort( $this->type_price );
					foreach ($this->type_price[$type_id] as $key => $value) {
						if ( $this->type_availability[$type_id][$key] ) {
							$current_week = $this->type_arrival[$type_id][$key];
							$week=mktime(0,0,0,date("m",$current_week),date("d",$current_week),date("Y",$current_week));
							for($i=0;$i<=($this->type_number_of_nights[$type_id][$key]-1);$i++) {
								$week=mktime(0,0,0,date("m",$current_week),date("d",$current_week)+$i,date("Y",$current_week));
								$available_days[$week]=true;
							}
						}
					}

					$start=mktime(0,0,0,date("m"),1,date("Y"));
					$eind=mktime(0,0,0,date("m"),date("d"),date("Y")+2);

					$time=$start;
					while($time<$eind) {

						$this->x->startElement('reservation');
						$this->x->startElement('reservationDates');

						while($available_days[$time]) {
							$time=mktime(0,0,0,date("m",$time),date("d",$time)+1,date("Y",$time));
						}
						$this->x->writeElement('beginDate', date("Y-m-d",$time));

						$time=mktime(0,0,0,date("m",$time),date("d",$time)+1,date("Y",$time));

						while(!$available_days[$time] and $time<$eind) {
							$time=mktime(0,0,0,date("m",$time),date("d",$time)+1,date("Y",$time));
						}


						// HomeAway uses "nights" and thus wants a week to end on Friday (Saturday-Friday): use -1
						$show_enddate = mktime(0, 0, 0, date("m",$time), date("d",$time)-1, date("Y",$time));

						$this->x->writeElement('endDate', date("Y-m-d",$show_enddate));

						// echo "<endDate>".date("Y-m-d",$show_enddate)."</endDate>\n";

						$this->x->endElement(); // close reservationDates
						$this->x->endElement(); // close reservation


						$prevent_hang_teller++;
						if($prevent_hang_teller>100000) {
							exit;
						}

					}

					$this->x->endElement(); // close reservations
					$this->x->endElement(); // close advertiserReservations

				}
			}
		}

		$this->x->endElement(); // close advertiser
		$this->x->endElement(); // close advertisers
		$this->x->endElement(); // close ratePeriodsBatch
	}
}

