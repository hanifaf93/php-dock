function getLebelMonth() {
    var nows = new Date();
    let day = nows.getDate();
    var totalCurrentMonth = new Date(nows.getFullYear(), nows.getMonth() + 1, 0).getDate();
    var numberOfLebel = [];

    for (let i = 1; i <= totalCurrentMonth; i++) {
        numberOfLebel.push(i);
        //uncomment show full month
        if (day == i) {
            break;
        }
    }
    return numberOfLebel;
}

function getFulldate() {
    var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var myDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
    var date = new Date();
    var day = date.getDate();
    var month = date.getMonth();
    var thisDay = date.getDay(), thisDay = myDays[thisDay];
    var yy = date.getYear();
    var year = (yy < 1000) ? yy + 1900 : yy;

    return thisDay + ', ' + day + ' ' + months[month] + ' ' + year;
}