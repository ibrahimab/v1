<?php

namespace Chalet\Api;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class PriceTable extends Endpoint
{
    /**
     * @var integer
     */
    const API_METHOD_GET_TABLE = 1;

    /**
     * @var integer
     */
    const API_METHOD_GET_TOTAL_PRICE = 2;

    /**
     * @var string
     */
    const DEFAULT_CURRENCY     = 'euro';

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_GET_TABLE => [

            'method'   => 'getTable',
            'required' => ['type_id'],
            'optional' => ['date', 'number_of_persons', 'legenda', 'weekendski', 'internal', 'availability', 'commission', 'multiple_currencies', 'active_currency'],
        ],

        self::API_METHOD_GET_TOTAL_PRICE => [

            'method'   => 'getTotalPrice',
            'required' => ['type_id', 'season_id_inquery', 'number_of_persons', 'date'],
            'optional' => [],
        ],

    ];

    /**
     * @return array
     */
    public function getTable()
    {
        $table = new \tarieventabel;
        $table->newWebsite = true;
        $table->show_afwijkend_legenda  = $this->request->query->getBoolean('legenda', true);
        $table->toon_interne_informatie = $this->request->query->getBoolean('internal', false);
        $table->toon_beschikbaarheid    = $this->request->query->getBoolean('availability', false);
        $table->toon_commissie          = $this->request->query->getBoolean('commission', false);
        $table->type_id                 = $this->request->query->get('type_id');
        $table->aantalpersonen          = $this->request->query->get('number_of_persons');
        $table->aankomstdatum           = $this->request->query->get('date');

        if (true === $this->request->query->getBoolean('weekendski', false)) {
            $table->show_afwijkend_legenda = false;
        }

        if (true === $this->request->query->getBoolean('multiple_currencies', false)) {

            $table->meerdere_valuta = true;
            $table->actieve_valuta  = $this->request->query->get('active_currency', self::DEFAULT_CURRENCY);
        }

        $result = [

            'html'              => $table->toontabel(),
            'real_prices_shown' => false,
            'prices_shown'      => null,
            'offer_active'      => false,
        ];

        if ($table->tarieven_getoond) {

            $result['prices_shown']      = $table->tarieven_getoond;
            $result['real_prices_shown'] = true;

            if ($table->aanbieding_actief) {
                $result['offer_active'] = true;
            }
        }

        return $result;
    }

    /**
     * price table: click to show total amount
     *
     * @return array
     */
    public function getTotalPrice()
    {
        $totalPrice = new \tarieventabel;
        $totalPrice->newWebsite = true;

        $totalPrice->type_id = $this->request->query->get('type_id');
        $totalPrice->seizoen_id = $this->request->query->get('season_id_inquery');

        //
        // @todo: show commission for resale-websites with loged in partner
        //
        // commissie tonen aan reisagenten?
        // if($vars["wederverkoop"] and (($vars["chalettour_logged_in"] and $vars["wederverkoop_commissie_inzien"]) or ($voorkant_cms and $_COOKIE["loginuser"]["chalet"]<>15 and $_COOKIE["loginuser"]["chalet"]<>26))) {
        //     $totalPrice->toon_commissie = true;
        // }

        //
        // @todo: include_bkk (when necessary)
        //
        // if(constant("include_bkk")===true) {
        //     $return["html"]=$tarieventabel_object->info_totaalprijs($_GET["ap"], $_GET["d"]);
        // }

        $result = [
            'html' => $totalPrice->specificatie_totaalprijs_below_pricetable($this->request->query->get('number_of_persons'), $this->request->query->get('date')),
        ];

        return $result;
    }
}
