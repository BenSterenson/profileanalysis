<?php
set_time_limit(0);
include 'DbWrapper.php';
include("../APi/api.php");


$startAttId = 14;
$endAttId = 20;


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
	//$getUrlQuery = 'SELECT `FacebookPhotoId`, `PhotoLink` FROM `photos` WHERE `Id` = '. $id;
	$getUrlQuery = "SELECT `PhotoLink` FROM `photos` AS a
					WHERE NOT EXISTS(SELECT *
					FROM noprofilepic AS b WHERE a.FacebookPhotoId = b.FakePhotoId)
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
	echo $api->image_Attributes ."<br>";
	$setIsValidPhoto = 0;

	if($face != -1) {
		// face found
		$setIsValidPhoto = 1;
		//$dbWrapper->insert($api->image_Attributes);
	}

	$updateQuery = "UPDATE `photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
	echo "$updateQuery<br>";
	//$result = $dbWrapper->execute($updateQuery);
	return;
} 

function insert_att_all_photo() {

	GLOBAL $startAttId;
	GLOBAL $endAttId;

	for ($i = $startAttId; $i <= $endAttId; $i++) {
		echo "id : $i <br>";
	    insert_attributes($i);
	}

} 

insert_att_all_photo();
?> 
