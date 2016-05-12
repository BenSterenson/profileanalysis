<?php
include 'Facebook_user.php';
set_time_limit(0);

class Facebook_photo implements JsonSerializable { 

	#region Fields
    private $FacebookId = NULL;
    private $FacebookPhotoId = NULL;
    private $UpdateDate = NULL;
	private $PhotoLink = NULL;
	private $NumOfLikes = 0;
	private $isValidPhoto = 0;
	#endregion Fields

	#region Constructors
	public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 1:
                self::__construct1($argv[0]);
                break;
            case 6:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3], $argv[4], $argv[5] );
         }
    }
 
	public function __construct1($userID) {
        $this->FacebookId = $userID;
		$this->UpdateDate = date("Y-m-d:H:i:s");
		$this->extract_photo_Info();
    }
	
	public function __construct2($FacebookId, $FacebookPhotoId, $UpdateDate, $PhotoLink, $NumOfLikes, $isValidPhoto) {
        $this->FacebookId = $FacebookId;
		$this->FacebookPhotoId = $FacebookPhotoId;
		$this->UpdateDate = $UpdateDate;
		$this->PhotoLink = $PhotoLink;
		$this->NumOfLikes = $NumOfLikes;
		$this->isValidPhoto = $isValidPhoto;
    }
	#endregion Constructors
	
	#region Setters
	function setPhotoId($photoID) {
		$this->FacebookPhotoId = $photoID;
	}
	function setUpdateDate() {
		$this->UpdateDate = date("Y-m-d:H:i:s");
	}
	function setPhotoLink($PhotoLink) {
		$this->PhotoLink = $PhotoLink;
	}
	function setNumOfLikes($NumOfLikes) {
		$this->NumOfLikes = $NumOfLikes;
	}
	function setisValidPhoto($isValidPhoto) {
		$this->isValidPhoto = $isValidPhoto;
	}
	#endregion Setters
	
	#region Getters
	function getUserID() {
		return $this->FacebookId;
	}
	function getPhotoId() {
		return $this->FacebookPhotoId;
	}
	function getUpdateDate() {
		return $this->UpdateDate;
	}
	function getPhotoLink() {
		return $this->getPhotoLink;
	}
	function getNumOfLikes() {
		return $this->NumOfLikes;
	}
	function getisValidPhoto() {
		return $this->isValidPhoto;
	}
	#endregion Getters
	
	#region Methods
		public function jsonSerialize() {
        return Array(
           'FacebookId'			=> $this->FacebookId + 0,
           'FacebookPhotoId' 	=> $this->FacebookPhotoId + 0,
           'UpdateDate' 		=> $this->UpdateDate + 0,
		   'PhotoLink' 			=> $this->PhotoLink,
		   'NumOfLikes'			=> $this->NumOfLikes + 0,
		   'isValidPhoto'		=> $this->isValidPhoto
        );
    }
	
	function get_redirectURL($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		$r_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);
		return $r_url;
	}

	function extract_likes_number($FacebookPhotoId){
		$profile_pic_url =  "http://www.facebook.com/".$FacebookPhotoId;
		
		if (strcmp($profile_pic_url,'http://www.facebook.com/') == 0){
			echo "no data - insert null";
			return 0;
		}
		
		$html = get_html($profile_pic_url);
		$like_tag = '"likecount":';
		return extract_tag($like_tag ,$html);
	}
	
	function updateNumOfLikes(){
		$this->setNumOfLikes($this->extract_likes_number($this->FacebookPhotoId));
	}
	function unsetPhoto(){
		$this->FacebookId = NULL;
		$this->FacebookPhotoId = NULL;
		$this->UpdateDate = NULL;
		$this->PhotoLink = NULL;
		$this->NumOfLikes = 0;
		$this->isValidPhoto = 0;
		return;
	}
	function extract_photo_Info() {
		$profile_pic =  "http://graph.facebook.com/".$this->FacebookId."/picture?width=9999"; // large image
		$noPhotoLink = 'https://fbstatic-a.akamaihd.net/rsrc.php/v2/yo/r/UlIqmHJn-SK.gif';
		$photo_url = $this->get_redirectURL($profile_pic);
		
		if ($noPhotoLink == $photo_url){
			$this->unsetPhoto();
			return;
		}
		$this->PhotoLink = $this->get_redirectURL($profile_pic);
		
		preg_match('/_(\d+)_/', $this->PhotoLink, $matches);
		$this->FacebookPhotoId = $matches[1];
	
		$this->updateNumOfLikes();
	}
	
	//############### print ###############//
    function __toString() { 
        return "FacebookId : " . $this->FacebookId . " <br>Photo Id : " . $this->FacebookPhotoId . " <br>Update Date : " . $this->UpdateDate . " <br>Photo Link : " . $this->PhotoLink . " <br>Num Of Likes : " . $this->NumOfLikes . " <br>is Valid Photo : " . $this->isValidPhoto; 
    } 
	#endregion Methods
} 

?> 
