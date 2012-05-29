<?php

include("admin/allfunctions.php");

mail("jeroen@webtastic.nl","Cookies Ylaise",wt_dump($_COOKIE,false));

echo "Gegevens zijn verzonden.<p>Bedankt!";

?>