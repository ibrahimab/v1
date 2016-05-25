<?php

//
// iframe solution to display old booking form and calc form on new website
//

$connect_legacy_new_iframe = true;

$setIframeUrl = false;

$iframe_files = [
    'calc' => 'calc',
    'option-request' => 'beschikbaarheid',
    'ask-our-advice' => 'vraag-ons-advies',
    'book' => 'boeken',
];

$iframe_type = $_GET['iframe'];

if (!$iframe_type) {
    $iframe_type = 'book';
}

if ($iframe_files[$iframe_type]) {

    include '../' . $iframe_files[$iframe_type] . '.php';

    if (in_array($iframe_type, ['book'])) {
        $setIframeUrl = true;
    }

} else {
    echo 'error';
    trigger_error('iframe file ' . $iframe_type . ' not found', E_USER_NOTICE);
    exit;
}

?><!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Boeken</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster"/>

    <!-- <link rel="stylesheet" href="css/foundation.css" /> -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="js/vendor/jquery-1.12.3.min.js"></script>
    <script src="../scripts/functions.js?c=<?php echo filemtime("../scripts/functions.js"); ?>"></script>

    <script>

    function resize() {
        var height = document.getElementsByTagName("html")[0].scrollHeight;

        window.parent.postMessage(["setHeight", height], "*");

        // scroll back to top after reload of iframe content
        window.parent.postMessage(["scrollTop", 0], "*");

        window.scroll(0,0);
    }

    jQuery( document ).ready(function() {
        <?php
        if ($setIframeUrl) { ?>

            window.parent.postMessage(["setIframeUrl", window.location.href ], "*");

        <?php } ?>
    });

    </script>

    <?php

    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";

    switch ( $vars["websitetype"] ) {
        case 1:
            // Chalet.nl/be/eu
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_chalet.css?cache=".@filemtime("css/opmaak_chalet.css")."\" />\n";
            break;

        case 3:
            // Zomerhuisje
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_zomerhuisje.css?cache=".@filemtime("css/opmaak_zomerhuisje.css")."\" />\n";
            break;

        case 4:
            // Chalettour.nl
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_chalet.css?cache=".@filemtime("css/opmaak_chalet.css")."\" />\n";
            break;

        case 6:
            // Chaletsinvallandry
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_vallandry.css?cache=".@filemtime("css/opmaak_vallandry.css")."\" />\n";
            break;

        case 7:
            // Italissima
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_italissima.css?cache=".@filemtime("css/opmaak_italissima.css")."\" />\n";
            break;

        case 9:
            // Venturasol
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_venturasol.css?cache=".@filemtime("css/opmaak_venturasol.css")."\" />\n";
            break;

    }

    ?>

    <style>

    html, div {
        font-family: "Roboto";
        font-style: normal;
        font-weight: 300;
        margin: 0;
        padding: 0;
    }

    .wtform_input {
        font-family: "Roboto";
        font-style: normal;
        font-weight: 300;
    }

    body {
        background-color: #fff;
        font-size: 1.2rem;
        margin: 0;
        padding: 0;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: "Roboto";
    }

    p, div {
        font-family: "Roboto";
    }

    .boxshadow {
        box-shadow: none !important;
    }

    select {
        font-family: "Roboto" !important;
        font-size: 14px !important;
        font-weight: normal !important;
        color: #333 !important;
    }

    input[type=text], input[type=email] {
        font-size: 14px !important;
        color: #333 !important;
        font-weight: normal !important;
    }

    @media only screen and (max-width: 700px) {

        table.wtform_table {
            width: 100%;
            border: 0;
        }

        .wtform_cell_left {
            display: block;
            width: 90%;
        }

        .wtform_cell_right {
            display: block;
            margin-top: -5px;
            margin-bottom: 5px;
            width: 90%;
        }

        .wtform_cell_right select {
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .wtform_cell_right input {
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .wtform_cell_right input[type=radio], .wtform_cell_right input[type=checkbox] {
            width: 20px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        label {
            display: inline-block;
            margin: 0 30px 0 0;
        }

        .wtform_input {
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .wtform_input_narrow {
            width: 90px !important;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        table.table {
            width: 100%;
        }

        table.table tr td:first-child {
            display: block;
            padding-top: 5px;
            width: 100%;
        }

        table.table tr td:nth-child(2), table.table tr td:nth-child(3), table.table tr td:nth-child(4), table.table tr td:nth-child(5), table.table tr td:nth-child(6), table.table tr td:nth-child(7), table.table tr td:nth-child(8), table.table tr td:nth-child(9), table.table tr td:nth-child(10), table.table tr td:nth-child(11) {
            display: inline-block;
            padding-bottom: 5px;
        }

        .small-link-to-extra-info {
            display: none;
        }

        body.iframe-ask-our-advice .wtform_calendar_img {
            display: none;
        }
    }

    </style>

  </head>
  <body onLoad="resize();" onresize="resize();" class="iframe-<?php echo wt_he($iframe_type); ?>">

    <?php

    include '../content/' . $iframe_files[$iframe_type] . '.html';

    ?>

  </body>
</html>
