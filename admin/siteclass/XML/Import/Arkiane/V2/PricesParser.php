<?php
namespace Chalet\XML\Import\Arkiane\V2;

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
    private $requiredFields = ['t_debut', 't_promo_montant', 'l_ref'];

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
        $element = $this->xml->LINE->Tarif;

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

        foreach ($this->xml as $line) {

            $typeID   = trim($line->Tarif->l_ref);
            $start    = \DateTime::createFromFormat('d/m/Y', trim($line->Tarif->t_debut));
            $start->setTime(0, 0, 0);

            $unixtime = $start->getTimestamp();

            $prices[$typeID][$unixtime] = trim($line->Tarif->t_promo_montant);
        }

        return $prices;
    }
}
