<?php
/**
 * File for class NewyseServiceStructObjectCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjectCriteria originally named ObjectCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjectCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ResourceId;
    /**
     * The ObjectId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ObjectId;
    /**
     * Constructor method for ObjectCriteria
     * @see parent::__construct()
     * @param long $_resourceId
     * @param long $_objectId
     * @return NewyseServiceStructObjectCriteria
     */
    public function __construct($_resourceId = NULL,$_objectId = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ObjectId'=>$_objectId),false);
    }
    /**
     * Get ResourceId value
     * @return long|null
     */
    public function getResourceId()
    {
        return $this->ResourceId;
    }
    /**
     * Set ResourceId value
     * @param long $_resourceId the ResourceId
     * @return long
     */
    public function setResourceId($_resourceId)
    {
        return ($this->ResourceId = $_resourceId);
    }
    /**
     * Get ObjectId value
     * @return long|null
     */
    public function getObjectId()
    {
        return $this->ObjectId;
    }
    /**
     * Set ObjectId value
     * @param long $_objectId the ObjectId
     * @return long
     */
    public function setObjectId($_objectId)
    {
        return ($this->ObjectId = $_objectId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjectCriteria
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
