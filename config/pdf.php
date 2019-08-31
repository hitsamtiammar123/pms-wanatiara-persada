<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4-L',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
    'tempDir'               => base_path('../temp/'),
    'font_path'             => public_path('fonts/'),
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
