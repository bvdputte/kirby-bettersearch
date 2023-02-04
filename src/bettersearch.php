<?php
/**
* Fork of the native search() component
* Only searches for full matches
*/
function bettersearch(Kirby\Cms\App $kirby, Kirby\Cms\Collection $collection, string $query = null, $params = []) {
    // empty search query
    if (empty(trim($query ?? '')) === true) {
        return $collection->limit(0);
    }

    if (is_string($params) === true) {
        $params = ['fields' => Str::split($params, '|')];
    }

    $defaults = [
        'fields'    => [],
        'score'     => []
    ];

    $options     = array_merge($defaults, $params);
    $collection  = clone $collection;

    // returns an empty collection if there is no search query
    if(empty($query)) return $collection->limit(0);

    $scores  = [];
    $results = $collection->filter(function ($item) use ($query, $options, &$scores) {
        $data   = $item->content()->toArray();
        $keys   = array_keys($data);
        $keys[] = 'id';

        if ($item instanceof User) {
            $keys[] = 'name';
            $keys[] = 'email';
            $keys[] = 'role';
        } elseif ($item instanceof Page) {
            // apply the default score for pages
            $options['score'] = array_merge([
                'id'    => 64,
                'title' => 64,
            ], $options['score']);
        }

        if (empty($options['fields']) === false) {
            $fields = array_map('strtolower', $options['fields']);
            $keys   = array_intersect($keys, $fields);
        }
        // var_dump($keys);

        $scoring = [
            'hits'  => 0,
            'score' => 0
        ];

        foreach ($keys as $key) {
            $score = $options['score'][$key] ?? 1;
            $value = $data[$key] ?? (string)$item->$key();

            $lowerValue = Str::lower($value);

            // check for exact query matches
            if ($matches = preg_match_all('!' . preg_quote($query) . '!i', $value, $r)) {
                $scoring['score'] += 2 * $score;
                $scoring['hits']  += $matches;
            }

            // check for any match
            // if ($matches = preg_match_all($preg, $value, $r)) {
            //     $scoring['score'] += $matches * $score;
            //     $scoring['hits']  += $matches;
            // }
        }

        $scores[$item->id()] = $scoring;
        return $scoring['hits'] > 0;
    });

    return $results->sort(
        fn ($item) => $scores[$item->id()]['score'],
        'desc'
    );
}
