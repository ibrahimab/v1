<?php

/**
* XML-export for affiliate-provider TradeTracker
*
* @author: Jeroen Boschman (jeroen@webtastic.nl)
* @since: 2015-05-29 22:00
*/

class xmlTradetracker extends xmlExport
{

	/**  name of the export (used in the filename for caching)  */
	protected $name = "tradetracker";

	/**
	 * call the parent constructor
	 *
	 * @return void
	 */
	function __construct()
	{

		parent::__construct();

	}

	/**
	 * create XML according to TradeTracker-structure
	 * and store data in parent object $this->x (XMLWriter)
	 *
	 * @return void
	 */
	protected function createSpecificXML()
	{

		$this->x->startElement('productFeed');
		$this->x->writeAttribute('created', date("c"));

		if (is_array($this->type_data)) {
			foreach ($this->type_data as $type_id => $type_data) {

				if( is_array($this->type_price[$type_id]) ) {

					$this->x->startElement('product');

					$this->x->writeElement('ID', $type_data['type_id']);
					$this->x->writeElement('name', $type_data['fullname']);
					$this->x->writeElement('description', $type_data['description']);
					$this->x->writeElement('productURL', $type_data['url']);

					$this->x->writeElement('imageURL', $type_data['main_image']);

					if( is_array($type_data['extra_image']) ) {
						foreach ($type_data['extra_image'] as $fototeller => $imgage_URL) {
							# Op Chalet.be maximaal 10 foto's (op verzoek van TradeTracker)
							if($fototeller<=10 or $this->website<>"B") {
								$this->x->writeElement('imageURL', $imgage_URL);
							}
						}
					}


					$this->x->writeElement('continent', iconv("Windows-1252", "UTF-8", txt("europa", "xml")));
					$this->x->writeElement('country', $type_data['land']);
					$this->x->writeElement('isoCodeDeparture', '');
					$this->x->writeElement('isoCodeArrival', $type_data['isocode']);
					$this->x->writeElement('region', $type_data['skigebied']);
					$this->x->writeElement('province', '');
					$this->x->writeElement('city', $type_data['plaats']);
					$this->x->writeElement('latitude', $type_data['gps_lat']);
					$this->x->writeElement('longitude', $type_data['gps_long']);

					$this->x->writeElement('accommodationType', $type_data['accommodationType']);
					$this->x->writeElement('unitType', $type_data['unitType']);
					$this->x->writeElement('serviceType', $type_data['serviceType']);
					$this->x->writeElement('transportType', 'ev');
					$this->x->writeElement('categoryPath', '');
					$this->x->writeElement('categories', '');
					$this->x->writeElement('subcategories', '');
					$this->x->writeElement('subsubcategories', '');
					$this->x->writeElement('lastMinute', '');
					$this->x->writeElement('flightIncluded', '0');
					$this->x->writeElement('busIncluded', '0');
					$this->x->writeElement('trainIncluded', '0');

					// petsAllowed
					if( $this->type_kenmerken[$type_id]["huisdieren-toegestaan"] ) {
						$petsAllowed="1";
					} else {
						$petsAllowed="";
					}
					$this->x->writeElement('petsAllowed', $petsAllowed);

					$this->x->writeElement('smokingAllowed', '');
					$this->x->writeElement('childrenAllowed', '');
					$this->x->writeElement('childFriendly', '');

					// Wi-Fi
					if( $this->type_kenmerken[$type_id]["internet-via-wifi"] ) {
						$wifi="1";
					} else {
						$wifi="";
					}
					$this->x->writeElement('wifi', $wifi);

					// Swimming pool
					if( $this->type_kenmerken[$type_id]["zwembad"] or $this->type_kenmerken[$type_id]["zwembad-prive"] ) {
						$swimmingPool="1";
					} else {
						$swimmingPool="";
					}
					$this->x->writeElement('swimmingPool', $swimmingPool);

					// facilities
					foreach ($this->facilites_show_array as $key => $value) {
						// echo $value." ";
						if( $this->type_kenmerken[$type_id][$value] ) {
							$this->x->writeElement('facilities', iconv("Windows-1252", "UTF-8", txt($value, "kenmerken")));
						}
					}

					$this->x->writeElement('stars', $type_data['kwaliteit']);
					$this->x->writeElement('rating', $type_data['totaaloordeel']);
					$this->x->writeElement('distanceToCentre', '');
					$this->x->writeElement('distanceToSea', '');
					$this->x->writeElement('distanceToPool', '');
					$this->x->writeElement('minPersons', '1');
					$this->x->writeElement('maxPersons', $type_data['maxaantalpersonen']);


					// additional
					$this->x->startElement('additional');
					$this->x->writeElement("persons_from", $type_data["optimaalaantalpersonen"]);
					$this->x->writeElement("persons_to", $type_data["maxaantalpersonen"]);
					$this->x->writeElement("accommodationType_full", $this->config->soortaccommodatie[$type_data["soortaccommodatie"]]);
					$this->x->writeElement("region_id", $type_data["skigebied_id"]);
					$this->x->writeElement("city_id", $type_data["plaats_id"]);
					$this->x->writeElement('FromPrice_description', $type_data['vanaf']);
					if( $type_data['special_offer']['description'] ) {
						$this->x->writeElement('special_offer_description', $type_data['special_offer']['description']);
					}
					if( $type_data['special_offer']['percentage'] ) {
						$this->x->writeElement('special_offer_percentage', $type_data['special_offer']['percentage']);
					}
					if( $type_data['special_offer']['euro'] ) {
						$this->x->writeElement('special_offer_euro', $type_data['special_offer']['euro']);
					}

					$this->x->writeElement('GroupID', $type_data['accommodatie_id']);

					if( is_array( $type_data["distance"]) ) {

						foreach ($type_data["distance"] as $key => $value) {
							$this->x->writeElement($key, $value);
						}
					}
					$this->x->endElement(); // close additional


					$this->x->writeElement('priceType', $type_data['price_text']);
					if( is_array($this->type_bkk[$type_id])) {

						$last_seizoen_id = max( array_keys( $this->type_bkk[$type_id] ) );

						// included in price
						if( is_array( $this->type_bkk[$type_id][$last_seizoen_id]["included"] )) {
							$this->x->startElement('includedInPrice');
							foreach ($this->type_bkk[$type_id][$last_seizoen_id]["included"] as $key => $value) {
								$this->x->writeElement('value', $value);
							}
							$this->x->endElement(); // close includedInPrice
						} else {
							$this->x->writeElement('includedInPrice', '');
						}

						// excluded from price
						if( is_array( $this->type_bkk[$type_id][$last_seizoen_id]["excluded"] )) {

							$this->x->startElement('excludedFromPrice');

							foreach ($this->type_bkk[$type_id][$last_seizoen_id]["excluded"] as $key => $value) {

								$unit  = '';
								$price = '';

								if (isset($this->type_bkk[$type_id][$last_seizoen_id]['excluded_price'][$key]) && $this->type_bkk[$type_id][$last_seizoen_id]['excluded_price'][$key] > 0) {

									$price = $this->type_bkk[$type_id][$last_seizoen_id]['excluded_price'][$key];
									$price = (' ' . (html_entity_decode('&euro;')) . sprintf('%.2f', $price));
								}

								if (isset($this->type_bkk[$type_id][$last_seizoen_id]['unit'][$key])) {
									$unit = (' ' . $this->type_bkk[$type_id][$last_seizoen_id]['unit'][$key]);
								}

								$this->x->writeElement('value', $value . $unit . $price);
							}

							$this->x->endElement(); // close excludedFromPrice

						} else {
							$this->x->writeElement('excludedFromPrice', '');
						}

					} else {
						$this->x->writeElement('includedInPrice', '');
						$this->x->writeElement('excludedFromPrice', '');
					}


					$this->x->startElement('variations');

					foreach ($this->type_price[$type_id] as $key => $value) {

						$this->x->startElement('variation');

						$this->x->writeElement('arrivalDate', date("d-m-Y", $this->type_arrival[$type_id][$key]));
						$this->x->writeElement('returnDate', date("d-m-Y", $this->type_departure[$type_id][$key]));

						$this->x->writeElement('duration', $this->type_number_of_nights[$type_id][$key]);
						$this->x->writeElement('durationType', iconv("Windows-1252", "UTF-8", txt("nachten", "xml")));


						$this->x->writeElement('price', number_format($this->type_price[$type_id][$key], 2, ".", ""));

						$this->x->endElement(); // close variation

					}

					$this->x->endElement(); // close variations

					$this->x->endElement(); // close product

				}
			}
		}

		$this->x->endElement(); // close productFeed
	}

}
