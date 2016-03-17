<?php
namespace Chalet\XML\Import\Arkiane\V1;

use InvalidArgumentException;

/**
 * UnavailabilityParser
 *
 * @author  Eric Minang <eric@chalet.nl>
 * @package Chalet
 */
class UnavailabilityParser
{
    /**
     * @var SimpleXMLElement
     */
    protected $xml;
    /**
     * @var SimpleXMLElement
     */
    private $requiredFields = ['ocpt_debut', 'ocpt_fin', 'lot_ref'];

     /**
     * Constructor
     *
     * @param SimpleXMLElement $xml
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
        $element = $this->xml->LINE;

        foreach ($this->requiredFields as $field) {

            if (!isset($element->{$field})) {
                throw new \InvalidArgumentException(sprintf('Availability feed for Arkiane is not available, validation failed for field %s', $field));
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

        $twoYears    = 86400 * 365 * 2;
        $unavailable = [];
        $now         = new \DateTime();
        $sevenDays   = new \DateInterval('P7D');

        foreach($this->xml as $line) {

            $start  = \DateTime::createFromFormat('d/m/Y', trim($line->ocpt_debut));
            $start->setTime(0, 0, 0);

            $end    = \DateTime::createFromFormat('d/m/Y', trim($line->ocpt_fin));
            $end->setTime(0, 0, 0);

            $typeID = trim($line->lot_ref);

            if ($end->format('Y') > ($now->format('Y') + 1)) {

                // hoge jaartallen: niet meenemen
                $end->setTimestamp($now->getTimestamp() + $twoYears);
            }

            # Doorlopen van begin tot eind
            $weekend     = $start->getTimestamp();
            $lastWeekend = $end->getTimestamp();

            while ($weekend < $lastWeekend) {

                $weekendDate = new \DateTime();
                $weekendDate->setTimestamp($weekend);

                // actual arrival date: niet_beschikbaar
                $unavailable[$typeID][$weekend] = true;

                // also state Saturdays as niet_beschikbaar
                if ($weekendDate->format('w') <> 6) {

                    $altWeek = dichtstbijzijnde_zaterdag($weekend);
                    $unavailable[$typeID][$altWeek] = true;
                }

                $weekend = $weekendDate->modify('+7 days')->getTimestamp();
            }
        }

        return $unavailable;
    }

}