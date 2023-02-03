<?php

/**
* Fork of the native $pages->search() method
* Only searches for full matches
*/
function search($collection, $query, $params = array()) {

    if(is_string($params)) {
        $params = array('fields' => str::split($params, '|'));
    }

    $defaults = array(
        'minlength' => 2,
        'fields'    => array(),
        'words'     => false,
        'score'     => array()
    );

    $options = array_merge($defaults, $params);

    if(empty($query)) return $collection->limit(0);

    $searchData = [];

    $results = $collection->filter(function($page) use($query, $options) {
        $data = $page->content()->toArray();
        $keys = array_keys($data);

        if(!empty($options['fields'])) {
            $keys = array_intersect($keys, $options['fields']);
        }

        $searchHits  = 0;
        $searchScore = 0;

        foreach($keys as $key) {
            $score = a::get($options['score'], $key, 1);

            // check for full matches
            if($matches = preg_match_all('!' . preg_quote($query) . '!i', $data[$key], $r)) {
                $searchHits  += $matches;
                $searchScore += $matches * $score;
            }
        }

        if($searchHits > 0) {
            $searchData[$page->id()] = [
                'searchHits' => $searchHits,
                'searchScore' => $searchScore
            ];
            return true;
        }

        return false;
    });

    // Sort the search data based on the search score
    uasort($searchData, function($a, $b) {
        return $b['searchScore'] - $a['searchScore'];
    });

    return $results;
}
