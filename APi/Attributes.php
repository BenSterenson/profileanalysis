<?php

class Attributes {
    
    private $Gender; //female/male;
    private $EyeColor; // RGB hex color value
    private $HairColor; // RGB hex color value
    private $HasBeard; //yes/no
    private $HasGlasses; //yes/no
    private $HasSmile; //yes/no
    private $Age;
    private $UpdateDate; //date of api check
	private $UpdatedByUser;
	
	#region Setters
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
	function getGender() {
		return $this->FacebookId;
	}
	function getEyeColor() {
		return $this->FirstName;
	}
	function getHairColor() {
		return $this->LastName;
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
	
	function __toString() { 
        return "Gender : " . $this->Gender . " <br>
        Eye Color : " . $this->EyeColor . " <br>
        Hair Color : " . $this->HairColor . " <br>
        Has Beard : " . $this->HasBeard . " <br>
        Has Glasses : " . $this->HasGlasses . " <br>
        Has Smile : " . $this->HasSmile . " <br>
        Age : " . $this->Age . " <br>
        Update Date : " . $this->UpdateDate . " <br>
        Updated By User : " . $this->UpdatedByUser . " <br>";
    } 
}


?>