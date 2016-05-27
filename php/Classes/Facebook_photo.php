<?php
//include 'Facebook_user.php'; // MICHAEL: DO WE NEED IT??
set_time_limit(0);

class Facebook_photo implements JsonSerializable { 

	#region Fields
    private $Id = NULL;
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
            case 2:
                self::__construct2($argv[0],$argv[1]);
                break;
            case 6:
                self::__construct3( $argv[0], $argv[1], $argv[2], $argv[3], $argv[4], $argv[5] );
         }
    }
 
	public function __construct1($userID) {
        $this->FacebookId = $userID;
		$this->UpdateDate = date("Y-m-d:H:i:s");
		$this->extract_photo_Info(1);
    }

	public function __construct2($userID, $NumOfLikes) {
        $this->FacebookId = $userID;
  		$this->NumOfLikes = $NumOfLikes;
		$this->UpdateDate = date("Y-m-d:H:i:s");
		$this->extract_photo_Info(0);
    }
	public function __construct3($FacebookId, $FacebookPhotoId, $UpdateDate, $PhotoLink, $NumOfLikes, $isValidPhoto) {
        $this->FacebookId = $FacebookId;
		$this->FacebookPhotoId = $FacebookPhotoId;
		$this->UpdateDate = $UpdateDate;
		$this->PhotoLink = $PhotoLink;
		$this->NumOfLikes = $NumOfLikes;
		$this->isValidPhoto = $isValidPhoto;
    }
	#endregion Constructors
	
	#region Setters
	function setId($Id) {
		$this->Id = $Id;
	}
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
	function getId() {
		return $this->Id;
	}	
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
        	'Id'				=> $this->FacebookId + 0,
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
		$this->Id = NULL;
		$this->FacebookId = NULL;
		$this->FacebookPhotoId = NULL;
		$this->UpdateDate = NULL;
		$this->PhotoLink = NULL;
		$this->NumOfLikes = 0;
		$this->isValidPhoto = 0;
		return;
	}
	function extract_photo_Info($extractLikes) {
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
		
		if($extractLikes == 1)
			$this->updateNumOfLikes();
	}
	
	//############### print ###############//
    function __toString() { 
        return "Id : " . $this->Id . " <br>FacebookId : " . $this->FacebookId . " <br>Photo Id : " . $this->FacebookPhotoId . " <br>Update Date : " . $this->UpdateDate . " <br>Photo Link : " . $this->PhotoLink . " <br>Num Of Likes : " . $this->NumOfLikes . " <br>is Valid Photo : " . $this->isValidPhoto; 
    } 
	#endregion Methods
} 

?> 
