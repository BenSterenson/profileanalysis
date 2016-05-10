<?php
include 'DbWrapper.php';
#include 'Facebook_user.php';
include 'Facebook_photo.php';


function extract_likes_num($FacebookPhotoId){
		$profile_pic_url =  "http://www.facebook.com/".$FacebookPhotoId;
		
		if (strcmp($profile_pic_url,'http://www.facebook.com/') == 0){
			echo "no data - insert null";
			return 0;
		}
		
		$html = get_html($profile_pic_url);
		$like_tag = '"likecount":';
		return extract_tag($like_tag ,$html);
}

$file = fopen("../fb_users/imported_data_cvs/users.txt", "r") or exit("Unable to open file!");
while(!feof($file)) {
	$line = fgets($file);
	$uid = rtrim($line, " \t\r\n");
	$user = new Facebook_user($uid);

	if (($user->getFirstName() == 'CanvasToBlobBundle"') || ( $user->getFirstName() == 'FileHashWorkerBundle"')) {
		$user->setFirstName("");
		$user->setLastName("");
	}
	
	echo "<br>".$user."<br>";
	
	$dbWrapper = new DbWrapper();
	$dbWrapper->update($user);

	$IdQuery = 'SELECT `Id`, `FacebookPhotoId` FROM `photos` WHERE `FacebookId` = '. $user->getUserID();
	echo $IdQuery;
	$result = $dbWrapper->execute($IdQuery);
	$row = ($result->fetch_assoc());
	$myId = $row['Id'];
	$myPhotoId = $row['FacebookPhotoId'];

	echo "my id: ".$myId;
	echo "my photoid : ".$myPhotoId . "<br>";
	$likes = extract_likes_num($myPhotoId);
	echo "likes". $likes . "<br>";
	$LikesQuery = 'UPDATE `photos` SET `NumOfLikes`= '. $likes. ' WHERE `Id`= '.$myId;
	echo $LikesQuery . "<br>";
	$dbWrapper->execute($LikesQuery);


}




?> 
