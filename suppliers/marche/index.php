<?php
class MarcheHolidays {
    private $_SERVER_URL = "api.marcheholiday.it";
    private $_SCRIPT = "/APIserver.php";
    private $_API_PORT = 80;
    private $_API_USER = "chaletmh";
    private $_API_PASS = "chalet11";
    private $_PARAMS = array();
    private $_CHAP = null;
    const INVALID_LOGIN = "invalid login";
    public function __construct() {
        
    }
    
    /**
     * Get the MarcheHolidays api url
     * @return string
     */
    private function getHostUrl() {
        $address = $this->_SERVER_URL.$this->_SCRIPT."?challenge=1";
        return $address;
    }
    
    
    private function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    /**
     * Get the challenge
     * @param string $function
     * @return string
     */
    private function getChallenge($function = "get_house_availability") {
        if($this->_CHAP == null){
            $challenge = $this->file_get_contents_curl( $this->getHostUrl() );
            if($challenge != "")
                $this->_CHAP = md5($challenge.$this->_API_PASS);
        }
        
        return $this->_CHAP;
    }
    /**
     * Sets the parameters for response
     * @param type $params
     */
    public function setParams($params){
        $this->_PARAMS = $params;
    }
    
    /**
     * Returns the XML message
     * @param type $chap
     * @param type $function
     * @return string
     */
    private function createMessage($chap, $function = "get_house_availability"){
        $xm = "<REQUEST>\n  <FUNCTION>".$function."</FUNCTION>\n";
        $xm .= "  <AUTH>\n    <CHAP>$chap</CHAP>\n    <USER>".$this->_API_USER."</USER>\n  </AUTH>\n";

        $xm .= "  <FIELDS>\n";
        foreach ($this->_PARAMS as $name => $value){
                $xm .= "    <".$name.">".$value."</".$name.">\n";
            
        }

        $xm .= "  </FIELDS>\n";
        $xm .= "</REQUEST>\n";

        $req = "POST ".$this->_SCRIPT." HTTP/1.0\r\nHost: ".$this->_SERVER_URL."\r\nContent-Type: text/xml; charset=utf-8\r\nContent-Length: ".strlen($xm)."\r\n\r\n".$xm;
       
        return $req;        
    }
        
    /**
     * Get results from server
     * @param type $message
     * @return type
     * @throws Exception
     */
    private function getResp($message){
        $fs = fsockopen($this->_SERVER_URL, $this->_API_PORT);                
        $reply = NULL;
        if ($fs) {

            fputs($fs, $message);

            while ($l = fgets($fs)){
                    $reply .= $l;
            }
        }else{
            throw new Exception('fopen socket has failed.');
        }

        $reply =  substr($reply, strpos($reply,"\r\n\r\n")+4);
        return $reply;
    }
    
    /**
     * Get an object from xml
     * @param type $response
     * @return type
     */
    private function getObject($response){
        $simpleXmlObject = simplexml_load_string($response);
        return $simpleXmlObject;
    }
    
    /**
     * Get data from server
     * @param type $function
     * @return type
     */
    public function getData($function){
        
        $chap = $this->getChallenge($function);
        
        $message = $this->createMessage($chap);
        
        $response = $this->getResp($message);

        $object = $this->getObject($response);
                
        return $object;
    }
    
    /**
     * Gets all information from MarcheHolidays
     * @param type $function
     * @return type
     */
    public function getDataFromServer($function="get_house_availability"){
        
        $object = $this->getData($function);
        
        $unixdir = dirname(dirname(dirname(__FILE__))) . "/";
                
        if($object[0] == self::INVALID_LOGIN){
            $this->_CHAP = null;
            $object = $this->getData($function);
        }
        
        return $object;
    }
    
    /**
     * Get all availabilities for an accommodation
     * @param type $accommodationCode
     * @param type $start
     * @param type $stop
     */
    public function getAvailability($accommodationCode, $start, $stop){
        $startDate = new DateTime($start);
        $endDate = new DateTime($stop);
        
        $difference = $startDate->diff($endDate);
        $numweek = (int)floor($difference->days/7);
        
        $this->setParams(array(
                            "idhouse"   => $accommodationCode,
                            "da_data"   => $start,
                            "numweeks"  => $numweek
                            ));
        
        $accommodations = $this->getDataFromServer();
        $getFirstDay = getdate(strtotime($start));
        if($getFirstDay['weekday'] != "Saturday"){
            $nextSaturday = strtotime("next Saturday", strtotime($start));
        } else {
            $nextSaturday = strtotime($start);
        }
        $lastWeek = strtotime($start . " +".$numweek." weeks");

        $accNotAvail = array();
        foreach($accommodations->DATA_ROW as $accommodation){
            $timeNotAvailable = strtotime($accommodation->from);
            
            $getThisDay = getdate($timeNotAvailable);
            
            if($getThisDay['weekday'] != "Saturday"){
                $timeNotAvailable = strtotime("last Saturday", $timeNotAvailable);
            }
            
            $accNotAvail[$timeNotAvailable] = 0;
            $intervalSaturday = strtotime("next Saturday", $timeNotAvailable);
        
            while($intervalSaturday <= strtotime($accommodation->to)){
                $accNotAvail[$intervalSaturday] = 0;
                $intervalSaturday = strtotime("next Saturday", $intervalSaturday);
            }
        }
        $allAccommodations = array();
        while($lastWeek >= $nextSaturday){
            $allAccommodations[$nextSaturday] = 1;
            $nextSaturday = strtotime("next Saturday", $nextSaturday);
        }
        
        $availabilityArray = array_replace($allAccommodations, $accNotAvail);
        
        if($this->_CHAP == null)
            $availabilityArray = array();
        
        return $availabilityArray;
    }
}

?>

