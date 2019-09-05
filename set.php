<?php
/*created by lp - 31.08.2019*/
$headTitle = "Set erfassen";
require_once('./base/header.php');
$msg = '';
$msgClass = '';

// Form val's
$selectKategories = getKategories();

if (isset($_GET['success'])) {
    $msg = 'Eintrag wurde erfolgreich erstellt';
    $msgClass = 'card-panel teal accent-2';
}

if (isset($_POST['action'])) {
    // POST val's
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $beschrieb = isset($_POST['beschrieb']) ? filter_var($_POST['beschrieb'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $kategorie_id = isset($_POST['kategorie_id']) ? filter_var($_POST['kategorie_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $indispo = ($_POST['indispo'] == 'on') ? true : false;
    $aktiv = ($_POST['aktiv'] == 'on') ? true : false;
    $filename = isset($_POST['filename']) ? filter_var($_POST['filename'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $notiz = isset($_POST['notiz']) ? filter_var($_POST['notiz'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $bild_id = '';


    // Obligatorische Felder prüfen.
    if (!empty($name) && !empty($kategorie_id)) {

        //Bevor das Equipment gespeichert werden kann, muss falls ein Equipmentbild gesetzt ist, noch dessen id erzeugt werden.
        if (isset($filename)) {
            $bild_id = insertFilename($filename);
        };

        if (insertSet($name, $beschrieb, $kategorie_id, $indispo, $aktiv, $notiz, $bild_id)) {

            Header('Location: set.php?success=1');
        } else {
            $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
            $msgClass = 'card-panel red lighten-1';
        }
    } else {
        $msg = 'Name und Kategorie sind zwingend';
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


function insertSet($name, $beschrieb, $kategorie_id, $indispo, $aktiv, $notiz, $bild_id)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO set_(
            `name`,
            beschrieb,
            notiz,
            indispo,
            aktiv,
            kategorie_id,
            bild_id
        ) 
        VALUES
            (:name,
            :beschrieb,
            :notiz,
            :indispo,
            :aktiv,
            :kategorie_id,
            :bild_id);";

    $stmt = $pdo->prepare($insertQuery);
    echo $insertQuery . '<br>';
    echo $name . " " . $beschrieb . " " . $kategorie_id . " " . $indispo . " " . $aktiv . " " . $notiz . " " . $bild_id;

    if ($stmt->execute(['name' => $name, 'beschrieb' => $beschrieb, 'notiz' => $notiz, 'indispo' => $indispo, 'aktiv' => $aktiv, 'kategorie_id' => $kategorie_id, 'bild_id' => $bild_id])) {

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
                    <input id="beschrieb" name="beschrieb" type="text" maxlength="60" value="<?php echo isset($_POST['beschrieb']) ? $beschrieb : ''; ?>">
                    <label for="beschrieb">Beschrieb [zB dispo Funk]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="kategorie_id">
                        <option value="" disabled selected>wähle eine Option</option>
                        <?php foreach ($selectKategories as $kat) : ?>
                            <option value="<?php echo $kat->kategorie_id; ?>"><?php echo $kat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Equipment Kategorie</label>
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
                <div id="lp-switch" class="switch col s12 offset-s3 m3 offset-m1">
                    <label>
                        dispo | Aus
                        <input name="indispo" type="checkbox" checked="checked">
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