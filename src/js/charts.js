function setChartWfaOneMonth(presensi = null, id_chart = null) {

    var barChart = document.getElementById(id_chart).getContext('2d');
    var myBar = new Chart(barChart, {
        type: 'bar',
        data: {
            labels: getLebelMonth(),
            datasets: [{
                label: 'WFH',
                backgroundColor: '#ff6384',
                borderWidth: 1,
                data: presensi['wfh']
            }, {
                label: 'WFO',
                backgroundColor: '#DAF7A6',
                borderWidth: 1,
                data: presensi['wfo']
            }, {
                label: 'Statelit',
                backgroundColor: '#33FFE6',
                borderWidth: 1,
                data: presensi['satelit']
            }]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Persentase WFA Bulan ini'
            }
        }
    });
}


function setChartWfaOneDay(presensi = null, id_chart = null) {
    let now = new Date();
    let toDay = now.getDate();
    var ctx = document.getElementById(id_chart).getContext('2d');
    var myDoughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [presensi['wfh'][toDay - 1], presensi['wfo'][toDay - 1], presensi['satelit'][toDay - 1]],
                backgroundColor: [
                    '#ff6384', '#DAF7A6', '#33FFE6'
                ],
            }],
            labels: ['WFH', 'WFO', 'Satelit',]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Persentase WFA ' + getFulldate()
            },
            legend: {
                labels: {
                    boxWidth: 30
                }
            }
        },
    });
}


function setChartPresensi(presensi = null, id_chart = null) {
    var presensiChart = document.getElementById(id_chart);
    var presensiChartCtx = presensiChart.getContext('2d');
    var myChart = new Chart(presensiChartCtx, {
        type: 'bar',
        data: {
            labels: ['Check in', 'Belum', 'Cuti', 'Sakit', 'Sppd'],
            datasets: [{
                backgroundColor: '#ff6384',
                borderWidth: 1,
                data: [presensi['check_in'], presensi['belum'], presensi['cuti'], presensi['sakit'], presensi['sppd']]
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Tingkat Presensi Personil ' + getFulldate()
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem) {
                        return tooltipItem.yLabel;
                    }
                }
            }
        }
    });

    presensiChart.onclick = function (evt) {
        var activePoints = myChart.getElementsAtEvent(evt);

        if (activePoints[0]) {
            var chartData = activePoints[0]['_chart'].config.data;
            var idx = activePoints[0]['_index'];

            var label = chartData.labels[idx];
            if (label == 'Belum') {
                setModalTable(userNotCehckin);
                $('#notCheckIn').modal('show');
            }
        }
    };
}


function setChartPresensiDir(presensi_dir = null, id_chart = null) {
    var presensiDirChart = document.getElementById(id_chart).getContext('2d');

    var myChart = new Chart(presensiDirChart, {
        type: 'bar',
        data: {
            labels: ['Non Dir', 'OPS', 'FBS', 'SALES'],
            datasets: [{
                borderWidth: 1,
                data: [presensi_dir['non_dir'], presensi_dir['ops'], presensi_dir['fbs'], presensi_dir['sales']],
                type: 'line',
                borderColor: 'red',
                backgroundColor: [
                    'rgba(255, 99, 132, 0)',
                    'rgba(54, 162, 235, 0)',
                    'rgba(255, 206, 86, 0)',
                    'rgba(75, 192, 192, 0)',
                    'rgba(153, 102, 255, 0)',
                    'rgba(0, 0, 0, 0)'
                ],
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Tingkat Presensi Direktorat ' + getFulldate()
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var currentValue = dataset.data[tooltipItem.index];
                        return currentValue + "%";
                    }
                }
            },
            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: 100,
                        stepSize: 20,
                        beginAtZero: true,
                        callback: function (value) {
                            return value + "%"
                        }
                    },
                    scaleLabel: {
                        display: true,
                    },
                }],
                xAxes: [{
                    display: true,
                }],
            }
        }
    });
}


function setChartLateDir(presensi = null, id_chart = null) {
    var presensiChart = document.getElementById(id_chart);
    var presensiChartCtx = presensiChart.getContext('2d');

    var myChart = new Chart(presensiChartCtx, {
        type: 'doughnut',
        data: {
            labels: ['Non Dir', 'OPS', 'FBS', 'Sales'],
            datasets: [{
                borderWidth: 1,
                data: [
                    presensi['non_dir_late'].length,
                    presensi['ops_late'].length,
                    presensi['fbs_late'].length,
                    presensi['sales_late'].length
                ],
                backgroundColor: [
                    '#ff6384', '#DAF7A6', '#33FFE6', '#E36C09'
                ],
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                position: 'top',
                text: 'Personil Terlambat Per Direktorat ' + getFulldate(),
            },
            legend: {
                labels: {
                    boxWidth: 30
                }
            }
        }
    });

    presensiChart.onclick = function (evt) {
        var activePoints = myChart.getElementsAtEvent(evt);

        if (activePoints[0]) {
            var chartData = activePoints[0]['_chart'].config.data;
            var idx = activePoints[0]['_index'];

            var label = chartData.labels[idx];

            var lateUser = '';

            if (label == 'Non Dir') {
                lateUser = presensi['non_dir_late'];
            } else if (label == 'OPS') {
                lateUser = presensi['ops_late'];
            } else if (label == 'FBS') {
                lateUser = presensi['fbs_late'];
            } else {
                lateUser = presensi['sales_late'];
            }

            setModalTable(lateUser);

            $('#notCheckIn').modal('show');
        }
    };
}

function setChartWfaYear(presensi = null, id_chart = null, year) {

    if (chartWfaYear != null) {
        chartWfaYear.destroy();
    }

    var yAxesticks = [];
    var highestVal;
    var barChart = document.getElementById(id_chart).getContext('2d');
    chartWfaYear = new Chart(barChart, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des'],
            datasets: [{
                label: 'WFH',
                backgroundColor: '#ff6384',
                borderWidth: 1,
                data: presensi['wfh'],
            }, {
                label: 'WFO',
                backgroundColor: '#DAF7A6',
                borderWidth: 1,
                data: presensi['wfo']
            }, {
                label: 'Statelit',
                backgroundColor: '#33FFE6',
                borderWidth: 1,
                data: presensi['satelit']
            }, {
                label: 'Late',
                backgroundColor: '#5A4D4D',
                borderWidth: 1,
                data: presensi['late']
            }]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: `Persentase WFA Tahun ${year}`
            },
            scales: {
                yAxes: [
                    {
                        ticks: {
                            min: 0,
                            max: this.max,// Your absolute max value
                            callback: function (value, index, values) {
                                yAxesticks = values;
                                return value;
                                // return (value / this.max * 100).toFixed(0) + '%'; // convert it to percentage
                            },
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'KARYAWAN',
                        },
                    },
                ],
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
                            return previousValue + currentValue;
                        });
                        var currentValue = dataset.data[tooltipItem.index];
                        var precentage = Math.floor(((currentValue / yAxesticks[0]) * 100));
                        return `${currentValue} Karyawan | ${precentage}%`;
                    }
                }
            }
        }
    });
}