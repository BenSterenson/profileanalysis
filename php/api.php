<?php
include 'DbWrapper.php';
include("../APi/api.php");

require_once 'abstract_api.php';
class API extends abstract_api
{
    public function __construct($request, $origin) {
        parent::__construct($request);

    }

    /**
     * Get Eye Color
     */
     protected function getEyeColors() {
        if ($this->method == 'GET') {
            return "Success";
        } else {
            return "Only accepts GET requests";
        }
     }

     protected function getHairColors() {
        if ($this->method == 'GET') {
            return "Success";
        } else {
            return "Only accepts GET requests";
        }
     }
    protected function getGender() {
        if ($this->method == 'GET') {
            return "Success";
        } else {
            return "Only accepts GET requests";
        }
     }

    protected function getGlasses() {
        if ($this->method == 'GET') {
            return "Success";
        } else {
            return "Only accepts GET requests";
        }
     }
    
    protected function getGlasses() {
        if ($this->method == 'GET') {
            return "Success";
        } else {
            return "Only accepts GET requests";
        }
     }
	 
	 protected function get_tiny_url($url)  {  
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}


	protected function insert_attributes($id, $debugPrint = 1) {
		// connect to DB 
		$dbWrapper = new DbWrapper();
		//$getUrlQuery = 'SELECT `FacebookPhotoId`, `PhotoLink` FROM `Photos` WHERE `Id` = '. $id;
		$getUrlQuery = "SELECT `PhotoLink` FROM `Photos` AS a
						WHERE NOT EXISTS(SELECT *
						FROM NoProfilePic AS b WHERE a.FacebookPhotoId = b.FakePhotoId)
						AND `Id` = $id";
		// run Query
		if($debugPrint == 1){
			echo "$getUrlQuery <br>";
		}

		$result = $dbWrapper->execute($getUrlQuery);

		if ($result->num_rows == 0) {
			//no profile pic
			return;
		}

		$row = ($result->fetch_assoc());
		$picUrl = $row['PhotoLink']; // extracted link

		$picUrl = get_tiny_url($picUrl);
		if($debugPrint == 1){
			echo "pic url: ".$picUrl. "<br>";
		}
		chdir('../APi/');

		// run in betaface
		$api = new betaFaceApi($id);
		$face = $api->get_Image_attributes($picUrl);

		if($debugPrint == 1){
			echo $api->image_Attributes;
		}

		$setIsValidPhoto = 0;

		if($face != -1) {
			// face found
			if($debugPrint == 1){
				echo "face found!!!! <br>";
			}
			$setIsValidPhoto = 1;
			$dbWrapper->insert($api->image_Attributes);
		}
		$updateQuery = "UPDATE `Photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
		if($debugPrint == 1){
			echo "$updateQuery <br><br>";
		}
		$result = $dbWrapper->execute($updateQuery);
		return;
	} 
	 
	protected function addAttributes() {
		if ($this->method == 'GET') {
			return $this->insert_attributes($this->args[0]);
		} 
	}
 }
 
 // Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new API($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
 ?>