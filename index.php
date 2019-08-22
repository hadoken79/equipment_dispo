<?php
/*created by lp - 27.07.2019*/
require_once('./base/header.php');

$pdo = PdoConnector::getConn();

//Es sollen hier nur Kategorien zur Auswahl stehen, von Equipment, was in Dispo verfügbar ist
$kat_query = "SELECT kategorie_id,name FROM kategorie WHERE kategorie_id in 
(SELECT kategorie_id FROM equipment WHERE indispo=true AND geloescht=false)
AND geloescht=false;";
$kategorien = $pdo->query($kat_query);

//Alle Sets und alles equipment //TODO variable aus ajax mit abruf von Datum
$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d");
echo $date;

$set_eq_query = "SELECT equipment.equipment_id, 
equipment.name AS eq_name, 
equipment.beschrieb AS eq_beschrieb, 
set_.set_id, 
set_.beschrieb AS set_beschrieb, 
set_.name AS set_name, 
buchung.buchung_id, 
buchung.reserviert_fuer, 
buchung.user, 
equipmentbild.filename,
kategorie.name AS kat_name
FROM equipment LEFT JOIN set_ 
ON equipment.set_id = set_.set_id LEFT JOIN buchung 
ON equipment.equipment_id = buchung.equipment_id LEFT JOIN kategorie 
ON equipment.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON equipment.bild_id = equipmentbild.bild_id WHERE equipment.geloescht=false ORDER BY set_.name DESC;";
$sets_eq = $pdo->query($set_eq_query);

//Verbindung trennen
$pdo = null;

?>
<main>
    <div class="container">
        <div class="row">
            <aside id="lp-kal" class="input-field col s12 m3">
                <input type="text" placeholder="Wähle ein Datum" class="datepicker">
                <!-- Dropdown Trigger -->
                <a class='dropdown-trigger btn' href='#' data-target='dropdown1'>Kategorien</a>

                <!-- Dropdown Inhalt -->
                <ul id='dropdown1' class='dropdown-content'>
                    <li><a href="#!">Alle</a></li>
                    <li class="divider" tabindex="-1"></li>
                    <?php foreach ($kategorien->fetchAll() as $kategorie) : ?>
                    <li><a href="#!"><?php echo $kategorie->name; ?></a></li>
                    <?php endforeach ?>
                </ul>
            </aside>
            <!-- Die Collection Elemente -->
            <div id="lp-card" class="col s12 m9">
                <ul class="collection">
                    <?php
                    foreach ($sets_eq->fetchAll() as $eq_set) {
                        $titel = $eq_set->set_name ? $eq_set->set_name : $eq_set->eq_name;
                        $beschrieb = $eq_set->set_beschrieb ? $eq_set->set_beschrieb : $eq_set->eq_beschrieb;
                        $pfad = 'c:/bilder/';
                        $bild = $eq_set->filename ? $pfad . $eq_set->filename : 'images/yuna.jpg';
                        if ($eq_set->reserviert_fuer === date("Y-m-d")) {
                            $titel = '<b>RESERVIERT</b>';
                        };
                        echo "<li class='collection-item avatar'>";
                        echo "<img src={$bild} alt='' class='circle'>";
                        echo "<span class='title'>{$titel}</span>";
                        echo "<p>{$beschrieb}</p>";
                        echo "<a href='#!' class='secondary-content'><i class='material-icons'>playlist_add</i></a>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</main>
<?php


foreach ($sets_eq->fetchAll() as $eq_set) {
    $titel = $eq_set->set_name ? $eq_set->set_name : $eq_set->eq_name;
    $beschrieb = $eq_set->set_beschrieb ? $eq_set->set_beschrieb : $eq_set->eq_beschrieb;
    $pfad = 'c:/bilder/';
    $bild = $eq_set->filename ? $pfad . $eq_set->filename : 'images/yuna.jpg';
    if ($eq_set->reserviert_fuer === date("Y-m-d")) {
        $titel = '<b>RESERVIERT</b>';
        $beschrieb = '';
    };
    echo "<li class='collection-item avatar'>";
    echo "<img src={$bild} alt='' class='circle'>";
    echo "<span class='title'>{$titel}</span>";
    echo "<p>{$beschrieb}</p>";
    echo "<a href='#!' class='secondary-content'><i class='material-icons'>grade</i></a>";
    echo "</li>";

    echo $eq_set->reserviert_fuer . '<br>';
    echo 'Titel=' . $titel . '<br>';
    echo 'Beschrieb=' . $beschrieb . '<br>';
    echo 'Bild=' . $bild . '<br>';
    echo 'eq_id=' . $eq_set->equipment_id . '<br>';
    echo 'set_id=' . $eq_set->set_id . '<br>';
    echo '<hr><br>';
}

require_once('./base/footer.php');
?>