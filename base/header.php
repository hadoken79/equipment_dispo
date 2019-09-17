<?php
/*created by lp - 2019-07-27*/
//require_once('./db/db.php');
require_once('./db/PdoConnector.php');
session_start();
if (!isset($_SESSION['user'])) {
    Header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./lib/css/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Dispo-Equipment</title>
</head>

<body>
    <header class="z-depth-3">
        <ul id="slide-out" class="sidenav">
            <div class="background">
                <img src="images/cam.jpg">
            </div>
            <li><a class="waves-effect" href="index.php"><i class="material-icons cyan-text text-darken-4">add_shopping_cart</i>buchen</a></li>
            <div class="divider"></div>
            </li>
            <li><a class="waves-effect" href="dashboard.php"><i class="material-icons cyan-text text-darken-4">tune</i>dashboard</a></li>
            <li><a class="waves-effect" href="equipment.php"><i class="material-icons cyan-text text-darken-4">keyboard_voice</i>neues Equipment</a></li>
            <li><a class="waves-effect" href="set.php"><i class="material-icons cyan-text text-darken-4">videocam</i>neues Set</a></li>
            <li><a class="waves-effect" href="lieferant.php"><i class="material-icons cyan-text text-darken-4">store</i>neuer Lieferant</a></li>
            <li><a class="waves-effect" href="kategorie.php"><i class="material-icons cyan-text text-darken-4">layers</i>neue Kategorie</a></li>
            <li><a class="waves-effect" href="kategorie.php"><i class="material-icons cyan-text text-darken-4">domain</i>neuer Lagerort</a></li>
        </ul>
        <a href="#" data-target="slide-out" id="lp-nav-trigger" class="sidenav-trigger btn-floating left"><i class="material-icons">menu</i></a>
        <h5 class="white-text center-align">

            <?php echo $headTitle; ?></h5>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            //Sidenav
            const optionsNav = {
                edge: 'left',
            };
            const elem2 = document.querySelectorAll('.sidenav');
            const nav = M.Sidenav.init(elem2, optionsNav);
        });
    </script>