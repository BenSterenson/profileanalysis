<?php
set_time_limit(0);

$file = fopen("../DB_backup/imported_data_cvs/Sorted_ALL_UID.txt", "r") or exit("Unable to open file!");
$outputfile = fopen("../DB_backup/imported_data_cvs/updated_UID.txt", "a") or die("Unable to open file!");

//Output a line of the file until the end is reached
while(!feof($file))
  {
	$line = fgets($file);
	$uid = rtrim($line, "\r\n");

	$date_str = date("Y-m-d:H:i:s");
	$profile_pic =  "http://graph.facebook.com/".$uid."/picture?width=9999"; // large image

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$profile_pic);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_exec($ch);

	$r_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	//echo $r_url ."<br>";
	curl_close($ch);
	
	$str = $uid ."\t". $date_str ."\t". $profile_pic ."\t". $r_url;
	fwrite($outputfile, "\n". $str);

	echo $str."<br>";
  }
fclose($file);
fclose($outputfile);
?>

