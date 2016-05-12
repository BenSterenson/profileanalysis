<?php
include("api.php");

$api = new betaFaceApi();


/*Add here the image url*/
$face = $api->get_Image_attributes("http://www.math.tau.ac.il/~milo/design/images/tova11.jpg");

/*Example- how to print the Attributes*/

echo $api->image_Attributes->UpdateDate;
echo "<br>";
echo $api->image_Attributes->EyeColor;
echo "<br>";
echo $api->image_Attributes->HasGlasses;
echo "<br>";
echo $api->image_Attributes->Age;
echo "<br>";
echo $api->image_Attributes->Gender;

?>
