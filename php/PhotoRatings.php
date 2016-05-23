<?php

class PhotoRatings implements JsonSerializeable
{
	#region	Fields
	private Id		   = -1; 
	private HotCount   = -1;
	private NotCount   = -1;
	private PhotoId    = -1; 
	private FacebookId = -1;
	#endregion	Fields
	
	#region	Constructors
	public function __construct($id,$HotCount,$PhotoId,$FacebookId){
		$this->Id = $id;
		$this->HotCount = $HotCount;
		$this->PhotoId = $PhotoId;
		$this->FacebookId = $FacebookId;
	}
	#endregion	Constructors
	
	#region Setters
	function setId($Id) {
		$this->Id = $Id;
	}
	function setFacebookId($FacebookId) {
		$this->FacebookId = $FacebookId);
	}
	function setHotCount($HotCount) {
		$this->HotCount = $HotCount;
	}
	function setNotCount($NotCount) {
		$this->NotCount = $NotCount;
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
	function getHotCount() {
		return $this->HotCount;
	}
	function getNotCount() {
		return $this->NotCount;
	}
	function getPhotoId() {
		return $this->PhotoId;
	}
	#endregion Getters
	
	#region Methods
	public function jsonSerialize() {
        return Array(
			'Id' => $this.Id + 0,
			'HotCount'	=> $this->HotCount + 0,
			'NotCount'	=> $this->NotCount + 0,
			'PhotoId' => $this->PhotoId + 0,
			'FacebookId' => $this->FacebookId + 0 
        );
    }
	
	#print
	function __toString(){
		return "Id : " . $this->Id . "<br>FacebookId : " . $this->FacebookId . "<br>HotCount : " . $this->HotCount .
				"<br>PhotoId : " . $this->PhotoId . "<br>"; 
	}
	#endregion Methods
}
?>