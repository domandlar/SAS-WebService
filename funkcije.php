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
        while ($row = mysqli_fetch_assoc($rez)) {
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
function DodajKolegij($naziv, $semestar, $studij)
{
	$tekst = "";
    $upit = "INSERT INTO kolegij (naziv, semestar, studij) VALUES ('$naziv', '$semestar', '$studij')";
    DodajUBazu($upit);
    $message = "Kolegij je dodan u bazu.";
    DeliverResponse("OK", $message, "");
}
function AzurirajKolegij($idKolegija, $naziv, $semestar, $studij)
{
	$tekst = "";
    $upit ="UPDATE kolegij SET naziv='$naziv', semestar='$semestar', studij='$studij' WHERE id_kolegija='$idKolegija'"; 
    DodajUBazu($upit);
    $message = "Kolegij je ažururan.";
    DeliverResponse("OK", $message, "");
}
function DohvatiDvorane($tipDvorane){
    $tekst = "";
    $dvorane = array();
    $upit = "SELECT * FROM dvorana WHERE tip_dvorane = '$tipDvorane'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($rez)) {
            $pom = array('id_dvorane' => $row["id_dvorane"], 'naziv' => $row["naziv"], 'kapacitet' => $row["kapacitet"]);
            array_push($dvorane, $pom);
        }
        $message = "Pronađene dvorane.";
        DeliverResponse('OK', $message, $dvorane);
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
        while ($row = mysqli_fetch_assoc($rez)) {
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
function DohvatiAktivnostiProfesoraPoDanuIzvodenja($profesor, $danIzvodenja)
{
    $tekst = "";
    $aktivnosti = array();
    $upit = "SELECT id_aktivnosti, kolegij.naziv kolegij, tip_aktivnosti.naziv tip_aktivnosti, pocetak, kraj, dvorana.naziv dvorana, dan_izvodenja FROM aktivnost JOIN aktivnost_has_profesor ON id_aktivnosti=aktivnost_id 
        JOIN profesor ON profesor_id=id_profesora JOIN kolegij ON kolegij_id=id_kolegija JOIN tip_aktivnosti ON tip_aktivnosti_id=id_tip_aktivnosti JOIN dvorana ON id_dvorane=dvorana_id WHERE id_profesora='$profesor' AND dan_izvodenja='$danIzvodenja' ORDER BY pocetak";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($rez)) {
            $pom = array('id' => $row["id_aktivnosti"], 'kolegij' => $row["kolegij"], 'tip_aktivnosti' => $row["tip_aktivnosti"], 'pocetak' => $row["pocetak"], 'kraj' => $row["kraj"], 'dvorana' => $row["dvorana"], 'dan_izvodenja' => $row["dan_izvodenja"]);
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
function DohvatiAktivnostiStudentaPoDanuIzvodenja($student, $danIzvodenja)
{
    $tekst = "";
    $aktivnosti = array();
    $upit = "SELECT id_aktivnosti, kolegij.naziv kolegij, tip_aktivnosti.naziv tip_aktivnosti, pocetak, kraj, dvorana.naziv dvorana, dan_izvodenja FROM aktivnost JOIN student_has_aktivnost ON id_aktivnosti=aktivnost_id 
        JOIN student ON student_id=id_studenta JOIN kolegij ON kolegij_id=id_kolegija JOIN tip_aktivnosti ON tip_aktivnosti_id=id_tip_aktivnosti JOIN dvorana ON id_dvorane=dvorana_id WHERE id_studenta='$student' AND dan_izvodenja='$danIzvodenja' ORDER BY pocetak";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($rez)) {
            $pom = array('id' => $row["id_aktivnosti"], 'kolegij' => $row["kolegij"], 'tip_aktivnosti' => $row["tip_aktivnosti"], 'pocetak' => $row["pocetak"], 'kraj' => $row["kraj"], 'dvorana' => $row["dvorana"], 'dan_izvodenja' => $row["dan_izvodenja"]);
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
    JOIN dvorana ON id_dvorane=dvorana_id JOIN kolegij ON kolegij_id=id_kolegija WHERE id_profesora='$profesor' AND tip_aktivnosti_id='$tipAktivnost'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($rez)) {
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

function DodajAktivnostProfesora($profesor, $dozvoljenoIzostanaka, $pocetak, $kraj, $danIzvodenja, $dvorana, $kolegij, $tipAktivnost){
    $tekst = "";
    $aktivnosti = array();
    $tipAktivnost = DohvatiIdTipaAktivnosti($tipAktivnost);
    $upit = "INSERT INTO aktivnost (dozvoljeno_izostanaka, pocetak, kraj, dan_izvodenja, dvorana_id, kolegij_id, tip_aktivnosti_id) 
    VALUES ('$dozvoljenoIzostanaka', '$pocetak', '$kraj', '$danIzvodenja', '$dvorana', '$kolegij', '$tipAktivnost')";
    DodajUBazu($upit);
    $upit = "SELECT id_aktivnosti FROM aktivnost ORDER BY 1 DESC LIMIT 1";
    $rez = DohvatiIzBaze($upit);
    $row = mysqli_fetch_assoc($rez);
    $aktivnost = $row['id_aktivnosti'];
    $upit = "INSERT INTO aktivnost_has_profesor (aktivnost_id, profesor_id) VALUES ('$aktivnost', '$profesor')";
    DodajUBazu($upit);
    $message = "Korisnik je dodan u bazu.";
    DeliverResponse("OK", $message, "");
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
    $upit = "SELECT id_tip_aktivnosti FROM tip_aktivnosti WHERE naziv ='$nazivTipaAktivnosti'";
    $rez = DohvatiIzBaze($upit);
    $row = mysqli_fetch_assoc($rez);
    return $row['id_tip_aktivnosti'];
}
function DohvatiAktivnostiStudentaPoTipuAktivnosti($student, $tipAktivnost)
{
    $tekst = "";
    $aktivnosti = array();
    $tipAktivnost = DohvatiIdTipaAktivnosti($tipAktivnost);
    $upit = "SELECT id_aktivnosti, dozvoljeno_izostanaka, pocetak, kraj, dan_izvodenja, dvorana.naziv dvorana, kolegij.naziv kolegij FROM aktivnost JOIN student_has_aktivnost ON id_aktivnosti=aktivnost_id JOIN student ON student_id=id_studenta 
    JOIN dvorana ON id_dvorane=dvorana_id JOIN kolegij ON kolegij_id=id_kolegija WHERE id_studenta='$student' AND tip_aktivnosti_id='$tipAktivnost'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($rez)) {
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

function DohvacanjeEvidencijeStudenta($idStudenta, $kolegij, $aktivnost)
{
    $tekst = "";
    $dolasci = array();
    $upit = "SELECT k.naziv AS kolegij ,ta.naziv AS aktivnost,a.dan_izvodenja,d.prisutan, d.tjedan_nastave 
    FROM kolegij k, tip_aktivnosti ta, aktivnost a, dolasci d, student s, student_has_aktivnost sha, student_has_kolegij shk
     WHERE a.kolegij_id=k.id_kolegija AND a.tip_aktivnosti_id=ta.id_tip_aktivnosti AND d.aktivnost_id=a.id_aktivnosti 
     AND s.id_studenta=sha.student_id AND sha.aktivnost_id=a.id_aktivnosti AND shk.student_id=s.id_studenta AND k.id_kolegija=shk.kolegij_id 
     AND s.id_studenta='$idStudenta' AND k.id_kolegija='$kolegij' and a.tip_aktivnosti_id='$aktivnost'";
    $rez = DohvatiIzBaze($upit);
    if ($rez->num_rows > 0) {
        while ($row = $rez->mysqli_fetch_assoc()) {
            $pom = array('kolegij' => $row["kolegij"], 'aktivnost' => $row["aktivnost"],'prisutan' => $row["prisutan"], 'tjedanNastave' => $row["tjedan_nastave"], 'dan_izvodenja' => $row["dan_izvodenja"]);
            array_push($dolasci, $pom);
        }
        $message = "Pronađene evidencije.";
        DeliverResponse('OK', $message, $dolasci);
    }else {
        $pom = array('id' => "-1", 'naziv' => "");
        array_push($dolasci, $pom);
        $message = "Nema zapisa u bazi.";
        DeliverResponse('NOT OK', $message, $dolasci);
    }

}