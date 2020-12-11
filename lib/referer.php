<?php

class Referer
{
    public static $referer = array(
        'http://www.google.com/?q=',
        'http://www.usatoday.com/search/results?q=',
        'http://engadget.search.aol.com/search?q=',
        'https://www.ecosia.org/search?q=',
        'https://play.google.com/store/search?q=',
        'https://duckduckgo.com/?q=',
        'https://xnxx.com/search/',
        'https://www.youtube.com/results?search_query=',
        'https://pornhub.com/search/',
        'https://github.com/search?q='
    );

    public static function random()
    {
        return self::$referer[array_rand(self::$referer)] . rand(5, 12);
    }
}
