<?php
/*created by lp - 18.08.2019*/
//require_once('./base/header.php');
session_start();
if (isset($_POST['action'])) {

    $user = (isset($_POST['user']) && !empty($_POST['user'])) ? filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS) : null;

    if ($user === 'linus') {
        $_SESSION['user'] = 'linus';
        Header('Location: index.php');
        echo "logged in";
    }
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
    <header>
    </header>
    <main>

        <div id="lp-logwrapper" class="container">
            <form id="lp-form" class="col s12" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="row">
                    <div class="input-field col s8 offset-s2 m4 offset-m4">
                        <input id="user" name="user" type="text" class="validate">
                        <label for="user">username</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s8 offset-s2 m4 offset-m4">
                        <input id="password" name="password" type="password" class="validate">
                        <label for="password">passwort</label>
                    </div>
                </div>
                <div class="row center-align">
                    <button class="btn waves-effect waves-light" type="submit" name="action">login
                        <i class="material-icons right">send</i>
                    </button>
                </div>

            </form>
        </div>


    </main>

    <?php
    require_once('./base/footer.php');
    ?>