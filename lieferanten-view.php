<?php
/*created by lp - 20.09.2019*/
$headTitle = "Lieferanten-Übersicht";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}



$order = 'firma';
$dir = 'ASC';

if (isset($_GET['sort'])) {
    static $count = 0;
    $orders = array("firma", "kontaktname", "telefonnummer", "webseite");
    $key = array_search($_GET['sort'], $orders);
    $order = $orders[$key];

    $directions = array("ASC", "DESC");
    $key = array_search($_GET['dir'], $directions);
    $dir = $directions[$key];

    $lieferanten = getAlllieferanten($order, $dir);
    $count++;
} else {
    $lieferanten = getAlllieferanten($order, $dir);
}


function getAlllieferanten($order, $dir)
{
    $pdo = PdoConnector::getConn();
    $getQuery = "SELECT lieferant_id, firma, kontaktname, telefonnummer, webseite FROM lieferant WHERE geloescht = false ORDER BY $order $dir;";
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
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=firma&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Firma</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=kontaktname&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Kontaktname</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=telefonnummer&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Telefonnummer</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=webseite&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Webseite</a> </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($lieferanten as $lieferant) : ?>
                    <tr class="hoverable">
                        <td><a class=" waves-effect tooltipped" data-position="top" data-tooltip="Element <wbr> bearbeiten" href="lieferant.php?id=<?php echo $lieferant->lieferant_id; ?>"><i class="material-icons cyan-text text-darken-4">remove_red_eye</i></a></td>
                        <td><?php echo $lieferant->firma; ?></td>
                        <td><?php echo $lieferant->kontaktname; ?></td>
                        <td><?php echo $lieferant->telefonnummer; ?></td>
                        <td><?php echo $lieferant->webseite; ?></td>
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