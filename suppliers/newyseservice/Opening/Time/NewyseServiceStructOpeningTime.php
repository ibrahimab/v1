<?php
/**
 * File for class NewyseServiceStructOpeningTime
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructOpeningTime originally named OpeningTime
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructOpeningTime extends NewyseServiceWsdlClass
{
    /**
     * The StartTime
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $StartTime;
    /**
     * The EndTime
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $EndTime;
    /**
     * The Location
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Location;
    /**
     * The DayString
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $DayString;
    /**
     * The DateString
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $DateString;
    /**
     * The Comments
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Comments;
    /**
     * Constructor method for OpeningTime
     * @see parent::__construct()
     * @param string $_startTime
     * @param string $_endTime
     * @param string $_location
     * @param string $_dayString
     * @param string $_dateString
     * @param string $_comments
     * @return NewyseServiceStructOpeningTime
     */
    public function __construct($_startTime = NULL,$_endTime = NULL,$_location = NULL,$_dayString = NULL,$_dateString = NULL,$_comments = NULL)
    {
        parent::__construct(array('StartTime'=>$_startTime,'EndTime'=>$_endTime,'Location'=>$_location,'DayString'=>$_dayString,'DateString'=>$_dateString,'Comments'=>$_comments),false);
    }
    /**
     * Get StartTime value
     * @return string|null
     */
    public function getStartTime()
    {
        return $this->StartTime;
    }
    /**
     * Set StartTime value
     * @param string $_startTime the StartTime
     * @return string
     */
    public function setStartTime($_startTime)
    {
        return ($this->StartTime = $_startTime);
    }
    /**
     * Get EndTime value
     * @return string|null
     */
    public function getEndTime()
    {
        return $this->EndTime;
    }
    /**
     * Set EndTime value
     * @param string $_endTime the EndTime
     * @return string
     */
    public function setEndTime($_endTime)
    {
        return ($this->EndTime = $_endTime);
    }
    /**
     * Get Location value
     * @return string|null
     */
    public function getLocation()
    {
        return $this->Location;
    }
    /**
     * Set Location value
     * @param string $_location the Location
     * @return string
     */
    public function setLocation($_location)
    {
        return ($this->Location = $_location);
    }
    /**
     * Get DayString value
     * @return string|null
     */
    public function getDayString()
    {
        return $this->DayString;
    }
    /**
     * Set DayString value
     * @param string $_dayString the DayString
     * @return string
     */
    public function setDayString($_dayString)
    {
        return ($this->DayString = $_dayString);
    }
    /**
     * Get DateString value
     * @return string|null
     */
    public function getDateString()
    {
        return $this->DateString;
    }
    /**
     * Set DateString value
     * @param string $_dateString the DateString
     * @return string
     */
    public function setDateString($_dateString)
    {
        return ($this->DateString = $_dateString);
    }
    /**
     * Get Comments value
     * @return string|null
     */
    public function getComments()
    {
        return $this->Comments;
    }
    /**
     * Set Comments value
     * @param string $_comments the Comments
     * @return string
     */
    public function setComments($_comments)
    {
        return ($this->Comments = $_comments);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructOpeningTime
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
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
