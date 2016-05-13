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
#endregion Comments
//include 'Facebook_user.php';
include 'Facebook_photo.php';
//include("../APi/Attributes.php");

class DbWrapper {
	
	#region Fields
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "profileanalysis";
	
	private $allowed_tables_array = array();
	
	public $connection = null;
	#endregion Fields
	
	#region Constructors
	public function __construct() {
		$allowed_tables_array						= array('Users', 'Photos', 'PhotoAttributes');
		$allowed_columns_array['Users']				= array('FacebookId', 'FirstName', 'LastName');
		$allowed_columns_array['Photos'] 			= array('Id', 'FacebookPhotoId', 'FacebookId','UpdateDate', 'PhotoLink', 'NumOfLikes', 'IsValidPhoto');
		$allowed_columns_array['PhotoAttributes'] 	= array('Id', 'PhotoId', 'Gender', 'EyeColor', 'HasBeard', 'HasGlasses', 'HasSmile', 'Age', 'UpdateDate', 'UpdatedByUser');
	
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
			echo "<br>trying to execute: " . $string . "<br>";
			execute($string);
			return;
		}
	}
	
	private function update_cell($tableName, $keyCol, $keyValue, $coloumnName, $value) {
		if(in_array($tableName, $this->allowed_tables_array) &&
			in_array($keyCol, $allowed_columns_array[$tableName]) &&
			in_array($coloumnName, $allowed_columns_array[$tableName]))
		{
			$string = "UPDATE " . $tableName . " SET " . $coloumnName . " = \"" . $value . "\" WHERE " . $keyCol . " = \"" . $keyValue . "\"" ;
			echo "<br>trying to execute: " . $string . "<br>";	
			execute($string);
			return;
		}
	}
	#endregion Methods (private)
	
	#region Methods (public)
	public function execute($sql_string) {
		return $this->connection->query($sql_string);
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
		return $bestFit;
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
					WHERE Users.FacebookId = Photos.FacebookId AND Photos.FacebookPhotoId = PhotoAttributes.PhotoId ";
					
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
				$string = $string . " (" . $object->getUserID() . ", " . $object->getFirstName() . ", " . $object->getLastName() . ")";
				break;
				
			case "Photos":
				$string = "INSERT INTO Photos ";
				$string = $string . " (FacebookPhotoId, FacebookId, UpdateDate, PhotoLink, NumOfLikes, IsValidPhoto) VALUES ";
				$string = $string . " (" . $object->getPhotoId() . ", " . $object->getUserID() . ", " . $object->getUpdateDate() . ", " .
										$object->getPhotoLink() . ", " . $object->getNumOfLikes()  . ", " . getisValidPhoto() . ")";
				break;
			
			case "Attributes":
				$string = "INSERT INTO PhotoAttributes ";
				$string = $string . " (PhotoId, Gender, EyeColor, HairColor, HasBeard, HasGlasses, HasSmile, Age, UpdateDate, UpdatedByUser) VALUES ";
				$string = $string . " (" . $object->getPhotoId() . ", " . $object->getGender() . ", " . $object->getEyeColor() . ", " .
										$object->getHairColor() . ", " . $object->getHasBeard()  . ", " . $object->getHasGlasses() . ", " .
										$object->getHasSmile() . ", " . $object->getAge() . ", " . $object->getUpdateDate() . ", " . $object->getUpdatedByUser() . ")";

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

		$sqlFindDup = ' SELECT `Id` FROM `photos` WHERE `FacebookPhotoId` IN (
						SELECT `FacebookPhotoId` FROM `photos` GROUP BY `FacebookPhotoId` HAVING count(*) > 1
						)';

		$sqlSetDupZero ="UPDATE `photos`
				SET `IsValidPhoto` = 0
				WHERE `Id` IN (
					SELECT `Id` FROM ($sqlFindDup)AS `tbltmp`)";

		$this->execute($sqlSetDupZero);
	}

	public function addDupToNoProfilePic() {
		// add duplicatetd FacebookPhotoId to noprofilepic
		$sqlFindDup = 'SELECT `FacebookPhotoId`, COUNT(*) `c` FROM `photos` GROUP BY `FacebookPhotoId` HAVING c > 1';
		$result = $this->execute($sqlFindDup);
		while ($row = ($result->fetch_assoc())) {
			$FacebookPhotoId = $row['FacebookPhotoId'];
			echo "adding $FacebookPhotoId to noprofilepic <br>";
			$sqladdDupNoPic = 'INSERT INTO `noprofilepic` (`FakePhotoId`) SELECT '. $FacebookPhotoId .' FROM dual WHERE NOT EXISTS (SELECT * FROM `noprofilepic` WHERE `FakePhotoId`= ' . $FacebookPhotoId .')';
			$this->execute($sqladdDupNoPic);
		}
	}
	#endregion Methods (public)
	
}