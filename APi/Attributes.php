<?php

class Attributes {
    
    var $Gender; //female/male;
    var $EyeColor; // RGB hex color value
    var $HairColor; // RGB hex color value
    var $HasBeard; //yes/no
    var $HasGlasses; //yes/no
    var $HasSmile; //yes/no
    var $Age;
    var $UpdateDate; //date of api check




	function __toString() { 
        return "Gender : " . $this->Gender . " <br>
        Eye Color : " . $this->EyeColor . " <br>
        Hair Color : " . $this->HairColor . " <br>
        Has Beard : " . $this->HasBeard . " <br>
        Has Glasses : " . $this->HasGlasses . " <br>
        Has Smile : " . $this->HasSmile . " <br>
        Age : " . $this->Age . " <br>
        Update Date : " . $this->UpdateDate . " <br>";
    } 
}


?>