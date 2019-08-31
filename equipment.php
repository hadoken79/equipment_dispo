<?php
/*created by lp - 31.08.2019*/
require_once('./base/header.php');
?>

<main>
    <div class="container">
        <form id="lp-form" action="">
            <div class="row">
                <div class="input-field col s12 m4">
                    <input type="hidden" name="equipment_id" value="NULL">
                    <input id="name" type="text" maxlength="25">
                    <label for="name">Equipment Name [genauer Typ]</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="beschrieb" type="text" maxlength="40">
                    <label for="beschrieb">Beschrieb [zB dispo Funk]</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="serie" type="text" maxlength="100">
                    <label for="serie">Serien Nummer</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m4">
                    <input id="disabled" type="text" maxlength="100">
                    <label for="barcode">Barcode [inaktiv]</label>
                </div>
                <div class="input-field col s12 m4">
                    <!--Google Chrome übernimmt das html5 maxlenght attr. nicht bei number. desshalb js-->
                    <input id="kaufjahr" type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4">
                    <label for="kaufjahr">Kaufjahr</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="kaufpreis" type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10">
                    <label for="kaufpreis">Kaufpreis</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m4">
                    <select id='katpicker' onchange="katchanged(this.value)">
                        <option value="" disabled selected>wähle eine Option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </select>
                    <label>Equipment Kategorie</label>
                </div>
                <div class="input-field col s12 m4">
                    <select>
                        <option value="" disabled selected>wähle eine Option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </select>
                    <label>falls Equipment Teil eines Sets</label>
                </div>
                <div class="input-field col s12 m4">
                    <select>
                        <option value="" disabled selected>wähle eine Option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </select>
                    <label>Lagerort des Equipments</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m4">
                    <textarea id="notiz" class="materialize-textarea" maxlength="255"></textarea>
                    <label for="notiz">Interne Infos [optional]</label>
                </div>
                <div id="lp-switch" class="switch col s12 m4">
                    <label>
                        dispo | Aus
                        <input id="indispo" type="checkbox">
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
                <div id="lp-switch" class="switch col s12 m4">
                    <label>
                        aktiv | Aus
                        <input id="aktiv" type="checkbox" checked="checked">
                        <span class="lever"></span>
                        Ein
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="file-field input-field col s12 m4">
                    <div class="btn">
                        <span>Bild</span>
                        <input type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="optionales Equipmentbild">
                    </div>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light" type="submit" name="action">Speichern
                    <i class="material-icons right">send</i>
                </button>
            </div>
    </div>
    </form>
    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const options = {};
        const elems = document.querySelectorAll('select');
        const instances = M.FormSelect.init(elems, options);
        //console.log(instances[0].getSelectedValues());
    });
</script>




<?php
require_once('./base/footer.php');
?>