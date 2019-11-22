<?php
/*created by lp - 31.08.2019*/
$headTitle = "Lieferant";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}
$msg = '';
$msgClass = '';


if (isset($_GET['success'])) {

    switch ($_GET['success']) {
        case 1:
            $msg = 'Eintrag wurde erfolgreich erstellt';
            $msgClass = 'card-panel teal accent-2';
            break;
        case 2:
            $msg = 'Eintrag wurde erfolgreich gelöscht';
            $msgClass = 'card-panel teal accent-2';
    }
}

if (isset($_POST['action'])) {
    // POST val's
    $firma = filter_var($_POST['firma'], FILTER_SANITIZE_SPECIAL_CHARS);
    $strasse = (isset($_POST['strasse']) && !empty($_POST['strasse'])) ? filter_var($_POST['strasse'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $plz = (isset($_POST['plz']) && !empty($_POST['plz'])) ? filter_var($_POST['plz'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $ort = (isset($_POST['ort']) && !empty($_POST['ort'])) ? filter_var($_POST['ort'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $kontakt = (isset($_POST['kontakt']) && !empty($_POST['kontakt'])) ? filter_var($_POST['kontakt'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $tel = (isset($_POST['tel']) && !empty($_POST['tel'])) ? filter_var($_POST['tel'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $web = (isset($_POST['web']) && !empty($_POST['web'])) ? filter_var($_POST['web'], FILTER_SANITIZE_SPECIAL_CHARS) : "N/A";
    $update = (isset($_POST['update']) && !empty($_POST['update'])) ? filter_var($_POST['update'], FILTER_SANITIZE_SPECIAL_CHARS) : false;
    $lieferant_id = (isset($_POST['lieferant_id']) && !empty($_POST['lieferant_id'])) ? filter_var($_POST['lieferant_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;

    // Obligatorische Felder prüfen.
    if (!empty($firma)) {


        if ($update) {

            if (updateLieferant($lieferant_id, $firma, $strasse, $plz, $ort, $kontakt, $tel, $web)) {
                Header('Location: lieferant.php?success=1');
            } else {
                $msg = 'Beim Versuch das Update in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
                $msgClass = 'card-panel red lighten-1';
            }
        } else {

            if (insertLieferant($firma, $strasse, $plz, $ort, $kontakt, $tel, $web)) {

                Header('Location: lieferant.php?success=1');
            } else {
                $msg = 'Beim Versuch in die Datenbank zu speichern ist ein Fehler aufgetreten. ev. gibt es ein Verbindungsproblem.';
                $msgClass = 'card-panel red lighten-1';
            }
        }
    } else {
        $msg = 'Firmenname ist zwingend';
        $msgClass = 'card-panel red lighten-1';
    }
}

if (isset($_GET['id'])) {
//Call von Dashboard -> Form befüllen.
    $lieferant_id = filter_var($_GET['id'], FILTER_SANITIZE_SPECIAL_CHARS);

    $lieferant_data = getLieferant($lieferant_id);

    $firma = $lieferant_data->firma;
    $strasse = $lieferant_data->strasse;
    $plz = $lieferant_data->plz;
    $ort = $lieferant_data->ort;
    $kontakt = $lieferant_data->kontaktname;
    $tel = $lieferant_data->telefonnummer;
    $web = $lieferant_data->webseite;
}

if (isset($_POST['delete'])) {
    $lieferant_id = isset($_POST['lieferant_id']) ? filter_var($_POST['lieferant_id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    if (deleteLieferant($lieferant_id)) {
        Header('Location: set.php?success=2');
    }
}

function getLieferant($lieferant_id)
{
    $pdo = PdoConnector::getConn();
    $lieferantQuery = "SELECT * FROM lieferant WHERE lieferant_id = ?;";
    $stmt = $pdo->prepare($lieferantQuery);
    $stmt->execute([$lieferant_id]);
    $pdo = null;
    $selectedLieferant = $stmt->fetch();
    return $selectedLieferant;
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

function updateLieferant($lieferant_id, $firma, $strasse, $plz, $ort, $kontakt, $tel, $web)
{
    $pdo = PdoConnector::getConn();
    $lieferantUpdateQuery = "UPDATE lieferant 
    SET firma = :firma,
    strasse = :strasse,
    plz = :plz,
    ort = :ort,
    kontaktname = :kontakt,
    telefonnummer = :tel,
    webseite = :web
    WHERE lieferant_id = :lieferant_id;";
    $stmt = $pdo->prepare($lieferantUpdateQuery);


    if ($stmt->execute(['firma' => $firma, 'strasse' => $strasse, 'plz' => $plz, 'ort' => $ort, 'kontakt' => $kontakt, 'tel' => $tel, 'web' => $web, 'lieferant_id' => $lieferant_id])) {
        $pdo = null;
        return true;
    } else {
        return false;
    }
}

function deleteLieferant($lieferant_id)
{
    $pdo = PdoConnector::getConn();
    $updateQuery = "UPDATE lieferant SET
            geloescht = True
            WHERE lieferant_id = :lieferant_id";


    $stmt = $pdo->prepare($updateQuery);

    if ($stmt->execute(['lieferant_id' => $lieferant_id])) {

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
                    <!--Bei Update gleiches Formular, aber andere abfrage 'UPDATE'-->
                    <input type="hidden" name="lieferant_id" value="<?php echo (isset($_GET['id']) && !empty($lieferant_id)) ? $lieferant_id : 'NULL'; ?>">
                    <input type="hidden" name="update" value="<?php echo isset($_GET['id']) ? true : false; ?>">
                    <input id="firma" name="firma" type="text" value="<?php echo ((isset($_POST['firma']) || isset($_GET['id'])) && !empty($firma)) ? $firma : ''; ?>" maxlength="25" required>
                    <label class="active" for="firma">Firma</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="strasse" name="strasse" type="text" maxlength="60" value="<?php echo ((isset($_POST['strasse']) || isset($_GET['id'])) && !empty($strasse)) ? $strasse : ''; ?>">
                    <label class="active" for="strasse">Strasse und Hausnummer</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="plz" name="plz" type="text" maxlength="10" value="<?php echo ((isset($_POST['plz']) || isset($_GET['id'])) && !empty($plz)) ? $plz : ''; ?>">
                    <label class="active" for="plz">Postleitzahl</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input id="ort" name="ort" type="text" value="<?php echo ((isset($_POST['ort']) || isset($_GET['id'])) && !empty($ort)) ? $ort : ''; ?>" maxlength="40">
                    <label class="active" for="ort">Ortschaft</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="kontakt" name="kontakt" type="text" value="<?php echo ((isset($_POST['kontakt']) || isset($_GET['id'])) && !empty($kontakt)) ? $kontakt : ''; ?>" maxlength="40">
                    <label class="active" for="kontakt">Ansprechpartner</label>
                </div>
                <div class="input-field col s12 m6 l4">
                    <input id="tel" name="tel" type="text"  value="<?php echo ((isset($_POST['tel']) || isset($_GET['id'])) && !empty($tel)) ? $tel : ''; ?>" maxlength="25">
                    <label class="active" for="tel">Telefon</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6 l4">
                    <input id="web" name="web" type="text" value="<?php echo ((isset($_POST['web']) || isset($_GET['id'])) && !empty($web)) ? $web : ''; ?>" maxlength="50">
                    <label class="active" for="web">Webseite</label>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light" type="submit" name="action"><?php echo isset($_GET['id']) ? "Update" : "Speichern" ?>
                    <i class="material-icons right">send</i>
                </button>
                <?php echo isset($_GET['id']) ? "<button data-target='modal1' id='lp-del' class='btn waves-effect waves-light red darken-3 right modal-trigger'>Löschen
                    <i class='material-icons right'>delete</i>
                </button>" : ""; ?>
            </div>

             <!-- Modal Structure -->
             <div id="modal1" class="modal">
                <div class="modal-content">
                    <h4>Löschen</h4>
                    <p>Willst Du den Lieferant permanent löschen?</p>
                </div>
                <div class="modal-footer">
                    <button id='lp-del' class='btn waves-effect waves-light red darken-3 right' type='submit' name='delete'>Löschen
                        <i class='material-icons right'>delete</i>
                    </button>
                </div>
            </div>
    </div>
    </form>
    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {

    
        const optionsMod = {};
        const elems2 = document.querySelectorAll('.modal');
        const modal = M.Modal.init(elems2, optionsMod);
    });
</script>




<?php
require_once('./base/footer.php');
?>