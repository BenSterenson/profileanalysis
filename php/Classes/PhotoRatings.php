<?php

class PhotoRatings implements JsonSerializeable
{
	#region	Fields
	private $Id		   = -1; 
	private $IsHot  	= NULL;
	private $PhotoId    = -1; 
	private $FacebookId = -1;
	#endregion	Fields
	
	#region	Constructors
	public function __construct($id,$IsHot,$PhotoId,$FacebookId){
		$this->Id = $id;
		$this->IsHot = $IsHot;
		$this->PhotoId = $PhotoId;
		$this->FacebookId = $FacebookId;
	}
	#endregion	Constructors
	
	#region Setters
	function setId($Id) {
		$this->Id = $Id;
	}
	function setFacebookId($FacebookId) {
		$this->FacebookId = $FacebookId;
	}
	function setIsHot($IsHot) {
		$this->IsHot = $IsHot;
	}
	function setPhotoId($PhotoId) {
		$this->PhotoId = $PhotoId;
	}
	#endregion Setters
	
	#region Getters
	function getId(){
		return $this->Id;
	}
	function getFacebookId() {
		return $this->FacebookId;
	}
	function getIsHot() {
		return $this->IsHot;
	}
	function getPhotoId() {
		return $this->PhotoId;
	}
	#endregion Getters
	
	#region Methods
	public function jsonSerialize() {
        return Array(
			'Id' => $this.Id + 0,
			'IsHot'	=> $this->IsHot,
			'PhotoId' => $this->PhotoId + 0,
			'FacebookId' => $this->FacebookId + 0 
        );
    }
	
	#print
	function __toString(){
		return "Id : " . $this->Id . "<br>FacebookId : " . $this->FacebookId . "<br>IsHot : " . $this->IsHot .
				"<br>PhotoId : " . $this->PhotoId . "<br>"; 
	}
	#endregion Methods
}
?>