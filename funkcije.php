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
function DohvatiKolegijeProfesora($profesor)
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
    $upit = "SELECT * FROM aktivnost JOIN profesor_has_aktivnost ON id_aktivnosti=aktivnost_id JOIN profesor ON profesr_id=id_profesora JOIN dvorana ON id_dvorane=dvorana_id WHERE id_profesora='$profesor'";
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