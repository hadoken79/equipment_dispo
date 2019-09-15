<?php
/*created by lp - 15.09.2019*/
$headTitle = "Equipment-Übersicht";
require_once('./base/header.php');



$order = 'name';
$dir = 'ASC';

if (isset($_GET['sort'])) {
    static $count = 0;
    $orders = array("name", "beschrieb", "serien_nr", "kaufjahr");
    $key = array_search($_GET['sort'], $orders);
    $order = $orders[$key];

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
echo $count;
?>

<main>

    <div class="lp-container">
        <table class="striped responsive-table">
            <thead>
                <tr>
                    <th></th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=name'; ?>">Name</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=beschrieb'; ?>">Beschrieb</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=kaufjahr'; ?>">Kaufjahr</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=serien_nr'; ?>">Seriennummer</a> </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($equipments as $equipment) : ?>
                    <tr class="hoverable">
                        <td><a class=" waves-effect" href="equipment.php?id=<?php echo $equipment->equipment_id; ?>"><i class="material-icons cyan-text text-darken-4">remove_red_eye</i></a></td>
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

<?php

require_once('./base/footer.php');
?>