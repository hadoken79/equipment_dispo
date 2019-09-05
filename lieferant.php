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
    $firma = filter_var($_POST['firma'], FILTER_SANITIZE_SPECIAL_CHARS);
    $strasse = isset($_POST['strasse']) ? filter_var($_POST['strasse'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $plz = isset($_POST['plz']) ? filter_var($_POST['plz'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $ort = isset($_POST['ort']) ? filter_var($_POST['ort'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $kontakt = isset($_POST['kontakt']) ? filter_var($_POST['kontakt'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $tel = isset($_POST['tel']) ? filter_var($_POST['tel'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $web = isset($_POST['web']) ? filter_var($_POST['web'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";

    // Obligatorische Felder prüfen.
    if (!empty($firma)) {


        if (insertLieferant($firma, $strasse, $plz, $ort, $kontakt, $tel, $web)) {

            Header('Location: lieferant.php?success=1');
        } else {
            $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
            $msgClass = 'card-panel red lighten-1';
        }
    } else {
        $msg = 'Firmenname ist zwingend';
        $msgClass = 'card-panel red lighten-1';
    }
}

function insertLieferant($firma, $strasse, $plz, $ort, $kontakt, $tel, $web)
{
    $pdo = PdoConnector::getConn();
    $insertQuery = "INSERT INTO lieferant(
         firma,
         strasse,
         plz,
         ort,
         kontaktname,
         telefonnummer,
         webseite
        ) 
        VALUES
        (:firma,
         :strasse,
         :plz,
         :ort,
         :kontaktname,
         :telefonnummer,
         :webseite);";

    $stmt = $pdo->prepare($insertQuery);

    if ($stmt->execute(['firma' => $firma, 'strasse' => $strasse, 'plz' => $plz, 'ort' => $ort, 'kontaktname' => $kontakt, 'telefonnummer' => $tel, 'webseite' => $web])) {

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
                    <input type="hidden" name="lieferant_id" value="NULL">
                    <input id="firma" name="firma" type="text" value="<?php echo isset($_POST['firma']) ? $firma : ''; ?>" maxlength="25" required>
                    <label for="firma">Firma</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="strasse" name="strasse" type="text" maxlength="60" value="<?php echo isset($_POST['strasse']) ? $strasse : ''; ?>">
                    <label for="strasse">Strasse und Hausnummer</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="plz" name="plz" type="text" maxlength="15" value="<?php echo isset($_POST['plz']) ? $plz : ''; ?>">
                    <label for="plz">Postleitzahl</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input type="hidden" name="lieferant_id" value="NULL">
                    <input id="ort" name="ort" type="text" value="<?php echo isset($_POST['ort']) ? $ort : ''; ?>" maxlength="40">
                    <label for="ort">Ortschaft</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="kontakt" name="kontakt" type="text" maxlength="40" value="<?php echo isset($_POST['kontakt']) ? $kontakt : ''; ?>">
                    <label for="kontakt">Ansprechpartner</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <!--Bei Chrome funktioniert das html5 maxlenght nur bei text, desshalb js-->
                    <input id="tel" name="tel" type="number" maxlength="15" value="<?php echo isset($_POST['tel']) ? $tel : ''; ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="25">
                    <label for="tel">Telefon</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <!--Bei Chrome funktioniert das html5 maxlenght nur bei text, desshalb js-->
                    <input id="web" name="web" type="text" maxlength="40" value="<?php echo isset($_POST['web']) ? $tel : ''; ?>" maxlength="25">
                    <label for="web">Webseite</label>
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