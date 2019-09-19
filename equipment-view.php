<?php
/*created by lp - 15.09.2019*/
$headTitle = "Equipment-Übersicht";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}


//TODO AUTO-Switch für ASC und DESC einfügen

$order = 'name';
$dir = 'ASC';

if (isset($_GET['sort'])) {
    static $count = 0;
    $orders = array("name", "beschrieb", "serien_nr", "kaufjahr");
    $key = array_search($_GET['sort'], $orders);
    $order = $orders[$key];

    $directions = array("ASC", "DESC");
    $key = array_search($_GET['dir'], $directions);
    $dir = $directions[$key];

    $equipments = getAllEquipments($order, $dir);
    $count++;
} else {
    $equipments = getAllEquipments($order, $dir);
}


function getAllEquipments($order, $dir)
{
    $pdo = PdoConnector::getConn();
    $getQuery = "SELECT equipment_id, name, beschrieb, serien_nr, kaufjahr FROM equipment WHERE geloescht = false ORDER BY $order $dir;";
    $result = $pdo->query($getQuery)->fetchAll();

    return $result;
}



?>

<main>

    <div class="lp-container">
        <table class="striped responsive-table">
            <thead>
                <tr>
                    <th></th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=name&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Name</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=beschrieb&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Beschrieb</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=kaufjahr&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Kaufjahr</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=serien_nr&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Seriennummer</a> </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($equipments as $equipment) : ?>
                    <tr class="hoverable">
                        <td><a class=" waves-effect tooltipped" data-position="top" data-tooltip="Element <wbr> bearbeiten" href="equipment.php?id=<?php echo $equipment->equipment_id; ?>"><i class="material-icons cyan-text text-darken-4">remove_red_eye</i></a></td>
                        <td><?php echo $equipment->name; ?></td>
                        <td><?php echo $equipment->beschrieb; ?></td>
                        <td><?php echo $equipment->kaufjahr; ?></td>
                        <td><?php echo $equipment->serien_nr; ?></td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>


</main>

<script>
    //Tooltip
    document.addEventListener('DOMContentLoaded', function() {
        const options = {
            enterDelay: 400
        };

        const elems = document.querySelectorAll('.tooltipped');
        const tipps = M.Tooltip.init(elems, options);
    });
</script>

<?php

require_once('./base/footer.php');
?>