<?php
include "dbWrapper.php";

$row = 'SELECT * FROM `users` WHERE `FacebookId` = 502424842';
$dbWrapper = new DbWrapper();
$result = $dbWrapper->execute($row);
$foo = new Facebook_user($result,0);
echo $foo;
?> 