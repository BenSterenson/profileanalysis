<?php

class PhotoComments implements JsonSerializeable{
	
	private Id = NULL; 
	private Comment = NULL;
	private PhotoId = NULL; 
	private FacebookId = NULL;
	private Time = NULL;
	
	public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 4:
                self::__construct1($argv[0],$argv[1],$argv[2],$argv[3],$argv[4]);
                break;
            case 5:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3], $argv[4]);
         }
    }
	
	public function __construct1($Id,$FBId,$Comment,$PhotoId){
		$this->Id = $Id;
		$this->FacebookId = $FBId;
		$this->Comment = $Comment;
		$this->PhotoId = $PhotoId;
		$this->Time = date("Y-m-d:H:i:s");
		
	}
		
	public function __construct2($Id,$FBId,$Comment,$PhotoId,$Time){
		$this->Id = $Id;
		$this->FacebookId = $FBId;
		$this->Comment = $Comment;
		$this->PhotoId = $PhotoId;
		$this->Time = $Time;
		
	}
		
	
	#region Setters
	function setId($Id) {
		$this->Id = $Id;
	}
	function setFacebookId($FacebookId) {
		$this->FacebookId = $FacebookId);
	}
	function setComment($Comment) {
		$this->Comment = $Comment;
	}
	function setPhotoId($PhotoId) {
		$this->PhotoId = $PhotoId;
	}
	function setTime() {
		$this->Time = date("Y-m-d:H:i:s");
	}
	#endregion Setters
	
	#region Getters
	function getId(){
		return this->Id;
	}
	function getFacebookId() {
		return $this->FacebookId;
	}
	function getComment() {
		return $this->Comment;
	}
	function getPhotoId() {
		return $this->PhotoId;
	}
	function getTime() {
		return $this->Time;
	}
	#endregion Getters
	
	public function jsonSerialize() {
        return Array(
			'Id' => $this.Id + 0,
			'FacebookId'	=> $this->FacebookId + 0,
			'Comment' => $this->Comment,
			'PhotoId' => $this->PhotoId + 0,
			'Time' => $this->Time + 0 
         
        );
    }
	
	#print
	function __toString(){
		return "Id : " . $this->Id . "<br>FacebookId : " . $this->FacebookId . "<br>Comment : " . $this->Comment .
				"<br>PhotoId : " . $this->PhotoId . "<br>Time : " . $this->Time . "<br>"; 
	}
	
}

?>