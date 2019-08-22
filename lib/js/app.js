
document.addEventListener('DOMContentLoaded', function () {
    //Datumspicker
    const optionsDP = {
        format: 'dd-mmm-yyyy',
        onSelect: function () { date_to_yyyymmdd(picker.date) },
    };
    const elem1 = document.querySelector('.datepicker');
    const picker = M.Datepicker.init(elem1, optionsDP);
    //picker.open();        

    //Kategorienfilter
    const optionsKF = {
        hover: false,
        onCloseStart: function () { callKat(filter.focusedIndex) }
    };
    var elem2 = document.querySelector('.dropdown-trigger');
    var filter = M.Dropdown.init(elem2, optionsKF);


});


//javascript Datetime in mysql Date umwandeln
function date_to_yyyymmdd(date) {
    let year, month, day;
    let sqldate;
    year = String(date.getFullYear());
    month = String(date.getMonth() + 1);
    if (month.length == 1) {
        month = "0" + month;
    }
    day = String(date.getDate());
    if (day.length == 1) {
        day = "0" + day;
    }
    sqldate = year + "-" + month + "-" + day;

    //AJAX to php
    const xhr = new XMLHttpRequest();


}
//Kategorie an PHP übergeben
function callKat(focusedIndex) {
    console.log(focusedIndex);
    //M.toast({ html: focusedIndex }, 3000);
}









