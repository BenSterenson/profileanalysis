<?php

class Attributes implements JsonSerializable {

	#region Fields
    private $PhotoId; //photoId;
    private $Gender; //female/male;
    private $EyeColor; // RGB hex color value
    private $HairColor; // RGB hex color value
    private $HasBeard; //yes/no
    private $HasGlasses; //yes/no
    private $HasSmile; //yes/no
    private $Age;
    private $UpdateDate; //date of api check
	private $UpdatedByUser;
	#endregion Fields
	
	#region Constructors
	public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 1:
                self::__construct1($argv[0]);
                break;

            case 2:
            	self::__construct2($argv[0], $argv[1]);
            	break;

         }
    }

	public function __construct1($PhotoId) {
	    $this->PhotoId = $PhotoId;
	}

	public function __construct2($row, $update) {
		//if ($row->num_rows == 1) {
		//	$row = ($row->fetch_assoc());
			$this->PhotoId = $row['PhotoId'];
			$this->Gender = $row['Gender'];
			$this->EyeColor = $row['EyeColor'];
			$this->HairColor = $row['HairColor'];
			$this->HasBeard = $row['HasBeard'];
			$this->HasGlasses = $row['HasGlasses'];
			$this->HasSmile = $row['HasSmile'];
			$this->Age = $row['Age'];
		//}
		return;
    }



	#endregion Constructors
	
	#region Setters
	function setPhotoId($PhotoId) {
		$this->PhotoId = $PhotoId;
	}
	function setGender($Gender) {
		$this->Gender = $Gender;
	}
	function setEyeColor($EyeColor) {
		$this->EyeColor = $EyeColor;
	}
	function setHairColor($HairColor) {
		$this->HairColor = $HairColor;
	}
	function setHasBeard($HasBeard) {
		$this->HasBeard = $HasBeard;
	}
	function setHasGlasses($HasGlasses) {
		$this->HasGlasses = $HasGlasses;
	}
	function setHasSmile($HasSmile) {
		$this->HasSmile = $HasSmile;
	}
	function setAge($Age) {
		$this->Age = $Age;
	}
	function setUpdateDate($UpdateDate) {
		$this->UpdateDate = $UpdateDate;
	}
	function setUpdatedByUser($UpdatedByUser) {
		$this->UpdatedByUser = $UpdatedByUser;
	}
	#endregion Setters

	#region Getters
	function getPhotoId() {
		return $this->PhotoId;
	}
	function getGender() {
		return $this->Gender;
	}
	function getEyeColor() {
		return $this->EyeColor;
	}
	function getHairColor() {
		return $this->HairColor;
	}
	function getHasBeard() {
		return $this->HasBeard;
	}
	function getHasGlasses() {
		return $this->HasGlasses;
	}
	function getHasSmile() {
		return $this->HasSmile;
	}
	function getAge() {
		return $this->Age;
	}
	function getUpdateDate() {
		return $this->UpdateDate;
	}
	function getUpdatedByUser() {
		return $this->UpdatedByUser;
	}
	#endregion Getters
	
	#region Methods
	public function jsonSerialize() {
        return Array(
		   'PhotoId'		=> $this->PhotoId + 0,
           'Gender'			=> $this->Gender,
           'EyeColor'  		=> $this->EyeColor,
           'HairColor' 		=> $this->HairColor,
		   'HasBeard' 		=> $this->HasBeard,
		   'HasGlasses' 	=> $this->HasGlasses,
		   'HasSmile' 		=> $this->HasSmile,
		   'Age' 			=> $this->Age + 0,
		   'UpdateDate' 	=> $this->UpdateDate + 0,
		   'UpdatedByUser' 	=> $this->UpdatedByUser
        );
    }
	
	function __toString() { 
        return "PhotoId : " . $this->PhotoId . " <br>
        Gender : " . $this->Gender . " <br>
        Eye Color : " . $this->EyeColor . " <br>
        Hair Color : " . $this->HairColor . " <br>
        Has Beard : " . $this->HasBeard . " <br>
        Has Glasses : " . $this->HasGlasses . " <br>
        Has Smile : " . $this->HasSmile . " <br>
        Age : " . $this->Age . " <br>
        Update Date : " . $this->UpdateDate . " <br>
        Updated By User : " . $this->UpdatedByUser . " <br>";
    } 
	#endregion Methods
}
?>