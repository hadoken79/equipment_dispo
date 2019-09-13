<?php
/*created by lp - 31.08.2019*/
$headTitle = "Set";
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
    $filename = isset($_POST['filename']) ? filter_var($_POST['filename'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $notiz = isset($_POST['notiz']) ? filter_var($_POST['notiz'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $bild_id = null;
    $update = isset($_POST['update']) ? filter_var($_POST['update'], FILTER_SANITIZE_SPECIAL_CHARS) : false;
    $set_id = isset($_POST['set_id']) ? filter_var($_POST['set_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;


    // Obligatorische Felder prüfen.
    if (!empty($name) && !empty($kategorie_id)) {

        //Bevor das Equipment gespeichert werden kann, muss falls ein Equipmentbild gesetzt ist, noch dessen id erzeugt werden.
        if (!empty($filename)) {
            $bild_id = insertFilename($filename);
        };

        if($update){

            if (updateSet($set_id, $name, $beschrieb, $kategorie_id, $indispo, $aktiv, $notiz, $bild_id)) {

                Header('Location: set.php?success=1');

            } else {
                $msg = 'Beim Versuch das Update in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
                $msgClass = 'card-panel red lighten-1';
            }

        }else{

            if (insertSet($name, $beschrieb, $kategorie_id, $indispo, $aktiv, $notiz, $bild_id)) {

                Header('Location: set.php?success=1');
            } else {
                $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
                $msgClass = 'card-panel red lighten-1';
            }

        }

        
    } else {
        $msg = 'Name und Kategorie sind zwingend';
        $msgClass = 'card-panel red lighten-1';
    }
}

if(isset($_GET['id'])){
      //Call von Dashboard -> Form befüllen.
      $set_id = filter_var($_GET['id'], FILTER_SANITIZE_SPECIAL_CHARS);

      $set_data = getSet($set_id);
  
      $name = $set_data->name;
      $beschrieb = $set_data->beschrieb;
      $kategorie_id = $set_data->kategorie_id;
      $indispo = $set_data->indispo;
      $aktiv = $set_data->aktiv;
      $notiz = $set_data->notiz;
      $bild_id = $set_data->bild_id;
      $filename = getFilename($bild_id);
}

function insertFileName($filename)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO equipmentbild(
            `filename`
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
    

    if ($stmt->execute(['name' => $name, 'beschrieb' => $beschrieb, 'notiz' => $notiz, 'indispo' => $indispo, 'aktiv' => $aktiv, 'kategorie_id' => $kategorie_id, 'bild_id' => $bild_id])) {

        $pdo = null;
        return true;
    }
}

function updateSet($set_id, $name, $beschrieb, $kategorie_id, $indispo, $aktiv, $notiz, $bild_id)
{
    $pdo = PdoConnector::getConn();
    $setUpdateQuery = "UPDATE set_ 
    SET `name` = :name,
    beschrieb = :beschrieb,
    kategorie_id = :kategorie_id,
    indispo = :indispo,
    aktiv = :aktiv,
    notiz = :notiz,
    bild_id = :bild_id
    WHERE set_id = :set_id";

    $stmt = $pdo->prepare($setUpdateQuery);
    if($stmt->execute(['name' => $name, 'beschrieb' => $beschrieb, 'kategorie_id' => $kategorie_id, 'indispo' => $indispo, 'aktiv' => $aktiv, 'notiz' => $notiz, 'bild_id' => $bild_id, 'set_id' => $set_id])){
        $pdo = null;
        return true;
    }
}

function getSet($set_id)
{
    $pdo = PdoConnector::getConn();
    $setQuery = "SELECT * FROM set_ WHERE set_id = ?;";
    $stmt = $pdo->prepare($setQuery);
    $stmt->execute([$set_id]);
    $pdo = null;
    $selectedSet = $stmt->fetch();
    return $selectedSet;
}

function getKategories()
{
    $pdo = PdoConnector::getConn();
    $selectKategories = $pdo->query("SELECT kategorie_id, `name` FROM kategorie WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectKategories;
}

function getFilename($bild_id)
{
    if(!$bild_id){return '';};
    $pdo = PdoConnector::getConn();
    $fileQuery = "SELECT filename FROM equipmentbild WHERE bild_id = ?;";
    $stmt = $pdo->prepare($fileQuery);
    $stmt->execute([$bild_id]);
    $filename = $stmt->fetch()->filename;
    return $filename;
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
                    <input type="hidden" name="set_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : "NULL"?>">
                    <input type="hidden" name="update" value="<?php echo isset($_GET['id']) ? true : false;?>"><!--Bei Update gleiches Formaular, aber andere abfrage 'UPDATE'-->
                    <input id="name" name="name" type="text" value="<?php echo (isset($_POST['name']) || isset($_GET['id'])) ? $name : ''; ?>" maxlength="25" required>
                    <label for="name">Equipment Name [genauer Typ]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="beschrieb" name="beschrieb" type="text" maxlength="60" value="<?php echo isset($_POST['beschrieb']) || isset($_GET['id']) ? $beschrieb : ''; ?>">
                    <label for="beschrieb">Beschrieb [zB dispo Funk]</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <select name="kategorie_id">
                        <option value="" disabled <?php echo isset($_GET['id']) ? !is_null($kategorie_id) ? "" : "selected" : "selected"; ?>>wähle eine Option</option>
                        <?php foreach ($selectKategories as $kat) : ?>
                            <option value="<?php echo $kat->kategorie_id; ?>" <?php echo isset($_GET['id']) ? $kat->kategorie_id === $kategorie_id ? "selected" : "" : ""; ?>"><?php echo $kat->name; ?></option>
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
                        <input class="file-path validate" type="text" value="<?php echo isset($_POST['filename']) || isset($_GET['id']) ? $filename ? $filename: '' : ''; ?>" placeholder="optionales Equipmentbild">
                    </div>
                </div>
                <div class="input-field col s12 m8">
                    <textarea id="notiz" name="notiz" class="materialize-textarea" maxlength="255"><?php echo isset($_POST['notiz']) || isset($_GET['id']) ? $notiz ? $notiz : '' : ''; ?></textarea>
                    <label for="notiz">Interne Infos [optional]</label>
                </div>
            </div>
            <div class="row">
                <div id="lp-switch" class="switch col s12 offset-s3 m3 offset-m1">
                    <label>
                        dispo | Aus
                        <input name="indispo" type="checkbox" <?php echo isset($_GET['id']) ? $indispo == true ? "checked='checked'" : "" : ""; ?>>
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
                <div id="lp-switch" class="switch col s12 s12 offset-s3 m3 offset-m1">
                    <label>
                        aktiv | Aus
                        <input name="aktiv" type="checkbox" <?php echo isset($_GET['id']) ? $aktiv == true ? "checked='checked'" : "" : "checked='checked'"; ?>>
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light" type="submit" name="action"><?php echo isset($_GET['id']) ? "Update" : "Speichern"?>
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