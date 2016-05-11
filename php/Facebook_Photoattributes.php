<?php
set_time_limit(0);
include 'DbWrapper.php';
include '../APi/api.php';

$startAttId = 1;
$endAttId = 2;


function update_attributes($id) {
	// connect to DB 
	$dbWrapper = new DbWrapper();
	$getUrlQuery = 'SELECT `PhotoLink` FROM `photos` WHERE `Id` = '. $id;
	echo $getUrlQuery."<br>";
	// run Query
	$result = $dbWrapper->execute($getUrlQuery);
	$row = ($result->fetch_assoc());
	$picUrl = $row['PhotoLink']; // extracted link

	echo "pic url: ".$picUrl. "<br>";
	
	// run in betaface
	$api = new betaFaceApi();
	$face = $api->get_Image_attributes($picUrl);
	if($face == 1){
		$dbWrapper->update($api);
		$updateQuery = 'UPDATE `photos` SET `IsValidPhoto` = 1 WHERE `Id` = '. $id;
		echo $updateQuery."<br>";
		$result = $dbWrapper->execute($updateQuery);
	}


} 

function update_all_pictures() {

	GLOBAL $startAttId;
	GLOBAL $endAttId;

	for ($i = $startAttId; $i <= $endAttId; $i++) {
	    update_attributes($i);
	}

} 

update_all_pictures();
?> 
