<?php
include 'DbWrapper.php';

function extract_html($FacebookPhotoId) {
	$profile_pic_url =  "http://en-gb.facebook.com/".$FacebookPhotoId;
	$html = get_html($profile_pic_url);
	return $html;
}
function extract_name($html) {
	$name = '"ownername":';
	$name = extract_tag($name ,$html);
	return $name;
}

function extract_likes_num($html){
	$like_tag = '"likecount":';
	$likes = extract_tag($like_tag ,$html);

	if($likes == "" || $likes == " ")
		$likes = 0;

	return $likes;
}


$file = fopen("../DB_backup/imported_data_cvs/users.txt", "r") or exit("Unable to open file!");
while(!feof($file)) {
	$line = fgets($file);
	$uid = rtrim($line, " \t\r\n");
	$dbWrapper = new DbWrapper();


	/*
	$user = new Facebook_user($uid);

	if (($user->getFirstName() == 'CanvasToBlobBundle"') || ( $user->getFirstName() == 'FileHashWorkerBundle"')) {
		$user->setFirstName("");
		$user->setLastName("");
	}
	
	echo "<br>".$user."<br>";
	//$dbWrapper->update($user);
	*/
	$IdQuery = 'SELECT `Id`, `FacebookPhotoId` FROM `photos` WHERE `FacebookId` = '. $uid;
	echo $IdQuery;
	$result = $dbWrapper->execute($IdQuery);
	$row = ($result->fetch_assoc());
	$myId = $row['Id'];
	$myPhotoId = $row['FacebookPhotoId'];

	if(($myPhotoId == 0) || ($myPhotoId == 10150004552801856) || ($myPhotoId == 10150004552801901) || ($myPhotoId == 10150004552801937))
		continue;

	echo "<br> my id: ".$myId;
	echo "<br> my photoid : ".$myPhotoId;
	$html = extract_html($myPhotoId);
	$full_name = extract_name($html);
	$likes = extract_likes_num($html);

	list($first_name, $last_name)  = array_pad(explode(" ", $full_name, 2),2 ,null);
		
	if (($first_name == 'CanvasToBlobBundle"') || ( $first_name == 'FileHashWorkerBundle"')){
		echo "<br>";
		continue;
	}

	$user = new Facebook_user($uid,$first_name,$last_name);
	echo "$user";
	echo "<br> likes : $likes <br><br>";

	$dbWrapper->update($user);

	$LikesQuery = 'UPDATE `photos` SET `NumOfLikes`= '. $likes. ' WHERE `Id`= '.$myId;
	echo $LikesQuery . "<br>";
	$dbWrapper->execute($LikesQuery);


}
?> 