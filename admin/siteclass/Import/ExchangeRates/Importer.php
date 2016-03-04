<?php

namespace Chalet\Import\ExchangeRates;

/**
 * Import Exchange Rates and save in database table `diverse_instellingen`
 *
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 * @package Chalet
 **/
class Importer
{

    /**
     * @var string url to download rates
     */
    const RATES_URL = 'http://api.fixer.io/latest';

    /**
     * @var array rates to import, with corresponding database-field
     */
    private $ratesToImport = [
        'GBP' => 'wisselkoers_pond'
    ];

    /**
     * @var \DB_sql
     */
    private $db;

    /**
     * @param \DB_sql $db
     */
    function __construct(\DB_sql $db)
    {
        $this->db = $db;
    }

    /**
     * Import all exchange rates
     *
     * @return void
     **/
    public function all()
    {

        $rates_json = null;

        try {

            $ch = curl_init(self::RATES_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $rates = curl_exec($ch);

            $rates_json = json_decode($rates, true);

        } catch (Exception $e) {

        }

        if (isset($rates_json)) {

            $query = [];

            foreach ($this->ratesToImport as $currency => $fieldName) {

                if (is_float($rates_json['rates'][$currency]) && $rates_json['rates'][$currency] > 0) {
                    $query[] = ", " . $fieldName . "='" . round($rates_json['rates'][$currency], 4) . "'";
                }

            }

        }

        if (count($query) > 0) {
            $this->db->query("UPDATE diverse_instellingen SET " . ltrim(implode(',', $query), ',') . " WHERE diverse_instellingen_id=1;");
            echo $this->db->query;
        } else {
            trigger_error( "_notice: exchange rates not saved",E_USER_NOTICE );
        }
    }
}
