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
	
    protected static function getColors($attributeName, $arr)
	{
		$colorCountsArray = API::$myDbWrapper->getNumberByAtt($attributeName, $arr);
		
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

	protected function extractVarsAtt($arr){
		$arrName = array(	'gender' => $this->args[0],
							'eyeColor' => $this->args[1],
							'hairColor' => $this->args[2],
							'hasBeard' => $this->args[3],
							'hasGlasses' => $this->args[4],
							'hasSmile' => $this->args[5],
							'age' => $this->args[6]);		
		return $arrName;
	}

	protected function getEyeColors()
	{
		$arrName = $this->extractVarsAtt($this->args);
		if ($this->method == 'GET')
		{
			return API::getColors("EyeColor", $arrName);
        }
		else
		{
            return "Only accepts GET requests";
        }
    }
	
    protected function getHairColors()
	{
		$arrName = $this->extractVarsAtt($this->args);
		if ($this->method == 'GET')
		{
			return API::getColors("HairColor", $arrName);
        }
		else
		{
            return "Only accepts GET requests";
        }
    } 
	 
    protected function getGender()
	{
		$arrName = $this->extractVarsAtt($this->args);

        if ($this->method == 'GET')
		{
            $genderCountArray = API::$myDbWrapper->getNumberByAtt("Gender", $arrName);
			
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
		$arrName = $this->extractVarsAtt($this->args);
        if ($this->method == 'GET')
		{
            $glassesCountArray = API::$myDbWrapper->getNumberByAtt("HasGlasses", $arrName);
			
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
		$arrName = $this->extractVarsAtt($this->args);
        if ($this->method == 'GET')
		{
            $beardCountArray = API::$myDbWrapper->getNumberByAtt("HasBeard", $arrName);
			
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
		$arrName = $this->extractVarsAtt($this->args);
        if ($this->method == 'GET')
		{
            $smileCountArray = API::$myDbWrapper->getNumberByAtt("HasSmile", $arrName);
			
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
		$arrName = $this->extractVarsAtt($this->args);
        if ($this->method == 'GET')
		{
            $ageCountArray = API::$myDbWrapper->getNumberByAtt("Age", $arrName);
			
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
	
	protected function getHistory()
	{
		$FBID = $this->args[0];
		if ($this->method == 'GET')
		{
			$HistoryArr = API::$myDbWrapper->getHistory($FBID);
			$myArray = array(
				"AttributeName" => $HistoryArr[0],
				"FilterValue" => $HistoryArr[1],
				"SessionId" => $HistoryArr[2]);
			return json_encode($myArray);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	protected function getPhotoComments()
	{
		$PhotoID = $this->args[0];
		if ($this->method == 'GET')
		{
			$PhotoComments = API::$myDbWrapper->getPhotoComments($PhotoID);
			$myArray = array(
				"Comment" => $PhotoComments[0],
				"FirstName" => $PhotoComments[1],
				"LastName" => $PhotoComments[2],
				"PhotoLink" => $PhotoComments[3],
				"Time" => $PhotoComments[4]);
			return json_encode($myArray);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	protected function getPhotoRatings()
	{
		$PhotoID = $this->args[0];
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

	protected function insertComment() 
	{
		$PhotoID = $this->args[0];
		$FacebookId = $this->args[1];
		$Comment = $this->args[2];

		if($PhotoID == -1 || $FacebookId == -1 || $Comment == "")
			return "Missing information";

		if ($this->method == 'GET')
		{
			$PhotoComments = API::$myDbWrapper->insertComment($PhotoID, $FacebookId, $Comment);
			return;
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	protected function InsertHistory() 
	{
		$FacebookId = $this->args[0];
		$AttributeName = $this->args[1];
		$FilterValue = $this->args[2];
		$SessionId = $this->args[3];

		if ($this->method == 'GET')
		{
			$PhotoComments = API::$myDbWrapper->InsertHistory($FacebookId, $AttributeName, $FilterValue, $SessionId);
			return;
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}

	protected function GetPhotos() 
	{
		$start = $this->args[0];
		$stop = $this->args[1] - $start;
		$gender = $this->args[2];
		$eyeColor = $this->args[3];
		$hairColor = $this->args[4];
		$hasBeard = $this->args[5];
		$hasGlasses = $this->args[6];
		$hasSmile = $this->args[7];
		$age = $this->args[8];
		
		if ($this->method == 'GET')
		{
			return API::$myDbWrapper->GetPhotos($start, $stop, $gender, $eyeColor, $hairColor, $hasBeard, $hasGlasses, $hasSmile ,$age);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}

	protected function GetTotalAttNum() 
	{	
		$tblName = $this->args[0] == -1 ? "PhotoAttributes" : $this->args[0];
		$byUser = $this->args[1];
		if ($this->method == 'GET')
		{
			$totalAtt = API::$myDbWrapper->countTotalByTbl($tblName, $byUser);
			$myArray = array("Total_$tblName" => $totalAtt);
			return json_encode($myArray);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}

	protected function getMostLiked() 
	{	
		$limit = $this->args[0] == -1 ? 10 : $this->args[0];
		$gender = $this->args[1];

		if ($this->method == 'GET')
		{
			$totalAtt = API::$myDbWrapper->getMostLikedWithAtt($limit, $gender);
			$myArray = array("Total_$tblName" => $totalAtt);
			return json_encode($myArray);
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	protected function login() 
	{	
		$FacebookId = $this->args[0];
		$FirstName = $this->args[1];
		$LastName = $this->args[2];
		$NumOfLikes = $this->args[3];

		if ($this->method == 'GET')
		{
			$totalAtt = API::$myDbWrapper->login($FacebookId, $FirstName, $LastName, $NumOfLikes);
			return;
		}		
		else 
		{
            return "Only accepts GET requests";
        }
	}
	
	
	#endregion
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