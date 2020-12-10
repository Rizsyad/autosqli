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
        'https://xnxx.com/search/'
    );

    public static function random()
    {
        return self::$referer[array_rand(self::$referer)] . rand(5, 12);
    }
}
