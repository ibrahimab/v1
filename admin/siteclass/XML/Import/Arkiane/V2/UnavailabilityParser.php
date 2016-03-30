<?php
namespace Chalet\XML\Import\Arkiane\V2;

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
    private $requiredFields = ['o_debut', 'o_fin', 'l_ref'];

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
        $element = $this->xml->LINE->Planning;

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

            $start  = \DateTime::createFromFormat('d/m/Y', trim($line->Planning->o_debut));
            $end    = \DateTime::createFromFormat('d/m/Y', trim($line->Planning->o_fin));

            $start->setTime(0, 0, 0);
            $end->setTime(0, 0, 0);

            if (false === $start || false === $end) {
                continue;
            }

            $typeID = trim($line->Planning->l_ref);

            if ($end->format('Y') > ($now->format('Y') + 1)) {

                // hoge jaartallen: niet meenemen
                $end->setTimestamp($now->getTimestamp() + $twoYears);
            }

            # Doorlopen van begin tot eind
            $weekend = $start->getTimestamp();
            $lastWeekend = $end->getTimestamp();

            // convert begin and end dates to saturdays
            if ($start->format('w') != 6) {
                $weekend = laatstezaterdag($weekend);
            }

            if ($end->format('w') != 6) {
                $lastWeekend = komendezaterdag($lastWeekend);
            }

            while ($weekend < $lastWeekend) {

                $weekendDate = new \DateTime();
                $weekendDate->setTimestamp($weekend);

                 // actual arrival date: niet_beschikbaar
                $unavailable[$typeID][$weekend] = true;

                $weekend = $weekendDate->modify('+7 days')->getTimestamp();
            }
        }

        return $unavailable;
    }

}
