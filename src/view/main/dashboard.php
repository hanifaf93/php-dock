<?php
include '../../include/session.php';
include '../../include/database.php';
include '../../view/layout/header.php';

if ($_SESSION['role'] != 'admin') {
	header('location: ../../index.php');
	exit();
}

date_default_timezone_set($_SESSION['timezone']);

if (isset($_GET['m'])) {
	$m = $_GET['m'];
} else {
	$m = 1;
}
if (isset($_GET['y'])) {
	$y = $_GET['y'];
} else {
	$y = date('Y');
}
?>
<style>
	.loader-wrapper {
		width: 100%;
		height: 100%;
		position: absolute;
		top: 0;
		left: 0;
		background-color: #242F3F;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.loader {
		display: inline-block;
		width: 30px;
		height: 30px;
		position: relative;
		border: 4px solid #Fff;
		animation: loader 2s infinite ease;
	}

	.loader-inner {
		vertical-align: top;
		display: inline-block;
		width: 100%;
		background-color: #fff;
		animation: loader-inner 2s infinite ease-in;
	}

	@keyframes loader {
		0% {
			transform: rotate(0deg);
		}

		25% {
			transform: rotate(180deg);
		}

		50% {
			transform: rotate(180deg);
		}

		75% {
			transform: rotate(360deg);
		}

		100% {
			transform: rotate(360deg);
		}
	}

	@keyframes loader-inner {
		0% {
			height: 0%;
		}

		25% {
			height: 0%;
		}

		50% {
			height: 100%;
		}

		75% {
			height: 100%;
		}

		100% {
			height: 0%;
		}
	}
</style>
<script>
	$(function() {
		$('#navbar-dashboard').addClass('active');
	});
</script>


<div class="container" style="margin-top: 80px">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					REPORT WFA TAHUNAN
				</div>
				<div class="panel-body">
					<div class="col-md-4 col-md-offset-4" style="margin-bottom: 40px;">
						<select id="year" class="form-control sm">
							<option value="2020" selected>2020</option>
							<option value="2021">2021</option>
						</select>
					</div>
					<canvas id="myWfaYear"></canvas>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					REPORT WFA BULANAN
				</div>
				<div class="panel-body">
					<canvas id="myWfaOneMonth"></canvas>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					REPORT WFA HARIAN
				</div>
				<div class="panel-body">
					<canvas id="myWfaOneDay"></canvas>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					REPORT PERSONIL
				</div>
				<div class="panel-body">
					<canvas id="myPresensiChart"></canvas>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					REPORT DIREKTORAT
				</div>
				<div class="panel-body">
					<canvas id="myPresensiDirChart" colours="[{ fillColor: '#ffff00' }, { fillColor: '#0066ff' }]"></canvas>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					REPORT TERLAMBAT
				</div>
				<div class="panel-body">
					<canvas id="myLateDirChart"></canvas>
				</div>
			</div>
		</div>
	</div>

</div>

<div id="notCheckIn" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="notCheckInLabel">
					List User
				</h4>
			</div>
			<div class="modal-body">
				<table id="notCheckInTable" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="th-sm">No</th>
							<th class="th-sm">Nama</th>
							<th class="th-sm">Unit</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
</div>

<div class="loader-wrapper">
	<span class="loader"><span class="loader-inner"></span></span>
</div>

<script src="../../js/chart.js@2.8.0"></script>
<script src="../../js/helper.js"></script>
<script src="../../js/store.js"></script>
<script src="../../js/charts.js"></script>
<script>
	//Aybis 8-Jan-2021
	let year = $('#year').val();
	let chartWfaYear = null;

	$('#year').on('change', function() {
		let year = $(this).val();
		getDataWfaYear(year);
	})

	function getDataWfaYear(year) {

		$.get('https://api.pins.co.id/api/wfa-year', {
			year: year
		}, function(data) {
			dataWfa = [];
			dataWfa = data;
			setChartWfaYear(dataWfa, 'myWfaYear', year);
		})
	}

	function getDataWfaOneMonth() {
		$.get('https://api.pins.co.id/api/wfa_one_month', function(data) {
			dataWfa = data;
			setChartWfaOneMonth(dataWfa, 'myWfaOneMonth');
			setChartWfaOneDay(dataWfa, 'myWfaOneDay');
		})
	}

	function getDataDir() {
		$.get('https://api.pins.co.id/api/presensi_dir', function(data) {
			dataWfa = data;
			setChartPresensiDir(dataWfa, 'myPresensiDirChart');
			setChartLateDir(dataWfa, 'myLateDirChart');
		})
	}

	function getDataPresensi() {
		$.get('https://api.pins.co.id/api/presensi', function(data) {
			dataWfa = data;
			setChartPresensi(dataWfa, 'myPresensiChart');

		})
	}



	// call function get data and show chart 

	getDataWfaYear(year);
	getDataWfaOneMonth();
	getDataPresensi();
	getDataDir();

	//Aybis 8-Jan-2021


	$(window).on("load", function() {
		$(".loader-wrapper").fadeOut("slow");
	});

	var modalTable;
	var userNotCehckin = getNotCheckIn();
	var userPresensiDir = getPresensiDir();
	var userPresensi = getPresensi();
	var userWfaOneMonth = getWfaOneMonth();
	var userWfaYear = getWfaYear();

	// setChartPresensi(userPresensi, 'myPresensiChart');
	// setChartPresensiDir(userPresensiDir, 'myPresensiDirChart');
	// setChartLateDir(userPresensiDir, 'myLateDirChart');
	// setChartWfaOneMonth(userWfaOneMonth, 'myWfaOneMonth');
	// setChartWfaOneDay(userWfaOneMonth, 'myWfaOneDay');
	// setChartWfaYear(dataWfa, 'myWfaYear');


	function setModalTable(datas) {

		$("#notCheckInTable").find("tr:gt(0)").remove();

		if ($.fn.dataTable.isDataTable('#notCheckInTable')) {
			modalTable = $('#notCheckInTable').DataTable();
			modalTable.clear();
			modalTable.destroy();
		}

		$.each(datas, function(i, val) {
			$('#notCheckInTable').append(
				"<tr>" +
				"<td>" + (i + 1) + "</td>" +
				"<td>" + val.name + "</td>" +
				"<td>" + val.unit.name + "</td>" +
				"</tr>"
			);
		});

		modalTable = $('#notCheckInTable').DataTable({
			"pagingType": "simple"
		});
	}
</script>
<?php include '../../view/layout/footer.php'; ?>
</body>

</html>