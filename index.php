<?php

include __DIR__ . '/src/bettersearch.php';

// For composer
@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bvdputte/kirby-bettersearch', [
    'pagesMethods' => [
        'bettersearch' => function ($query, $params = array()) {
            return search($this, $query, $params);
        }
    ]
]);
