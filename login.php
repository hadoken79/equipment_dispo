<?php
/*created by lp - 18.08.2019*/
require_once('conf\config.php');
$msg = '';
$msgClass = '';
$sendactiv = '';

session_start();

if (isset($_POST['action'])) {

    $user = (isset($_POST['user']) && !empty($_POST['user'])) ? filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $fulluser = '';
    $pwd = (isset($_POST['password']) && !empty($_POST['password'])) ? $_POST['password'] : null;
    $userdn = '';
    $grp = '';

    //user bekannt?
    if (authUser($user, $fulluser, $userdn, $pwd)) {
        $_SESSION['user'] = $fulluser;
        //falls Admin, menü aktiv || für test ad wird nach gruppe mathematicians gesucht
        if (checkGroup($user, 'mathematicians')) {
            $_SESSION['grp'] = 'adm';
        }
        Header('Location: index.php');
    } else {
        $msg = 'Username oder Passwort falsch';
        $msgClass = 'card-panel red lighten-1';
        @$_SESSION['fail']++;
        if ($_SESSION['fail'] >= 3) {
            $msg = 'zu viele Fehlversuche | Login für 30 Sekunden inaktiv';
            $msgClass = 'card-panel red lighten-1';
            $sendactiv = 'disabled';
        }
    }
}
function authUser($user, &$fulluser, &$userdn, $pwd)
{
    $ldap_dom = ADDOM;
    $ldap_rdn = ADRDN;
    $ldap_dn = 'uid=' . $user . ',' . $ldap_rdn;
    $ldap_password = $pwd;

    $ldap_con = ldap_connect($ldap_dom);
    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);


    if (@ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {
        $filter = "uid={$user}";
        $result = ldap_search($ldap_con, $ldap_rdn, $filter) or exit("unable to search");
        $entries = ldap_get_entries($ldap_con, $result);


        $fulluser = $entries[0]['cn'][0];
        $userdn = $entries[0]['dn'][0];
        ldap_unbind($ldap_con);

        return true;
    } else {
        return false;
    }
}

function checkGroup($user, $group)
{
    $ldap_dom = ADDOM;
    $user_dn = "uid={$user},dc=example,dc=com";
    $group_dn = "ou={$group},dc=example,dc=com";



    $ldap_con = ldap_connect($ldap_dom);
    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    $attributes = array('uniquemember');
    $filter = "ou={$group}";
    $result = ldap_search($ldap_con, $group_dn, $filter) or exit("unable to search");
    $entries = ldap_get_entries($ldap_con, $result);

    //echo '<pre>';
    //print_r($entries[0]['uniquemember']);
    //echo '<pre>';
    //echo '<hr>';
    if (in_array($user_dn, $entries[0]['uniquemember'])) {
        return true;
    } else {
        return false;
    }
}


function letEmWait()
{
    $_SESSION['fail'] = 0;
    header('refresh: 30; url=login.php');
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
        <h5 class="white-text center-align">login</h5>
    </header>
    <main>

        <div id="lp-logwrapper" class="container">
            <?php if ($msg != '') : ?>
                <div class="<?php echo $msgClass; ?>"><?php echo $msg; ?></div>
            <?php endif; ?>
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
                    <button class="btn waves-effect waves-light <?php echo $sendactiv; ?>" type="submit" name="action">login
                        <i class="material-icons right">send</i>
                    </button>
                </div>

            </form>
        </div>


    </main>

    <?php
    if (!empty($sendactiv)) {
        letEmWait();
    }
    require_once('./base/footer.php');
    ?>