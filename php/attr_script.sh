#!/bin/sh
StartUID=632
COUNT=1
NUM_OF_PROFILES=5


while [ $COUNT -le $NUM_OF_PROFILES ]
do
echo ${StartUID}
echo ${COUNT}
echo 'c/xampp/php/php.exe c/xampp/htdocs/profileanalysis/php/Facebook_Photoattributes.php -- '${StartUID}''
/C/xampp/php/php.exe /C/xampp/htdocs/profileanalysis/php/Facebook_Photoattributes.php -- '${StartUID}'

COUNT=$(( $COUNT+1))
StartUID=$(( $StartUID+1))
done
sleep(30)
#rename file
echo "Test Done!"

