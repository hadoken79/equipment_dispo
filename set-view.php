<?php
/*created by lp - 15.09.2019*/
$headTitle = "Set-Übersicht";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}

$sets = getAllSets();

function getAllSets()
{
    $pdo = PdoConnector::getConn();
    $getQuery = "SELECT set_id, name, beschrieb FROM set_ WHERE geloescht = false;";
    $result = $pdo->query($getQuery)->fetchAll();

    return $result;
}

function getEquipmentsInSets($set_id)
{
    $pdo = PdoConnector::getConn();
    $getQuery = "SELECT equipment_id, name, beschrieb, kaufjahr, serien_nr FROM equipment WHERE geloescht = false AND set_id = ?;";
    $stmt = $pdo->prepare($getQuery);
    $stmt->execute([$set_id]);
    $result = $stmt->fetchAll();

    return $result;
}



?>

<main>

    <div class="lp-container">

        <ul class="collapsible">
            <?php foreach ($sets as $set) : ?>
                <li class="tooltipped hoverable" data-position="top" data-tooltip="Klick mich um zugewiesenes Equipment zu sehen">
                    <div class="collapsible-header"><a class=" waves-effect tooltipped" data-position="top" data-tooltip="Element <wbr> bearbeiten" href="set.php?id=<?php echo $set->set_id; ?>"><i class="material-icons cyan-text text-darken-4">remove_red_eye</i></a><?php echo $set->name; ?></div>
                    <div class="collapsible-body">
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
                                <?php $equipments = getEquipmentsInSets($set->set_id); ?>
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
                </li>
            <?php endforeach; ?>
        </ul>
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

        const optionsCol = {

        }
        const elems2 = document.querySelectorAll('.collapsible');
        const collapsible = M.Collapsible.init(elems2, optionsCol);
    });
</script>

<?php

require_once('./base/footer.php');
?>