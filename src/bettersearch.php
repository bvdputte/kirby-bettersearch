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

    $results = $collection->filter(function($page) use($query, $options) {
        $data = $page->content()->toArray();
        $keys = array_keys($data);

        if(!empty($options['fields'])) {
            $keys = array_intersect($keys, $options['fields']);
        }

        $page->searchHits  = 0;
        $page->searchScore = 0;

        foreach($keys as $key) {
            $score = a::get($options['score'], $key, 1);

            // check for full matches
            if($matches = preg_match_all('!' . preg_quote($query) . '!i', $data[$key], $r)) {
                $page->searchHits  += $matches;
                $page->searchScore += $matches * $score;
            }
        }

        return $page->searchHits > 0 ? true : false;
    });

    $results = $results->sortBy('searchScore', SORT_DESC);

    return $results;
}
