<?php
/*created by lp - 15.09.2019*/
$headTitle = "Equipment-Übersicht";
require_once('./base/header.php');
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}



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

        <div class="row">
            <form class="col s12">
                <div class="row">
                    <div class="input-field col s6">
                        <i class="material-icons prefix">search</i>
                        <textarea id="icon_prefix2" class="materialize-textarea lp-search"></textarea>
                        <label for="icon_prefix2">suchen</label>
            </form>
        </div>
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
                    <tr id="<?php echo $equipment->equipment_id; ?>" class="hoverable lp-rows">
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
    let searchbar = document.querySelector('.lp-search').addEventListener('keyup', searchequipment);

    function searchequipment(e) {
        let input = e.target.value;

        //console.log(input);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'booking.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        let htmlparams = `search=${input}`;


        xhr.onload = function() {
            if (this.status === 200) {

                let ids = JSON.parse(this.response);
                //console.log(ids);

                let rows = document.querySelectorAll('.lp-rows');

                rows.forEach(function(row) {
                    row.classList.remove('hide');
                    let match = false;
                    for (i = 0; i < ids.length; i++) {
                        if (row.id == ids[i]['equipment_id']) {
                            match = true;
                            break;
                        }

                    }
                    if (match == false) {
                        row.classList.add('hide');
                    }
                })
            }

        }
        xhr.send(htmlparams);
    }
</script>

<?php

require_once('./base/footer.php');
?>