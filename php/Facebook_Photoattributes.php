<?php
set_time_limit(0);
include 'DbWrapper.php';
$startAttId = 1;
$endAttId = 100;


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
