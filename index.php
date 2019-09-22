<?php
/*created by lp - 27.07.2019*/
$headTitle = "Dispo-Equipment";
require_once('./base/header.php');


$pdo = PdoConnector::getConn();

//Es sollen hier nur Kategorien zur Auswahl stehen, von Equipment, was in Dispo verfügbar ist
$kat_query = "SELECT kategorie_id,name FROM kategorie WHERE kategorie_id in 
(SELECT kategorie_id FROM equipment WHERE indispo=true AND geloescht=false)
AND geloescht=false;";
$stmt = $pdo->prepare($kat_query);
$stmt->execute();
$kategorien = $stmt->fetchAll();


//Alle Sets
$set_query = "SELECT 
set_.set_id, 
set_.name,
set_.beschrieb,
equipmentbild.filename,
set_.kategorie_id,
kategorie.name AS kat_name,
set_.aktiv
FROM set_ LEFT JOIN kategorie 
ON set_.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON set_.bild_id = equipmentbild.bild_id 
WHERE set_.geloescht=false
ORDER BY name DESC;";

$stmt = $pdo->prepare($set_query);
$stmt->execute();
$sets = $stmt->fetchAll();

//Alles Equipment, was nicht in einem Set ist
$eq_query = "SELECT 
equipment.equipment_id, 
equipment.name,
equipment.beschrieb,
equipmentbild.filename,
equipment.kategorie_id,
kategorie.name AS kat_name,
equipment.aktiv
FROM equipment LEFT JOIN kategorie 
ON equipment.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON equipment.bild_id = equipmentbild.bild_id 
WHERE equipment.geloescht=false
AND equipment.set_id IS NULL
AND equipment.indispo=true
ORDER BY name DESC;";
$stmt = $pdo->prepare($eq_query);
$stmt->execute();
$equipments = $stmt->fetchAll();

//Verbindung trennen
$pdo = null;

?>

<main>


    <div class="container">
        <div class="progress hide">
            <div class="indeterminate"></div>
        </div>
        <div class="row">
            <aside id="lp-kal" class="input-field col s12 m3">
                <!-- Date picker -->
                <input type="text" placeholder="Wähle ein Datum" class="datepicker">
                <!-- Kategorie filter -->
                <div class="input-field">
                    <select id="lp-katfilter" class="browser-default" onchange="filterChanged(this.value)">
                        <option value="0">alle</option>
                        <?php foreach ($kategorien as $kategorie) : ?>
                            <option value="<?php echo $kategorie->kategorie_id; ?>"><?php echo $kategorie->name; ?></option>
                        <?php endforeach ?>
                    </select>
                    <!--<label>filtere nach Kategorien</label>-->
                </div>
            </aside>
            <!-- Die Collection Elemente -->
            <div id="lp-card" class="col s12 m9">
                <ul class="collection">
                    <?php
                    foreach ($sets as $set) {
                        $titel = $set->name;
                        $beschrieb = $set->beschrieb;
                        $pfad = 'c:/bilder/';
                        $bild = $set->filename ? $pfad . $set->filename : 'images/yuna.jpg';
                        $linkvis = '';
                        if (!$set->aktiv) {
                            $titel = '<b>Nicht verfügbar</b>';
                            $linkvis = 'hide';
                        };
                        echo "<li id='set{$set->set_id}' class='collection-item avatar setlist {$set->kategorie_id}'>";
                        echo "<img src={$bild} alt='' class='circle'>";
                        echo "<span class='title'>{$titel}</span>";
                        echo "<p class='truncate'>{$beschrieb}</p>";
                        echo "<p class='status'></p>";
                        echo "<a href='#!' class='secondary-content bookset {$linkvis}'><i class='material-icons'>playlist_add</i></a>";
                        echo "</li>";
                    }
                    foreach ($equipments as $equipment) {
                        $titel = $equipment->name;
                        $beschrieb = $equipment->beschrieb;
                        $pfad = 'c:/bilder/';
                        $bild = $equipment->filename ? $pfad . $equipment->filename : 'images/yuna.jpg';
                        $linkvis = '';
                        if (!$equipment->aktiv) {
                            $titel = '<b>Nicht verfügbar</b>';
                        };
                        echo "<li id='eqp{$equipment->equipment_id}' class='collection-item avatar eqlist {$equipment->kategorie_id}'>";
                        echo "<img src={$bild} alt='' class='circle'>";
                        echo "<span class='title'>{$titel}</span>";
                        echo "<p class='truncate'>{$beschrieb}</p>";
                        echo "<p class='status'></p>";
                        if ($equipment->aktiv) {
                            echo "<a href='#!' class='secondary-content bookeqp'><i class='material-icons'>playlist_add</i></a>";
                        };
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</main>
<script src="./lib/js/app.js"></script>
<?php

require_once('./base/footer.php');
?>