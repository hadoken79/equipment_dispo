<?php
require_once('./db/PdoConnector.php');

if (isset($_GET['checkdate'])) {
    $date = $_GET['checkdate'];
    checkforBooking($date);
} else if (isset($_POST['set'])) {
    $id = filter_var($_POST['set'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
    $user = filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS);
    bookSet($id, $date, $user);
    //sleep(1000);
    //echo "Set mit der id: " . $id . " wurde für " . $user . " am " . $date . " gebucht.<br> Technik wird informiert";
} else if (isset($_POST['eqp'])) {
    $id = filter_var($_POST['eqp'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
    $user = filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS);
    bookEquipment($id, $date, $user, false);
    //sleep(1000);
    //echo "Equipment mit der id: " . $eqp . " wurde für " . $user . " am " . $date . " gebucht.Technik wird informiert";
}

function checkforBooking($date)
{
    $pdo = PdoConnector::getConn();
    $bookquery = "SELECT 
    equipment.equipment_id AS eq_id,
    equipment.name AS eq_name,
    set_.set_id AS set_id,
    set_.name AS set_name
    FROM buchung 
    LEFT JOIN equipment ON buchung.equipment_id = equipment.equipment_id
    LEFT JOIN set_ ON equipment.set_id = set_.set_id
    WHERE reserviert_fuer = ? AND storniert=false;";

    $stmt = $pdo->prepare($bookquery);
    $stmt->execute([$date]);
    $bookings = $stmt->fetchAll();
    echo Json_encode($bookings);
}

function bookEquipment($id, $date, $user, $callFromSet)
{
    $pdo = PdoConnector::getConn();
    $eqbookquery = "INSERT INTO buchung(user, reserviert_fuer, equipment_id)
    VALUES (?, ?, ?);";
    $stmt = $pdo->prepare($eqbookquery);
    if ($stmt->execute([$user, $date, $id])) {
        if (!$callFromSet) {
            echo "Equipment wurde für " . $user . " am " . $date . " gebucht.<br> Technik wird informiert";
        };
    } else {
        echo "Buchung hat nicht geklappt, bitte mit der Technik anschauen";
    }
    $pdo = null;
}


function bookSet($id, $date, $user)
{
    $pdo = PdoConnector::getConn();
    $eq_inset_query = "SELECT equipment_id FROM equipment WHERE set_id =?;";
    $stmt = $pdo->prepare($eq_inset_query);
    $stmt->execute([$id]);
    $eqs_ids = $stmt->fetchAll();

    //ruft für jedes equipment im Set die ogrige funktion auf
    foreach ($eqs_ids as $eq_id) {
        bookEquipment($eq_id->equipment_id, $date, $user, true);
    }
    echo "Set wurde für " . $user . " am " . $date . " gebucht.<br> Technik wird informiert";
}
