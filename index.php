<?php

include __DIR__ . '/src/bettersearch.php';

// For composer
@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bvdputte/kirby-bettersearch', [
    'pagesMethods' => [
        'bettersearch' => function ($query, $params = array()) {
            return bettersearch(kirby(), $this, $query, $params);
        }
    ],
    'siteMethods' => [
        'bettersearch' => function ($query, $params = array()) {
            return bettersearch(kirby(), site()->index(), $query, $params);
        }
    ]
]);
