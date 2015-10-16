<?php
namespace Chalet\XML\Import\DirektHolidays;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class PricesParser
{
	/**
	 * @var SimpleXMLElement
	 */
	private $xml;

	/**
	 * @param SimpleXMLElement $xml
	 * @param boolean $test
	 */
	public function __construct($xml, $test)
	{
		$this->xml  = $xml;
		$this->test = $test;
	}
	
	/**
	 * @return array
	 */
	public function parse()
	{
        $products = $this->products();
        $rates    = $this->rates();
        $results  = [];

        foreach ($products as $productId => $rateplans) {

            foreach ($rateplans as $planId) {

                if (isset($rates[$planId])) {
                    $results[$productId] = $rates[$planId];
                }
            }
        }

        return $results;
	}
	
	/**
	 * @return array
	 */
	public function rates()
	{
		$plans      = $this->xml->RatePlans->RatePlan;
        $rates      = [];
		$attributes = [];
        $seasons    = [];
        $prices     = [];
        $result     = [];
		
		foreach ($plans as $plan) {

			$attributes['plan'] = $plan->attributes();
			$rateplanId         = (string)$attributes['plan']['RatePlanID'];
			
			// for every 'season', create array with start and end date of that season with season ID as key
			if ($rateplanId === 'seasons') {

				$rates = $plan->Rates->Rate;
				foreach ($rates as $rate) {

					$attributes['rate']        = $rate->attributes();
					$attributes['description'] = $rate->RateDescription->attributes();

					$name  = (string)$attributes['description']['Name'];
					$start = (string)$attributes['rate']['Start'];
					$end   = (string)$attributes['rate']['End'];

					if (!isset($seasons[$name])) {
						$seasons[$name] = [];
					}

					$start = \DateTime::createFromFormat('Y-m-d', $start);
					$end   = \DateTime::createFromFormat('Y-m-d', $end);
					$start = dichtstbijzijnde_zaterdag($start->getTimestamp());
					$end   = dichtstbijzijnde_zaterdag($end->getTimestamp());

					$seasons[$name][] = ['start' => $start, 'end' => $end];
				}
			}
			
			// for every rateplancode save the amount
			if ($rateplanId === 'prices') {

				$rates = $plan->Rates->Rate;
				foreach ($rates as $rate) {

					$attributes['rate'] = $rate->attributes();
					foreach ($rate->BaseByGuestAmts->BaseByGuestAmt as $amts) {

						$baseRatePlanCode = (string)$attributes['rate']['BaseRatePlanCode'];
						if (!isset($prices[$baseRatePlanCode])) {
							$prices[$baseRatePlanCode] = [];
						}

						$attributes['amts'] = $amts->attributes();
						$code               = (string)$attributes['amts']['Code'];
						$amount             = (string)$attributes['amts']['AmountAfterTax'];

						$prices[$baseRatePlanCode][$code] = $amount;
					}
				}
			}
		}
		
		// expand seasons with every weekend in an array
		// and save the price * 7 (price in XML is per night)
		foreach ($prices as $code => $priceArray) {

			if (!isset($code)) {
				$result[$code] = [];
			}

			foreach ($priceArray as $seasonId => $price) {

				$price = floatval($price);
				if ($price > 0) {

					if (isset($seasons[$seasonId])) {

						foreach ($seasons[$seasonId] as $season) {

							$weekend = $season['start'];
							while ($weekend < $season['end']) {

								$result[$code][$weekend] = ($price * 7);
								$interval		  		 = new \DateTime;
								$interval->setTimestamp($weekend);
								$weekend          		 = $interval->modify('+7 days')->getTimestamp();

								unset($interval);
							}
						}
					}
				}
			}
		}

		return $result;
	}
	
	/**
	 * @return array
	 */
    public function products()
    {
        $products = new HotelProducts($this->test);
        return $products->all();
    }
}