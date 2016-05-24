<?php
include 'DbWrapper.php';

require_once 'abstract_api.php';
class API extends abstract_api
{
	public static $myDbWrapper;
	
    public function __construct($request, $origin) {
        $myDbWrapper = 
		parent::__construct($request);
    }

	#region attributes count functions:
	
    protected static function getColors($attributeName)
	{
		$colorCountsArray = API::$myDbWrapper->getNumberByAtt($attributeName);
		
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
			return API::getColors("EyeColor");
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
			return API::getColors("HairColor");
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
            $genderCountArray = API::$myDbWrapper->getNumberByAtt("Gender");
			
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
            $glassesCountArray = API::$myDbWrapper->getNumberByAtt("HasGlasses");
			
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
            $beardCountArray = API::$myDbWrapper->getNumberByAtt("HasBeard");
			
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
            $smileCountArray = API::$myDbWrapper->getNumberByAtt("HasSmile");
			
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
    
	protected function getAge()
	{
        if ($this->method == 'GET')
		{
            $ageCountArray = API::$myDbWrapper->getNumberByAtt("Age");
			
			$myArray = array(
				"17-"	=> $ageCountArray[0],
				"18-24"	=> $ageCountArray[1],
				"25-35"	=> $ageCountArray[2],
				"36-45"	=> $ageCountArray[3],
				"46-55"	=> $ageCountArray[4],
				"56+"	=> $ageCountArray[5]);
			
			return json_encode($myArray);
        }
		else 
		{
            return "Only accepts GET requests";
        }
     }
	
	protected function getHistory($FBID)
	{
		if ($this->method == 'GET')
		{
			$HistoryArr = API::$myDbWrapper->getHistory($FBID);
			$myArray = array(
				"AttributeName" => $HistoryArr[0],
				"FilterValue" => $HistoryArr[1]);
			return json_encode($myArray);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	protected function getPhotoComments($PhotoID)
	{
		if ($this->method == 'GET')
		{
			$PhotoComments = API::$myDbWrapper->getPhotoComments($PhotoID);
			$myArray = array(
				"Comment" => $PhotoComments[0],
				"FirstName" => $PhotoComments[1],
				"LastName" => $PhotoComments[2],
				"PhotoLink" => $PhotoComments[3]);
			return json_encode($myArray);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	protected function getPhotoRatings()
	{
		if ($this->method == 'GET')
		{
			$RatingsArr = API::$myDbWrapper->getPhotoRatings($PhotoId);
			$myArray = array(
				"Hot" => $RatingsArr[0],
				"Not" => $RatingsArr[1]);
			return json_encode($myArray);
		
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}

	protected function insertComment($PhotoID, $FacebookId, $Comment, $Time) 
	{
		if ($this->method == 'GET')
		{
			$PhotoComments = API::$myDbWrapper->insertComment($PhotoID, $FacebookId, $Comment, $Time);
			return;
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}

	protected function GetPhotos($gender = -1, $eyeColor = -1, $hairColor = -1, $hasBeard = -1, $hasGlasses = -1, $hasSmile = -1 ,$age = -1) 
	{
		if ($this->method == 'GET')
		{
			$PhotosArr = API::$myDbWrapper->GetPhotos($gender, $eyeColor, $hairColor, $hasBeard, $hasGlasses, $hasSmile ,$age);
			return;
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	#endregion
	
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
 API::$myDbWrapper = new DbWrapper();
 
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