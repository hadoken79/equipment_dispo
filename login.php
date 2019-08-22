<?php
/*created by lp - 18.08.2019*/
require_once('./base/header.php');
?>
<main>

    <div id="lp-logwrapper" class="container">
        <form class="col s12">
            <div class="row">
                <div class="input-field col s8 offset-s2 m4 offset-m4">
                    <input id="user" type="text" class="validate">
                    <label for="user">username</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s8 offset-s2 m4 offset-m4">
                    <input id="password" type="password" class="validate">
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