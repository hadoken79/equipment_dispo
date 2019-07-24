

document.addEventListener('DOMContentLoaded', function () {
    //Datumspicker
    const optionsDP = {
        format: 'dd-mmm-yyyy',
        onSelect: function (date) { date_to_yyyymmdd(date) },
    };
    const elem1 = document.querySelector('.datepicker');
    const picker = M.Datepicker.init(elem1, optionsDP);


    //KategorienFilter
    const optionsKF = {};
    const elem2 = document.querySelector('select');
    const katFilter = M.FormSelect.init(elem2, optionsKF);

    //picker.open();
});

//javascript Datetime in mysql Date umwandeln
function date_to_yyyymmdd(date) {
    let year, month, day;
    year = String(date.getFullYear());
    month = String(date.getMonth() + 1);
    if (month.length == 1) {
        month = "0" + month;
    }
    day = String(date.getDate());
    if (day.length == 1) {
        day = "0" + day;
    }
    console.log(year + "-" + month + "-" + day);
}









