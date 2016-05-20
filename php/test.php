<?php
include "dbWrapper.php";


/*//## test 1 ###
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
$dbWrapper->addDupToNoProfilePic();
$dbWrapper->desableDupPhotos();
//## end test 2 ###

//## test 3 ###
$dbWrapper = new DbWrapper();
echo "test color identification";
$res=$dbWrapper->ColorNUMtoTXT(0xf7e92a);
echo $res;
//## end test 2 ###

*/
//$uid = 502424842;
//$first_name = 'Omri';
//$last_name = 'Zimbler';
//$user = new Facebook_user($uid, $first_name, $last_name);
//echo $user;

$dbWrapper = new DbWrapper();
print_r($dbWrapper->getNumberByAtt('Gender'));
echo "<br>";
print_r($dbWrapper->getNumberByAtt('HasBeard'));
echo "<br>";
print_r($dbWrapper->getNumberByAtt('HasGlasses'));
echo "<br>";
print_r($dbWrapper->getNumberByAtt('HasSmile'));
echo "<br>";
print_r($dbWrapper->getNumberByAtt('Age'));
echo "<br>";
print_r($dbWrapper->getNumberByAtt('EyeColor'));
echo "<br>";
print_r($dbWrapper->getNumberByAtt('HairColor'));

?> 