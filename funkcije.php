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
function DeliverResponse($status, $message, $data)
{
    $response['status'] = $status;
    $response['message'] = $message;

    $response['data'] = json_encode($data);
    $json_response = json_encode($response);
    echo $json_response;
}
function PrijavaProfesora($email, $lozinka)
{
    $tekst = "";
    $status = 0;
    if (isset($email)) {
        if (empty($email)) {
            $tekst .= "Niste unijeli tekst. \n";
        } else {

        }
    } else {
        $tekst .= "Nedostaje parametar s emailom. \n";
    }
    if (isset($lozinka)) {
        if (empty($lozinka)) {
            $tekst .= "Niste unijeli lozinku. \n";
        }
    } else {
        $tekst .= "Nedostaje parametar s emailom. \n";
    }
    if(!empty($email) && !empty($lozinka)){
        $upit = "SELECT * FROM profesor WHERE email='$email'";
        $rez = DohvatiIzBaze($upit);
        if ($rez->num_rows < 1) {
            $tekst .= "Korisnik ne postoji u bazi. \n";
        } else {
            $row = mysqli_fetch_assoc($rez);
            $lozinka = HashirajLozinku($row['email'], $lozinka);
            $idProfesora = $row['id_profesora'];
            $status = 1;
            if ($lozinka != $row["lozinka"]) {
                $tekst .= "Lozinke se ne podudaraju. \n";
            }
        }
    }
    if ($tekst == "") {
        if ($status != 0) {
            DeliverResponse('OK', 'Uspješna prijava', array('id_profesora' => $idProfesora));
        } else {
            DeliverResponse('OK', 'Došlo je do problema na webservisu', array('prijava' => "error"));
        }
    } else {
        DeliverResponse('NOT OK', $tekst, array('prijava' => "error"));
    }
}
function PrijavaStudenta($email, $lozinka)
{
    $tekst = "";
    $status = 0;
    if (isset($email)) {
        if (empty($email)) {
            $tekst .= "Niste unijeli email. \n";
        } else {

        }
    } else {
        $tekst .= "Nedostaje parametar s emailom. \n";
    }
    if (isset($lozinka)) {
        if (empty($lozinka)) {
            $tekst .= "Niste unijeli lozinku. \n";
        }
    } else {
        $tekst .= "Nedostaje parametar s lozinkom. \n";
    }
    if(!empty($email) && !empty($lozinka)){
        $upit = "SELECT * FROM student WHERE email='$email'";
        $rez = DohvatiIzBaze($upit);
        if ($rez->num_rows < 1) {
            $tekst .= "Korisnik ne postoji u bazi. \n";
        } else {
            $row = mysqli_fetch_assoc($rez);          
            $lozinka = HashirajLozinku($row['email'], $lozinka);
            $idStudenta = $row['id_studenta'];
            $status = 1;
            if ($lozinka != $row["lozinka"]) {
                $tekst .= "Lozinke se ne podudaraju. \n";
            }

        }
    }
    
    if ($tekst == "") {
        if ($status != 0) {
            DeliverResponse('OK', 'Uspješna prijava', array('id_studenta' => $idStudenta));
        } else {
            DeliverResponse('OK', 'Došlo je do problema na webservisu', array('prijava' => "error"));
        }
    } else {
        DeliverResponse('NOT OK', $tekst, array('prijava' => "error"));
    }
}
function RegistracijaProfesora($ime, $prezime, $email, $titula)
{
    $tekst = "";
    if (empty($ime)) {
        $tekst .= "Nije uneseno ime. \n";
    }

    if (empty($prezime)) {
        $tekst .= "Nije uneseno prezime. \n";
    }
    if (empty($email)) {
        $tekst .= "Nije unesen email. \n";
    }

    if (empty($titula)) {
        $tekst .= "Nije unesena titula. \n";
    }

    if ($tekst == "") {
        $lozinka = IzgenerirajLozinku();
        $hashiranaLozinka = HashirajLozinku($email, $lozinka);
        $upit = "INSERT INTO profesor (ime, prezime, titula, email, lozinka) VALUES ('$ime', '$prezime', '$titula', '$email', '$hashiranaLozinka')";
        DodajUBazu($upit);
    }
    if ($tekst == "") {
        DeliverResponse('OK', 'Uspješna registracija', array('email' => $email, 'lozinka' => $lozinka));
    } else {
        DeliverResponse('NOT OK', $tekst, array('registracija' => "error"));
    }
}
function RegistracijaStudenta($ime, $prezime, $email)
{
    $tekst = "";
    if (empty($ime)) {
        $tekst .= "Nije uneseno ime. \n";
    }

    if (empty($prezime)) {
        $tekst .= "Nije uneseno prezime. \n";
    }
    if (empty($email)) {
        $tekst .= "Nije unesen email. \n";
    }

    if ($tekst == "") {
        $lozinka = IzgenerirajLozinku();
        $hashiranaLozinka = HashirajLozinku($email, $lozinka);
        $upit = "INSERT INTO student (ime, prezime, email, lozinka) VALUES ('$ime', '$prezime', '$email', '$hashiranaLozinka')";
        DodajUBazu($upit);
        $upit = "SELECT id_studenta FROM student ORDER BY 1 DESC LIMIT 1";
        $rez = DohvatiIzBaze($upit);
        $student = mysqli_fetch_assoc($rez);
        SpremiSliku($student['id_studenta']);
    }
    if ($tekst == "") {
        DeliverResponse('OK', 'Uspješna registracija', array('email' => $email, 'lozinka' => $lozinka));
    } else {
        DeliverResponse('NOT OK', $tekst, array('registracija' => "error"));
    }
}
function IzgenerirajLozinku()
{
    $znakovi = "QWERTZUIOPASDFGHJKLYXCVBNMqwertzuiopasdfghjklyxcvbnm1234567890";
    $lozinka = array();
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, strlen($znakovi) - 1);
        $lozinka[] = $znakovi[$n];
    }
    return implode($lozinka);
}
function HashirajLozinku($email, $lozinka)
{
    $salt = hash("sha256", $email);
    return hash('sha256', $lozinka . $salt);
}
function SpremiSliku($student)
{
    $slika = array();
    $slika['name'] = $_FILES['slika']['name'];
    $slika['tmp_name'] = $_FILES['slika']['tmp_name'];
    $slika['size'] = $_FILES['slika']['size'];
    $tipSlike = end(explode('.', $slika['name']));
    $nazivSlike = uniqid('', true) . '.' . $tipSlike;
    if(!is_dir("slike"))
        mkdir("slike");
    if(!is_dir($student))
        mkdir("slike/" . $student);
    $link = 'slike/' . $student . '/' . $nazivSlike;
    move_uploaded_file($slika['tmp_name'], $link);
    $datum = date('Y:m:d');
    $upit = "INSERT INTO slika (link, datum_slikanja, student_id) VALUES ('$nazivSlike', '$datum', '$student')";
    DodajUBazu($upit);
}
function DohvatiKolegijeProfesora($profesor)
{
    $tekst = "";
    $kolegiji = array();
    $upit = "SELECT * FROM kolegij JOIN profesor_has_kolegij ON id_kolegija=kolegij_id JOIN profesor ON profesor_id=id_profesora WHERE id_profesora='$profesor'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('id' => $row["id_kolegija"], 'naziv' => $row["naziv"], 'semestar' => $row["semestar"], 'studij' => $row["studij"]);
            array_push($kolegiji, $pom);
        }
        $message = "Pronađeni kolegiji.";
        DeliverResponse('OK', $message, $kolegiji);
    } else {
        $pom = array('id' => "-1", 'naziv' => "");
        array_push($kolegiji, $pom);
        $message = "Nema zapisa u bazi.";
        DeliverResponse('NOT OK', $message, $kolegiji);
    }
}
function DohvatiAktivnostiProfesora($profesor)
{
    $tekst = "";
    $aktivnosti = array();
    $upit = "SELECT * FROM aktivnost JOIN aktivnost_has_profesor ON id_aktivnosti=aktivnost_id JOIN profesor ON profesor_id=id_profesora JOIN dvorana ON id_dvorane=dvorana_id WHERE id_profesora='$profesor'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('id' => $row["id_aktivnosti"], 'naziv' => $row["naziv"], 'semestar' => $row["semestar"], 'studij' => $row["studij"]);
            array_push($aktivnosti, $pom);
        }
        $message = "Pronađene aktivnosti.";
        DeliverResponse('OK', $message, $aktivnosti);
    } else {
        $pom = array('id' => "-1", 'naziv' => "");
        array_push($aktivnosti, $pom);
        $message = "Nema zapisa u bazi.";
        DeliverResponse('NOT OK', $message, $aktivnosti);
    }
}
function DohvatiAktivnostiProfesoraPoTipuAktivnosti($profesor, $tipAktivnost)
{
    $tekst = "";
    $aktivnosti = array();
    $tipAktivnost = DohvatiIdTipaAktivnosti($tipAktivnost);
    $upit = "SELECT id_aktivnosti, dozvoljeno_izostanaka, pocetak, kraj, dan_izvodenja, dvorana.naziv dvorana, kolegij.naziv kolegij FROM aktivnost JOIN aktivnost_has_profesor ON id_aktivnosti=aktivnost_id JOIN profesor ON profesor_id=id_profesora 
    JOIN dvorana ON id_dvorane=dvorana_id JOIN kolegij ON kolegij_id=id_kolegija WHERE id_profesora='$profesor' AND tip_aktivnosti='$tipAktivnost'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('id' => $row["id_aktivnosti"], 'kolegij' => $row["kolegij"], 'dan_izvodenja' => $row["dan_izvodenja"], 'pocetak' => $row["pocetak"], 'kraj' => $row["kraj"], 'dozvoljeno_izostanaka' => $row["dozvoleno_izostanaka"], 'dvorana' => $row["dvorana"]);
            array_push($aktivnosti, $pom);
        }
        $message = "Pronađene aktivnosti.";
        DeliverResponse('OK', $message, $aktivnosti);
    } else {
        $pom = array('id' => "-1", 'naziv' => "");
        array_push($aktivnosti, $pom);
        $message = "Nema zapisa u bazi.";
        DeliverResponse('NOT OK', $message, $aktivnosti);
    }
}
function DohvatiAktivnostiProfesoraPoKolegiju($profesor, $kolegij)
{
    $tekst = "";
    $kolegiji = array();
    $upit = "SELECT * FROM kolegij JOIN profesor_has_kolegij ON id_kolegija=kolegij_id JOIN profesor ON profesr_id=id_profesora WHERE id_profesora='$profesor'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('id' => $row["id_kolegija"], 'naziv' => $row["naziv"], 'semestar' => $row["semestar"], 'studij' => $row["studij"]);
            array_push($kolegiji, $pom);
        }
        $message = "Pronađene aktivnosti.";
        DeliverResponse('OK', $message, $aktivnosti);
    } else {
        $pom = array('id' => "-1", 'naziv' => "");
        array_push($vrsta, $pom);
        $message = "Nema zapisa u bazi.";
        DeliverResponse('NOT OK', $message, $aktivnosti);
    }
    //Završit!!!!
}
function DohvatiIdTipaAktivnosti($nazivTipaAktivnosti)
{
    $upit = "SELECT id_tip_aktivnost WHERE naziv ='$nazivTipaAktivnosti'";
    $rez = DohvatiIzBaze($upit);
    $row = mysqli_fetch_assoc($rez);
    return $row['id_tip_aktivnosti'];
}
function DohvacanjeEvidencije($kolegij, $aktivnost)
{
    $tekst = "";
    $dolasci = array();
    $studenti = array();
    $upit = "SELECT * FROM aktivnost JOIN kolegij ON kolegij_id=id_kolegija WHERE id_aktivnosti='$aktivnost' AND id_kolegija='$kolegij'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('id_kolegija' => $row["id_kolegija"], 'naziv_kolegija' => $row["id_aktivnosti"], 'dozvoljeno_izostanaka' => $row["dozvoljeno_izostanaka"],
                'pocetak' => $row["pocetak"], 'kraj' => $row["kraj"], 'dan_izvodenja' => $row["dan_izvodenja"]);
            array_push($dolasci, $pom);
        }
    }
    $upit = "SELECT * FROM dolasci JOIN aktivnost ON aktivnost_id=id_aktivnosti JOIN kolegij ON kolegij_id=id_kolegija WHERE id_aktivnosti='$aktivnost' AND id_kolegija='$kolegij'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('student_id' => $row["student_id"], 'tjedan_nastave' => $row["tjedan_nastave"], 'prisutan' => $row["prisutan"]);
            array_push($studenti, $pom);
        }
        array_push($dolasci, $studenti);
    }
    //DelivrResponse...

}
function DohvacanjeEvidencijePoTerminu($kolegij, $aktivnost, $tjedanNastave)
{
    $tekst = "";
    $dolasci = array();
    $studenti = array();
    $upit = "SELECT * FROM aktivnost JOIN kolegij ON kolegij_id=id_kolegija WHERE id_aktivnosti='$aktivnost' AND id_kolegija='$kolegij'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('id_kolegija' => $row["id_kolegija"], 'naziv_kolegija' => $row["id_aktivnosti"], 'dozvoljeno_izostanaka' => $row["dozvoljeno_izostanaka"],
                'pocetak' => $row["pocetak"], 'kraj' => $row["kraj"], 'dan_izvodenja' => $row["dan_izvodenja"]);
            array_push($dolasci, $pom);
        }
    }
    $upit = "SELECT * FROM dolasci JOIN aktivnost ON aktivnost_id=id_aktivnosti JOIN kolegij ON kolegij_id=id_kolegija WHERE id_aktivnosti='$aktivnost' AND id_kolegija='$kolegij' AND tjedan_nastave='$tjedanNastave'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('student_id' => $row["student_id"], 'prisutan' => $row["prisutan"]);
            array_push($studenti, $pom);
        }
        array_push($dolasci, $studenti);
    }
    //DelivrResponse...

}
