<?php
include '../../include/session.php';
include '../../include/database.php';

date_default_timezone_set($_SESSION['timezone']);

$m_parsed = '';
if (isset($_GET['m'])) {
  $m = $_GET['m'];
  if($m < 10){
  	$m_parsed = '0'.$m;
  }
} else {
  $m = 1;
  $m_parsed = '01';
}
if (isset($_GET['y'])) {
  $y = $_GET['y'];
} else {
  $y = date('Y');
}
if (isset($_GET['unit']) && ($_SESSION['role'] == 'admin' || $_SESSION['isLeader'] == '1')) {
  $unit = $_GET['unit'];
} else {
  $unit = $_SESSION['unit'];
}
?>

<html>
<head>
	<title>POP | PINS</title>
</head>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
 
	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>

	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=pop_export_".$unit."_".$y."_".$m.".xls");
	?>
 
	<center>
		<h4>Aplikasi POP (Presensi Online Pegawai) <br/> PT PINS Indonesia</h4>
	</center>

	<?php
	$arr_holidays = array();
	$sql_holiday = 'SELECT * FROM holidays;';
	$view_holiday = mysqli_query($connection, $sql_holiday);
	if(mysqli_num_rows($view_holiday) != 0) {
		while($row = mysqli_fetch_assoc($view_holiday)) {
			array_push($arr_holidays, $row['holiday_date']);
		}
	}
	$arr_days = array();
	$days_in_m = cal_days_in_month(CAL_GREGORIAN, $m, $y);
	$begin = new DateTime("$y-$m_parsed-01");
	$end = new DateTime("$y-$m_parsed-$days_in_m");
	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($begin, $interval, $end);
	foreach ($period as $dt) {
		if($dt->format('D') != 'Sat' && $dt->format('D') != 'Sun') {
			array_push($arr_days, $dt->format('Y-m-d'));
		}
	}
	$arr_weekdays = array_diff($arr_days, $arr_holidays);
	$current_m_standard_h = count($arr_weekdays) * 8;
	?>
	<p><i>Note: Jumlah jam kerja normal bulan ini adalah <?php echo count($arr_weekdays);?> hari * 8 jam = <?php echo $current_m_standard_h;?> jam</i></p>
 
	<table border="1">
		<tr>
		<th>No</th>
		<th>NAMA</th>
		<th>UNIT</th>
		<th style="text-align: center;">TAHUN</th>
		<th style="text-align: center;">BULAN</th>
		<th style="text-align: center;">HADIR</th>
		<th style="text-align: center;">IZIN</th>
		<th style="text-align: center;">SAKIT</th>
		<th style="text-align: center;">SPPD</th>
		<th style="text-align: center;">CUTI</th>
		<th style="text-align: center;">TELAT</th>
		<th style="text-align: center;">JML (JAM)</th>
		<th>#</th>
		</tr>
		<?php
		$sql_resume = 'SELECT users.name AS name, users.username AS username, units.name AS unit,
			COUNT( CASE WHEN logs.type = "reguler" THEN 1 ELSE NULL END) AS hadir,
			COUNT( CASE WHEN logs.type = "ijin" THEN 1 ELSE NULL END) AS izin,
			COUNT( CASE WHEN logs.type = "sakit" THEN 1 ELSE NULL END) AS sakit,
			COUNT( CASE WHEN logs.type = "sppd" THEN 1 ELSE NULL END) AS sppd,
			COUNT( CASE WHEN logs.type = "cuti" THEN 1 ELSE NULL END) AS cuti,
			COUNT( CASE WHEN logs.type = "reguler" AND EXTRACT(HOUR_MINUTE FROM logs.check_in) > "815" THEN 1 ELSE NULL END) AS datang_telat,
			SUM( CASE WHEN logs.type = "reguler" AND logs.check_out != "0000-00-00 00:00:00" AND TIMESTAMPDIFF(HOUR, logs.check_in, logs.check_out) < 24 THEN TIMESTAMPDIFF(HOUR, logs.check_in, logs.check_out) ELSE 0 END) AS jml_jam_kerja
			FROM logs
			JOIN users ON users.username = logs.username
			JOIN units ON units.unit_id = users.unit_id
			WHERE MONTH(logs.check_in) = "' .$m. '"
			AND YEAR(logs.check_in) = "' .$y. '"
			AND units.unit_id = "'.$unit.'"
			GROUP BY users.name
			ORDER BY users.name;';
		
		$view_resume = mysqli_query($connection, $sql_resume);
		if(mysqli_num_rows($view_resume) != 0) {
			$no = 0;
			while($row = mysqli_fetch_assoc($view_resume)) {
				$no ++;					
				echo "<tr>\n";
				echo "<td style=\"vertical-align: middle;\">".$no."</td>\n";
				echo "<td style=\"vertical-align: middle;\">".$row['name']."</td>\n";
				echo "<td style=\"vertical-align: middle;\">".$row['unit']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$y."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$m_parsed."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['hadir']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['izin']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['sakit']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['sppd']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['cuti']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['datang_telat']."</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">".$row['jml_jam_kerja']."</td>\n";
				echo "<td></td>";
				echo "</tr>\n";
			}
		}
		?>
	</table>
</body>
</html>