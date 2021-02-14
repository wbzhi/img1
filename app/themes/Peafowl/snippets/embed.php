<?php

if (!defined('access') or !access) {
    die('This file cannot be directly accessed.');
}
global $embed_tpl;
$embed_tpl = [
    'links' => [
        'label' => _s('Links'),
        'options' => [
            'direct-links'	=> [
                'label'		=> _s('Direct links'),
                'template'	=> '%URL%',
                'size'		=> 'full'
            ],
            'viewer-links' => [
                'label'		=> _s('Viewer links'),
                'template'	=> '%URL_SHORT%',
                'size'		=> 'viewer'
            ]
        ]
    ],
    'html-codes' => [
        'label' => _s('HTML Codes'),
        'options' => [
            'html-embed'	=> [
                'label'		=> _s('HTML image'),
                'template'	=> '<img src="%URL%" alt="%FILENAME%" border="0">',
                'size'		=> 'full'
            ],
            'html-embed-full'	=> [
                'label'		=> _s('HTML full linked'),
                'template'	=> '<a href="%URL_SHORT%"><img src="%URL%" alt="%FILENAME%" border="0"></a>',
                'size'		=> 'full'
            ]
        ]
    ],
    'bbcodes' => [
        'label' => _s('BBCodes'),
        'options' => [
            'bbcode-embed'	=> [
                'label'		=> _s('BBCode full'),
                'template'	=> '[img]%URL%[/img]',
                'size'		=> 'full'
            ],
            'bbcode-embed-full' => [
                'label'		=> _s('BBCode full linked'),
                'template'	=> '[url=%URL_SHORT%][img]%URL%[/img][/url]',
                'size'		=> 'full'
            ]
        ]
    ],
    'markdown' => [
        'label'	=> 'Markdown',
        'options' => [
            'markdown-embed'	=> [
                'label'		=> _s('Markdown full'),
                'template'	=> '![%FILENAME%](%URL%)',
                'size'		=> 'full'
            ],
            'markdown-embed-full' => [
                'label'		=> _s('Markdown full linked'),
                'template'	=> '[![%FILENAME%](%URL%)](%URL_SHORT%)',
                'size'		=> 'full'
            ]
        ]
    ]
];