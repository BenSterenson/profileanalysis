<?php
include "dbWrapper.php";


//## test 1 ###
//retrieve user from sql and update first name last name by sending num:
// 0 - dont update retrieve from database
// 1 - update scrape facebook
$row = 'SELECT * FROM `users` WHERE `FacebookId` = 502424842';
$dbWrapper = new DbWrapper();
$result = $dbWrapper->execute($row);
$foo = new Facebook_user($result, 0);
echo $foo;
//## end test 1 ###

//## test 2 ###
$dbWrapper = new DbWrapper();
//$dbWrapper->addDupToNoProfilePic();
$dbWrapper->desableDupPhotos();
//## end test 2 ###

?> 