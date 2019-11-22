<?php
/*created by lp - 18.08.2019*/
require_once('conf/config.php');
$msg = '';
$msgClass = '';
$sendactiv = '';


if (isset($_POST['action'])) {

    $user = (isset($_POST['user']) && !empty($_POST['user'])) ? filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $fulluser = '';
    $pwd = (isset($_POST['password']) && !empty($_POST['password'])) ? $_POST['password'] : 0; //im Moment ist anonymer Bind möglich, darum darf pwd nicht leer sein.
    $ldap_usr_dn = '';
    $grp = '';

    //user bekannt?
    if (authUser($user, $fulluser, $ldap_usr_dn, $pwd)) {
        //gültigkeit, pfad, domain, secure, httponly
        sessionStart(0, '/', '', false, false);
        
        $_SESSION['user'] = $fulluser;
        
        //falls Admin, menü aktiv || für es wird nach TB-Admin gesucht
        if (checkGroup($fulluser, 'TB-Admin', $ldap_usr_dn)) {
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


function authUser($user, &$fulluser, &$ldap_usr_dn, $pwd)
{
    $ldap_dom = ADDOM;
    $ldap_search_usr_dn = ADUSR;
    $ldap_search_pwd = ADPWD;
    $ldap_search_rdn = ADRDN;
                
    $ldap_usr_pwd = $pwd; //darf nicht leer sein   
    
    $ldap_con = ldap_connect($ldap_dom);
    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    //nach user suchen
    if (ldap_bind($ldap_con, $ldap_search_usr_dn, $ldap_search_pwd)) {
        
        $filter = "sAMAccountName={$user}";
        $result = ldap_search($ldap_con, $ldap_search_rdn, $filter) or exit("unable to search");
        $entries = ldap_get_entries($ldap_con, $result);

        ldap_unbind($ldap_con);
        
        if(count($entries) > 1){
            //falls user bekannt, credentials abfragen
            $fulluser = $entries[0]['name'][0]; //per Referenz, für Session
            $ldap_usr_dn = $entries[0]['distinguishedname'][0];

            //echo $ldap_usr_dn;
            
          /*echo '<pre>';
            print_r($entries);
            echo '<pre>';
            echo '<hr>';
            echo 'name= '. $fulluser . '<br>';
            echo 'loginname= '. $loginuser . '<br>';  */

            //neue Verbindung
            $ldap_con = ldap_connect($ldap_dom);
            //bind mit user versuchen
            if ($info = ldap_bind($ldap_con, utf8_decode($ldap_usr_dn), $ldap_usr_pwd)) {
                ldap_unbind($ldap_con);
                return true;
            }else {
                return false;
            }
        } else{
            return false;
        }
       
    } else {
        echo "Keine Verbindung zu Active Directory";
    }
}

function checkGroup($user, $group, $ldap_usr_dn)
{
    $ldap_dom = ADDOM;
    $ldap_search_usr_dn = ADUSR;
    $group_dn = ADGRPDN;
    $ldap_search_pwd = ADPWD;


    $ldap_con = ldap_connect($ldap_dom);

    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (@ldap_bind($ldap_con, $ldap_search_usr_dn, $ldap_search_pwd)) {

    $filter = "CN={$group}";
    $result = ldap_search($ldap_con, $group_dn, $filter) or exit("unable to search");
    $entries = ldap_get_entries($ldap_con, $result);

    //echo '<pre>';
    //print_r($entries);
    //echo '<pre>';
    //echo '<hr>';

        if (in_array($ldap_usr_dn, $entries[0]['member'])) {
            
        return true;
        } else {
            return false;
        }
    }
  
}


function letEmWait()
{
    $_SESSION['fail'] = 0;
    header('refresh: 30; url=login.php');
}

function sessionStart($lifetime, $path, $domain, $secure, $httpOnly)
{
    session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
    session_start();
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
        <div class="progress hide">
            <div class="indeterminate"></div>
        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let loader = document.querySelector('.progress');
            document.querySelector('.btn').addEventListener('click', function() {
                loader.classList.remove('hide');
            })
        });
    </script>

    <?php
    if (!empty($sendactiv)) {
        letEmWait();
    }
    require_once('./base/footer.php');
    ?>