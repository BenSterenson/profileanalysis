<?php
#region Comments
		// Users:
// KEY	// FacebookId 		int
		// FirstName 	string
		// LastName 	string
		
		// Photos:
		// Id				int
// KEY	// FacebookPhotoId 	int
		// FacebookId 		int
		// UpdateDate 		date	// text format: date("Y-m-d:H:i:s")
		// PhotoLink 		text
		// NumOfLikes 		int
		// IsValidPhoto		bool
		
		// PhotoAttributes:
// KEY	// Id 				int
		// PhotoId 			int
		// Gender 			bool	// true => male, false => female
		// EyeColor 		int
		// HairColor		int
		// HasBeard 		bool
		// HasGlasses 		bool
		// HasSmile 		bool
		// Age				int
		// UpdateDate	 	date	// text format: date("Y-m-d:H:i:s")
		// UpdatedByUser	bool
		
		// PhotoComments:
// KEY	// Id 			int 
		// Comment	 	text 
		// PhotoId 		int  
		// FacebookId 	int 
		// Time 		date
		
		// History:
// KEY	// Id 				int 
		// FacebookId 		int 
		// AttributeName 	text  
		// FilterValue	 	text 
		// SessionId 		text
		
 		// PhotoRatings:
// KEY	// Id 				int 
		// IsHot 			bool
		// PhotoId 			int 
		// FacebookId 		int
#endregion Comments

include 'Classes/Facebook_photo.php';
include 'Classes/Facebook_user.php';
include 'Classes/PhotoComments.php';
include 'Classes/History.php';
include 'Classes/PhotoRatings.php';
//include 'Classes/Attributes.php';
include '../BetafaceAPI/BetafaceAPI.php';

define("NUMOfCOLORS",11);

class DbWrapper {
	
	#region Fields
	private $servername = "profilyze.cwhbbmexocbn.eu-west-1.rds.amazonaws.com:3306";//"localhost";
	private $username = "profilyze";//"root";
	private $password = "profilyze";//"";
	private $dbname = "profilyze";//"profileanalysis";
	
	private $allowed_tables_array = array();
	
	public $connection = null;
	#endregion Fields
	
	#region Constructors
	public function __construct() {
		$this->allowed_tables_array						= array('Users', 'Photos', 'PhotoAttributes','PhotoComments','History','PhotoRatings');
		$this->allowed_columns_array['Users']			= array('FacebookId', 'FirstName', 'LastName');
		$this->allowed_columns_array['Photos'] 			= array('Id', 'FacebookPhotoId', 'FacebookId','UpdateDate', 'PhotoLink', 'NumOfLikes', 'IsValidPhoto');
		$this->allowed_columns_array['PhotoAttributes'] = array('Id', 'PhotoId', 'Gender', 'EyeColor', 'HasBeard', 'HasGlasses', 'HasSmile', 'Age', 'UpdateDate', 'UpdatedByUser');
		$this->allowed_columns_array['PhotoComments'] 	= array('Id','FacebookId','Comment','PhotoId','Time');
		$this->allowed_columns_array['History'] 		= array('Id','FacebookId','AttributeName','FilterValue','SessionId');
		$this->allowed_columns_array['PhotoRatings'] 	= array('Id','IsHot','PhotoId','FacebookId');
		
		$this->connection = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($this->connection->connect_error) {
			die("Connection failed: " . $this->connection->connect_error);
			$this->connection = NULL;
		}
    }
	
	public function __destruct() {
		$this->connection->close();
	}
	#endregion Constructors
	
	#region Methods (private)
	private function update_col($tableName, $coloumnName, $value) {
		if(in_array($tableName, $allowed_tables_array) && in_array($coloumnName, $allowed_columns_array[$tableName]))
		{
			$string = "UPDATE " . $tableName . " SET " . $coloumnName . " = \"" . $value . "\"";
			//echo "<br>trying to execute: " . $string . "<br>";
			$this->execute($string);
			return;
		}
	}
	
	private function update_cell($tableName, $keyCol, $keyValue, $coloumnName, $value) {
		if(in_array($tableName, $this->allowed_tables_array) &&
			in_array($keyCol, $this->allowed_columns_array[$tableName]) &&
			in_array($coloumnName, $this->allowed_columns_array[$tableName]))
		{
			$string = "UPDATE " . $tableName . " SET " . $coloumnName . " = \"" . $value . "\" WHERE " . $keyCol . " = \"" . $keyValue . "\"" ;
			//echo "<br>trying to execute: " . $string . "<br>";	
			$this->execute($string);
			return;
		}
		//echo "<br> failed to UPDATE <br>";
	}
	#endregion Methods (private)
	
	#region Methods (public)
	public function execute($sql_string) {
		//echo "execute: ". $sql_string. "<br>";
		return $this->connection->query($sql_string);
	}

	public function ColorStrToNUM($str) {
		switch(strtolower($str)) {
			case "red":
				return 0;
			case "green":
				return 1;
			case "yellow":
				return 2;
			case "blue":
				return 3;
			case "orange":
				return 4;
			case "purple":
				return 5;
			case "pink":
				return 6;
			case "brown":
				return 7;
			case "black":
				return 8;
			case "gray":
				return 9;
			case "white":
				return 10;
			case "":
				return -1;
			case "undetermined":
				return -1;
		}
	}
	public function ColorNumToStr($num) {
		switch($num) {
			case 0:
				return "red";
			case 1:
				return "green";
			case 2:
				return "yellow";
			case 3:
				return "blue";
			case 4:
				return "orange";
			case 5:
				return "purple";
			case 6:
				return "pink";
			case 7:
				return "brown";
			case 8:
				return "black";
			case 9:
				return "gray";
			case 10:
				return "white";
			case -1:
				return "undetermined";
		}
	}
	
