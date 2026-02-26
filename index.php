<?php

include __DIR__ . '/src/bettersearch.php';

// For composer
@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bvdputte/bettersearch', [
	'pagesMethods' => [
		'bettersearch' => function ($query, $params = array(), $customIndexer = null) {
			return bettersearch(kirby(), $this, $query, $params, $customIndexer);
		}
	],
	'siteMethods' => [
		'bettersearch' => function ($query, $params = array(), $customIndexer = null) {
			return bettersearch(kirby(), site()->index(), $query, $params, $customIndexer);
		}
	]
]);
