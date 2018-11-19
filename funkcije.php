<?php
include_once "./baza.class.php";

function DodajUBazu($upit)
{
    $db = new Baza();
    $db->spojiDB();
    $rez = $db->selectDB($upit);
    $db->zatvoriDB();
}

function DohvatiIzBaze($upit)
{
    $db = new baza;
    $db->spojiDB();
    $rez = $db->selectDB($upit);
    $db->zatvoriDB();
    return $rez;
}