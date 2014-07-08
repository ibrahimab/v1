<?php
/**
 * File for class NewyseServiceStructReservation
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservation originally named Reservation
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservation extends NewyseServiceWsdlClass
{
    /**
     * The ReservationId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ReservationId;
    /**
     * The ReservationNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ReservationNumber;
    /**
     * The ArrivalDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $ArrivalDate;
    /**
     * The DepartureDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $DepartureDate;
    /**
     * The BookDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $BookDate;
    /**
     * The Status
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Status;
    /**
     * The TotalPrice
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $TotalPrice;
    /**
     * The CustomerTotalPrice
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $CustomerTotalPrice;
    /**
     * The AgentTotalPrice
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $AgentTotalPrice;
    /**
     * The PayingCustomerTotalPrice
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $PayingCustomerTotalPrice;
    /**
     * The Resort
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Resort;
    /**
     * The DistributionChannel
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $DistributionChannel;
    /**
     * The BillLines
     * @var NewyseServiceStructBillLines
     */
    public $BillLines;
    /**
     * The AgentBillLines
     * @var NewyseServiceStructAgentBillLines
     */
    public $AgentBillLines;
    /**
     * The PayingCustomerBillLines
     * @var NewyseServiceStructPayingCustomerBillLines
     */
    public $PayingCustomerBillLines;
    /**
     * The Infotexts
     * @var NewyseServiceStructInfotexts
     */
    public $Infotexts;
    /**
     * The ReservedResources
     * @var NewyseServiceStructReservedResources
     */
    public $ReservedResources;
    /**
     * The Customer
     * @var NewyseServiceStructCustomer
     */
    public $Customer;
    /**
     * Constructor method for Reservation
     * @see parent::__construct()
     * @param long $_reservationId
     * @param string $_reservationNumber
     * @param dateTime $_arrivalDate
     * @param dateTime $_departureDate
     * @param dateTime $_bookDate
     * @param string $_status
     * @param double $_totalPrice
     * @param double $_customerTotalPrice
     * @param double $_agentTotalPrice
     * @param double $_payingCustomerTotalPrice
     * @param string $_resort
     * @param string $_distributionChannel
     * @param NewyseServiceStructBillLines $_billLines
     * @param NewyseServiceStructAgentBillLines $_agentBillLines
     * @param NewyseServiceStructPayingCustomerBillLines $_payingCustomerBillLines
     * @param NewyseServiceStructInfotexts $_infotexts
     * @param NewyseServiceStructReservedResources $_reservedResources
     * @param NewyseServiceStructCustomer $_customer
     * @return NewyseServiceStructReservation
     */
    public function __construct($_reservationId = NULL,$_reservationNumber = NULL,$_arrivalDate = NULL,$_departureDate = NULL,$_bookDate = NULL,$_status = NULL,$_totalPrice = NULL,$_customerTotalPrice = NULL,$_agentTotalPrice = NULL,$_payingCustomerTotalPrice = NULL,$_resort = NULL,$_distributionChannel = NULL,$_billLines = NULL,$_agentBillLines = NULL,$_payingCustomerBillLines = NULL,$_infotexts = NULL,$_reservedResources = NULL,$_customer = NULL)
    {
        parent::__construct(array('ReservationId'=>$_reservationId,'ReservationNumber'=>$_reservationNumber,'ArrivalDate'=>$_arrivalDate,'DepartureDate'=>$_departureDate,'BookDate'=>$_bookDate,'Status'=>$_status,'TotalPrice'=>$_totalPrice,'CustomerTotalPrice'=>$_customerTotalPrice,'AgentTotalPrice'=>$_agentTotalPrice,'PayingCustomerTotalPrice'=>$_payingCustomerTotalPrice,'Resort'=>$_resort,'DistributionChannel'=>$_distributionChannel,'BillLines'=>$_billLines,'AgentBillLines'=>$_agentBillLines,'PayingCustomerBillLines'=>$_payingCustomerBillLines,'Infotexts'=>$_infotexts,'ReservedResources'=>$_reservedResources,'Customer'=>$_customer),false);
    }
    /**
     * Get ReservationId value
     * @return long|null
     */
    public function getReservationId()
    {
        return $this->ReservationId;
    }
    /**
     * Set ReservationId value
     * @param long $_reservationId the ReservationId
     * @return long
     */
    public function setReservationId($_reservationId)
    {
        return ($this->ReservationId = $_reservationId);
    }
    /**
     * Get ReservationNumber value
     * @return string|null
     */
    public function getReservationNumber()
    {
        return $this->ReservationNumber;
    }
    /**
     * Set ReservationNumber value
     * @param string $_reservationNumber the ReservationNumber
     * @return string
     */
    public function setReservationNumber($_reservationNumber)
    {
        return ($this->ReservationNumber = $_reservationNumber);
    }
    /**
     * Get ArrivalDate value
     * @return dateTime|null
     */
    public function getArrivalDate()
    {
        return $this->ArrivalDate;
    }
    /**
     * Set ArrivalDate value
     * @param dateTime $_arrivalDate the ArrivalDate
     * @return dateTime
     */
    public function setArrivalDate($_arrivalDate)
    {
        return ($this->ArrivalDate = $_arrivalDate);
    }
    /**
     * Get DepartureDate value
     * @return dateTime|null
     */
    public function getDepartureDate()
    {
        return $this->DepartureDate;
    }
    /**
     * Set DepartureDate value
     * @param dateTime $_departureDate the DepartureDate
     * @return dateTime
     */
    public function setDepartureDate($_departureDate)
    {
        return ($this->DepartureDate = $_departureDate);
    }
    /**
     * Get BookDate value
     * @return dateTime|null
     */
    public function getBookDate()
    {
        return $this->BookDate;
    }
    /**
     * Set BookDate value
     * @param dateTime $_bookDate the BookDate
     * @return dateTime
     */
    public function setBookDate($_bookDate)
    {
        return ($this->BookDate = $_bookDate);
    }
    /**
     * Get Status value
     * @return string|null
     */
    public function getStatus()
    {
        return $this->Status;
    }
    /**
     * Set Status value
     * @param string $_status the Status
     * @return string
     */
    public function setStatus($_status)
    {
        return ($this->Status = $_status);
    }
    /**
     * Get TotalPrice value
     * @return double|null
     */
    public function getTotalPrice()
    {
        return $this->TotalPrice;
    }
    /**
     * Set TotalPrice value
     * @param double $_totalPrice the TotalPrice
     * @return double
     */
    public function setTotalPrice($_totalPrice)
    {
        return ($this->TotalPrice = $_totalPrice);
    }
    /**
     * Get CustomerTotalPrice value
     * @return double|null
     */
    public function getCustomerTotalPrice()
    {
        return $this->CustomerTotalPrice;
    }
    /**
     * Set CustomerTotalPrice value
     * @param double $_customerTotalPrice the CustomerTotalPrice
     * @return double
     */
    public function setCustomerTotalPrice($_customerTotalPrice)
    {
        return ($this->CustomerTotalPrice = $_customerTotalPrice);
    }
    /**
     * Get AgentTotalPrice value
     * @return double|null
     */
    public function getAgentTotalPrice()
    {
        return $this->AgentTotalPrice;
    }
    /**
     * Set AgentTotalPrice value
     * @param double $_agentTotalPrice the AgentTotalPrice
     * @return double
     */
    public function setAgentTotalPrice($_agentTotalPrice)
    {
        return ($this->AgentTotalPrice = $_agentTotalPrice);
    }
    /**
     * Get PayingCustomerTotalPrice value
     * @return double|null
     */
    public function getPayingCustomerTotalPrice()
    {
        return $this->PayingCustomerTotalPrice;
    }
    /**
     * Set PayingCustomerTotalPrice value
     * @param double $_payingCustomerTotalPrice the PayingCustomerTotalPrice
     * @return double
     */
    public function setPayingCustomerTotalPrice($_payingCustomerTotalPrice)
    {
        return ($this->PayingCustomerTotalPrice = $_payingCustomerTotalPrice);
    }
    /**
     * Get Resort value
     * @return string|null
     */
    public function getResort()
    {
        return $this->Resort;
    }
    /**
     * Set Resort value
     * @param string $_resort the Resort
     * @return string
     */
    public function setResort($_resort)
    {
        return ($this->Resort = $_resort);
    }
    /**
     * Get DistributionChannel value
     * @return string|null
     */
    public function getDistributionChannel()
    {
        return $this->DistributionChannel;
    }
    /**
     * Set DistributionChannel value
     * @param string $_distributionChannel the DistributionChannel
     * @return string
     */
    public function setDistributionChannel($_distributionChannel)
    {
        return ($this->DistributionChannel = $_distributionChannel);
    }
    /**
     * Get BillLines value
     * @return NewyseServiceStructBillLines|null
     */
    public function getBillLines()
    {
        return $this->BillLines;
    }
    /**
     * Set BillLines value
     * @param NewyseServiceStructBillLines $_billLines the BillLines
     * @return NewyseServiceStructBillLines
     */
    public function setBillLines($_billLines)
    {
        return ($this->BillLines = $_billLines);
    }
    /**
     * Get AgentBillLines value
     * @return NewyseServiceStructAgentBillLines|null
     */
    public function getAgentBillLines()
    {
        return $this->AgentBillLines;
    }
    /**
     * Set AgentBillLines value
     * @param NewyseServiceStructAgentBillLines $_agentBillLines the AgentBillLines
     * @return NewyseServiceStructAgentBillLines
     */
    public function setAgentBillLines($_agentBillLines)
    {
        return ($this->AgentBillLines = $_agentBillLines);
    }
    /**
     * Get PayingCustomerBillLines value
     * @return NewyseServiceStructPayingCustomerBillLines|null
     */
    public function getPayingCustomerBillLines()
    {
        return $this->PayingCustomerBillLines;
    }
    /**
     * Set PayingCustomerBillLines value
     * @param NewyseServiceStructPayingCustomerBillLines $_payingCustomerBillLines the PayingCustomerBillLines
     * @return NewyseServiceStructPayingCustomerBillLines
     */
    public function setPayingCustomerBillLines($_payingCustomerBillLines)
    {
        return ($this->PayingCustomerBillLines = $_payingCustomerBillLines);
    }
    /**
     * Get Infotexts value
     * @return NewyseServiceStructInfotexts|null
     */
    public function getInfotexts()
    {
        return $this->Infotexts;
    }
    /**
     * Set Infotexts value
     * @param NewyseServiceStructInfotexts $_infotexts the Infotexts
     * @return NewyseServiceStructInfotexts
     */
    public function setInfotexts($_infotexts)
    {
        return ($this->Infotexts = $_infotexts);
    }
    /**
     * Get ReservedResources value
     * @return NewyseServiceStructReservedResources|null
     */
    public function getReservedResources()
    {
        return $this->ReservedResources;
    }
    /**
     * Set ReservedResources value
     * @param NewyseServiceStructReservedResources $_reservedResources the ReservedResources
     * @return NewyseServiceStructReservedResources
     */
    public function setReservedResources($_reservedResources)
    {
        return ($this->ReservedResources = $_reservedResources);
    }
    /**
     * Get Customer value
     * @return NewyseServiceStructCustomer|null
     */
    public function getCustomer()
    {
        return $this->Customer;
    }
    /**
     * Set Customer value
     * @param NewyseServiceStructCustomer $_customer the Customer
     * @return NewyseServiceStructCustomer
     */
    public function setCustomer($_customer)
    {
        return ($this->Customer = $_customer);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservation
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
