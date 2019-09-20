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
$bookings = getNextBookings(); //->bookings.php


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
                <div class="card large blue-grey lighten-1 white-text">
                    <div class="card-content">
                        <span class="card-title">nächste Buchungen</span>
                        <ul class="collection">
                        <?php foreach ($bookings as $booking) : ?>
                            <li id="<?php echo $booking['id']; ?>" class="collection-item black-text">
                                <div><?php echo $booking['buchung']; ?><a href="#!" class="secondary-content cb"><i class="material-icons">delete</i></a></div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6">
                <div class="card blue-grey lighten-1">
                    <div class="card-content white-text">
                        <span class="card-title">Equipment</span>
                        <p>I am a very simple card. I am good at containing small bits of information.
                            I am convenient because I require little markup to use effectively.</p>
                    </div>
                    <div class="card-action">
                        <a href="equipment-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m6">
                <div class="card blue-grey lighten-1">
                    <div class="card-content white-text">
                        <span class="card-title">Sets</span>
                        <p>I am a very simple card. I am good at containing small bits of information.
                            I am convenient because I require little markup to use effectively.</p>
                    </div>
                    <div class="card-action">
                        <a href="set-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6">
                <div class="card blue-grey lighten-1">
                    <div class="card-content white-text">
                        <span class="card-title">Lieferanten</span>
                        <p>I am a very simple card. I am good at containing small bits of information.
                            I am convenient because I require little markup to use effectively.</p>
                    </div>
                    <div class="card-action">
                        <a href="lieferanten-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m6">
                <div class="card blue-grey lighten-1">
                    <div class="card-content white-text">
                        <span class="card-title">Kategorien</span>
                        <p>I am a very simple card. I am good at containing small bits of information.
                            I am convenient because I require little markup to use effectively.</p>
                    </div>
                    <div class="card-action">
                        <a href="kategorien-view.php">Verwalten</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6">
                <div class="card blue-grey lighten-1">
                    <div class="card-content white-text">
                        <span class="card-title">Lagerorte</span>
                        <p>I am a very simple card. I am good at containing small bits of information.
                            I am convenient because I require little markup to use effectively.</p>
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
        console.log(id);
        
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'booking.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        let htmlparams = `cancelId=${id}`;
        //let loader = document.querySelector('.progress');




    xhr.onload = function () {
        if (this.status === 200) {
            //loader.classList.add('hide');
            M.toast({ html: this.responseText }, 3000);
            e.target.parentElement.parentElement.parentElement.classList.add('hide');

        } else {
            M.toast({ html: 'FEHLER! ' + this.responseText }, 5000);
        }
    }
    xhr.send(htmlparams);
    //loader.classList.remove('hide');
    }
}
</script>




<?php
require_once('./base/footer.php');
?>