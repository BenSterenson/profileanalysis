<?php
set_time_limit(0);
include 'DbWrapper.php';
include("../APi/api.php");


<<<<<<< HEAD
$startAttId = 101;
$endAttId = 201;
=======
$startAttId = 31;
$endAttId = 100;
>>>>>>> 34d0e362eb7266a7329c2915880df9a4e01ca061


function get_tiny_url($url)  {  
	$ch = curl_init();  
	$timeout = 5;  
	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
	$data = curl_exec($ch);  
	curl_close($ch);  
	return $data;  
}


function insert_attributes($id) {
	// connect to DB 
	$dbWrapper = new DbWrapper();
	//$getUrlQuery = 'SELECT `FacebookPhotoId`, `PhotoLink` FROM `Photos` WHERE `Id` = '. $id;
	$getUrlQuery = "SELECT `PhotoLink` FROM `Photos` AS a
					WHERE NOT EXISTS(SELECT *
					FROM NoProfilePic AS b WHERE a.FacebookPhotoId = b.FakePhotoId)
					AND `Id` = $id";
	// run Query
	echo "$getUrlQuery <br>";
	$result = $dbWrapper->execute($getUrlQuery);

	if ($result->num_rows == 0) {
		//no profile pic
		return;
	}

	$row = ($result->fetch_assoc());
	$picUrl = $row['PhotoLink']; // extracted link

	$picUrl = get_tiny_url($picUrl);
	echo "pic url: ".$picUrl. "<br>";
	chdir('../APi/');

	// run in betaface
	$api = new betaFaceApi($id);
	$face = $api->get_Image_attributes($picUrl);
	echo $api->image_Attributes;
	$setIsValidPhoto = 0;
	echo $face." <br>";
	if($face != -1 || $face != false) {
		echo "face found!!!! <br>";
		// face found
		$setIsValidPhoto = 1;
		$dbWrapper->insert($api->image_Attributes);
	}
	if($face == false){
		echo "API call to upload image failed! : $id";
	}

	$updateQuery = "UPDATE `Photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
	echo "$updateQuery <br><br>";
	$result = $dbWrapper->execute($updateQuery);
	return;
} 

function insert_att_all_photo() {

	GLOBAL $startAttId;
	GLOBAL $endAttId;

	for ($i = $startAttId; $i <= $endAttId; $i++) {
		echo "id : $i <br>";
	    insert_attributes($i);
	    if ($id % 10 == 0){
	    	echo "sleeping for 5 sec";
	    	sleep(5);
	    }
	}

} 

insert_att_all_photo();
?> 
