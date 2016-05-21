<?php

class PhotoRAtings implements JsonSerializeable{
	
	private Id = NULL; 
	private IsHot = NULL;
	private PhotoId = NULL; 
	private FacebookId = NULL;
	
	public function __construct($id,$isHot,$PhotoId,$FacebookId){
		$this->Id = $id;
		$this->IsHot = $isHot;
		$this->PhotoId = $PhotoId;
		$this->FacebookId = $FacebookId;
	}
	
	#region Setters
	function setId($Id) {
		$this->Id = $Id;
	}
	function setFacebookId($FacebookId) {
		$this->FacebookId = $FacebookId);
	}
	function setIsHot($IsHot) {
		$this->Comment = $IsHot;
	}
	function setPhotoId($PhotoId) {
		$this->PhotoId = $PhotoId;
	}
	#endregion Setters
	
	#region Getters
	function getId(){
		return this->Id;
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
	
	#print
	function __toString(){
		return "Id : " . $this->Id . "<br>FacebookId : " . $this->FacebookId . "<br>IsHot : " . $this->IsHot .
				"<br>PhotoId : " . $this->PhotoId . "<br>"; 
	}
	
}
?>