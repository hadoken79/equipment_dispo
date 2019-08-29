<?php
/*created by lp - 27.07.2019*/
require_once('./base/header.php');
?>

<main>
    <div class="container">
        <form action="">
            <div class="row">
                <div class="input-field col s12 m4">
                    <input id="name" type="text" class="validate">
                    <label for="name">Equipment Name [genauer Typ]</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="beschrieb" type="text" class="validate">
                    <label for="beschrieb">Beschrieb [zB dispo Funk]</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="serie" type="text" class="validate">
                    <label for="serie">Serien Nummer</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m4">
                    <input id="disabled" type="text" class="validate">
                    <label for="barcode">Barcode [inaktiv]</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="kaufjahr" type="number" class="validate">
                    <label for="kaufjahr">Kaufjahr</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="kaufpreis" type="number" class="validate">
                    <label for="kaufpreis">Kaufpreis</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m4">
                    <select id='katpicker'>
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
                    <label>ist Equipment Teil eines Sets</label>
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
                    <textarea id="info" class="materialize-textarea"></textarea>
                    <label for="info">Interne Infos</label>
                </div>
                <div class="col s12 m4 offset-m1">
                    <label>
                        <input type="checkbox" class="filled-in" />
                        <span>Dispo</span>
                    </label>
                    <label>
                        <input type="checkbox" class="filled-in" checked="checked" />
                        <span>aktiv</span>
                    </label>
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

        //const katpicker = M.FormSelect.getInstance(instances[0]);
        //katpicker.getSelectedValues();
        //console.log(instances);
        console.log(instances[0].getSelectedValues());
    });
</script>




<?php
require_once('./base/footer.php');
?>