	public function ColorTXTtoNUM($txt) {
		switch(strtolower($txt)) {
			case "red":
				return 0xFF0000;
			case "green":
				return 0x00FF00;
			case "yellow":
				return 0xFFFF00;
			case "blue":
				return 0x0000FF;
			case "orange":
				return 0xFFA500;
			case "purple":
				return 0x800080;
			case "pink":
				return 0xFFC0CB;
			case "brown":
				return 0x964B00;
			case "black":
				return 0x000000;
			case "gray":
				return 0x808080;
			case "white":
				return 0xFFFFFF;
		}
	}
	public function ColorNUMtoTXT($num) {
		if($num == -1)
			return $this->ColorStrToNUM("undetermined");
		
		$colors = array ("red", "green", "yellow", "blue", "orange", "purple", "pink", "brown", "black", "gray", "white");
		$minDist = 0xFFFFFFF;
		$bestFit = "white"; // assuming given black as input
		
		$inR = ($num & 0xFF0000) >> 16;
		$inG = ($num & 0x00FF00) >> 8;
		$inB = ($num & 0x0000FF);
		
		foreach ($colors as $color) {
			$colorHex = $this->ColorTXTtoNUM($color);
			$curR = ($colorHex & 0xFF0000) >> 16;
			$curG = ($colorHex & 0x00FF00) >> 8;
			$curB = ($colorHex & 0x0000FF);		
			
			$curDist = sqrt(($curR - $inR)**2 + ($curG - $inG)**2 + ($curB - $inB)**2);
			if ($curDist < $minDist) {
				$minDist = $curDist;
				$bestFit = $color;
			}
		}
		return $this->ColorStrToNUM($bestFit);
	}
					
