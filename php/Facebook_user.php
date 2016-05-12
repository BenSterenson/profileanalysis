<?php
set_time_limit(0);

#region Global Functions
function get_html($url) {
	$proxy = '117.135.250.133';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
	curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
	curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYPORT, "8081"); // your proxy port number

	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}

function extract_tag($tag, $html){
	$data = "";
	if(preg_match('#'.$tag.'(.+?),#i',$html, $matches)){
		$data = $matches[1];
	
		// remove "" from name
		if(strcmp($tag,'"name":') == 0){
			$data = substr($matches[1],1,-1);
		}
	}
	return $data;
}

#endregion Global Functions

class Facebook_user implements JsonSerializable { 

	#region Fields
    private $FacebookId = NULL;
    private $FirstName = NULL;
    private $LastName = NULL;
	#endregion Fields
	
	#region Constructors
	public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 1:
                self::__construct1($argv[0]);
                break;
            case 3:
                self::__construct2( $argv[0], $argv[1], $argv[2] );
         }
    }
 
	public function __construct1($userID) {
        $this->FacebookId = $userID;
		$this->extract_profile_info();
    }
	
	public function __construct2($userID, $FirstName, $LastName) {
        $this->FacebookId = $userID;
		$this->FirstName = $FirstName;
		$this->LastName = $LastName;
    }
	#endregion Constructors
	
	#region Setters
	function setUserID($userID) {
		$this->FacebookId = $userID;
	}
	function setFirstName($FirstName) {
		$this->FirstName = $FirstName;
	}
	function setLastName($LastName) {
		$this->LastName = $LastName;
	}
	#endregion Setters

	#region Getters
	function getUserID() {
		return $this->FacebookId;
	}
	function getFirstName() {
		return $this->FirstName;
	}
	function getLastName() {
		return $this->LastName;
	}
	#endregion Getters
	
	#region Methods
	public function jsonSerialize() {
        return Array(
           'FacebookId'	=> $this->FacebookId + 0,
           'FirstName'  => $this->FirstName,
           'LastName' 	=> $this->LastName
        );
    }
	
	function extract_profile_info() {
		$profile_url =  "http://www.facebook.com/".$this->FacebookId;
	
		if (strcmp($profile_url,'http://www.facebook.com/') == 0){
			echo "to delete - no data - insert null";
			// insert to db
		}
		echo $profile_url;
		$html = get_html($profile_url);
		$name_tag = '"name":';
		$full_name = extract_tag($name_tag, $html);

		list($first_name, $last_name)  = array_pad(explode(" ", $full_name, 2),2 ,null);
		
		$this->FirstName = $first_name;
		$this->LastName = $last_name;
	}
	
	//############### print ###############//
    function __toString() { 
        return "FacebookId : " . $this->FacebookId . " <br>First Name : " . $this->FirstName . " <br>Last Name : " . $this->LastName; 
    } 
	#endregion Methods
} 

?> 
