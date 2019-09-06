<?php
/*created by lp - 31.08.2019*/
$headTitle = "Lieferant erfassen";
require_once('./base/header.php');
$msg = '';
$msgClass = '';


if (isset($_GET['success'])) {
    $msg = 'Eintrag wurde erfolgreich erstellt';
    $msgClass = 'card-panel teal accent-2';
}

if (isset($_POST['action'])) {
    // POST val's
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);

    // Obligatorische Felder prüfen.
    if (!empty($name)) {


        if (insertKategorie($name)) {

            Header('Location: kategorie.php?success=1');
        } else {
            $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
            $msgClass = 'card-panel red lighten-1';
        }
    } else {
        $msg = 'Name ist zwingend';
        $msgClass = 'card-panel red lighten-1';
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

?>

<main>
    <div class="container">
        <?php if ($msg != '') : ?>
            <div class="<?php echo $msgClass; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form id="lp-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input type="hidden" name="kategorie_id" value="NULL">
                    <input id="name" name="name" type="text" value="<?php echo isset($_POST['firma']) ? $name : ''; ?>" maxlength="25" required>
                    <label for="name">Kategorie-Name</label>
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