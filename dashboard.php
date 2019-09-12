<?php
/*created by lp - 31.08.2019*/
$headTitle = "Dashboard";
require_once('./base/header.php');
$msg = '';
$msgClass = '';

// Form val's
$selectKategories = getKategories();
$selectSets = getSets();
$selectLagerorte = getLagerorte();
$selectLieferanten = getLieferanten();

if (isset($_GET['success'])) {
    $msg = 'Eintrag wurde erfolgreich erstellt';
    $msgClass = 'card-panel teal accent-2';
}

if (isset($_POST['action'])) {
    // POST val's
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $beschrieb = isset($_POST['beschrieb']) ? filter_var($_POST['beschrieb'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $serien_nr = isset($_POST['serien_nr']) ? filter_var($_POST['serien_nr'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $barcode = isset($_POST['barcode']) ? filter_var($_POST['barcode'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $kaufjahr = isset($_POST['kaufjahr']) ? filter_var($_POST['kaufjahr'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $kaufpreis = isset($_POST['kaufpreis']) ? filter_var($_POST['kaufpreis'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $kategorie_id = isset($_POST['kategorie_id']) ? filter_var($_POST['kategorie_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $set_id = isset($_POST['set_id']) ?  filter_var($_POST['set_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $lagerort_id = isset($_POST['lagerort_id']) ? filter_var($_POST['lagerort_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $lieferant_id = isset($_POST['lieferant_id']) ? filter_var($_POST['lieferant_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $indispo = ($_POST['indispo'] == 'on') ? true : false;
    $aktiv = ($_POST['aktiv'] == 'on') ? true : false;
    $filename = isset($_POST['filename']) ? filter_var($_POST['filename'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $notiz = isset($_POST['notiz']) ? filter_var($_POST['notiz'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $bild_id = '';
    //wird nachher per Reverenz an inserEquipment übergeben. variable wird nach erstellung für Lieferant_equipment benötigt
    $equipment_id = '';

    // Obligatorische Felder prüfen.
    if (!empty($name) && !empty($kategorie_id) && !empty($serien_nr)) {

        //Bevor das Equipment gespeichert werden kann, muss falls ein Equipmentbild gesetzt ist, noch dessen id erzeugt werden.
        if (isset($filename)) {
            $bild_id = insertFilename($filename);
        };

        if (insertEquipment($equipment_id, $name, $beschrieb, $serien_nr, $barcode, $kaufjahr, $kaufpreis, $kategorie_id, $set_id, $lagerort_id, $indispo, $aktiv, $notiz, $bild_id)) {

            Header('Location: equipment.php?success=1');

            //falls ein Lieferant gesetzt wurde, kann jetzt noch der Lieferant_Equipment-Table ergänzt werden
            if (isset($lieferant_id)) {
                insertLieferant_Equipment($lieferant, $equipment_id);
            };
        } else {
            $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
            $msgClass = 'card-panel red lighten-1';
        }
    } else {
        $msg = 'Name, Kategorie und Seriennummer sind zwingend';
        $msgClass = 'card-panel red lighten-1';
    }
}
function insertFileName($filename)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO equipmentbild(
            `filename`,
        )
        VALUES
            (?);";
    $stmt = $pdo->prepare($insertQuery);
    if ($stmt->execute([$filename])) {
        $bild_id = $pdo->query("SELECT bild_id FROM equipmentbild WHERE filename = $filename");
        $pdo = null;
        return $bild_id;
    }
}

function insertLieferant_Equipment($lieferant_id, $equipment_id)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO lieferant_equipment(
            lieferant_id,
            equipment_id
        )
        VALUES
        (:equipment_id)
        (:lieferant_id);";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute(['equipment_id' => $equipment_id, 'lieferant_id' => $lieferant_id]);
    $pdo = null;
}

function insertEquipment(&$equipment_id, $name, $beschrieb, $serien_nr, $barcode, $kaufjahr, $kaufpreis, $kategorie_id, $set_id, $lagerort_id, $indispo, $aktiv, $notiz, $bild_id)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO equipment(
            `name`,
            beschrieb,
            notiz,
            serien_nr,
            barcode,
            indispo,
            aktiv,
            kaufjahr,
            kaufpreis,
            set_id,
            kategorie_id,
            bild_id,
            lagerort_id
        ) 
        VALUES
            (:name,
            :beschrieb,
            :notiz,
            :serien_nr,
            :barcode,
            :indispo,
            :aktiv,
            :kaufjahr,
            :kaufpreis,
            :set_id,
            :kategorie_id,
            :bild_id,
            :lagerort_id);";

    $stmt = $pdo->prepare($insertQuery);

    if ($stmt->execute(['name' => $name, 'beschrieb' => $beschrieb, 'notiz' => $notiz, 'serien_nr' => $serien_nr, 'barcode' => $barcode, 'indispo' => $indispo, 'aktiv' => $aktiv, 'kaufjahr' => $kaufjahr, 'kaufpreis' => $kaufpreis, 'set_id' => $set_id, 'kategorie_id' => $kategorie_id, 'bild_id' => $bild_id, 'lagerort_id' => $lagerort_id])) {
        $equipment_id = $pdo->query("SELECT equipment_id FROM equipment WHERE name == $name AND beschrieb = $beschrieb AND serien_nr == $serien_nr AND geloescht == false");
        $pdo = null;
        return true;
    }
}

function getKategories()
{
    $pdo = PdoConnector::getConn();
    $selectKategories = $pdo->query("SELECT kategorie_id, `name` FROM kategorie WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectKategories;
}

function getSets()
{
    $pdo = PdoConnector::getConn();
    $selectSets = $pdo->query("SELECT set_id, `name` FROM set_ WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectSets;
}

function getLagerorte()
{
    $pdo = PdoConnector::getConn();
    $selectLagerorte = $pdo->query("SELECT lagerort_id, `name` FROM lagerort WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectLagerorte;
}

function getLieferanten()
{
    $pdo = PdoConnector::getConn();
    $selectLieferanten = $pdo->query("SELECT lieferant_id, `firma` FROM lieferant WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectLieferanten;
}
?>

<main>

    <div class="row">
        <div class="col s12 m6">

            <div class="col s12 m6 l4">
                <div class="card small">
                    <div class="card-content">
                        <span class="card-title">Card Title</span>
                        <p>I am a very simple card. I am good at containing small bits of information.
                            I am convenient because I require little markup to use effectively.</p>
                    </div>
                    <div class="card-action">
                        <a href="equipment.php?id=4" class="btn-floating btn-medium waves-effect waves-light blue"><i class="material-icons">remove_red_eye</i></a>
                        <a class="btn-floating btn-medium waves-effect waves-light red"><i class="material-icons">delete</i></a>
                    </div>
                </div>
            </div>


        </div>
    </div>

</main>






<?php
require_once('./base/footer.php');
?>