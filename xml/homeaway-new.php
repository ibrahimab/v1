<?php



//
// XML-export HomeAway
//
//

// redirect to original url


$new_url = preg_replace("@homeaway-new@", "homeaway", $_SERVER["REQUEST_URI"]);

header("Location: ".$new_url, true, 301);
exit;
