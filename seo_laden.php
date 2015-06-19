<?php
include 'admin/allfunctions.php';
include 'admin/vars.php';
$tabellen = [
    
    'land',
    'plaats',
    'skigebied',
];

$db2 = new DB_Sql;

foreach ($tabellen as $tabel) {

    $find = $db->query('SELECT '  . $tabel . '_id, naam, naam_de, naam_en, naam_fr FROM ' . $tabel);
    $results = [];
    while ($db->next_record()) {
        
        $nl = $db->f('naam');
        $de = $db->f('naam_de');
        $en = $db->f('naam_en');
        $fr = $db->f('naam_fr');
        
        $nl = (trim($nl) === '' ? 'NULL' : "'" . wt_convert2url_seo($nl) . "'");
        $de = (trim($de) === '' ? 'NULL' : "'" . wt_convert2url_seo($de) . "'");
        $en = (trim($en) === '' ? 'NULL' : "'" . wt_convert2url_seo($en) . "'");
        $fr = (trim($fr) === '' ? 'NULL' : "'" . wt_convert2url_seo($fr) . "'");
        
        $db2->query("UPDATE " . $tabel . " SET seonaam    = " . $nl . ",
                                               seonaam_de = " . $de . ",
                                               seonaam_en = " . $en . ",
                                               seonaam_fr = " . $fr . "
                     WHERE " . $tabel . "_id = " . $db->f($tabel . '_id'));
    }
}