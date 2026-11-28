<?php

declare(strict_types=1);

return [
    'upload_enabled' => true,

    // Wired in F1-16 to the admin-only Media Library (`content_images`) endpoint.
    'upload_url' => null,

    'editor' => [
        // Disable inline-style, raw-HTML, and unnecessary formatting plugins per TDD §8.2.
        // Removing GeneralHtmlSupport ensures disabled features' markup is stripped instead of preserved.
        'disabled_plugins' => [
            'Alignment',
            'FontBackgroundColor',
            'FontColor',
            'FontFamily',
            'FontSize',
            'GeneralHtmlSupport',
            'Highlight',
            'HtmlComment',
            'HtmlEmbed',
            'ImageResize',
            'ListProperties',
            'PageBreak',
            'ShowBlocks',
            'SourceEditing',
            'Strikethrough',
            'Style',
            'Subscript',
            'Superscript',
            'TableCellProperties',
            'TableProperties',
            'TodoList',
            'Underline',
        ],

        'disabled_toolbar_items' => [],

        'options' => [
            'licenseKey' => env('CKEDITOR_LICENSE_KEY', 'GPL'),

            'menuBar' => [
                'isVisible' => false,
            ],

            'toolbar' => [
                'items' => [
                    'undo',
                    'redo',
                    '|',
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'code',
                    'removeFormat',
                    '|',
                    'link',
                    'insertImage',
                    'insertTable',
                    'blockQuote',
                    'codeBlock',
                    'mediaEmbed',
                    'horizontalLine',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'outdent',
                    'indent',
                ],
                'shouldNotGroupWhenFull' => false,
            ],

            'heading' => [
                'options' => [
                    ['model' => 'paragraph', 'title' => 'Paragraph', 'class' => 'ck-heading_paragraph'],
                    ['model' => 'heading2', 'view' => 'h2', 'title' => 'Heading 2', 'class' => 'ck-heading_heading2'],
                    ['model' => 'heading3', 'view' => 'h3', 'title' => 'Heading 3', 'class' => 'ck-heading_heading3'],
                    ['model' => 'heading4', 'view' => 'h4', 'title' => 'Heading 4', 'class' => 'ck-heading_heading4'],
                ],
            ],

            'image' => [
                'toolbar' => [
                    'imageTextAlternative',
                    '|',
                    'toggleImageCaption',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:wrapText',
                    'imageStyle:breakText',
                ],
            ],

            'codeBlock' => [
                'languages' => [
                    ['language' => 'plaintext', 'label' => 'Plain text'],
                    ['language' => 'php', 'label' => 'PHP'],
                    ['language' => 'js', 'label' => 'JavaScript'],
                    ['language' => 'ts', 'label' => 'TypeScript'],
                    ['language' => 'bash', 'label' => 'Bash'],
                    ['language' => 'python', 'label' => 'Python'],
                    ['language' => 'sql', 'label' => 'SQL'],
                    ['language' => 'yaml', 'label' => 'YAML'],
                    ['language' => 'dockerfile', 'label' => 'Dockerfile'],
                    ['language' => 'html', 'label' => 'HTML'],
                    ['language' => 'css', 'label' => 'CSS'],
                    ['language' => 'json', 'label' => 'JSON'],
                ],
            ],

            'mediaEmbed' => [
                'previewsInData' => false,
                'removeProviders' => [
                    'dailymotion',
                    'spotify',
                    'instagram',
                    'twitter',
                    'googleMaps',
                    'flickr',
                    'facebook',
                ],
            ],

            'link' => [
                'addTargetToExternalLinks' => true,
                'defaultProtocol' => 'https://',
            ],

            'table' => [
                'contentToolbar' => [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                ],
            ],
        ],
    ],
];
