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
$db         = $mongodb->files;

$transfers = [

	[
		'collection'  => 'accommodations',
		'directories' => [

			[
				'name' => 'accommodaties',
				'kind' => 'main',
				'rank' => false,
			],
			[
				'name' => 'hoofdfoto_accommodatie',
				'kind' => 'main',
				'rank' => false,
			],
			[
				'name' => 'accommodaties_aanvullend',
				'kind' => 'additional',
				'rank' => true,
			],
			[
				'name' => 'accommodaties_aanvullend_onderaan',
				'kind' => 'additional-below',
				'rank' => true,
			],
			[
				'name' => 'accommodaties_aanvullend_breed',
				'kind' => 'additional',
				'rank' => true,
			],
	],
];

$do = [];
foreach ($transfers as $transfer) {

    $gridfs = $db->getGridFS($transfer['collection']);

	foreach ($transfer['directories'] as $directory) {

	    $directoryIterator = new RecursiveDirectoryIterator('/var/www/chalet.nl/html/pic/cms/' . $directory['name']);
	    $iterator          = new RecursiveIteratorIterator($directoryIterator);
	    $regexIterator     = new RegexIterator($iterator, '/^.+\.jpg$/i', RecursiveRegexIterator::GET_MATCH);

	    if (iterator_count($regexIterator) > 0) {

	        foreach ($regexIterator as $file) {

	            $pathinfo 			  = pathinfo($file[0]);
				list($width, $height) = getimagesize($file[0]);

				if (true === $directory['rank']) {

					list($id, $rank) = explode('-', $pathinfo['filename']);

				} else {

					$id   = $pathinfo['filename'];
					$rank = 1;
				}

	            $gridfs->storeFile($file[0], [

	                'metadata' => [

	                    'file_id'  => intval($id),
	                    'rank'     => intval($rank),
						'filename' => $pathinfo['filename'] . '.' . $pathinfo['extension'],
						'kind'	   => $directory['kind'],
						'width'    => $width,
						'height'   => $height,
	                ],
	            ]);
	        }
	    }
	}
}