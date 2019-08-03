<?php
/*created by lp - 27.07.2019*/
require_once('./base/header.php');
//mysqli
//$handler = new DBHandler();
//$kategorien = $handler->getContent("SELECT name, kategorie_id FROM kategorie WHERE geloescht=false;");

//pdo
$pdo = PdoConnector::getConn();
//todo join. nur kategorien von equiopment, dass in diso ist, sollen dargestellt werden
$kategorien_res = $pdo->query("SELECT * FROM kategorie WHERE geloescht=false");



//Am Ende Verbindung trennen
 $pdo = null;
?>
    <div class="container">
        <div class="row">
            <aside id="lp-kal" class="input-field col s12 m3">
                <input type="text" placeholder="Wähle ein Datum" class="datepicker">
                <select>
                <option value=0>Alle Kategorien</option>
               <?php foreach ($kategorien_res->fetchAll() as $kategorie) :?>
                <option value=<?php echo $kategorie->kategorie_id;?>><?php echo $kategorie->name;?></option>
                    <?php endforeach ?>;
                </select>
                <label></label>
            </aside>

            <div id="lp-card" class="col s12 m9">
                <ul class="collection">
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                    <li id="lp-listItem" class="collection-item avatar">
                        <img src="images/yuna.jpg" alt="" class="circle">
                        <span class="title">Title</span>
                        <p>First Line</p>
                        <a href="#" class="secondary-content"><i class="material-icons">playlist_add</i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php
require_once('./base/footer.php');
?>
   