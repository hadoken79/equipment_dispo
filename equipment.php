<?php
/*created by lp - 31.08.2019*/
$headTitle = "Equipment";
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
    //empty prüft unter anderem auch auf den Zahlenwert '0', darum müssen alle ids > 0 sein.
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $beschrieb = (isset($_POST['beschrieb']) && !empty($_POST['beschrieb'])) ? filter_var($_POST['beschrieb'], FILTER_SANITIZE_SPECIAL_CHARS) : 'N/A';
    $serien_nr = (isset($_POST['serien_nr']) && !empty($_POST['serien_nr'])) ? filter_var($_POST['serien_nr'], FILTER_SANITIZE_SPECIAL_CHARS) : 'N/A';
    $barcode = (isset($_POST['barcode']) && !empty($_POST['barcode'])) ? filter_var($_POST['barcode'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $kaufjahr = (isset($_POST['kaufjahr']) && !empty($_POST['kaufjahr'])) ? filter_var($_POST['kaufjahr'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $kaufpreis = (isset($_POST['kaufpreis']) && !empty($_POST['kaufpreis'])) ? filter_var($_POST['kaufpreis'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $kategorie_id = (isset($_POST['kategorie_id']) && !empty($_POST['kategorie_id'])) ? filter_var($_POST['kategorie_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $set_id = (isset($_POST['set_id']) && !empty($_POST['set_id'])) ? filter_var($_POST['set_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $lagerort_id = (isset($_POST['lagerort_id']) && !empty($_POST['lagerort_id'])) ? filter_var($_POST['lagerort_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $lieferant_id = (isset($_POST['lieferant_id']) && !empty($_POST['lieferant_id'])) ? filter_var($_POST['lieferant_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $indispo = (isset($_POST['indispo']) && $_POST['indispo'] == 'on') ? true : false;
    $aktiv = (isset($_POST['aktiv']) && $_POST['aktiv'] == 'on') ? true : false;
    $filename = (isset($_POST['filename']) && !empty($_POST['filename'])) ? filter_var($_POST['filename'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $notiz = (isset($_POST['notiz']) && !empty($_POST['notiz'])) ? filter_var($_POST['notiz'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $update = (isset($_POST['update']) && !empty($_POST['update'])) ? filter_var($_POST['update'], FILTER_SANITIZE_SPECIAL_CHARS) : false;
    $bild_id = null;
    //wird nachher per Reverenz an insertEquipment übergeben. variable wird nach Erstellung für Lieferant_equipment benötigt. Wenn Formular als Update kommt, wird hier bereits eine id übergeben.
    $equipment_id = isset($_POST['equipment_id']) && !empty($_POST['equipment_id']) ? filter_var($_POST['equipment_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;

    // Obligatorische Felder prüfen.
    if (!empty($name) && !empty($kategorie_id)) {

        //Bevor das Equipment gespeichert werden kann, muss falls ein Equipmentbild gesetzt ist, noch dessen id erzeugt werden.
        if (!empty($filename)) {
            $bild_id = insertFilename($filename);
        };

        if ($update) {

            if (updateEquipment($equipment_id, $name, $beschrieb, $serien_nr, $barcode, $kaufjahr, $kaufpreis, $kategorie_id, $set_id, $lagerort_id, $indispo, $aktiv, $notiz, $bild_id)) {
                if (isset($lieferant_id)) {

                    updateLieferant_Equipment($lieferant_id, $equipment_id);
                }

                Header('Location: equipment.php?success=1');
            }
        } else {

            if (insertEquipment($equipment_id, $name, $beschrieb, $serien_nr, $barcode, $kaufjahr, $kaufpreis, $kategorie_id, $set_id, $lagerort_id, $indispo, $aktiv, $notiz, $bild_id)) {

                //falls ein Lieferant gesetzt wurde, kann jetzt noch der Lieferant_Equipment-Table ergänzt werden
                if (isset($lieferant_id)) {
                    insertLieferant_Equipment($lieferant_id, $equipment_id);
                }

                Header('Location: equipment.php?success=1');
            } else {
                $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
                $msgClass = 'card-panel red lighten-1';
            }
        }
    } else {
        //fallback, falls browser html5 required nicht unterstützt.
        $msg = 'Name und Kategorie sind zwingend';
        $msgClass = 'card-panel red lighten-1';
    }
}

if (isset($_GET['id'])) {
    //Call von Dashboard -> Form befüllen.
    $equipment_id = filter_var($_GET['id'], FILTER_SANITIZE_SPECIAL_CHARS);

    $equipment_data = getEquipment($equipment_id);

    $name = $equipment_data->name;
    $beschrieb = $equipment_data->beschrieb;
    $serien_nr = $equipment_data->serien_nr;
    $barcode = $equipment_data->barcode;
    $kaufjahr = $equipment_data->kaufjahr;
    $kaufpreis = $equipment_data->kaufpreis;
    $kategorie_id = $equipment_data->kategorie_id;
    $set_id = $equipment_data->set_id;
    $lagerort_id = $equipment_data->lagerort_id;
    $lieferant_id = getLieferant($equipment_id);
    $indispo = $equipment_data->indispo;
    $aktiv = $equipment_data->aktiv;
    $notiz = $equipment_data->notiz;
    $bild_id = $equipment_data->bild_id;
    $filename = getFilename($bild_id);
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
    } else {
        $pdo = null;
        $msg = 'Fehler beim Versuch Filename in Datenbank zu speichern';
        $msgClass = 'card-panel red lighten-1';
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
        (:lieferant_id,
        :equipment_id);";
    $stmt = $pdo->prepare($insertQuery);
    if (!$stmt->execute(['equipment_id' => $equipment_id, 'lieferant_id' => $lieferant_id])) {
        $msg = 'Fehler beim Versuch Lieferant zu Equipment in Datenbank zu speichern';
        $msgClass = 'card-panel red lighten-1';
    }
    $pdo = null;
}

function updateLieferant_Equipment($lieferant_id, $equipment_id)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "UPDATE lieferant_equipment SET lieferant_id = :lieferant_id WHERE equipment_id = :equipment_id;";

    $stmt = $pdo->prepare($insertQuery);
    if (!$stmt->execute(['equipment_id' => $equipment_id, 'lieferant_id' => $lieferant_id])) {
        $msg = 'Fehler beim Versuch Lieferant zu Equipment in Datenbank zu speichern';
        $msgClass = 'card-panel red lighten-1';
    }
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

        $idQuery = "SELECT equipment_id FROM equipment WHERE serien_nr = ? AND geloescht = false;";
        $stmt = $pdo->prepare($idQuery);
        $stmt->execute([$serien_nr]);

        $idObj = $stmt->fetch();
        $equipment_id = $idObj->equipment_id;
        $pdo = null;
        if (!empty($equipment_id)) {
            return true;
        }
    } else {
        return false;
    }
}

function updateEquipment(&$equipment_id, $name, $beschrieb, $serien_nr, $barcode, $kaufjahr, $kaufpreis, $kategorie_id, $set_id, $lagerort_id, $indispo, $aktiv, $notiz, $bild_id)
{
    $pdo = PdoConnector::getConn();
    $updateQuery = "UPDATE equipment SET
            `name` = :name,
            beschrieb = :beschrieb,
            notiz = :notiz,
            serien_nr = :serien_nr,
            barcode = :barcode,
            indispo = :indispo,
            aktiv = :aktiv,
            kaufjahr = :kaufjahr,
            kaufpreis = :kaufpreis,
            set_id = :set_id,
            kategorie_id = :kategorie_id,
            bild_id = :bild_id,
            lagerort_id = :lagerort_id 
            WHERE equipment_id = :equipment_id";


    $stmt = $pdo->prepare($updateQuery);

    if ($stmt->execute(['name' => $name, 'beschrieb' => $beschrieb, 'notiz' => $notiz, 'serien_nr' => $serien_nr, 'barcode' => $barcode, 'indispo' => $indispo, 'aktiv' => $aktiv, 'kaufjahr' => $kaufjahr, 'kaufpreis' => $kaufpreis, 'set_id' => $set_id, 'kategorie_id' => $kategorie_id, 'bild_id' => $bild_id, 'lagerort_id' => $lagerort_id, 'equipment_id' => $equipment_id])) {

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

function getEquipment($id)
{
    $pdo = PdoConnector::getConn();
    $equipmentQuery = "SELECT * FROM equipment WHERE geloescht = false AND equipment_id = ?;";
    $stmt = $pdo->prepare($equipmentQuery);
    $stmt->execute([$id]);
    $selectEquipment = $stmt->fetch();
    $pdo = null;
    return $selectEquipment;
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

function getLieferant($equipment_id)
{
    $pdo = PdoConnector::getConn();
    $lieferantQuery = "SELECT lieferant_id, firma FROM lieferant 
    WHERE geloescht = false AND lieferant_id = 
    (SELECT lieferant_id FROM lieferant_equipment WHERE geloescht = false AND equipment_id = $equipment_id);";

    $selectLieferant = $pdo->query($lieferantQuery)->fetchAll();
    $pdo = null;
    return $selectLieferant;
}

function getFilename($bild_id)
{
    return '';
}
?>

<main>
    <div class="container">
        <?php if ($msg != '') : ?>
            <div class="<?php echo $msgClass; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form id="lp-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input type="hidden" name="equipment_id" value="<?php echo (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : "NULL" ?>">
                    <input type="hidden" name="update" value="<?php echo isset($_GET['id']) ? true : false; ?>">
                    <!--Bei Update gleiches Formular, aber andere abfrage 'UPDATE'-->
                    <input id="name" name="name" type="text" value="<?php echo ((isset($_POST['name']) || isset($_GET['id'])) && !empty($name)) ? $name : ''; ?>" maxlength="25" required>
                    <label for="name">Equipment Name [genauer Typ]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="beschrieb" name="beschrieb" type="text" maxlength="60" value="<?php echo ((isset($_POST['beschrieb']) || isset($_GET['id'])) && !empty($beschrieb)) ? $beschrieb : ''; ?>">
                    <label for="beschrieb">Beschrieb [zB dispo Funk]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="serie" name="serien_nr" type="text" maxlength="100" value="<?php echo ((isset($_POST['serien_nr']) || isset($_GET['id'])) && !empty($serien_nr)) ? $serien_nr : ''; ?>">
                    <label for="serie">Serien Nummer</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input id="disabled" name="barcode" type="text" maxlength="100" value="<?php echo ((isset($_POST['barcode']) || isset($_GET['id'])) && !empty($barcode)) ? $barcode : ''; ?>">
                    <label for="barcode">Barcode [inaktiv]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <!--Google Chrome übernimmt das html5 maxlenght attr. nicht bei number. desshalb js-->
                    <input id="kaufjahr" name="kaufjahr" type="number" value="<?php echo ((isset($_POST['kaufjahr']) || isset($_GET['id'])) && !empty($kaufjahr)) ? $kaufjahr : ''; ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4">
                    <label for="kaufjahr">Kaufjahr</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="kaufpreis" name="kaufpreis" type="number" value="<?php echo ((isset($_POST['kaufpreis']) || isset($_GET['id'])) && !empty($kaufpreis)) ? $kaufpreis : ''; ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10">
                    <label for="kaufpreis">Kaufpreis</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <select name="kategorie_id">
                        <option value="" disabled <?php echo ((isset($_GET['id']) || isset($_POST['kategorie_id'])) && !empty($kategorie_id)) ? "" : "selected"; ?>>wähle eine Option</option>
                        <?php foreach ($selectKategories as $kat) : ?>
                            <option value="<?php echo $kat->kategorie_id; ?>" <?php echo ((isset($_GET['id']) || isset($_POST['kategorie_id'])) && $kat->kategorie_id === $kategorie_id) ? "selected" : ""; ?>><?php echo $kat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Equipment Kategorie</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="set_id">
                        <option value="" disabled <?php echo ((isset($_GET['id']) || isset($_POST['set_id'])) && !empty($set_id)) ? "" : "selected"; ?>>wähle eine Option</option>
                        <?php foreach ($selectSets as $set) : ?>
                            <option value="<?php echo $set->set_id; ?>" <?php echo ((isset($_GET['id']) || isset($_POST['set_id'])) && $set->set_id === $set_id) ? "selected" : ""; ?>><?php echo $set->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>falls Equipment Teil eines Sets</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="lagerort_id">
                        <option value="" disabled <?php echo ((isset($_GET['id']) || isset($_POST['lagerort_id'])) && !empty($lagerort_id)) ? "" : "selected"; ?>>wähle eine Option</option>
                        <?php foreach ($selectLagerorte as $lagerort) : ?>
                            <option value="<?php echo $lagerort->lagerort_id; ?>" <?php echo ((isset($_GET['id']) || isset($_POST['lagerort_id'])) && $lagerort->lagerort_id === $lagerort_id) ? "selected" : ""; ?>><?php echo $lagerort->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Lagerort [nur für Dispo]</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <select name="lieferant_id">
                        <option value="" disabled <?php echo ((isset($_GET['id']) || isset($_POST['lieferant_id'])) && !empty($lieferant_id)) ? "" : "selected"; ?>>wähle eine Option</option>
                        <?php foreach ($selectLieferanten as $lieferant) : ?>
                            <option value="<?php echo $lieferant->lieferant_id; ?>" <?php echo ((isset($_GET['id']) || isset($_POST['lieferant_id'])) && $lieferant->lieferant_id === $lieferant_id) ? "selected" : ""; ?>><?php echo $lieferant->firma; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Lieferant</label>
                </div>
                <div id="lp-switch" class="switch col s12 offset-s3 m3 offset-m1">
                    <label>
                        dispo | Aus
                        <input name="indispo" type="checkbox" <?php echo ((isset($_GET['id']) || isset($_POST['indispo'])) && $indispo == true) ? "checked='checked'" : ""; ?>>
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
                <div id="lp-switch" class="switch col s12 s12 offset-s3 m3 offset-m1">
                    <label>
                        aktiv | Aus
                        <!-- per default aktiv-->
                        <input name="aktiv" type="checkbox" <?php echo ((isset($_GET['id']) || isset($_POST['aktiv'])) && $aktiv == true) || (!isset($_GET['id']) && !isset($_POST['aktiv'])) ? "checked='checked'" : ""; ?>>
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="file-field input-field col s12 m4">
                    <div class="btn">
                        <span>Bild</span>
                        <input type="file" name="filename">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" value="<?php echo (isset($_POST['filename']) || isset($_GET['id'])) && $filename ? $filename : ''; ?>" placeholder="optionales Equipmentbild">
                    </div>
                </div>
                <div class="input-field col s12 m8">
                    <textarea id="notiz" name="notiz" class="materialize-textarea" maxlength="255"><?php echo isset($_POST['notiz']) || isset($_GET['id']) ? $notiz : ''; ?></textarea>
                    <label for="notiz">Interne Infos [optional]</label>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light" type="submit" name="action"><?php echo isset($_GET['id']) ? "Update" : "Speichern" ?>
                    <i class="material-icons right">send</i>
                </button>
            </div>
    </div>
    </form>
    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const options = {};
        const elems = document.querySelectorAll('select');
        const instances = M.FormSelect.init(elems, options);
        //console.log(instances[0].getSelectedValues());
    });
</script>





<?php
require_once('./base/footer.php');
?>