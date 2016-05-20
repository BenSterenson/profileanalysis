<?php
include 'DbWrapper.php';

require_once 'abstract_api.php';
class API extends abstract_api
{
	var $dbWrapper;
    public function __construct($request, $origin) {
        parent::__construct($request);

		$this->dbWrapper = new DbWrapper();
    }

	// building statistical distribution:
	
    protected function getColors($attributeName)
	{
		$colorCountsArray = $this->dbWrapper->getNumberByAtt($attributeName);
		
		$myArray = array(
			"red" 		=> $colorCountsArray[0],
			"green"		=> $colorCountsArray[1],
			"yellow"	=> $colorCountsArray[2],
			"blue"		=> $colorCountsArray[3],
			"orange"	=> $colorCountsArray[4],
			"purple"	=> $colorCountsArray[5],
			"pink" 		=> $colorCountsArray[6],
			"brown"		=> $colorCountsArray[7],
			"black"	 	=> $colorCountsArray[8],
			"gray" 		=> $colorCountsArray[9],
			"white" 	=> $colorCountsArray[10]);
		
		// returning json in format: {"red": 10,"black":8, ... }
		return json_encode($myArray);
	}

	protected function getEyeColors()
	{
		if ($this->method == 'GET')
		{
			return getColors("EyeColor");
        }
		else
		{
            return "Only accepts GET requests";
        }
    }
	
    protected function getHairColors()
	{
		if ($this->method == 'GET')
		{
			return getColors("HairColor");
        }
		else
		{
            return "Only accepts GET requests";
        }
    } 
	 
    protected function getGender()
	{
        if ($this->method == 'GET')
		{
            $genderCountArray = $dbWrapper->getNumberByAtt("Gender");
			
			$myArray = array(
				"female"	=> $genderCountArray[0],
				"male"		=> $genderCountArray[1]);
			
			return json_encode($myArray);
        }
		else 
		{
            return "Only accepts GET requests";
        }
     }

    protected function getGlasses()
	{
        if ($this->method == 'GET')
		{
            $glassesCountArray = $dbWrapper->getNumberByAtt("HasGlasses");
			
			$myArray = array(
				"no"	=> $glassesCountArray[0],
				"yes"	=> $glassesCountArray[1]);
			
			return json_encode($myArray);
        }
		else 
		{
            return "Only accepts GET requests";
        }
     }
	
	protected function getBeard()
	{
        if ($this->method == 'GET')
		{
            $beardCountArray = $dbWrapper->getNumberByAtt("HasBeard");
			
			$myArray = array(
				"no"	=> $beardCountArray[0],
				"yes"	=> $beardCountArray[1]);
			
			return json_encode($myArray);
        }
		else 
		{
            return "Only accepts GET requests";
        }
     }
    			
	protected function getSmile()
	{
        if ($this->method == 'GET')
		{
            $smileCountArray = $dbWrapper->getNumberByAtt("HasSmile");
			
			$myArray = array(
				"no"	=> $smileCountArray[0],
				"yes"	=> $smileCountArray[1]);
			
			return json_encode($myArray);
        }
		else 
		{
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