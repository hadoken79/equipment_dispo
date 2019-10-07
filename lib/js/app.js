
// ********* Eventlisteners **********

document.addEventListener('DOMContentLoaded', function () {
    //Datumspicker
    const optionsDP = {
        format: 'dd-mmm-yyyy',
        setDefaultDate: new Date(),
        onSelect: function () { let sqldate = date_to_yyyymmdd(picker.date); askforbookings(sqldate) },
    };
    const elem1 = document.querySelector('.datepicker');
    const picker = M.Datepicker.init(elem1, optionsDP);

    //prüfen on zwischenzeitlich jemand anderes Buchung gemacht, während inaktiv. PULL...
    /*
    window.setInterval(function () {
        let picker = M.Datepicker.getInstance(elem1);
        let date = picker.date;
        if (date) {
            askforbookings(date_to_yyyymmdd(picker.date));
        }
    }, 2000);
    */

    //wartet auf Updatecall vom Server. 
    let firstMessage = 0;
    let source = new EventSource("broadcast.php");
    
    source.onmessage = function(event) {

    //console.log("Message received " + event.data);
    if(firstMessage != event.data){
        //Wenn Broadcastmesssage ein update, hat jemand gebucht und ui braucht update
            let picker = M.Datepicker.getInstance(elem1);
            let date = picker.date;
            let sqldate = date_to_yyyymmdd(date);
            askforbookings(sqldate);
        firstMessage = event.data;
    }
    };


    document.querySelector('.collection').addEventListener('click', bookitem);

    //*******   Functions **********

    function date_to_yyyymmdd(pickeddate) {

        let workdate = new Date(pickeddate);
        //javascript Datetime in mysql Date umwandeln
        let year, month, day;
        let sqldate;
        year = String(workdate.getFullYear());
        month = String(workdate.getMonth() + 1);
        if (month.length == 1) {
            month = "0" + month;
        }
        day = String(workdate.getDate());
        if (day.length == 1) {
            day = "0" + day;
        }
        return sqldate = year + "-" + month + "-" + day;
    }


    function updateUISetBookings(response) {

        //erst werden alle Sets durchsucht
        let lis = document.querySelectorAll('.setlist');

        //wiederherstellen des ursprungszustands
        lis.forEach(function (li) {

            let bild = li.firstChild;
            let status = bild.nextSibling.nextSibling.nextSibling;
            status.textContent = '';
            let link = status.nextSibling;
            link.classList.remove('hide');
        });
        //falls bookings für Sets vorliegen
        response.forEach(function (booking) {

            lis.forEach(function (li) {
                if (li.id == 'set' + booking.set_id) {
                    let bild = li.firstChild;
                    let status = bild.nextSibling.nextSibling.nextSibling;
                    status.textContent = 'RES. | ' + booking.user;
                    let link = status.nextSibling;
                    link.classList.add('hide');
                }
            });
        });
    }

    function updateUIEquipmentBookings(response) {
        //nun werden alle Equipments durchsucht
        let lis = document.querySelectorAll('.eqlist');

        //wiederherstellen des ursprungszustands
        lis.forEach(function (li) {

            let bild = li.firstChild;
            let status = bild.nextSibling.nextSibling.nextSibling;
            status.textContent = '';
            let link = status.nextSibling;
            if (link) { link.classList.remove('hide'); };

        });
        //falls bookings für Equipment vorliegen
        response.forEach(function (booking) {

            lis.forEach(function (li) {
                if (li.id == 'eqp' + booking.eq_id) {
                    let bild = li.firstChild;
                    let status = bild.nextSibling.nextSibling.nextSibling;
                    status.textContent = 'RES. | ' + booking.user;
                    let link = status.nextSibling;
                    link.classList.add('hide');
                }
            });
        });
    }

    function askforbookings(sqldate) {

        //console.log('ask')
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'booking.php?checkdate=' + sqldate, true);

        xhr.onload = function () {
            if (this.status === 200) {

                let response = JSON.parse(this.responseText);
                updateUISetBookings(response);
                updateUIEquipmentBookings(response);
            } else {
                error.log('Fehler bei Abfrage nach Buchung js46');
            }
        }
        xhr.send();

    }

    function bookitem(e) {
        //Es muss unterschieden werden, ob die Buchung ein Set oder Equipment betrifft

        if (e.target.parentElement.classList.contains('bookeqp')) {
            let id = e.target.parentElement.parentElement.id.substring(3);
            let picker = M.Datepicker.getInstance(elem1);
            let date = picker.date;
            if (!date) { M.toast({ html: 'zuerst ein Datum wählen!' }, 1500); return }
            if (date_to_yyyymmdd(date) < date_to_yyyymmdd(new Date())) { M.toast({ html: 'Das gewälte Datum liegt in der Vergangenheit!' }, 1500); return };
            //let user = window.prompt('gib deinen Namen ein!')
            bookcall(id, 'eqp', date_to_yyyymmdd(date));


        } else if (e.target.parentElement.classList.contains('bookset')) {
            let id = e.target.parentElement.parentElement.id.substring(3);
            let picker = M.Datepicker.getInstance(elem1);
            let date = picker.date;
            if (!date) { M.toast({ html: 'wähle erst ein Datum!' }, 1500); return }
            if (date_to_yyyymmdd(date) < date_to_yyyymmdd(new Date())) { M.toast({ html: 'Das gewälte Datum liegt in der Vergangenheit!' }, 1500); return };
            bookcall(id, 'set', date_to_yyyymmdd(date));
        }
    }

    function bookcall(id, type, date) {

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'booking.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        let htmlparams = `${type}=${id}&date=${date}`;
        let loader = document.querySelector('.progress');




        xhr.onload = function () {
            if (this.status === 200) {
                loader.classList.add('hide');
                M.toast({ html: this.responseText }, 10000);
                askforbookings(date);

            } else {
                M.toast({ html: 'FEHLER! ' + this.responseText }, 5000);
            }
        }
        xhr.send(htmlparams);
        loader.classList.remove('hide');



    }

    function bookupdate() {
        let picker = M.Datepicker.getInstance(elem1);
        let date = picker.date;
        let sqldate = date_to_yyyymmdd(date);
        askforbookings(sqldate);
    }

});

function filterChanged(val) {
    let lis = document.querySelectorAll('li');
    lis.forEach(function (li) {
        if (!li.classList.contains(val) && val != 0) {
            li.classList.add('hide');
        } else {
            li.classList.remove('hide');
        }
    });
}









