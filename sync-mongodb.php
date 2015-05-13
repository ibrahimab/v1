<?php

function dump()
{
    $args = func_get_args();
    foreach ($args as $arg) {

        echo '<pre>', var_dump($arg) , '</pre>';
        echo '<hr />';
    }
}

/**
 * Connecting to MongoDB
 */
$mongodb    = new MongoClient();
$db         = $mongodb->chalet_files;

$directories = [

    [
        'old' => 'accommodaties',
        'new' => 'accommodations.big',
    ],

    [
        'old' => 'hoofdfoto_accommodatie',
        'new' => 'accommodations.large',
    ],

    [
        'old' => 'accommodaties_aanvullend',
        'new' => 'accommodations.additional',
    ],

    [
        'old' => 'accommodaties_aanvullend_onderaan',
        'new' => 'accommodations.additional.down',
    ],

    [
        'old' => 'accommodaties_aanvullend_breed',
        'new' => 'accommodations.additional.large',
    ],

    [
        'old' => 'types',
        'new' => 'types.additional',
    ],

    [
        'old' => 'types_breed',
        'new' => 'types.additional.large',
    ],

    [
        'old' => 'types_specifiek',
        'new' => 'types.big',
    ],

    [
        'old' => 'hoofdfoto_type',
        'new' => 'types.large',
    ],

    [
        'old' => 'types_specifiek_tn',
        'new' => 'types.tiny',
    ],

    [
        'old' => 'plaatsen',
        'new' => 'places.normal',
    ],

    [
        'old' => 'plaatsen_breed',
        'new' => 'places.big',
    ],

    [
        'old' => 'plaatsen_landkaarten',
        'new' => 'places.maps',
    ],
];

$do = [];
foreach ($directories as $directory) {

    if (false === in_array($directory['new'], $do)) {
        continue;
    }

    $grid              = $db->getGridFS($directory['new']);
    $directoryIterator = new RecursiveDirectoryIterator('pic/cms/' . $directory['old']);
    $iterator          = new RecursiveIteratorIterator($directoryIterator);
    $regexIterator     = new RegexIterator($iterator, '/^.+\.jpg$/i', RecursiveRegexIterator::GET_MATCH);

    if (iterator_count($regexIterator) > 0) {

        $i = 0;
        foreach ($regexIterator as $file) {

            $pathinfo        = pathinfo($file[0]);
            list($id, $rank) = explode('-', $pathinfo['filename']);

            $grid->storeFile($file[0], [

                'metadata' => [

                    'id'         => intval($id),
                    'rank'       => (null === $rank ? 1 : intval($rank)),
                    'created_at' => new MongoDate(),
                ],
            ]);
        }
    }
}

$grid   = $db->getGridFS('places.normal');
// $cursor = $grid->find();
// $cursor->next();// $cursor->next();$cursor->next();$cursor->next();$cursor->next();$cursor->next();$cursor->next();$cursor->next();
$image  = $grid->findOne();
// $image  = $grid->findOne(['metadata.id' => 1]);
// $image = $cursor->current();
//dump($image);exit;
header('Content-type: image/jpg');
echo $image->getBytes();

$mongodb->close();
exit;