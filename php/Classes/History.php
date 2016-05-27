<?php
class History implements JsonSerializable {

	#region Fields
	private $Id = NULL; 
	private $FacebookId = NULL;
	private $AttributeName = NULL; 
	private $FilterValue = NULL;
	private $SessionId = NULL;
	#endregion Fields`
		
	#region Constructors
	public function __construct($FBId,$AttributeName,$FilterValue,$SessionId){
		$this->FacebookId = $FBId;
		$this->AttributeName = $AttributeName;
		$this->FilterValue = $FilterValue;
		$this->SessionId = $SessionId;
	}
	#endregion Constructors
	
	#region Setters
	function setFacebookId($FacebookId) {
		$this->FacebookId = $FacebookId;
	}
	function setAttributeName($AttributeName) {
		$this->AttributeName = $AttributeName;
	}
	function setFilterValue($FilterValue) {
		$this->FilterValue = $FilterValue;
	}
	function setSessionId($SessionId) {
		$this->SessionId = $SessionId;
	}
	#endregion Setters
	
	#region Getters
	function getId(){
		return $this->Id;
	}
	function getFBID() {
		return $this->FacebookId;
	}
	function getAttributeName() {
		return $this->AttributeName;
	}
	function getFilterValue() {
		return $this->FilterValue;
	}
	function getSessionId() {
		return $this->SessionId;
	}
	#endregion Getters
	
	#region Methods
	public function jsonSerialize() {
        return Array(
			'Id' => $this.Id + 0,
			'FacebookId'	=> $this->FacebookId + 0,
			'AttributeName' => $this->AttributeName,
			'FilterValue' => $this->FilterValue,
			'SessionId' => $this->SessionId + 0 
        );
    }
	
	#print
	function __toString(){
		return "Id : " . $this->Id . "<br>FacebookId : " . $this->FacebookId . "<br>AttributeName : " . $this->AttributeName .
				"<br>FilterValue : " . $this->FilterValue . "<br>SessionId : " . $this->SessionId . "<br>"; 
	}
	#endregion Methods
}

?>