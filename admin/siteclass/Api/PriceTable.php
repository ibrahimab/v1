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
     * @var string
     */
    const DEFAULT_CURRENCY     = 'euro';

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_GET_TABLE => [

            'method'   => 'getTable',
            'required' => ['type_id', 'season_id'],
            'optional' => ['legenda', 'weekendski', 'internal', 'availability', 'commission', 'multiple_currencies', 'active_currency'],
        ],
    ];

    /**
     * @return array
     */
    public function getTable()
    {
        $table = new \tarieventabel;
        $table->show_afwijkend_legenda  = $this->request->query->getBoolean('legenda', true);
        $table->toon_interne_informatie = $this->request->query->getBoolean('internal', false);
        $table->toon_beschikbaarheid    = $this->request->query->getBoolean('availability', false);
        $table->toon_commissie          = $this->request->query->getBoolean('commission', false);
        $table->seizoen_id              = implode(', ', [0, $this->request->query->get('season_id')]);
        $table->type_id                 = $this->request->query->get('type_id');

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
}