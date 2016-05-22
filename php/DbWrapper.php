<?php
#region Comments
		// Users:
// KEY	// FacebookId 	int
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
		
		/* PhotoComments:
//Key	Id 			int 
		Comment 	text 
		PhotoId 	int  
		FacebookId 	int 
		Time 		date */
		
		/* History:
//Key	Id 				int 
		FacebookId 		int 
		AttributeName 	text  
		FilterValue 	text 
		SessionId 		text  */
		
/* 		PhotoRatings:
//Key	Id 				int 
		IsHot 			bool
		PhotoId 		int 
		FacebookId 		int */

	#endregion Comments
//include 'Facebook_user.php';
include 'Facebook_photo.php';
include("../APi/Attributes.php");

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
		$this->allowed_columns_array['Users']				= array('FacebookId', 'FirstName', 'LastName');
		$this->allowed_columns_array['Photos'] 			= array('Id', 'FacebookPhotoId', 'FacebookId','UpdateDate', 'PhotoLink', 'NumOfLikes', 'IsValidPhoto');
		$this->allowed_columns_array['PhotoAttributes'] 	= array('Id', 'PhotoId', 'Gender', 'EyeColor', 'HasBeard', 'HasGlasses', 'HasSmile', 'Age', 'UpdateDate', 'UpdatedByUser');
		$this->allowed_columns_array['PhotoComments'] = array('Id','FacebookId','Comment','PhotoId','Time');
		$this->allowed_columns_array['History'] = array('Id','FacebookId','AttributeName','FilterValue','SessionId');
		$this->allowed_columns_array['PhotoRatings'] = array('Id','IsHot','PhotoId','FacebookId');
		
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
				return 11;
			case "undetermined":
				return 11;
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
			case 11:
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
		if($num =="")
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
				$primaryKey = "FacebookPhotoId";
				$primaryVal = $object->getPhotoId();
				
				$this->update_cell($tableName, $primaryKey, $primaryVal, "FacebookId",   $object->getUserID());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "UpdateDate",   $object->getUpdateDate());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "PhotoLink",    $object->getPhotoLink());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "NumOfLikes",   $object->getNumOfLikes());
				$this->update_cell($tableName, $primaryKey, $primaryVal, "IsValidPhoto", $object->getIsValidPhoto());
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
				$value = $object->getHasSmile();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");
				
				$coloumnName = "Age";
				$value = $object->getAge();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");

				$coloumnName = "UpdateDate";
				$value = $object->getUpdateDate();
				execute("UPDATE PhotoAttributes SET " . $coloumnName . " = \"" . $value . "\" WHERE PhotoId = " . $photoId . " AND UpdatedByUser = false");
				
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
		$result = execute(	"SELECT * FROM " .
								" (SELECT * FROM Photos ORDER by Photos.Id DESC) " .
							" WHERE Photos.FacebookId = " . $id .
							" LIMIT 1 ");
		
		if ($result->num_rows > 0) {
			return new Facebook_photo($result->fetch_assoc());
		}
		return NULL;
	}
	
	public function filterBy_JSON($FacebookId, $FirstName, $LastName,
						 $PhotoUpdatedDateFROM, $PhotoUpdatedDateTO, $NumOfLikesFROM, $NumOfLikesTO,
						 $Gender, $EyeColor, $HasBeard, $HasGlasses, $HasSmile, $AgeFROM, $AgeTO,
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
					WHERE Users.FacebookId = photos.FacebookId AND photos.FacebookPhotoId = PhotoAttributes.PhotoId ";
					
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
			switch (strtolower($Gender[0])) {
				case 'f':
				$gender = false;
				break;
				
				default:
				$gender = true;
				break;
			}
			$string = $string . " AND PhotoAttributes.Gender = " . $gender;
		}
		
		if ($EyeColor != NULL) {
			$string = $string . " AND PhotoAttributes.EyeColor = " . "\"". $LastName . "\"";
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
			$string = $string . " AND Photos.Age >= " . $AgeFROM;
		}
		
		if ($AgeTO != NULL) {
			$string = $string . " AND Photos.Age <= " . $AgeTO;
		}
		
		if ($AttUpdateDateFROM != NULL) {
			$string = $string . " AND Photos.AttUpdateDate >= " . $AttUpdateDateFROM;
		}
		
		if ($AttUpdateDateTO != NULL) {
			$string = $string . " AND Photos.AttUpdateDate >= " . $AttUpdateDateTO;
		}

		$result = $this->execute($string);
		
		$rows = array();
		while($r = mysqli_fetch_assoc($result)) {
			$rows[] = $r;
		}
		return json_encode($rows, JSON_NUMERIC_CHECK );
		#endregion else
	}
	
	public function insert($object) {
	
		switch (get_class($object)) {
			case "Users":
				$string = "INSERT INTO Users ";
				$string = $string . " (FacebookId, FirstName, LastName) VALUES ";
				$string = $string . " (" . $object->getUserID() . ", '" . $object->getFirstName() . "', '" . $object->getLastName() . "')";
				break;
				
			case "Photos":
				$string = "INSERT INTO Photos ";
				$string = $string . " (FacebookPhotoId, FacebookId, UpdateDate, PhotoLink, NumOfLikes, IsValidPhoto) VALUES ";
				$string = $string . " (" . $object->getPhotoId() . ", " . $object->getUserID() . ", '" . $object->getUpdateDate() . "', '" .
										$object->getPhotoLink() . "', " . $object->getNumOfLikes()  . ", " . getisValidPhoto() . ")";
				break;
			
			case "Attributes":
				$string = "INSERT INTO PhotoAttributes ";
				$string = $string . " (PhotoId, Gender, EyeColor, HairColor, HasBeard, HasGlasses, HasSmile, Age, UpdateDate, UpdatedByUser) VALUES ";
				$string = $string . " (" . $object->getPhotoId() . ", " . $object->getGender() . ", '" . $this->ColorNUMtoTXT($object->getEyeColor()) . "', '" .
										$this->ColorNUMtoTXT($object->getHairColor()) . "', " . $object->getHasBeard()  . ", " . $object->getHasGlasses() . ", " .
										$object->getHasSmile() . ", " . $object->getAge() . ", '" . $object->getUpdateDate() . "', " . $object->getUpdatedByUser() . ")";
				break;
			case "History":
				$string = "INSERT INTO History ";
				$string = $string . " (Id,FacebookId,AttributeName,FilterValue,SessionId) VALUES ";
				$string = $string . " (" . $object->getId() . ", " . $object->getFBID() . ", '" . $object->getAttributeName() . "', '" .
										$object->getFilterValue() . "', " . $object->getSessionId() . ")";
				break;
			case "PhotoComments":
				$string = "INSERT INTO PhotoComments ";
				$string = $string . " (Id,FacebookId,Comment,PhotoId,Time) VALUES ";
				$string = $string . " (" . $object->getId() . ", " . $object->getFacebookId() . ", '" . $object->getComment() . "', '" .
										$object->getPhotoId() . "', " . $object->getTime() . ")";
				break;
			case "PhotoRatings":
				$string = "INSERT INTO PhotoComments ";
				$string = $string . " (Id,isHot,PhotoId,FacebookId) VALUES ";
				$string = $string . " (" . $object->getId() . ", " . $object->getIsHot() . ", '" . $object->getPhotoId() . "', '" .
										$object->getFacebookId() . ")";
				break;
		}
		////echo $string;
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
						SELECT `FacebookPhotoId` FROM `Photos` GROUP BY `FacebookPhotoId` HAVING count(*) > 1
						)';

		$sqlSetDupZero ="UPDATE `Photos`
				SET `IsValidPhoto` = 0
				WHERE `Id` IN (
					SELECT `Id` FROM ($sqlFindDup)AS `tbltmp`)";

		$this->execute($sqlSetDupZero);
	}

	public function addDupToNoProfilePic() {
		// add duplicatetd FacebookPhotoId to NoProfilePic
		$sqlFindDup = 'SELECT `FacebookPhotoId`, COUNT(*) `c` FROM `Photos` GROUP BY `FacebookPhotoId` HAVING c > 1';
		$result = $this->execute($sqlFindDup);
		while ($row = ($result->fetch_assoc())) {
			$FacebookPhotoId = $row['FacebookPhotoId'];
			//echo "adding $FacebookPhotoId to NoProfilePic <br>";
			$sqladdDupNoPic = 'INSERT INTO `NoProfilePic` (`FakePhotoId`) SELECT '. $FacebookPhotoId .' FROM dual WHERE NOT EXISTS (SELECT * FROM `NoProfilePic` WHERE `FakePhotoId`= ' . $FacebookPhotoId .')';
			$this->execute($sqladdDupNoPic);
		}
	}

	public function getNumberAge($att) {
		//SELECT count(*) FROM PhotoAttributes where Age > 0 AND Age < 5 AND UpdatedByUser = 0
		$sql_format_s = 'SELECT count(*) as cnt FROM PhotoAttributes where  %1$s <= %2$d AND UpdatedByUser = 0';
		$sql_format = 'SELECT count(*) as cnt FROM PhotoAttributes where  %1$s >= %2$d AND %1$s <= %3$d AND UpdatedByUser = 0';
		$sql_format_b = 'SELECT count(*) as cnt FROM PhotoAttributes where  %1$s > %2$d AND UpdatedByUser = 0';

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

	public function getNumberBinaryAtt($att) {
		$res_arr[] = $this->execute("SELECT count(*) as cnt FROM PhotoAttributes where " . $att ." = 0 AND UpdatedByUser = 0");
		$res_arr[] = $this->execute("SELECT count(*) as cnt FROM PhotoAttributes where " . $att ." = 1 AND UpdatedByUser = 0");
		return $res_arr;
	}

	public function getNumberByColor($colorType) {
		$BaseString = "SELECT count(*) as cnt FROM PhotoAttributes where UpdatedByUser = 0";

		for ($i = 0; $i <= NUMOfCOLORS; $i++) {
			$colorStr = $BaseString . " AND " . $colorType . " = " . $i;
			$res_arr[] = $this->execute($colorStr);
		}
		return $res_arr;
	}

	public function getNumberByAtt($att) {
		$binAttArr = array('Gender', 'HasBeard', 'HasGlasses', 'HasSmile');

		// Age
		if (strcmp($att,'Age') == 0) {
			$res_arr = $this->getNumberAge($att);
		}

		//Gender', 'HasBeard', 'HasGlasses', 'HasSmile'
		else if (in_array($att, $binAttArr)) {
			$res_arr = $this->getNumberBinaryAtt($att);
		}

		// Eyecolor, HairColor
		else {
			$color = strcmp($att,'EyeColor') == 0 ? 'EyeColor' : 'HairColor';
			$res_arr = $this->getNumberByColor($color);
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

		$string = " SELECT FacebookId, PhotoLink, NumOfLikes, PhotoId, Gender, EyeColor, HairColor, HasBeard, HasGlasses, HasBeard, HasSmile, Age
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
		$res_arr[] = $this->execute("SELECT AttributeName,FilterValue FROM History where FacebookId = " . $FBID );
		
			while ($row = $res_arr->fetch_assoc()) {
			$AttributeName = $row['AttributeName'];
			$FilterValue = $row['FilterValue'];
			$HistoryArr[] = array($AttributeName, $FilterValue);
		}
		return $HistoryArr;
	
	}
	
	public function getPhotoRatings($PhotoId)
	{
		$res_arr[] = $this->execute("SELECT count(IsHot) as cnt FROM PhotoRatings where IsHot = 1 AND PhotoId = " . $PhotoId );
		$res_arr[] = $this->execute("SELECT count(IsHot) as cnt FROM PhotoRatings where IsHot = 0 AND PhotoId = " . $PhotoId );
		return $res_arr;
	}
	
	
	#endregion Methods (public)

}