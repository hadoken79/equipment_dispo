<?php
/*created by lp - 2019-07-27*/
require_once('./db/PdoConnector.php');

if (!isset($_SESSION)) {
    session_start();
}

//falls user direkt auf index und nicht login gehen, muss die session zur abfrage ob eingeloggt, wieder zerstört werden.
//die zu verwendende session wird in login.php erzeugt.
if (isset($_GET['logout']) || !isset($_SESSION['user'])) {
    $_SESSION = array();

    // Session-Cookie löschen falls vorhanden.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
    Header('Location: login.php');
}

$_SESSION['fail'] = 0;
if (!isset($_SESSION['grp']) || @$_SESSION['grp'] != 'adm') {
    $nav = 'hide';
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
    <noscript>
        <h4>Javascript scheint in Deinem Browser deaktiviert zu sein. <br>Diese Seite kann nicht ohne Javascript benutzt werden...</h4>
        <a href="https://www.enable-javascript.com/de/">wie aktiviere ich javascript?</a>
    </noscript>

    <header class="z-depth-3 <?php echo ($headTitle === 'invisible') ? 'hide' : ''; ?>">
        <ul id="slide-out" class="sidenav">
            <div class="background">
                <img src="images/cam.jpg">
            </div>
            <li><a class="waves-effect <?php echo $nav; ?>" href="index.php"><i class="material-icons cyan-text text-darken-4">add_shopping_cart</i>buchen</a></li>
            <div class="divider"></div>
            </li>
            <li><a class="waves-effect <?php echo $nav; ?>" href="dashboard.php"><i class="material-icons cyan-text text-darken-4">tune</i>dashboard</a></li>
            <li><a class="waves-effect <?php echo $nav; ?>" href="equipment.php"><i class="material-icons cyan-text text-darken-4">keyboard_voice</i>neues Equipment</a></li>
            <li><a class="waves-effect <?php echo $nav; ?>" href="set.php"><i class="material-icons cyan-text text-darken-4">videocam</i>neues Set</a></li>
            <li><a class="waves-effect <?php echo $nav; ?>" href="lieferant.php"><i class="material-icons cyan-text text-darken-4">store</i>neuer Lieferant</a></li>
            <li><a class="waves-effect <?php echo $nav; ?>" href="kategorie.php"><i class="material-icons cyan-text text-darken-4">layers</i>neue Kategorie</a></li>
            <li><a class="waves-effect <?php echo $nav; ?>" href="lagerort.php"><i class="material-icons cyan-text text-darken-4">domain</i>neuer Lagerort</a></li>
            <!--logout ist für jede Gruppe verfügbar-->
            <li><a class="waves-effect" href="<?php echo $_SERVER['PHP_SELF'] . '?logout=true'; ?>"><i class="material-icons cyan-text text-darken-4">exit_to_app</i>logout</a></li>
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