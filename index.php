<?php

include __DIR__ . DS . 'src' . DS . 'bettersearch.php';

Kirby::plugin('bvdputte/kirby-bettersearch', [
    'pagesMethods' => [
        'bettersearch' => function ($query, $params = array()) {
            return search($this, $query, $params);
        }
    ]
]);
