<?php

require_once('./db/PdoConnector.php');


if (!isset($_SESSION)) {
    session_start();
}


if (isset($_GET['checkdate'])) {
    $date = $_GET['checkdate'];
    checkforBooking($date);
} else if (isset($_POST['set'])) {
    $id = filter_var($_POST['set'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
    $user = $_SESSION['user'];
    bookSet($id, $date, $user);
} else if (isset($_POST['eqp'])) {
    $id = filter_var($_POST['eqp'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
    $user = $_SESSION['user'];
    bookEquipment($id, $date, $user, false);
} else if (isset($_POST['cancelId'])) {
    $id = filter_var($_POST['cancelId'], FILTER_SANITIZE_SPECIAL_CHARS);
    cancelBooking($id);
} else if (isset($_POST['search'])) {
    $searchstring = $_POST['search'];
    $searchIn = isset($_POST['searchIn']) ? filter_var($_POST['searchIn'], FILTER_SANITIZE_SPECIAL_CHARS) : 'name';


    searchEquipment($searchstring, $searchIn);
}
//wird vom Client angefordert um Buchungen für gewähltes DAtum zu erfahren
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

function getNextBookings($limit, $order = 'reserviert_fuer', $dir = 'ASC')
{
    $today = date('Y-m-d');

    $bookQuery = "SELECT 
    buchung.buchung_id, 
    buchung.equipment_id, 
    buchung.user, 
    buchung.reserviert_fuer,
    buchung.gebucht_am,
    equipment.set_id AS setID,
    equipment.beschrieb AS EQname,
    set_.name AS setname
    FROM buchung LEFT JOIN equipment
    ON buchung.equipment_id = equipment.equipment_id LEFT JOIN set_
    ON equipment.set_id = set_.set_id WHERE storniert = false AND buchung.reserviert_fuer >= ? ORDER BY $order $dir LIMIT $limit;";
    $pdo = PdoConnector::getConn();
    $stmt = $pdo->prepare($bookQuery);
    if ($stmt->execute([$today])) {
        $bookings = $stmt->fetchAll();
        $pdo = null;

        //Bei Sets werden Buchungen für alle beinhalteten Equipments ausgelöst, desshalb werden hier doppler gefiltert.
        //Zur besseren Übersicht reicht es, wenn im Falle eines Sets auch nur das Set als Ganzes in der Buchng erscheint.
        $datecache = '';
        $titelcache = '';

        $cleanbookings = array();

        foreach ($bookings as $booking) {
            $readDate = date('d-m-Y', strtotime($booking->reserviert_fuer));
            $titel = !isset($booking->setID) ? $booking->EQname : $booking->setname;
            $user = $booking->user;
            $bDate = $booking->gebucht_am;


            if ($datecache === $readDate && $titelcache === $titel) {
                continue;
            } else {
                //$elem = array('id' => $booking->buchung_id, 'buchung' => $readDate . ' | ' . $titel . ' reserviert für ' . $user);
                $elem = array('id' => $booking->buchung_id, 'rdate' => $readDate, 'bdate' => $bDate, 'name' => $titel, 'user' => $user);
                array_push($cleanbookings, $elem);

                $datecache = $readDate;
                $titelcache = $titel;
            }
        }
        return $cleanbookings;
    }
}

function getAllBookings($limit, $order = 'reserviert_fuer', $dir = 'ASC')
{ //Ansicht für Technik. Sets werden ignoriert, die Equipmentbuchungen werden im Detail angezeigt.
    $today = date('Y-m-d');

    $bookQuery = "SELECT 
    buchung.buchung_id, 
    buchung.equipment_id, 
    buchung.user, 
    buchung.reserviert_fuer,
    buchung.gebucht_am,
    equipment.name,
    equipment.beschrieb
    FROM buchung LEFT JOIN equipment
    ON buchung.equipment_id = equipment.equipment_id
    WHERE storniert = false AND buchung.reserviert_fuer >= ? ORDER BY $order $dir LIMIT $limit;";
    $pdo = PdoConnector::getConn();
    $stmt = $pdo->prepare($bookQuery);
    if ($stmt->execute([$today])) {
        $bookings = $stmt->fetchAll();
        $pdo = null;
        return $bookings;
    }
}

function bookEquipment($id, $date, $user, $callFromSet)
{
    $pdo = PdoConnector::getConn();
    $eqbookquery = "INSERT INTO buchung(user, reserviert_fuer, equipment_id)
    VALUES (?, ?, ?);";
    $stmt = $pdo->prepare($eqbookquery);
    if ($stmt->execute([$user, $date, $id])) {
        //user erhält aus dieser Funktion nur eine Benachrichtigung bei einzelnem equipment.
        //für Sets macht das die bookSet funktion.
        if (!$callFromSet) {
            //test um langsame Verbindung und loader zu testen.
            //sleep(5);
            $sqldate = strtotime($date);
            $readabledate = date('d-M-Y', $sqldate);
            echo 'Equipment wurde für ' . $user .  ' <br>am '  . $readabledate . ' gebucht. <br> Technik wird informiert';
            //mail oder slack an technik
        };
    } else {
        echo "Buchung hat nicht geklappt, <br>bitte mit der Technik in Verbindung setzten";
    }
    $pdo = null;
}


function bookSet($id, $date, $user)
{
    $pdo = PdoConnector::getConn();
    $eq_insert_query = "SELECT equipment_id FROM equipment WHERE set_id =?;";
    $stmt = $pdo->prepare($eq_insert_query);
    $stmt->execute([$id]);
    $eqs_ids = $stmt->fetchAll();

    if (empty($eqs_ids)) {
        echo 'Diesem Set scheint kein Equipment zugewiesen zu sein. <br> Bitte Technik kontaktieren.';
        exit();
    }

    //ruft für jedes equipment im Set die obrige funktion auf.
    foreach ($eqs_ids as $eq_id) {
        bookEquipment($eq_id->equipment_id, $date, $user, true);
    }
    $pdo = null;
    echo "Set wurde für " . $user . " am " . $date . " gebucht.<br> Technik wird informiert";
    //mail oder slack an technik
}

function cancelBooking($id)
{
    $pdo = PdoConnector::getConn();
    $checkIfSetQuery = "SELECT 
        buchung.buchung_id, 
        buchung.equipment_id, 
        buchung.user, 
        buchung.reserviert_fuer,
        equipment.set_id AS setID,
        set_.name AS setname
        FROM buchung LEFT JOIN equipment
        ON buchung.equipment_id = equipment.equipment_id LEFT JOIN set_
        ON equipment.set_id = set_.set_id WHERE storniert = false AND buchung_id = ?;";
    $stmt = $pdo->prepare($checkIfSetQuery);
    if ($stmt->execute([$id])) {
        $booking = $stmt->fetch();
        $cancelIds = array();
    } else {
        return "keine Antwort von der Datenbank";
    }
    $pdo = null;


    if (is_null($booking->setID)) {
        array_push($cancelIds, $booking->buchung_id);
    } else {
        $pdo = PdoConnector::getConn();
        $getSetBookings = "SELECT 
        buchung_id, 
        buchung.user, 
        buchung.reserviert_fuer, 
        equipment.set_id AS setID
        FROM buchung 
        LEFT JOIN equipment
        ON buchung.equipment_id = equipment.equipment_id 
        LEFT JOIN set_
        ON equipment.set_id = set_.set_id WHERE equipment.set_id IS NOT NULL  AND storniert = false AND buchung.user = :user AND buchung.reserviert_fuer = :datum;";
        $stmt = $pdo->prepare($getSetBookings);
        $stmt->execute(['user' => $booking->user, 'datum' => $booking->reserviert_fuer]);
        $result = $stmt->fetchAll();
        $pdo = null;
        foreach ($result as $elem) {
            array_push($cancelIds, $elem->buchung_id);
        }
    }

    foreach ($cancelIds as $id) {
        $stat = false;
        $pdo = PdoConnector::getConn();
        $stmt = $pdo->prepare("UPDATE buchung SET storniert = true WHERE buchung_id = ?");
        if ($stmt->execute([$id])) {
            $stat = true;
        }
        $pdo = null;
    }

    if ($stat) {
        echo "Buchung storniert";
    };
}

function searchEquipment($searchstring, $searchIn)
{
    $pdo = PdoConnector::getConn();
    $searchQuery = "SELECT equipment_id FROM equipment WHERE $searchIn LIKE :searchstring AND geloescht = false;";
    $searchstring = "%$searchstring%";
    $stmt = $pdo->prepare($searchQuery);
    $stmt->execute(array('searchstring' => $searchstring));
    $pdo = null;
    $result = $stmt->fetchAll();
    echo Json_encode($result);
}
