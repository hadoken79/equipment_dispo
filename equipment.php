<?php
/*created by lp - 31.08.2019*/
$headTitle = "Equipment erfassen";
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
    $beschrieb = isset($_POST['beschrieb']) ? filter_var($_POST['beschrieb'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
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
    $notiz = isset($_POST['notiz']) ? filter_var($_POST['notiz'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $bild_id = '';
    //wird nachher per reverenz an inserEquipment übergeben. variable wird nach erstellung für Lieferant_equipment benötigt
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
    <div class="container">
        <?php if ($msg != '') : ?>
            <div class="<?php echo $msgClass; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form id="lp-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input type="hidden" name="equipment_id" value="NULL">
                    <input id="name" name="name" type="text" value="<?php echo isset($_POST['name']) ? $name : ''; ?>" maxlength="25" required>
                    <label for="name">Equipment Name [genauer Typ]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="beschrieb" name="beschrieb" type="text" maxlength="40" value="<?php echo isset($_POST['beschrieb']) ? $beschrieb : ''; ?>">
                    <label for="beschrieb">Beschrieb [zB dispo Funk]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="serie" name="serien_nr" type="text" maxlength="100" value="<?php echo isset($_POST['serien_nr']) ? $serien_nr : ''; ?>" required>
                    <label for="serie">Serien Nummer</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input id="disabled" name="barcode" type="text" maxlength="100" value="<?php echo isset($_POST['barcode']) ? $barcode : ''; ?>">
                    <label for="barcode">Barcode [inaktiv]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <!--Google Chrome übernimmt das html5 maxlenght attr. nicht bei number. desshalb js-->
                    <input id="kaufjahr" name="kaufjahr" type="number" value="<?php echo isset($_POST['kaufjahr']) ? $kaufjahr : ''; ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4">
                    <label for="kaufjahr">Kaufjahr</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="kaufpreis" name="kaufpreis" type="number" value="<?php echo isset($_POST['kaufpreis']) ? $kaufpreis : ''; ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10">
                    <label for="kaufpreis">Kaufpreis</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <select name="kategorie_id">
                        <option value="" disabled selected>wähle eine Option</option>
                        <?php foreach ($selectKategories as $kat) : ?>
                            <option value="<?php echo $kat->kategorie_id; ?>"><?php echo $kat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Equipment Kategorie</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="set_id">
                        <option value="" disabled selected>wähle eine Option</option>
                        <?php foreach ($selectSets as $set) : ?>
                            <option value="<?php echo $set->set_id; ?>"><?php echo $set->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>falls Equipment Teil eines Sets</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="lagerort_id">
                        <option value="" disabled selected>wähle eine Option</option>
                        <?php foreach ($selectLagerorte as $lagerort) : ?>
                            <option value="<?php echo $lagerort->lagerort_id; ?>"><?php echo $lagerort->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Lagerort des Equipments</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <select name="lieferant_id">
                        <option value="" disabled selected>wähle eine Option</option>
                        <?php foreach ($selectLieferanten as $lieferant) : ?>
                            <option value="<?php echo $lieferant->lieferant_id; ?>"><?php echo $lieferant->firma; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Lieferant</label>
                </div>
                <div id="lp-switch" class="switch col s12 offset-s3 m3 offset-m1">
                    <label>
                        dispo | Aus
                        <input name="indispo" type="checkbox">
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
                <div id="lp-switch" class="switch col s12 s12 offset-s3 m3 offset-m1">
                    <label>
                        aktiv | Aus
                        <input name="aktiv" type="checkbox" checked="checked">
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
                        <input class="file-path validate" type="text" value="<?php echo isset($_POST['filename']) ? $filename : ''; ?>" placeholder="optionales Equipmentbild">
                    </div>
                </div>
                <div class="input-field col s12 m8">
                    <textarea id="notiz" name="notiz" class="materialize-textarea" maxlength="255"><?php echo isset($_POST['notiz']) ? $notiz : ''; ?></textarea>
                    <label for="notiz">Interne Infos [optional]</label>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light" type="submit" name="action">Speichern
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