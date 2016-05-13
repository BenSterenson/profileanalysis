<?php
include("api.php");

$api = new betaFaceApi(1);


/*Add here the image url*/
$face = $api->get_Image_attributes("http://www.math.tau.ac.il/~milo/design/images/tova11.jpg");

if($face ==-1){
    echo "no face in image";    
}
/*Prints the Attributes*/
else{
    echo $api->image_Attributes;
}


?>
