<?php
include '../../include/session.php';
include '../../include/database.php';
include '../../include/holiday.php';

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
if (isset($_GET['u']) && ($_SESSION['role'] == 'admin' || $_SESSION['isLeader'] == '1')) {
	$u = $_GET['u'];
} else {
	$u = $_SESSION['username'];
}
?>

<html>

<head>
	<title>POP | PINS</title>
</head>

<body>
	<style type="text/css">
		body {
			font-family: sans-serif;
		}

		table {
			margin: 20px auto;
			border-collapse: collapse;
		}

		table th,
		table td {
			border: 1px solid #3c3c3c;
			padding: 3px 8px;

		}

		a {
			background: blue;
			color: #fff;
			padding: 8px 10px;
			text-decoration: none;
			border-radius: 2px;
		}
	</style>

	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=pop_export_" . $u . "_" . $y . "_" . $m . ".xls");
	?>

	<center>
		<h4>Aplikasi POP (Presensi Online Pegawai) <br /> PT PINS Indonesia</h4>
	</center>

	<table border="1">
		<tr>
			<th>No</th>
			<th>USERNAME</th>
			<th>TIPE</th>
			<th>WFO/WFH</th>
			<th>HARI LIBUR</th>
			<th>CHECK IN / OUT</th>
			<th>TOTAL(JAM)</th>
			<th>LOKASI CHECK IN</th>
			<th>LOKASI CHECK OUT</th>
			<th>KETERANGAN</th>
			<th>IP ADDRESS</th>
		</tr>
		<?php
		$sql_log = 'SELECT 
		*
		FROM logs
		WHERE username = "' . $u . '"
		AND MONTH(check_in) = "' . $m . '"
		AND YEAR(check_in) = "' . $y . '"
		ORDER BY check_in ASC;';

		$view_log = mysqli_query($connection, $sql_log);
		if (mysqli_num_rows($view_log) != 0) {
			$start = 0;
			while ($row = mysqli_fetch_assoc($view_log)) {
				$start++;

				$d1 = new DateTime($row['check_in']);
				if ($row['check_out'] != '0000-00-00 00:00:00') {
					$d2 = new DateTime($row['check_out']);
				} else {
					$d2 = $d1;
				}
				$hour_total = intval($d2->diff($d1)->format('%H'));

				echo "<tr>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">" . $start . "</td>\n";
				echo "<td style=\"vertical-align: middle;\">" . $row['username'] . "</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['type'] . "</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['work_from'] . "</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">" . getHolidayDate(date('Y-m-d', strtotime($row['check_in']))) . "</td>\n";
				echo "<td style=\"vertical-align: middle;\">" . $row['check_in'] . "<br/>" . $row['check_out'] . "</td>\n";
				echo "<td style=\"vertical-align: middle; text-align: center;\">" . $hour_total . "</td>\n";
				echo "<td style=\"vertical-align: middle;\">" . $row['location'] . "</td>\n";
				echo "<td style=\"vertical-align: middle;\">" . $row['location_check_out'] . "</td>\n";
				echo "<td style=\"vertical-align: middle;\">" . $row['more_information'] . "</td>\n";
				echo "<td style=\"vertical-align: middle;\">" . $row['ip_address'] . "</td>\n";
				echo "</tr>\n";
			}
		}
		?>
	</table>
</body>

</html>