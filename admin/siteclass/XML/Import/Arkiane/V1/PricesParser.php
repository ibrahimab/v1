<?php
namespace Chalet\XML\Import\Arkiane\V1;

/**
 * @author  Eric Minang <eric@chalet.nl>
 * @package Chalet
 */
class PricesParser
{
    /**
     * @var SimpleXMLElement
     */
    private $xml;

    /**
     * @var SimpleXMLElement
     */
    private $requiredFields = ['ptar_debut', 'promo_montant', 'lot_ref'];

    /**
     * @param SimpleXMLElement $xml
     *
     */
    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return array
     */
    private function validate()
    {
        $element = $this->xml->Tarif;

        foreach ($this->requiredFields as $field) {

            if (!isset($element->{$field})) {
                throw new \InvalidArgumentException(sprintf('Prices feed for Arkiane is not available, validation failed for field %s', $field));
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function parse()
    {
        $this->validate();

        $prices = [];

        foreach ($this->xml as $tarif) {

            if (!isset($tarif->ptar_debut)) {
                continue;
            }

            $start    = \DateTime::createFromFormat('d/m/Y', trim($tarif->ptar_debut));
            $start->setTime(0, 0, 0);

            $unixtime = $start->getTimestamp();

            $prices[$unixtime] = trim($tarif->promo_montant);
        }

        return $prices;
    }
}
