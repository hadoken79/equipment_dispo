<?php
/*created by lp - 31.08.2019*/
$headTitle = "Dashboard";
require_once('./base/header.php');
require_once('booking.php');

if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    Header('Location: login.php');
}

$msg = '';
$msgClass = '';

// Form val's
$selectKategories = getKategories();
$selectSets = getSets();
$selectLagerorte = getLagerorte();
$selectLieferanten = getLieferanten();
$bookings = getNextBookings(10); //->bookings.php


function getKategories()
{
    $pdo = PdoConnector::getConn();
    $selectKategories = $pdo->query("SELECT kategorie_id, `name` FROM kategorie WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectKategories;
}

function getSets()
{
    $pdo = PdoConnector::getConn();
    $selectSets = $pdo->query("SELECT set_id, `name` FROM set_ WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectSets;
}

function getLagerorte()
{
    $pdo = PdoConnector::getConn();
    $selectLagerorte = $pdo->query("SELECT lagerort_id, `name` FROM lagerort WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectLagerorte;
}

function getLieferanten()
{
    $pdo = PdoConnector::getConn();
    $selectLieferanten = $pdo->query("SELECT lieferant_id, `firma` FROM lieferant WHERE geloescht = false")->fetchAll();
    $pdo = null;
    return $selectLieferanten;
}



?>

<main>

    <div class="container">
        <div class="row">
            <div class="col s12 m12">
                <div class="card large">
                    <div class="card-content">
                        <span class="card-title">nächste Buchungen</span>
                        <ul class="collection">
                            <?php foreach ($bookings as $booking) : ?>
                                <li id="<?php echo $booking['id']; ?>" class="collection-item black-text <?php echo ($booking['rdate'] == date('d-m-Y')) ? "yellow accent-2" : "";?>">
                                    <div><?php echo $booking['rdate'] . ' | ' . $booking['name'] . ' ---> ' . $booking['user']; ?><a href="#!" class="secondary-content cb"><i class="material-icons">delete</i></a></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-action">
                        <a href="booking-view.php">Mehr</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6">
                <div class="card tiny">
                    <div class="card-content">
                        <span class="card-title">Equipment</span>
                        <p>Hier kann unser Equipment eingesehen und angepasst werden.</p>
                    </div>
                    <div class="card-action">
                        <a href="equipment-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m6">
                <div class="card tiny">
                    <div class="card-content">
                        <span class="card-title">Sets</span>
                        <p>Eine Übersicht der Sets und zugehörigem Equipment.</p>
                    </div>
                    <div class="card-action">
                        <a href="set-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6">
                <div class="card tiny">
                    <div class="card-content">
                        <span class="card-title">Lieferanten</span>
                        <p>Kontaktdaten unserer Lieferanten für Equipment.</p>
                    </div>
                    <div class="card-action">
                        <a href="lieferanten-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m6">
                <div class="card tiny">
                    <div class="card-content">
                        <span class="card-title">Kategorien</span>
                        <p>Alle erfassten Kategorien.</p>
                    </div>
                    <div class="card-action">
                        <a href="kategorien-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6">
                <div class="card tiny">
                    <div class="card-content">
                        <span class="card-title">Lagerorte</span>
                        <p>Alle erfassten Lagerorte.</p>
                    </div>
                    <div class="card-action">
                        <a href="lagerort-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>



<script>
    document.querySelector('.collection').addEventListener('click', cancelBooking);

    function cancelBooking(e) {


        if (e.target.parentElement.classList.contains('cb')) {
            let id = e.target.parentElement.parentElement.parentElement.id;



            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'booking.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            let htmlparams = `cancelId=${id}`;
            console.log(id);





            xhr.onload = function() {
                if (this.status === 200) {

                    M.toast({
                        html: this.responseText
                    }, 3000);
                    e.target.parentElement.parentElement.parentElement.classList.add('hide');

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