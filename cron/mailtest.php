<?php

echo 'Mailtest\n\n';
include(__DIR__ . '/../admin/allfunctions.php');

// testchalet@outlook.com

wt_mail("check-auth-systeembeheer=webtastic.nl@verifier.port25.com","Testmail ".date("r"),"body test","info@chalet.nl","Chalet.nl");
wt_mail("testchalet@outlook.com","Testmail ".date("H:i"),"Dit is een test.","info@chalet.nl","Chalet.nl");


echo 'Mail is verzonden.\n';
