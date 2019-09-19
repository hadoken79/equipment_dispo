<?php
/*created by lp - 31.08.2019*/
$headTitle = "Kategorie";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}
$msg = '';
$msgClass = '';


if (isset($_GET['success'])) {
    $msg = 'Eintrag wurde erfolgreich erstellt';
    $msgClass = 'card-panel teal accent-2';
}

if (isset($_POST['action'])) {
    // POST val's
    $name = (isset($_POST['name']) && !empty($_POST['name'])) ? filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $update = (isset($_POST['update']) && !empty($_POST['update'])) ? true : false;
    $kategorie_id = (isset($_POST['kategorie_id']) && !empty($_POST['kategorie_id'])) ? filter_var($_POST['kategorie_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    // Obligatorische Felder prüfen.
    if (!is_null($name)) {

        if ($update) {

            if (updateKategorie($name, $kategorie_id)) {
                Header('Location: kategorie.php?success=1');
            }
        } else {

            if (insertKategorie($name)) {
                Header('Location: kategorie.php?success=1');
            } else {
                $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
                $msgClass = 'card-panel red lighten-1';
            }
        }
    } else {
        $msg = 'Name ist zwingend';
        $msgClass = 'card-panel red lighten-1';
    }
}

if (isset($_GET['id'])) {
    $kategorie_id = filter_var($_GET['id'], FILTER_SANITIZE_SPECIAL_CHARS);
    $kategorie_data = getKategorie($kategorie_id);
    $name = $kategorie_data->name;
}

function getKategorie($kategorie_id)
{

    $getKatQuery = "SELECT `name` FROM kategorie WHERE kategorie_id = ?;";
    $pdo = PdoConnector::getConn();
    $stmt = $pdo->prepare($getKatQuery);
    if ($stmt->execute([$kategorie_id])) {
        $pdo = null;
        $selectedKat = $stmt->fetch();
        return $selectedKat;
    }
}

function insertKategorie($name)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO kategorie(
         name
        ) 
        VALUES
        (:name);";

    $stmt = $pdo->prepare($insertQuery);

    if ($stmt->execute(['name' => $name])) {

        $pdo = null;
        return true;
    }
}

function updateKategorie($name, $kategorie_id)
{
    $updateQuery = "UPDATE kategorie SET `name` = :name WHERE kategorie_id = :kategorie_id;";
    $pdo = PdoConnector::getConn();
    $stmt = $pdo->prepare($updateQuery);

    if ($stmt->execute(['name' => $name, 'kategorie_id' => $kategorie_id])) {
        return true;
    }
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
                    <input type="hidden" name="update" value="<?php echo isset($_GET['id']) ? true : false; ?>">
                    <input type="hidden" name="kategorie_id" value="<?php echo isset($_GET['id']) ? $kategorie_id : 'NULL'; ?>">
                    <input id="name" name="name" type="text" value="<?php echo isset($_GET['id']) ? $name : '' ?>" maxlength="25" required>
                    <label for="name">Kategorie-Name</label>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light" type="submit" name="action"><?php echo isset($_GET['id']) ? 'Update' : 'Speichern'; ?>
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