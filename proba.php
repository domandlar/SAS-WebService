<?php
include_once "./baza.class.php";
header('Content-Type:application/json');
$tekst = "";
$dolasci = array();
$idStudenta=3;
$kolegij=1;
$aktivnost=2;
$upit = "SELECT k.naziv AS kolegij ,ta.naziv AS aktivnost,a.dan_izvodenja,d.prisutan, d.tjedan_nastave 
FROM kolegij k, tip_aktivnosti ta, aktivnost a, dolasci d, student s, student_has_aktivnost sha, student_has_kolegij shk
 WHERE a.kolegij_id=k.id_kolegija AND a.tip_aktivnosti_id=ta.id_tip_aktivnosti AND d.aktivnost_id=a.id_aktivnosti 
 AND s.id_studenta=sha.student_id AND sha.aktivnost_id=a.id_aktivnosti AND shk.student_id=s.id_studenta AND k.id_kolegija=shk.kolegij_id 
 AND s.id_studenta=$idStudenta AND k.id_kolegija=$kolegij and a.tip_aktivnosti_id=$aktivnost";
$rez = DohvatiIzBaze($upit);
//print_r($rez);
echo "<br>";
$row = mysqli_fetch_assoc($rez);
print_r($row);
echo "<br>";
/*if ($rez->num_rows > 0) {
    while ($row = $rez->mysqli_fetch_assoc()) {
        print_r($row);
        echo "<br>";
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
}*/

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
