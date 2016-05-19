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


	protected function insert_attributes($id) {
		// connect to DB 
		$dbWrapper = new DbWrapper();
		//$getUrlQuery = 'SELECT `FacebookPhotoId`, `PhotoLink` FROM `Photos` WHERE `Id` = '. $id;
		$getUrlQuery = "SELECT `PhotoLink` FROM `Photos` AS a
						WHERE NOT EXISTS(SELECT *
						FROM NoProfilePic AS b WHERE a.FacebookPhotoId = b.FakePhotoId)
						AND `Id` = $id";
		// run Query
		//echo "$getUrlQuery <br>";
		$result = $dbWrapper->execute($getUrlQuery);

		if ($result->num_rows == 0) {
			//no profile pic
			return;
		}

		$row = ($result->fetch_assoc());
		$picUrl = $row['PhotoLink']; // extracted link

		$picUrl = $this->get_tiny_url($picUrl);
		//echo "pic url: ".$picUrl. "<br>";
		chdir('../APi/');

		// run in betaface
		$api = new betaFaceApi($id);
		$face = $api->get_Image_attributes($picUrl);
		//echo $api->image_Attributes;
		$setIsValidPhoto = 0;
		//echo $face." <br>";
		if($face != -1 || $face != false) {
			//echo "face found!!!! <br>";
			// face found
			$setIsValidPhoto = 1;
			$dbWrapper->insert($api->image_Attributes);
			$updateQuery = "UPDATE `Photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
			$result = $dbWrapper->execute($updateQuery);

			return 1;
		}
		if($face == false){
			//echo "API call to upload image failed! : $id";
		}

		$updateQuery = "UPDATE `Photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
		//echo "$updateQuery <br><br>";
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