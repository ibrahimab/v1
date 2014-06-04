<?php
ini_set('memory_limit','512M');

/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/NewyseServiceAutoload.php';

class AlpinRentals {
    
    private static $_SESSION_KEY = null;
    private $_DATA = null;
    
    const WSDL_SESSION_TYPE = "normal";
    const WSDL_USERNAME = "chaletnl";
    const WSDL_PASSWORD = "chaletnl";
    const WSDL_LANGUAGE = "nl";
    const WSDL_DISTRIBUTIONCHANNELCODE = "TOCHA";
    
    public function __construct() {
        $this->newyseServiceServiceGet = new NewyseServiceServiceGet();

    }
    
    /**
     * Get the session key.
     * @return string
     */
    private function getSessionKey() {

        if(self::$_SESSION_KEY == null){
            
            $session_criteria = new NewyseServiceStructSessionCriteria(self::WSDL_SESSION_TYPE, self::WSDL_USERNAME, self::WSDL_PASSWORD, self::WSDL_LANGUAGE, self::WSDL_DISTRIBUTIONCHANNELCODE);
            
            $newyseServiceServiceCreate = new NewyseServiceServiceCreate();
            
            if($newyseServiceServiceCreate->createSession($session_criteria))
                self::$_SESSION_KEY = $newyseServiceServiceCreate->getResult();
            else
                 return $newyseServiceServiceCreate->getLastError();
        }
        
        return self::$_SESSION_KEY;
}
    /**
     * Returns the service status. Doesn't require auth. 
     * @return type
     */
    public function getStatus() {
        
        if($this->newyseServiceServiceGet->getInfo())
            return $this->newyseServiceServiceGet->getResult();
        else
            return $this->newyseServiceServiceGet->getLastError();
    }
    
    /**
     * Search for an accommodation - it doesn't work.
     * @param type $resortCode
     * @param type $startDate
     * @param type $endDate
     * @return type
     */
    public function findAccommodationTypes($resortCode, $startDate, $endDate) {
        
        $newyseServiceServiceFind = new NewyseServiceServiceFind();
        
        $newyseServiceServiceFind = new NewyseServiceServiceFind();
        $searchCriteria = new NewyseServiceStructAccommodationTypeSearchCriteria(null, null, $resortCode, date("Y-m-d\TH:i:s", strtotime($startDate)), date("Y-m-d\TH:i:s", strtotime($endDate)));

        if($newyseServiceServiceFind->findAccommodationTypes($this->getSessionKey(), $searchCriteria))
            return $newyseServiceServiceFind->getResult();
        else
            return $newyseServiceServiceFind->getLastError();
    }
    
    /**
     * Get accommodation based on a general criteria
     * @param type $criteria
     * @return type
     */
    public function getAcc($criteria){
        if($this->newyseServiceServiceGet->getAccommodationTypes($this->getSessionKey(), $criteria))
            return $this->newyseServiceServiceGet->getResult()->AccommodationTypes->AccommodationTypeItem;
        else
            return $this->newyseServiceServiceGet->getLastError();
    }
    
    /**
     * Get accommodations based on resort code
     * @param type $resortCode
     * @return type
     */
    public function getAccommodationsTypes($resortCode){
        $criteria = new NewyseServiceStructAccommodationTypeCriteria(null, $resortCode);
        return $this->getAcc($criteria);
    }
    
    /**
     * Get all resorts
     * @return type
     */
    public function getResorts() {
        $resortCriteria = new NewyseServiceStructResortCriteria(null, null, false);
        if($this->newyseServiceServiceGet->getResorts($this->getSessionKey(), $resortCriteria))
            return $this->newyseServiceServiceGet->getResult()->Resorts->ResortItem;
        else
            return $this->newyseServiceServiceGet->getLastError();
    }
    
    /**
     * Get an accommodation type based on resource id.
     * @param type $accCode
     * @return type
     */
    public function getAccommodationType($accCode) {
        $criteria = new NewyseServiceStructAccommodationTypeCriteria($accCode);
        $acc = $this->getAcc($criteria);
        return $acc[0];
    }
    
    /**
     * Retrieve Availabilities and Prices
     * @param string $acCode
     */
    private function retrieveData($accCode, $startDate, $endDate, $nrOfNights = 7){
        if($this->_DATA[$accCode][$startDate][$endDate] == null){
            $availabilityCriteria = new NewyseServiceStructAvailabilityCriteria($accCode, null, $startDate, $endDate, $nrOfNights);
            if($this->newyseServiceServiceGet->getResourceAvailability($this->getSessionKey(), $availabilityCriteria))
                 $this->_DATA[$accCode][$startDate][$endDate] = $this->newyseServiceServiceGet->getResult();
            else
                 return $this->newyseServiceServiceGet->getLastError();
        }
        
        return $this->_DATA[$accCode][$startDate][$endDate];
    }
    
    /**
     * Get availabilities for an accommodation. 
     * @param type $accommodationCode
     * @param type $startDate
     * @param type $endDate
     * @return type
     */
    public function getAvailability($accommodationCode, $startDate, $endDate){
        $availability = $this->retrieveData($accommodationCode, $startDate, $endDate);
        if(isset($availability->Availabilities->AvailabilityItem)){
            foreach($availability->Availabilities->AvailabilityItem as $item)
                if(date("w", strtotime($item->ArrivalDate)) == 6){
                    $dates[strtotime($item->ArrivalDate)] = 1;
                }
        }
        $day = strtotime("next Saturday", strtotime($startDate));
        
        while($day <= strtotime($endDate)){
            
            if(!isset($dates[$day]))
                $dates[$day] = 0;
            $day = strtotime("next Saturday", $day);
        }
        
        return $dates;
    }
    
    /**
     * Get Prices for an accommodation
     * @param type $accommodationCode
     * @param type $startDate
     * @param type $endDate
     * @return type
     */
    public function getPrices($accommodationCode, $startDate, $endDate){
        $data = $this->retrieveData($accommodationCode, $startDate, $endDate);
        if(isset($data->Availabilities->AvailabilityItem)){
            foreach($data->Availabilities->AvailabilityItem as $item){
                if(date("w", strtotime($item->ArrivalDate)) == 6) {
                    $prices[strtotime($item->ArrivalDate)] = $item->Prices->PriceItem[0]->PriceInclusive;
                }
            }
        }
        return $prices;
    }
    
    public function getAddress($addressManagerId){
        if($this->newyseServiceServiceGet->getAddress($this->getSessionKey(), new NewyseServiceStructAddressCriteria($addressManagerId)))
            return $this->newyseServiceServiceGet->getResult();
        else
            return $this->newyseServiceServiceGet->getLastError();
    }
    
    function __destruct() {
        $newyseServiceServiceDestroy = new NewyseServiceServiceDestroy();
        if($newyseServiceServiceDestroy->destroySession($this->getSessionKey()))
            $newyseServiceServiceDestroy->getResult();
        else
            return $newyseServiceServiceDestroy->getLastError();
    }
}

?>
