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
        'https://www.youtube.com/results?search_query=',
        'https://github.com/search?q=',
        'https://www.bing.com/search?q=',
        'https://yandex.com/search/?text=',
        'https://id.search.yahoo.com/search?p='
    );

    public static function random()
    {
        return self::$referer[array_rand(self::$referer)] . rand();
    }
}
