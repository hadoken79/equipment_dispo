<?php
/*created by lp - 18.08.2019*/
require_once('conf\config.php');
$msg = '';
$msgClass = '';
$sendactiv = '';


if (isset($_POST['action'])) {

    $user = (isset($_POST['user']) && !empty($_POST['user'])) ? filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
    $fulluser = '';
    $pwd = (isset($_POST['password']) && !empty($_POST['password'])) ? $_POST['password'] : '0'; //im Moment ist anonymer Bind möglich, darum darf pwd nicht leer sein.
    $userdn = '';
    $grp = '';

    //user bekannt?
    if (authUser($user, $fulluser, $userdn, $pwd)) {
        //gültigkeit, pfad, domain, secure, httponly
        sessionStart(0, '/', '', false, false);
        
        $_SESSION['user'] = $fulluser;
        
        //falls Admin, menü aktiv || für es wird nach TB-Admin gesucht
        if (checkGroup($fulluser, 'TB-Admin')) {
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
    $ldap_bindusr = ADUSR;
    $ldap_bindpwd = ADPWD;
    $loginuser = '';
   
    $ldap_rdn = ADRDN;
    $ldap_bind_usr_dn = 'CN=' . $ldap_bindusr . ',OU=Admins,DC=telebasel,DC=local';

                 
    $ldap_usr_pwd = $pwd;

    $ldap_con = ldap_connect($ldap_dom);
    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    //nach user suchen
    if (@ldap_bind($ldap_con, $ldap_bind_usr_dn, $ldap_bindpwd)) {
        
        $filter = "sAMAccountName={$user}";
        $result = ldap_search($ldap_con, $ldap_rdn, $filter) or exit("unable to search");
        $entries = ldap_get_entries($ldap_con, $result);

        if(count($entries) > 1){
            //bind mit user versuchen
            $fulluser = $entries[0]['name'][0];
            //offenbar muss bind mit full username erfolgen
            $ldap_usr_dn = 'CN=' . $fulluser . ',OU=Windows 10,OU=Users,OU=Staff,DC=telebasel,DC=local';
            //$loginuser = $entries[0]['samaccountname'][0];

           /*  echo '<pre>';
            print_r($entries);
            echo '<pre>';
            echo '<hr>';
            echo 'name= '. $fulluser . '<br>';
            echo 'loginname= '. $loginuser . '<br>'; */
            
            if (@ldap_bind($ldap_con, $ldap_usr_dn, $ldap_usr_pwd)) {
                ldap_unbind($ldap_con);
                return true;
            }else {
                ldap_unbind($ldap_con);
                return false;
            }

            
        } else{
            return false;
        }
       
    } else {
        echo "Keine Verbindung zu Active Directory";
    }
}

function checkGroup($user, $group)
{
    $ldap_dom = ADDOM;
    $ldap_bindusr = ADUSR;
    $user_dn = "CN=" . $user . ",OU=Windows 10,OU=Users,OU=Staff,DC=telebasel,DC=local";
    $group_dn = "OU=Groups,OU=Staff,DC=telebasel,DC=local";
    $ldap_bindpwd = ADPWD;
    $ldap_usr_dn = 'CN=' . $ldap_bindusr . ',OU=Admins,DC=telebasel,DC=local';
    
    $ldap_con = ldap_connect($ldap_dom);

    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (@ldap_bind($ldap_con, $ldap_usr_dn, $ldap_bindpwd)) {

    $filter = "CN={$group}";
    $result = ldap_search($ldap_con, $group_dn, $filter) or exit("unable to search");
    $entries = ldap_get_entries($ldap_con, $result);

    //echo '<pre>';
    //print_r($entries);
    //echo '<pre>';
    //echo '<hr>';

    if (in_array($user_dn, $entries[0]['member'])) {
        
       return true;
    } else {
        echo "nope";
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