<?php
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
// KEY	// Id 			int
		// PhotoId 		int
		// Gender 		bool		// true => male, false => female
		// EyeColor 	int			// [0,Red],  [1,Green], [2,Yellow], [3,Blue], [4,Orange], [5,Purple],
		// HairColor	int			// [6,Pink], [7,Brown], [8,Black]   [9,Gray], [10,White]
		// HasBeard 	bool
		// HasGlasses 	bool
		// HasSmile 	bool
		// Age			int
		// UpdateDate 	date		// text format: date("Y-m-d:H:i:s")

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
		$allowed_columns_array['PhotoAttributes'] 	= array('Id', 'PhotoId', 'Gender', 'EyeColor', 'HasBeard', 'HasGlasses', 'HasSmile', 'Age', 'UpdateDate');
	
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
	
	function ColorTXTtoNUM($txt) {
		switch(strtolower($txt)) {
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
		}
	}
	function ColorNUMtoTXT($num) {
		switch ($num) {
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
		}
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
	
	
	

	public function filterBy_JSON($FacebookId, $FirstName, $LastName, 	// User properties
						 $PhotoUpdatedDate, $NumOfLikes, 		// Photo properties
						 $Gender, $EyeColor, $HasBeard,			// Attributes properties
						 $HasGlasses, $HasSmile, $Age,
						 $AttUpdateDate) {
		$string = " SELECT *
					FROM Users, Photos, PhotoAttributes
					WHERE Users.FacebookId = Photos.FacebookId AND Photos.FacebookPhotoId = PhotoAttributes.PhotoId ";
		
		// find a specific person
		if ($FacebookId != NULL) {
			
			// if person is in DB
				// update and return
				
			// else 
				// create and return
		}
		
		// filter by parameters
		else {
			
			if ($FirstName != NULL) {
				$string = $string . " AND Users.FirstName" . "\"" . $FirstName . "\"";
			}
			if ($LastName != NULL) {
				$string = $string . " AND Users.LastName" . "\"". $LastName . "\"";
			}
			
			if ($PhotoUpdatedDate != NULL) {
				// $comp = $PhotoUpdatedDate[0]; // > < =
				// $date = substr($PhotoUpdatedDate, 1);
				
				$string = $string . " AND Photos.UpdateDate " . $PhotoUpdatedDate;
			}
			
			if ($NumOfLikes != NULL) {
				$string = $string . " AND Photos.NumOfLikes " . $NumOfLikes;
			}

			if ($Gender != NULL) {
				switch ($Gender[0]) {
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
				$string = $string . " AND PhotoAttributes.EyeColor = ". ColorTXTtoNUM($EyeColor);
			}
			
			if ($HasBeard != NULL) {
				switch ($HasBeard[0]) {
					case 'f':
					$gender = false;
					break;
					default:
					$gender = true;
					break;
				}
				$string = $string . " AND PhotoAttributes.Gender = " . $gender;				
			}
			if ($HasGlasses != NULL)	 	{ }
			if ($HasSmile != NULL) 			{ }
			if ($Age != NULL) 				{ }
			if ($AttUpdateDate != NULL) 	{ }
		}					 
	}




						 
	/*
	function build_insert_row_string($tableName, $FacebookId, $FirstName, $LastName, $PhotoId) {
		$string = "INSERT INTO " . $tableName;
		
		switch ($tableName) {
			case "Users":
				$string = $string . " (FacebookId, FirstName, LastName) VALUES ";
				break;
			
			case "Photos":
			
			break;
			
			case "PhotoAttributes":
			
			break;
		}
		$allowed_tables_array                     = array('Users', 'Photos', 'PhotoAttributes');
		$allowed_columns_array['Users']           = array('FacebookId', 'FirstName', 'LastName');
		$allowed_columns_array['Photos']          = array('Id', 'FacebookPhotoId', 'FacebookId','UpdateDate', 'PhotoLink', 'NumOfLikes', 'IsValidPhoto');
		$allowed_columns_array['PhotoAttributes'] = array('Id', 'PhotoId', 'Gender', 'EyeColor', 'HasBeard', 'HasGlasses', 'HasSmile', 'Age', 'UpdateDate');	
	$sql = "INSERT INTO MyGuests (firstname, lastname, email)
	VALUES ('John', 'Doe', 'john@example.com')";
	}
	*/
	#endregion Methods (public)
	
}