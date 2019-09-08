<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4-L',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
    'tempDir'               => storage_path('pdf'),
    'font_path'             => public_path('vendor/fonts/'),
    'font_data' => [
        'msyh' => [
			'R'  => 'chinese.msyh.ttf',
			'B'  => 'chinese.msyh.ttf',
			'I'  => 'chinese.msyh.ttf',
			'BI' => 'chinese.msyh.ttf',
			'useOTL' => 0x00,
			'useKashida' => 75,
        ],


	]
];
