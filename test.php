
<?php

 include("lib/mosaic.php");
 Use mosaic\auth;

 $mosaic = new auth();
 

$tokencont = file_get_contents("/var/www/html/db/mosaic-dev/tokens.json");
$Parsetoken = json_decode($tokencont,true);

foreach($Parsetoken as $token){
   echo $token;
   $tjson = json_decode($mosaic->authenticate($token,"refresh_token"),true);
   Print_R($mosaic->Verify($tjson[ 'access_token']));
}



        

        