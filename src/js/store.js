function getWfaOneMonth() {
    var datas = [];
    $.ajax({
        url: 'https://api.pins.co.id/api/wfa_one_month',
        type: 'GET',
        // header: {
        //     'Accept': 'application/json',
        // },
        dataType: 'json',
        async: false,
        success: function (response) {
            datas = response
        },

        error: function (request, error) {
            console.log("error");
        }
    });

    return datas;
}


function getPresensiDir() {
    var datas = [];

    $.ajax({
        url: 'https://api.pins.co.id/api/presensi_dir',
        type: 'GET',
        // header: {
        //     'Accept': 'application/json',
        // },
        dataType: 'json',
        async: false,
        success: function (response) {
            datas = response;
        },

        error: function (request, error) {
            console.log("error");
        }
    });

    return datas;
}


function getNotCheckIn() {
    var datas = [];

    $.ajax({
        url: 'https://api.pins.co.id/api/not_check_in',
        type: 'GET',
        // header: {
        //     'Accept': 'application/json',
        // },
        dataType: 'json',
        async: false,
        success: function (response) {
            datas = response;
        },

        error: function (request, error) {
            console.log("error");
        }
    });

    return datas;
}


function getPresensi() {
    var datas = [];
    $.ajax({
        url: 'https://api.pins.co.id/api/presensi',
        type: 'GET',
        // header: {
        //     'Accept': 'application/json',
        // },
        dataType: 'json',
        async: false,
        success: function (response) {
            datas = response;
        },

        error: function (request, error) {
            console.log("error");
        }
    });

    return datas;
}

function getWfaYear() {
    var datas = [];
    $.ajax({
        url: 'https://api.pins.co.id/api/wfa-year',
        type: 'GET',
        // header: {
        //     'Accept': 'application/json',
        // },
        dataType: 'json',
        async: false,
        success: function (response) {
            datas = response
        },

        error: function (request, error) {
            console.log("error");
        }
    });

    return datas;
}