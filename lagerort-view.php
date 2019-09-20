<?php
/*created by lp - 20.09.2019*/
$headTitle = "lagerort-Übersicht";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}



$order = 'name';
$dir = 'ASC';

if (isset($_GET['sort'])) {
    static $count = 0;
    $orders = array("name");
    $key = array_search($_GET['sort'], $orders);
    $order = $orders[$key];

    $directions = array("ASC", "DESC");
    $key = array_search($_GET['dir'], $directions);
    $dir = $directions[$key];

    $lagerorte = getAlllagerorte($order, $dir);
    $count++;
} else {
    $lagerorte = getAlllagerorte($order, $dir);
}


function getAlllagerorte($order, $dir)
{
    $pdo = PdoConnector::getConn();
    $getQuery = "SELECT lagerort_id, name FROM lagerort WHERE geloescht = false ORDER BY $order $dir;";
    $result = $pdo->query($getQuery)->fetchAll();
    $pdo = null;
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
                </tr>
            </thead>

            <tbody>
                <?php foreach ($lagerorte as $lagerort) : ?>
                    <tr class="hoverable">
                        <td><a class=" waves-effect tooltipped" data-position="top" data-tooltip="Element <wbr> bearbeiten" href="lagerort.php?id=<?php echo $lagerort->lagerort_id; ?>"><i class="material-icons cyan-text text-darken-4">remove_red_eye</i></a></td>
                        <td><?php echo $lagerort->name; ?></td>
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