	// function overrides database information with object's fields.
	public function update($object)	{
		switch (get_class($object))
		{
			case "Facebook_user":
				$tableName  = "Users";
				$primaryKey = "FacebookId";
				$primaryVal = $object->getUserID();
				$this->update_cell($tableName, $primaryKey, $primaryVal, "FirstName", $object->getFirstName());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "LastName",  $object->getLastName());
				break;
				
			case "Facebook_photo":
				$tableName  = "Photos";
				$primaryKey = "Id";
				$primaryVal = $object->getId();

				$this->update_cell($tableName, $primaryKey, $primaryVal, "FacebookPhotoId", $object->getPhotoId());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "FacebookId",  	$object->getUserID());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "UpdateDate",		$object->getUpdateDate());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "PhotoLink",		$object->getPhotoLink());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "NumOfLikes",  	$object->getNumOfLikes());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "IsValidPhoto",	$object->getIsValidPhoto());
				break;
				
			case "Attributes":
				$photoId = $object->getPhotoId();
				
				$coloumnName = "Gender";
				$value = $object->getGender();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");
				
				$coloumnName = "EyeColor";
				$value = $object->getEyeColor();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");

				$coloumnName = "HairColor";
				$value = $object->getHairColor();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");				
				
				$coloumnName = "HasBeard";
				$value = $object->getHasBeard();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");
				
				$coloumnName = "HasGlasses";
				$value = $object->getHasGlasses();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");				

				$coloumnName = "HasSmile";
				$value = $obect->getHasSmile();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");
				
				$coloumnName = "Age";
				$value = $object->getAge();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");

				$coloumnName = "UpdateDate";
				$value = $object->getUpdateDate();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");
				
				break;
			
			case "PhotoRatings":
				$tableName  = "PhotoRatings";
				$primaryKey = "Id";
				$primaryVal = $object->getId();
				$this->update_cell($tableName, $primaryKey, $primaryVal, "IsHot", $object->getIsHot());
				break;
		}
	}
	
	public function getAllPhotos($Facebook_user) {
		$id = $Facebook_user->FacebookId;
		$photos = execute("SELECT * FROM Photos WHERE Photos.FacebookId = " . $id);
		
		$photoArray = array();
		while ($row = $photos->fetch_assoc()) {
			$photoArray[] = new Facebook_photo($row);
		}
		return $photoArray;
	}
	
	public function getLastPhoto($Facebook_user) {
		$id = $Facebook_user->FacebookId;
		$result = execute("SELECT * FROM Photos WHERE FacebookId = $id ORDER BY UpdateDate DESC LIMIT 1");
		
		if ($result->num_rows > 0) {
			return new Facebook_photo($result->fetch_assoc());
		}
		return NULL;
	}
	
	public function filterBy_JSON($start, $stop, $FacebookId, $FirstName, $LastName,
						 $PhotoUpdatedDateFROM, $PhotoUpdatedDateTO, $NumOfLikesFROM, $NumOfLikesTO,
						 $Gender, $EyeColor, $HairColor, $HasBeard, $HasGlasses, $HasSmile, $AgeFROM, $AgeTO,
						 $AttUpdateDateFROM, $AttUpdateDateTO) {

		#region find a specific person
		if ($FacebookId != NULL) {
	
			$myUser = new Facebook_user($FacebookId);
			$myPhoto = new Facebook_photo($FacebookId);
	
			$personExist = $this->execute("SELECT * FROM Users WHERE Users.FacebookId = " . $FacebookId);
			
			// user exists, only update and return
			if ($personExist->num_rows > 0) {
				
				if ($myUser != NULL) {
					$this->update($myUser);
				}
				
				if ($myPhoto != NULL) {
					$photoExist = $this->execute("SELECT PhotoId as pi FROM " . $personExist . " WHERE pi = " . $myPhoto->getPhotoId());
					if ($photoExist->num_rows > 0) {
						$this->update($myPhoto);
					}
					else {
						$this->insert($myPhoto);
					}
				}
			}
			
			// user doesn't exist, insert and return
			else {
				
				if ($myUser != NULL) {
					$this->insert($myUser);
				}
				
				if ($myPhoto != NULL) {
					$this->insert($myPhoto);
				}				
			}
			
			return json_encode(array ($myUser->jsonSerialize(), $myPhoto->jsonSerialize()), JSON_NUMERIC_CHECK );
		}
		#endregion specific person
		
		#region else: filter by parameters
		$string = " SELECT *
					FROM Users, Photos, PhotoAttributes
					WHERE Users.FacebookId = Photos.FacebookId AND Photos.Id = PhotoAttributes.PhotoId ";
					
		if ($FirstName != NULL) {
			$string = $string . " AND Users.FirstName = " . "\"" . $FirstName . "\"";
		}
		if ($LastName != NULL) {
			$string = $string . " AND Users.LastName = " . "\"". $LastName . "\"";
		}
		
		if ($PhotoUpdatedDateFROM != NULL) {
			$string = $string . " AND Photos.UpdateDate >= " . $PhotoUpdatedDateFROM;
		}
		
		if ($PhotoUpdatedDateTO != NULL) {
			$string = $string . " AND Photos.UpdateDate <= " . $PhotoUpdatedDateTO;
		}
		
		if ($NumOfLikesFROM != NULL) {
			$string = $string . " AND Photos.NumOfLikes >= " . $NumOfLikesFROM;
		}
		
		if ($NumOfLikesTO != NULL) {
			$string = $string . " AND Photos.NumOfLikes <= " . $NumOfLikesTO;
		}
		if ($Gender != NULL) {
			$string = $string . " AND PhotoAttributes.Gender = " . $Gender;
		}
		
		if ($EyeColor != NULL) {
			$string = $string . " AND PhotoAttributes.EyeColor = " . $EyeColor;
		}

		if ($HairColor != NULL) {
			$string = $string . " AND PhotoAttributes.HairColor = " . $HairColor;
		}
		
		if ($HasBeard != NULL) {
			$string = $string . " AND PhotoAttributes.HasBeard = " . $HasBeard;	
		}
		
		if ($HasGlasses != NULL) {
			$string = $string . " AND PhotoAttributes.HasGlasses = " . $HasGlasses;
		}
		
		if ($HasSmile != NULL) {
			$string = $string . " AND PhotoAttributes.HasSmile = " . $HasSmile;
		}
		
		if ($AgeFROM != NULL) {
			$string = $string . " AND PhotoAttributes.Age >= " . $AgeFROM;
		}
		
		if ($AgeTO != NULL) {
			$string = $string . " AND PhotoAttributes.Age <= " . $AgeTO;
		}
		
		if ($AttUpdateDateFROM != NULL) {
			$string = $string . " AND Photos.AttUpdateDate >= " . $AttUpdateDateFROM;
		}
		
		if ($AttUpdateDateTO != NULL) {
			$string = $string . " AND Photos.AttUpdateDate >= " . $AttUpdateDateTO;
		}
		if ($AgeFROM != NULL || $AgeTO != NULL)
			$string = $string . " ORDER BY PhotoAttributes.Age ASC";


		$string = $string ." limit " . $start . ", " . $stop;
		//echo $string;
		$result = $this->execute($string);
		$rows = array();
		
		while($r = mysqli_fetch_assoc($result)) {
			$rows[] = $r;
		}

		return json_encode($rows, JSON_NUMERIC_CHECK);
		#endregion else
	}
	
	public function insert($object) {
		switch (get_class($object)) {
			case "Facebook_user":
				$string = "INSERT INTO Users ";
				$string = $string . " (FacebookId, FirstName, LastName) VALUES ";
				$string = $string . " (" . $object->getUserID() . ", '" . $object->getFirstName() . "', '" . $object->getLastName() . "')";
				break;
				
			case "Facebook_photo":
				$string = "INSERT INTO Photos ";
				$string = $string . " (FacebookPhotoId, FacebookId, UpdateDate, PhotoLink, NumOfLikes, IsValidPhoto) VALUES ";
				$string = $string . " (" . $object->getPhotoId() . ", " . $object->getUserID() . ", '" . $object->getUpdateDate() . "', '" .
										$object->getPhotoLink() . "', " . $object->getNumOfLikes()  . ", " . $object->getIsValidPhoto() . ")";
				break;
			
			case "Attributes":
				$eyeColor = $object->getEyeColor();
				$hairColor = $object->getHairColor();

				$eyeColor = $this->ColorNUMtoTXT($eyeColor);
				$hairColor = $this->ColorNUMtoTXT($hairColor);

				$object->setEyeColor($eyeColor);
				$object->setHairColor($hairColor);

				$string = "INSERT INTO PhotoAttributes ";
				$string = $string . " (PhotoId, Gender, EyeColor, HairColor, HasBeard, HasGlasses, HasSmile, Age, UpdateDate, UpdatedByUser) VALUES ";
				$string = $string . " (" . $object->getPhotoId() . ", " . $object->getGender() . ", '" . $object->getEyeColor() . "', '" .
										$object->getHairColor() . "', " . $object->getHasBeard()  . ", " . $object->getHasGlasses() . ", " .
										$object->getHasSmile() . ", " . $object->getAge() . ", '" . $object->getUpdateDate() . "', " . $object->getUpdatedByUser() . ")";
				break;
				
			case "History":
				$string = "INSERT INTO History ";
				$string = $string . " (FacebookId,AttributeName,FilterValue,SessionId) VALUES ";
				$string = $string . " (" . $object->getFBID() . ", '" . $object->getAttributeName() . "', '" .
										$object->getFilterValue() . "', '" . $object->getSessionId() . "')";
				break;
				
			case "PhotoComments":
				$string = "INSERT INTO PhotoComments ";
				$string = $string . " (FacebookId,Comment,PhotoId,Time) VALUES ";
				$string = $string . " (" . $object->getFacebookId() . ", '" . $object->getComment() . "', '" .
										$object->getPhotoId() . "', '" . $object->getTime() . "')";
				break;
				
			case "PhotoRatings":
				$string = "INSERT INTO PhotoRatings ";
				$string = $string . " (IsHot, PhotosId, FacebookId) VALUES ";
				$string = $string . " (" . $object->getIsHot() .	", " . 
										$object->getPhotoId() . ", " . $object->getFacebookId() . ")";
				break;
		}
		$this->execute($string);
	}
	
	public function desableDupPhotos() {
		// finds all users with same FacebookPhotoId and set IsValidPhoto to 0
		//		UPDATE `photos`
		//		SET `IsValidPhoto` = 0
		//		WHERE `Id` IN (
		//			SELECT `Id` FROM (
		//		    SELECT `Id` FROM `photos` WHERE `FacebookPhotoId` IN (
		//		    SELECT `FacebookPhotoId` FROM `photos` GROUP BY `FacebookPhotoId` HAVING count(*) > 1
		//			))AS `tbltmp`)

		$sqlFindDup = ' SELECT `Id` FROM `Photos` WHERE `FacebookPhotoId` IN (
						SELECT `FacebookPhotoId` FROM `Photos` GROUP BY `FacebookPhotoId` HAVING count(*) > 2
						)';

		$sqlSetDupZero ="UPDATE `Photos`
				SET `IsValidPhoto` = 0
				WHERE `Id` IN (
					SELECT `Id` FROM ($sqlFindDup)AS `tbltmp`)";

		$this->execute($sqlSetDupZero);
	}

	public function addDupToNoProfilePic() {
		// add duplicatetd FacebookPhotoId to NoProfilePic
		$sqlFindDup = 'SELECT `FacebookPhotoId`, COUNT(*) `c` FROM `Photos` GROUP BY `FacebookPhotoId` HAVING c > 2';
		$result = $this->execute($sqlFindDup);
		while ($row = ($result->fetch_assoc())) {
			$FacebookPhotoId = $row['FacebookPhotoId'];
			//echo "adding $FacebookPhotoId to NoProfilePic <br>";
			$sqladdDupNoPic = 'INSERT INTO `NoProfilePic` (`FakePhotoId`) SELECT '. $FacebookPhotoId .' FROM dual WHERE NOT EXISTS (SELECT * FROM `NoProfilePic` WHERE `FakePhotoId`= ' . $FacebookPhotoId .')';
			$this->execute($sqladdDupNoPic);
		}
	}
	public function noProfilePic(){
		$this->addDupToNoProfilePic();
		$this->desableDupPhotos();
		$string = 'SELECT Users.FacebookId , FirstName, LastName from Photos, Users
					where Users.FacebookId = Photos.FacebookId
					and Photos.FacebookPhotoId in (select * from NoProfilePic)
					order by FirstName Desc';

		$result = $this->execute($string);
		$arr = array();
		while ($row = ($result->fetch_assoc())) {
			$FacebookId = $row['FacebookId'];
			$FirstName = $row['FirstName'];
			$LastName = $row['LastName'];
			$arr[] = array($FacebookId, $FirstName, $LastName);
		}
		if(empty($arr))
			$arr = array();
		return $arr;
	}

	public function getNumberAge($att, $string) {
		//SELECT count(*) FROM PhotoAttributes where Age > 0 AND Age < 5 AND UpdatedByUser = 0
		$sql_format_s = 'SELECT count(*) as cnt FROM PhotoAttributes where  %1$s <= %2$d' . $string;
		$sql_format = 'SELECT count(*) as cnt FROM PhotoAttributes where  %1$s >= %2$d AND %1$s <= %3$d' . $string;
		$sql_format_b = 'SELECT count(*) as cnt FROM PhotoAttributes where  %1$s > %2$d' . $string;

		$age_from = 24;
		$age_to = $age_from + 10; 


		$res_arr[] = $this->execute(sprintf($sql_format_s, $att, 17));
		$res_arr[] = $this->execute(sprintf($sql_format, $att, 18, $age_from++));

		for ($i = 0; $i < 3; $i++) {
			$res_arr[] = $this->execute(sprintf($sql_format, $att, $age_from, $age_to));
			$age_from = $age_to + 1;
			$age_to += 10;
		}
		$res_arr[] = $this->execute(sprintf($sql_format_b, $att, $age_from));
		return $res_arr;
	}
	
	public function getNumberBinaryAtt($att, $string) {
		$res_arr[] = $this->execute("SELECT count(*) as cnt FROM PhotoAttributes where " . $att ." = 0" . $string);
		$res_arr[] = $this->execute("SELECT count(*) as cnt FROM PhotoAttributes where " . $att ." = 1" . $string);
		return $res_arr;
	}

	public function getNumberByColor($colorType, $string) {
		$BaseString = "SELECT count(*) as cnt FROM PhotoAttributes WHERE " . $colorType . " = ";

		for ($i = 0; $i <= NUMOfCOLORS; $i++) {
			$colorStr = $BaseString . $i . $string;
			$res_arr[] = $this->execute($colorStr);
		}
		return $res_arr;
	}

	public function getNumberByAtt($att, $arrAtt) {
		$binAttArr = array('Gender', 'HasBeard', 'HasGlasses', 'HasSmile');

		$string = $this->build_string_by_att($arrAtt);

		// Age
		if (strcmp($att,'Age') == 0) {
			$res_arr = $this->getNumberAge($att, $string);
		}

		//Gender', 'HasBeard', 'HasGlasses', 'HasSmile'
		else if (in_array($att, $binAttArr)) {
			$res_arr = $this->getNumberBinaryAtt($att, $string);
		}

		// Eyecolor, HairColor
		else {
			$color = strcmp($att,'EyeColor') == 0 ? 'EyeColor' : 'HairColor';
			$res_arr = $this->getNumberByColor($color, $string);
		}

		// create list
		$arr_length = count($res_arr);

		for ($i = 0; $i < $arr_length; $i++) {
				if($res_arr[$i]->num_rows == 0)
					return -1;
				
				$res_arr[$i] = $res_arr[$i]->fetch_assoc()["cnt"];
		}

		return $res_arr;
	}

	public function getMostLiked($limit) {
		//function returns list of top $limit liked profile pictures arr[0]-> userid, profilepic, num of likes
		//SELECT FacebookId, PhotoLink, NumOfLikes FROM profilyze.Photos ORDER BY NumOfLikes DESC LIMIT 10
		$res_arr = $this->execute("SELECT FacebookId, PhotoLink, NumOfLikes FROM Photos ORDER BY NumOfLikes DESC LIMIT " . $limit );

		while ($row = $res_arr->fetch_assoc()) {
			$FacebookId = $row['FacebookId'];
			$PhotoLink = $row['PhotoLink'];
			$NumOfLikes = $row['NumOfLikes'];
			$TopLikedArr[] = array($FacebookId, $PhotoLink, $NumOfLikes);
		}

		return $TopLikedArr;
	}

	public function getMostLikedWithAtt($limit, $gender = -1) {
		//function returns list of top $limit liked profile pictures arr[0]-> userid, profilepic, num of likes
		//SELECT FacebookId, PhotoLink, NumOfLikes FROM profilyze.Photos, profilyze.PhotoAttributes where Photos.Id = PhotoAttributes.PhotoId ORDER BY NumOfLikes DESC LIMIT 10 

		$string = 	"SELECT FacebookId, PhotoLink, NumOfLikes, PhotoId, Gender, EyeColor, HairColor, HasBeard, HasGlasses, HasBeard, HasSmile, Age, PhotoAttributes.UpdateDate, UpdatedByUser
					FROM Photos, PhotoAttributes
					where Photos.Id = PhotoAttributes.PhotoId";
		
		// filter by gender			
		if ($gender != -1) 
			$string = $string . " AND PhotoAttributes.Gender = ". $gender;
		
		$string = $string . " ORDER BY NumOfLikes DESC LIMIT " . $limit ;

		$res_arr = $this->execute($string);
		//$res_arr = $this->execute("SELECT FacebookId, PhotoLink, NumOfLikes, PhotoId, Gender, EyeColor, HairColor, HasBeard, HasGlasses, HasBeard, HasSmile, Age FROM Photos, PhotoAttributes where Photos.Id = PhotoAttributes.PhotoId ORDER BY NumOfLikes DESC LIMIT " . $limit );

		while ($row = $res_arr->fetch_assoc()) {
			$FacebookId = $row['FacebookId'];
			$PhotoLink = $row['PhotoLink'];
			$NumOfLikes = $row['NumOfLikes'];

			$att = new attributes($row,0);

			//echo $att;
			$TopLikedArr[] = array($FacebookId, $PhotoLink, $NumOfLikes, $att);
		}

		return $TopLikedArr;
	}
	
	public function getHistory($FBID)
	{
		$res_arr = $this->execute("SELECT AttributeName, FilterValue, SessionId FROM History where FacebookId = " . $FBID . " ORDER BY Id ASC");

		while ($row = $res_arr->fetch_assoc()) {
			$AttributeName = $row['AttributeName'];
			$FilterValue = $row['FilterValue'];
			$SessionId = $row['SessionId'];

			$HistoryArr[] = array(
				"AttributeName" => $AttributeName,
				"FilterValue" => $FilterValue,
				"SessionId" => $SessionId);
		}
		if(empty($HistoryArr))
			$HistoryArr = array();
		return $HistoryArr;
	
	}
	
	public function getPhotoRatings($PhotoId)
	{
		$tmp = $this->execute("SELECT count(IsHot) as cnt FROM PhotoRatings where IsHot = 1 AND PhotosId = " . $PhotoId)->fetch_assoc();
		$res_arr[] = $tmp['cnt'];
		$tmp = $this->execute("SELECT count(IsHot) as cnt FROM PhotoRatings where IsHot = 0 AND PhotosId = " . $PhotoId)->fetch_assoc();
		$res_arr[] = $tmp['cnt'];

		return $res_arr;
	}
	
	public function getPhotoComments($PhotoId) {

		$string = 	"SELECT PhotoComments.Time,PhotoComments.Comment, Users.FirstName, Users.LastName, Photos.PhotoLink
					FROM PhotoComments, Users, Photos
					where PhotoComments.PhotoId = $PhotoId
					AND PhotoComments.FacebookId = Users.FacebookId
					AND Photos.Id = (SELECT Photos.Id from Photos,PhotoComments where PhotoComments.FacebookId = Photos.FacebookId and Photos.FacebookId = Users.FacebookId ORDER BY UpdateDate DESC limit 1)
					ORDER BY PhotoComments.Time ASC";

		$res_arr = $this->execute($string);

		while ($row = $res_arr->fetch_assoc()) {
			$PhotoComments[] = array(	"Comment" => $row['Comment'],
										"FirstName" => $row['FirstName'],
										"LastName" => $row['LastName'],
										"PhotoLink" => $row['PhotoLink'],
										"Time" => $row['Time']);
		}
		if(empty($PhotoComments))
			$PhotoComments = array(NULL,NULL,NULL,NULL,NULL);
		return $PhotoComments;
	}

	public function insertComment($PhotoID, $FacebookId, $Comment) {
		$PhotoComments = new PhotoComments($PhotoID, $FacebookId, $Comment);
		$this->insert($PhotoComments);
	}

	public function InsertHistory($FacebookId, $AttributeName, $FilterValue, $SessionId) {
		$HistorySession = new History($FacebookId, $AttributeName, $FilterValue, $SessionId);
		$this->insert($HistorySession);
	}
	
	public function insertAttributesByUser($PhotoId, $Gender, $EyeColor, $HairColor, $HasBeard, $HasGlasses, $HasSmile, $Age) {
		$myAttributes = new Attributes($PhotoId);
		$myAttributes->setGender($Gender);
		$myAttributes->setEyeColor($this->ColorTXTtoNUM($this->ColorNumToStr($EyeColor)));
		$myAttributes->setHairColor($this->ColorTXTtoNUM($this->ColorNumToStr($HairColor)));
		$myAttributes->setHasBeard($HasBeard);
		$myAttributes->setHasGlasses($HasGlasses);
		$myAttributes->setHasSmile($HasSmile);
		$myAttributes->setAge($Age);
		$myAttributes->setUpdatedByUser(true);
		//echo $myAttributes;
		$this->insert($myAttributes);
	}
	
	private function verifyExistance($FB_user, $FB_photo) {
		//TODO FIX NEVER GETS TO SECOND VERIFY
	
		// assumes insert/update always works
		if (!$this->verifyExistance_user($FB_user))
			return false;

		if (!$this->verifyExistance_photo($FB_user, $FB_photo))
			return false;
		
		// success:
		return true;
	}
	
	private function verifyExistance_user($FB_user) {
		if (!$FB_user)
			return false;
		
		$FacebookId = $FB_user->getUserID();
		
		if (!$FacebookId)
			return false;
	
		$personExist = $this->execute("SELECT * FROM Users WHERE Users.FacebookId = " . $FacebookId);
		
		// user exists, only update
		if ($personExist->num_rows > 0)
			$this->update($FB_user);
		
		else
			$this->insert($FB_user);

		return true;
	}
	
	private function verifyExistance_photo($FB_user, $FB_photo) {
		if (!$FB_photo || !$FB_user)
			return false;
		
		$FacebookId = $FB_user->getUserID();
		$PhotoId	= $FB_photo->getPhotoId();
		if (!$FacebookId || !$PhotoId)
			return false;
		
		$photoExistStr = "SELECT Id as pi FROM Users, Photos 
								WHERE Users.FacebookId = Photos.FacebookId
								AND Photos.FacebookPhotoId = $PhotoId
								AND Users.FacebookId = $FacebookId";

		$photoExist = $this->execute($photoExistStr);
		
		if ($photoExist->num_rows > 0){
			$id = $photoExist->fetch_assoc()["pi"];
			$FB_photo->setId($id);

			$this->update($FB_photo);
		}
		else{
			$this->insert($FB_photo);

			$photoExist = $this->execute($photoExistStr);
			$id=$photoExist->fetch_assoc()["pi"];
			$FB_photo->setId($id);
		}
		return true;
	}

	public function extractAttByPhoto($api) {
		// send pic to betaface
		// check photo is not a fakephoto
		$id = $api->image_Attributes->getPhotoId();

		$getUrlQuery = "SELECT `PhotoLink` FROM `Photos` AS a
                WHERE NOT EXISTS(SELECT *
                FROM NoProfilePic AS b WHERE a.FacebookPhotoId = b.FakePhotoId)
                AND `Id` = " . $id;				

        $result = $this->execute($getUrlQuery);

        //no profile pic
        if ($result->num_rows == 0) 
            return -1;
        
        $row = ($result->fetch_assoc());
		$picUrl = $row['PhotoLink']; // extracted link

        $picUrl = $api->get_tiny_url($picUrl);
       	chdir('../BetafaceAPI/');

		$face = $api->get_Image_attributes($picUrl,0,1); //($picUrl,proxyuse,send)
        $setIsValidPhoto = 0;

        if($face != -1) {
            // face found
            $setIsValidPhoto = 1;
            $this->insert($api->image_Attributes);
        }
        if($face != 0){
            $updateQuery = "UPDATE `Photos` SET `IsValidPhoto` = $setIsValidPhoto WHERE `Id` = $id";
            $result = $this->execute($updateQuery);
        }
        return $setIsValidPhoto;	
	}

	
	public function login($FacebookId, $FirstName, $LastName, $NumOfLikes) {
		
		/*$string = 	"SELECT *
						FROM  Users, Photos, PhotoAttributes
						Where Users.FacebookId = $FacebookId  
						AND Users.FacebookId = Photos.FacebookId LIMIT 1";

		$result = $this->execute($string);
		// found user
		if ($result->num_rows > 0){
			$row = $result->fetch_assoc();
			return json_encode($row, JSON_NUMERIC_CHECK);
		}*/
		
		$FB_user 	= new Facebook_user($FacebookId, $FirstName, $LastName);
		$FB_photo 	= new Facebook_photo($FacebookId, $NumOfLikes);
		
		// update or insert as needed
		$exist = $this->verifyExistance($FB_user, $FB_photo); // returns true if exists


		$empty_att = new Attributes(-1);
		$result = $FB_user->jsonSerialize() + $FB_photo->jsonSerialize() + $empty_att->jsonSerialize();

		return json_encode($result);
	}

	public function extractAttributes($photoId, $iteration) {

		$skip = 0;
		
		// try to find in database:
		if ($iteration == 0) {
			$string = 	"SELECT *
						FROM  PhotoAttributes
						WHERE PhotoAttributes.PhotoId = $photoId AND PhotoAttributes.UpdatedByUser = 0";
						// old - seems unnecessary:
						// FROM  Users, Photos, PhotoAttributes
						// Where Users.FacebookId = Photos.FacebookId AND PhotoAttributes.PhotoId = $photoId AND PhotoAttributes.UpdatedByUser = 0 LIMIT 1";

	      	$result = $this->execute($string);

	   	    // found attributes in sql betaface
		    if ($result->num_rows > 0){
		    	$row = $result->fetch_assoc();
				$attributes = new Attributes($row, 0);
				$skip = 1;
		    }
		}
		// no attributes yet in sql
		if($skip == 0) {
      		$api = new betaFaceApi($photoId);
			$validPhoto = $this->extractAttByPhoto($api);
			if($validPhoto == 0 && $iteration == 0) {
				// no photo found call extractAttByPhoto again in 1 min.
				return 1;
			}
			else if($validPhoto == 0 && $iteration == 1) {
				//no face found.
				return -1;
			}
			else {
				//attributes found
				$attributes = $api->image_Attributes;
			}
		}

		$myPhoto = null;
		$string = "SELECT * FROM  Photos Where Photos.Id = $photoId";
		$result = $this->execute($string);

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$myPhoto = new Facebook_photo($row['FacebookId'], $row['FacebookPhotoId'], $row['UpdateDate'], $row['PhotoLink'], $row['NumOfLikes'], $row['IsValidPhoto']);
			$myPhoto->setId($photoId);
		}
		else {
			$myPhoto = new Facebook_photo(-1, -1, -1, -1, -1, -1);
			$myPhoto->setId(-1);
		}
		
		$myUser = null;
		$string = "SELECT * FROM  Users Where Users.FacebookId = " . $myPhoto->getUserID();
		$result = $this->execute($string);

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$myUser = new Facebook_user($row['FacebookId'], $row['FirstName'], $row['LastName']);
		}
		else {
			$myUser = new Facebook_user(-1,-1,-1);
		}

		return json_encode($myUser->jsonSerialize() + $myPhoto->jsonSerialize() + $attributes->jsonSerialize());
	}

	public function extract_html($FacebookPhotoId) {
		$profile_pic_url =  "http://en-gb.facebook.com/".$FacebookPhotoId;
		$html = get_html($profile_pic_url);
		return $html;
	}

	public function extract_name($html) {
		$name = '"ownername":';
		$name = extract_tag($name ,$html);
		return $name;
	}

	public function searchByUrl($profile_url) {
		function get_web_page( $url )
	    {
	        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
	        $options = array(
	            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
	            CURLOPT_POST           =>false,        //set to GET
	            CURLOPT_USERAGENT      => $user_agent, //set user agent
	            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
	            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
	            CURLOPT_RETURNTRANSFER => true,     // return web page
	            CURLOPT_HEADER         => false,    // don't return headers
	            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	            CURLOPT_ENCODING       => "",       // handle all encodings
	            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	            CURLOPT_TIMEOUT        => 120,      // timeout on response
	            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	        );
	        $ch      = curl_init( $url );
	        curl_setopt_array( $ch, $options );
	        $content = curl_exec( $ch );
	        $err     = curl_errno( $ch );
	        $errmsg  = curl_error( $ch );
	        $header  = curl_getinfo( $ch );
	        curl_close( $ch );
	        $header['errno']   = $err;
	        $header['errmsg']  = $errmsg;
	        $header['content'] = $content;
	        return $header;
	    }
	    
		/*Getting user id */
		$url = 'http://findmyfbid.com';
		$data = array('url' => $profile_url );
		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		function getData($data)
		{
			$dom = new DOMDocument;
			$dom -> loadHTML( $data );
			$divs = $dom -> getElementsByTagName('code');
			foreach ( $divs as $div )
			{
	            return $div -> nodeValue;
			}
		}
		$FacebookId = getData($result);  // User ID
		if ($FacebookId > 0){
			$FB_photo 	= new Facebook_photo($FacebookId);
			
			if( $FB_photo->getPhotoId() == NULL)
				return -1;
			
			$html = $this->extract_html($FB_photo->getPhotoId());
			$full_name = $this->extract_name($html);

			list($first_name, $last_name)  = array_pad(explode(" ", $full_name, 2),2 ,null);

			return $this->login($FacebookId, $first_name, $last_name, $FB_photo->getNumOfLikes());

		}
	}
	public function getAgeRange($age) {

		switch ($age) {
			case '-1':
				return array(-1,-1);
			case '0':
				return array(-1,17);
			case '1':
				return array(18,24);
			case '2':
				return array(25,35);
			case '3':
				return array(36,45);
			case '4':
				return array(46,55);
			case '5':
				return array(56,1000);
		}

	}

	public function GetPhotos($start,$stop, $Gender, $EyeColor, $hairColor, $HasBeard, $HasGlasses, $HasSmile ,$age) {

		$Gender 	= $Gender 		== -1 ? NULL : $Gender;
		$EyeColor 	= $EyeColor 	== -1 ? NULL : $EyeColor;
		$HairColor 	= $hairColor 	== -1 ? NULL : $hairColor;
		$HasBeard 	= $HasBeard 	== -1 ? NULL : $HasBeard;
		$HasGlasses = $HasGlasses 	== -1 ? NULL : $HasGlasses;
		$HasSmile 	= $HasSmile 	== -1 ? NULL : $HasSmile;
		$age 		= $age 			== -1 ? NULL : $this->getAgeRange($age);


		$res_arr = $this->filterBy_JSON($start, $stop, $FacebookId = NULL, $FirstName = NULL, $LastName = NULL,
						 $PhotoUpdatedDateFROM  = NULL, $PhotoUpdatedDateTO  = NULL, $NumOfLikesFROM = NULL, $NumOfLikesTO  = NULL,
						 $Gender, $EyeColor, $HairColor, $HasBeard, $HasGlasses, $HasSmile, $age[0], $age[1],
						 $AttUpdateDateFROM  = NULL, $AttUpdateDateTO  = NULL);

		// returns: [ {"FacebookId: Id, ... }, { ...} ]
		return $res_arr;
	}

	public function countTotalByTbl($tblName = "PhotoAttributes" , $byUser = -1) {

		$string = 	"SELECT count(*) as cnt FROM " . $tblName;

		if ($byUser != -1 && strcmp($tblName,"PhotoAttributes") == 0)
			$string = $string . " where UpdatedByUser = " . $byUser;

		$res = $this->execute($string);
		$res = $res->fetch_assoc()["cnt"];
		
		return $res;
	}

	public function build_string_by_att($arrAtt) {
		$string = "";

		$age = $this->getAgeRange($arrAtt['age']);

		if ($arrAtt['photoId'] != -1) {
			$string = $string . " AND PhotoAttributes.PhotoId = " . $arrAtt['photoId'];
			$string = $string . " AND PhotoAttributes.UpdatedByUser = 1";
		}
		else {
			$string = $string . " AND PhotoAttributes.UpdatedByUser = 0";
		}
		
		if ($arrAtt['gender'] != -1) {
			$string = $string . " AND PhotoAttributes.Gender = " . $arrAtt['gender'];
		}
		
		if ($arrAtt['eyeColor'] != -1) {
			$string = $string . " AND PhotoAttributes.EyeColor = " . $arrAtt['eyeColor'];
		}

		if ($arrAtt['hairColor'] != -1) {
			$string = $string . " AND PhotoAttributes.HairColor = " . $arrAtt['hairColor'];
		}
		
		if ($arrAtt['hasBeard'] != -1) {
			$string = $string . " AND PhotoAttributes.HasBeard = " . $arrAtt['hasBeard'];	
		}
		
		if ($arrAtt['hasGlasses'] != -1) {
			$string = $string . " AND PhotoAttributes.HasGlasses = " . $arrAtt['hasGlasses'];
		}
		
		if ($arrAtt['hasSmile'] != -1) {
			$string = $string . " AND PhotoAttributes.HasSmile = " . $arrAtt['hasSmile'];
		}

		if ($age[0] != -1) {
			$string = $string . " AND PhotoAttributes.Age >= " . $age[0];
		}
		
		if ($age[1] != -1) {
			$string = $string . " AND PhotoAttributes.Age <= " . $age[1];
		}

		return $string;
	}
	
	public function setPhotoRatings($PhotoId, $isHot, $FacebookId) {
		$photoRating = new PhotoRatings(-1, $isHot, $PhotoId, $FacebookId);
		
		$exist = $this->execute(
		"SELECT * FROM PhotoRatings WHERE PhotosId = " . $PhotoId . " AND FacebookId = " . $FacebookId);
		
		if ($exist->num_rows > 0) {
			$photoRating->setId($exist->fetch_assoc()["Id"]);
			$this->update($photoRating);
		}
		
		else {
			$this->insert($photoRating);
		}
	}
	

	private function ifNUll($att) {
		$string = "IFNULL((SELECT count($att) FROM PhotoAttributes
	where PhotoId = PI and UpdatedByUser = 1
	and $att = (select $att from PhotoAttributes where PhotoId = PI and UpdatedByUser = 0) 
	group by PhotoId),0) as c_" . $att;

		return $string;
	}
	public function buildStrMost_accurate(){
		$string0 = "SELECT * FROM (";
		$arr = array('Gender', 'EyeColor','HairColor', 'HasBeard','HasGlasses', 'HasSmile','Age');
		$len_arr = count($arr);

		$arr_p_str = "p_" . implode("+p_", $arr);
		$string1 = "SELECT * , (($arr_p_str)/$len_arr) as avg_att FROM (";

		$string2 = "SELECT PI,";

		for ($i=0; $i < $len_arr; $i++) { 
			$string2 .= "(c_"."$arr[$i] * 100 / tmp.total) as p_"."$arr[$i]";
			if ($i < $len_arr - 1)
				$string2 .= ', ';
		}
		$string2 .= " FROM (";

		$string3 = "SELECT PhotoId as PI, count(*) as total, ";

		for ($i=0; $i < $len_arr; $i++) { 
			$string3 .= $this->ifNUll($arr[$i]);
			if ($i < $len_arr - 1)
				$string3 .= ', ';
		}

		$string4 = " FROM PhotoAttributes where UpdatedByUser = 1 GROUP BY PI) as tmp ) as tmp1 ORDER BY avg_att DESC LIMIT 10";

		$string5 = ") as main, (SELECT Id, FacebookId, PhotoLink FROM Photos) as secondary WHERE main.PI = secondary.Id";

		$string = $string0 . $string1 . $string2 . $string3 . $string4 . $string5;
		return $string;
	}

	public function most_accurate(){
		$string = $this->buildStrMost_accurate();
		$result = $this->execute($string);
		$rows = array();
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return json_encode($rows);
	}
	#endregion Methods (public)

}

?>