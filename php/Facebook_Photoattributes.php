<?php
set_time_limit(0);
include 'DbWrapper.php';
include_once("../BetafaceAPI/BetafaceAPI.php");

$startAttId = 5977;
$endAttId = 7000;
$PROX_USE = 0;


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


function insert_attributes($id,$send) {
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
	GLOBAL $PROX_USE;
	$row = ($result->fetch_assoc());
	$picUrl = $row['PhotoLink']; // extracted link

	$picUrl = get_tiny_url($picUrl);
	echo "pic url: ".$picUrl. "<br>";
	chdir('../BetafaceAPI/');

	// run in betaface
	$api = new betaFaceApi($id);
	$send = $send == 1 ? 10 : 1; 

	$face = $api->get_Image_attributes($picUrl,$PROX_USE,$send);

	//$face = $api->get_Image_attributes($picUrl,$PROX_USE);
	echo $api->image_Attributes;
	$setIsValidPhoto = 0;

	if($face != -1) {
		// face found
		echo "face found!!!! <br>";
		$setIsValidPhoto = 1;
		$dbWrapper->insert($api->image_Attributes);
	}
	if($face != 0){
		$updateQuery = "UPDATE `Photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
		echo "$updateQuery <br><br>";
		$result = $dbWrapper->execute($updateQuery);
	}
	return;
} 

function insert_att_all_photo() {

	GLOBAL $startAttId;
	GLOBAL $endAttId;
	$count = 1;
	$read = 0;

	for ($iter = 0; $iter <= 50; $iter++) {

		echo "\n<br>#################################################<br>\n";
		echo "\n<br>#################### sending ####################<br>\n";
		echo "\n<br>#################################################<br>\n";

		for ($i = $startAttId; $i <= $endAttId; $i++) {
			ob_start();

		/*	if($count == 10) {
				$count = 1;
				echo "\n<br>#################################################<br>\n";
				echo "\n<br>#################### reading ####################<br>\n";
				echo "\n<br>#################################################<br>\n";

				echo "\n<br>sleeping 60 sec<br>\n";
				ob_end_flush();
				flush();
				$read = 1;
				sleep (60);
				break 1;
			}*/

			echo "id : $i <br>\n";
			insert_attributes($i,$read);
			// flush all output
			ob_end_flush();
			flush();
				 
				// close current session
			if (session_id()) session_write_close();
			$count ++;
		}



/*
		for ($j = $startAttId; $j <= $endAttId; $j++) {
			ob_start();
			
			if($count == 10) {
				$count = 1;
				$startAttId = $j;
				$read = 0;
				ob_end_flush();
				flush();
				break 1;
			}

			echo "id : $j <br>\n";
			insert_attributes($j,$read);
			// flush all output
			ob_end_flush();
			flush();
				 
				// close current session
			if (session_id()) session_write_close();
			$count ++;
		}*/
	}

} 
//$num = $argv[1];
//$num = 638;
//echo $num."\n";
insert_att_all_photo();
//insert_att_all_photo($num);
?> 
