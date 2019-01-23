<?php
include_once 'funkcije.php';

header('Content-Type:application/json');

if (isset($_GET)) {
    if (!empty($_GET['metoda'])) {
        if($_GET['metoda']=='registracija') {
            if ($_GET['uloga'] == 'profesor') {
                RegistracijaProfesora($_POST['ime'], $_POST['prezime'], $_POST['email'], $_POST['titula']);
            }
            if ($_GET['uloga'] == 'student') {
                RegistracijaStudenta($_POST['ime'], $_POST['prezime'], $_POST['email']);
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
        if($_GET['metoda']=='kolegij'){
			if($_GET['akcija']=='svi'){
				if($_GET['uloga']=='profesor'){
					DohvatiSveKolegije();
				}
				if($_GET['uloga']=='student'){
					DohvatiSveKolegije();
				}
			}
            if($_GET['akcija']=='dohvati'){
                if($_GET['uloga']=='profesor'){
                    DohvatiKolegijeProfesora($_GET['idUloge']);
                }
                if($_GET['uloga']=='student'){
                    DohvatiKolegijeStudenta($_GET['idUloge']);
                }
            }
            if($_GET['akcija']=='novi'){
                if($_GET['uloga']=='profesor'){
                    DodajKolegij($_POST['naziv'], $_POST['semestar'],$_POST['studij'], $_GET['idUloge']);
                }
            }
			if($_GET['akcija']=='dodaj'){
                if($_GET['uloga']=='profesor'){
                    DodajKolegijProfesoru($_POST['kolegij'], $_GET['idUloge']);
                }
				if($_GET['uloga']=='student'){
                    DodajKolegijStudntu($_POST['kolegij'], $_GET['idUloge']);
                }
            }
            if($_GET['akcija']=='azuriraj'){
				if($_GET['uloga']=='profesor'){
					AzurirajKolegij($_POST['idKolegija'], $_POST['naziv'], $_POST['semestar'],$_POST['studij']);
				}
            }
        }
        if($_GET['metoda']=='dvorana'){
            if($_GET['akcija']=='dohvati'){
                    DohvatiDvorane($_GET['tipDvorane']);    
            }
        }
        if($_GET['metoda']=='aktivnost'){
            if($_GET['akcija']=='dohvati'){
                if($_GET['uloga']=='profesor'){
                    if($_GET['tipAktivnosti'] == 'all'){
                        DohvatiAktivnostiProfesora($_GET['idUloge']);
                    }
                    else /*if($_GET['tipAktivnosti'] == 'seminar')*/{
                        DohvatiAktivnostiProfesoraPoTipuAktivnosti($_GET['idUloge'], $_GET['tipAktivnosti']);
                    }
                }
                else if($_GET['uloga']=='student'){
                    if($_GET['tipAktivnosti'] == 'all'){
                        
                    }
                    else /*if($_GET['tipAktivnosti'] == 'seminar')*/{
                        DohvatiAktivnostiStudentaPoTipuAktivnosti($_GET['idUloge'], $_GET['tipAktivnosti']);
                    }
                }
                if($_GET['uloga']=='student'){
                    //DohvatiKolegijeStudenta($_GET['id']);
                }
            }
            if($_GET['akcija']=='dohvatiPoDanu'){
                if($_GET['uloga']=='profesor'){
                    if($_GET['danIzvodenja'] == 'all'){
                        
                    }
                    else /*if($_GET['tipAktivnosti'] == 'seminar')*/{
                        DohvatiAktivnostiProfesoraPoDanuIzvodenja($_GET['idUloge'], $_GET['danIzvodenja']);
                    }
                }
                else if($_GET['uloga']=='student'){
                    if($_GET['danIzvodenja'] == 'all'){
                        
                    }
                    else /*if($_GET['tipAktivnosti'] == 'seminar')*/{
                        DohvatiAktivnostiStudentaPoDanuIzvodenja($_GET['idUloge'], $_GET['danIzvodenja']);
                    }
                }
                if($_GET['uloga']=='student'){
                    //DohvatiKolegijeStudenta($_GET['id']);
                }
            }
            if($_GET['akcija']=='nova'){
                if($_GET['uloga']=='profesor'){
                    DodajAktivnostProfesora($_POST['profesor'], $_POST['dozvoljenoIzostanaka'], $_POST['pocetak'], $_POST['kraj'], $_POST['danIzvodenja'], $_POST['dvorana'], $_POST['kolegij'], $_POST['tipAktivnosti'], $_POST['pocetakUpisa'], $_POST['krajUpisa']);
                }
            }
            if($_GET['akcija']=='azuriraj'){
            }
            if($_GET['akcija']=='obrisi'){
            }
        }
        
        if($_GET['metoda']== 'evidencija'){
            if($_GET['akcija']== 'dohvati'){
                if($_GET['uloga']== 'student'){
                    DohvacanjeEvidencijeStudenta($_GET['idUloge'],$_GET['kolegij'],$_GET['tipAktivnosti']);
                }
            }
        }
        
        if($_GET['metoda']=='evidentiraj'){
            if($_GET['akcija']=='generirajLozinku'){
                GenerirajLozinkuZaPrisustvo($_POST['aktivnost'],$_POST['tjedanNastave']);
            }
            if($_GET['akcija']=='zabiljeziLozinkom'){
                ZabiljeziPrisustvoLozinkom($_POST['student'], $_POST['lozinka'],$_POST['tjedanNastave'],$_POST['aktivnost']);
            }
        }
        if($_GET['metoda']== 'labosi'){
            if($_GET['akcija']== 'dohvati'){
                    DohvatiLabose($_GET['kolegij'], $_GET['student']);
            }
            if($_GET['akcija']== 'upisi'){
                UpisiLabos($_GET['student'], $_GET['aktivnost']);
            }
            if($_GET['akcija']== 'ponisti'){
                PonistiOdabirLabosa($_GET['student'], $_GET['aktivnost']);
            }
        }
    }
}
