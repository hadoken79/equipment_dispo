<?php
/*created by lp - 15.09.2019*/
$headTitle = "Booking-Übersicht";
require_once('./base/header.php');
require_once('booking.php');

if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}



$order = 'reserviert_fuer';
$dir = 'ASC';

if (isset($_GET['sort'])) {
    static $count = 0;
    $orders = array("reserviert_fuer", "name", "beschrieb", "user", "gebucht_am");
    $key = array_search($_GET['sort'], $orders);
    $order = $orders[$key];

    $directions = array("ASC", "DESC");
    $key = array_search($_GET['dir'], $directions);
    $dir = $directions[$key];

    $bookings = getAllBookings(100, $order, $dir);
    $count++;
} else {
    $bookings = getAllBookings(100, $order, $dir);
}




?>

<main>

    <div class="lp-container">
        <table class="striped responsive-table deletable">
            <thead>
                <tr>
                    <th></th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=reserviert_fuer&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Datum</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=name&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Equipment</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=beschrieb&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Beschrieb</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=user&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">User</a> </th>
                    <th><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?sort=gebucht_am&dir=' . (($dir === 'ASC') ? 'DESC' : 'ASC'); ?>">Buchungszeit</a> </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($bookings as $booking) : ?>
                    <tr id="<?php echo $booking->buchung_id; ?>" class="hoverable">
                        <td><a class=" waves-effect tooltipped cb" data-position="top" data-tooltip="Buchung stornieren" href="#!"><i class="material-icons cyan-text text-darken-4">delete</i></a></td>
                        <td><?php echo $booking->reserviert_fuer; ?></td>
                        <td><?php echo $booking->name; ?></td>
                        <td><?php echo $booking->beschrieb; ?></td>
                        <td><?php echo $booking->user; ?></td>
                        <td><?php echo $booking->gebucht_am; ?></td>
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
    document.querySelector('.deletable').addEventListener('click', cancelBooking);

    function cancelBooking(e) {
        //console.log(e.target);

        if (e.target.parentElement.classList.contains('cb')) {
            let id = e.target.parentElement.parentElement.parentElement.id;
            console.log(id);


            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'booking.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            let htmlparams = `cancelId=${id}`;




            xhr.onload = function() {
                if (this.status === 200) {

                    M.toast({
                        html: this.responseText
                    }, 5000);
                    //e.target.parentElement.parentElement.parentElement.classList.add('hide');

                    location.reload();

                } else {
                    M.toast({
                        html: 'FEHLER! ' + this.responseText
                    }, 5000);
                }

            }
            xhr.send(htmlparams);







        }
    }
</script>


<?php

require_once('./base/footer.php');
?>