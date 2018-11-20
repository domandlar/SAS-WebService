<?php
include_once 'funkcije.php';

header('Content-Type:application/json');

if (isset($_GET)) {
    if (!empty($_GET['metoda'])) {
        if($_GET['metoda']=='registracija') {
            if ($_GET['uloga'] == 'profesor') {
                RegistracijaProfesora($_POST['ime'], $_POST['prezime'], $_POST['titula']);
            }
            if ($_GET['uloga'] == 'student') {
                RegistracijaStudenta($_POST['ime'], $_POST['prezime']);
            }
        }
        if($_GET['metoda']=='prijava'){
            if ($_GET['uloga'] == 'profesor') {
                PrijavaProfesora($_GET['email'], $_GET['lozinka']);
            }
            if ($_GET['uloga'] == 'student') {
                 PrijavaStudenta($_GET['email'], $_GET['lozinka']);
            }
        }
    }
}
