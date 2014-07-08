<?php
/**
 * File for class NewyseServiceEnumObjectCleaningStatus
 * @package NewyseService
 * @subpackage Enumerations
 */
/**
 * This class stands for NewyseServiceEnumObjectCleaningStatus originally named objectCleaningStatus
 * @package NewyseService
 * @subpackage Enumerations
 */
class NewyseServiceEnumObjectCleaningStatus extends NewyseServiceWsdlClass
{
    /**
     * Constant for value 'CLEAN'
     * @return string 'CLEAN'
     */
    const VALUE_CLEAN = 'CLEAN';
    /**
     * Constant for value 'TO_BE_CLEANED'
     * @return string 'TO_BE_CLEANED'
     */
    const VALUE_TO_BE_CLEANED = 'TO_BE_CLEANED';
    /**
     * Constant for value 'IN_PROGRESS'
     * @return string 'IN_PROGRESS'
     */
    const VALUE_IN_PROGRESS = 'IN_PROGRESS';
    /**
     * Constant for value 'CHECKED'
     * @return string 'CHECKED'
     */
    const VALUE_CHECKED = 'CHECKED';
    /**
     * Constant for value 'POSTPONED'
     * @return string 'POSTPONED'
     */
    const VALUE_POSTPONED = 'POSTPONED';
    /**
     * Constant for value 'OCCUPIED_CLEAN'
     * @return string 'OCCUPIED_CLEAN'
     */
    const VALUE_OCCUPIED_CLEAN = 'OCCUPIED_CLEAN';
    /**
     * Return true if value is allowed
     * @uses NewyseServiceEnumObjectCleaningStatus::VALUE_CLEAN
     * @uses NewyseServiceEnumObjectCleaningStatus::VALUE_TO_BE_CLEANED
     * @uses NewyseServiceEnumObjectCleaningStatus::VALUE_IN_PROGRESS
     * @uses NewyseServiceEnumObjectCleaningStatus::VALUE_CHECKED
     * @uses NewyseServiceEnumObjectCleaningStatus::VALUE_POSTPONED
     * @uses NewyseServiceEnumObjectCleaningStatus::VALUE_OCCUPIED_CLEAN
     * @param mixed $_value value
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return in_array($_value,array(NewyseServiceEnumObjectCleaningStatus::VALUE_CLEAN,NewyseServiceEnumObjectCleaningStatus::VALUE_TO_BE_CLEANED,NewyseServiceEnumObjectCleaningStatus::VALUE_IN_PROGRESS,NewyseServiceEnumObjectCleaningStatus::VALUE_CHECKED,NewyseServiceEnumObjectCleaningStatus::VALUE_POSTPONED,NewyseServiceEnumObjectCleaningStatus::VALUE_OCCUPIED_CLEAN));
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
