<?php
include '../../include/session.php';
include '../../include/database.php';
include '../../include/holiday.php';
include '../../view/layout/header.php';

if ($_SESSION['isLeader'] != '1' && $_SESSION['role'] != 'admin') {
	header('location: ../../index.php');
	exit();
}

date_default_timezone_set($_SESSION['timezone']);

$m_parsed = '';
if (isset($_GET['m'])) {
	$m = $_GET['m'];
	if ($m < 10) {
		$m_parsed = '0' . $m;
	} else {
		$m_parsed = $m;
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
if (isset($_GET['unit']) && $_SESSION['role'] == 'admin') {
	$unit = $_GET['unit'];
} else {
	$unit = $_SESSION['unit_id'];
}

?>
<script>
	$(function() {
		$('#navbar-resume').addClass('active');
	});
</script>

<script>
	function search() {
		if ($('#opt_m').val() != '' && $('#opt_y').val() != '' && $('#opt_unit').val() != '') {
			var m = $('#opt_m').val();
			var y = $('#opt_y').val();
			var unit = $('#opt_unit').val();
			var url = 'resume.php?m=' + m + '&y=' + y + '&unit=' + unit;
			$(location).attr('href', url);
		}
	}
</script>
<br />
<br />
<br />
<br />
<div class="panel-group">
	<div class="panel panel-default" style="width: 95%; margin-left: auto; margin-right: auto;">
		<div class="panel-heading"><span class="glyphicon glyphicon-file"></span> RESUME
			<span class="pull-right"><a target="_blank" href="export.php?m=<?php echo $m; ?>&y=<?php echo $y; ?>&unit=<?php echo $unit; ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-cloud-download"></span> Export</a>
		</div>
		<div class="panel-body">
			<table>
				<tr>
					<td>Unit</td>
					<td>&nbsp;:&nbsp;</td>
					<td>
						<?php
						if ($_SESSION['role'] == 'admin') {
						?>
							<select class="form-control" id="opt_unit" style="margin-bottom: 5px; width: 200px;">
								<option value="">Pilih unit</option>
								<?php
								$sql_unit = 'SELECT *	FROM units ORDER BY name;';
								$view_unit = mysqli_query($connection, $sql_unit);
								if (mysqli_num_rows($view_unit) != 0) {
									while ($row = mysqli_fetch_assoc($view_unit)) {
								?><option value="<?php echo $row['unit_id']; ?>" <?php echo ($unit == $row['unit_id']) ? 'selected' : ''; ?>><?php echo $row['name']; ?></option>
								<?php
									}
								}
								?>
							</select>
						<?php
						} else if ($_SESSION['isLeader'] == '1') {
							echo $_SESSION['unit'];
						?>
							<input type="hidden" name="opt_unit" id="opt_unit" value="<?php echo $unit; ?>">
						<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td>Bulan</td>
					<td>&nbsp;:&nbsp;</td>
					<td>
						<select class="form-control" id="opt_m" style="margin-bottom: 5px; width: 200px;">
							<option value="">Pilih bulan</option>
							<?php
							for ($i = 1; $i <= 12; $i++) {
							?><option value="<?php echo $i; ?>" <?php echo ($i == $m) ? 'selected' : ''; ?>><?php echo $i; ?></option>
							<?php
							}
							?>
					</td>
				</tr>
				<tr>
					<td>Tahun</td>
					<td>&nbsp;:&nbsp;</td>
					<td>
						<select class="form-control" id="opt_y" style="margin-bottom: 5px; width: 200px;">
							<option value="">Pilih tahun</option>
							<?php
							$year_init = 2020;
							$year_range = intval(date('Y')) + 3;
							for ($i = $year_init; $i < $year_range; $i++) {
							?><option value="<?php echo $i; ?>" <?php echo ($i == $y) ? 'selected' : ''; ?>><?php echo $i; ?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>
						<button class="btn btn-primary" onclick="search();" style="margin-bottom: 5px;">Cari</button>
					</td>
				</tr>
			</table>
			<br />
			<?php
			$arr_holidays = array();
			$sql_holiday = 'SELECT * FROM holidays;';
			$view_holiday = mysqli_query($connection, $sql_holiday);
			if (mysqli_num_rows($view_holiday) != 0) {
				while ($row = mysqli_fetch_assoc($view_holiday)) {
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
				if ($dt->format('D') != 'Sat' && $dt->format('D') != 'Sun') {
					array_push($arr_days, $dt->format('Y-m-d'));
				}
			}
			$arr_weekdays = array_diff($arr_days, $arr_holidays);
			$current_m_standard_h = count($arr_weekdays) * 8;
			?>
			<p><i>Note: Jumlah jam kerja normal bulan ini adalah <?php echo count($arr_weekdays); ?> hari * 8 jam = <?php echo $current_m_standard_h; ?> jam</i></p>
			<div style="overflow-x: auto;">
				<table class="table table-bordered">
					<tr>
						<th>No</th>
						<th>NAMA</th>
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
					$sql_resume = 'SELECT users.name AS name, users.username AS username,
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
								WHERE MONTH(logs.check_in) = "' . $m . '"
								AND YEAR(logs.check_in) = "' . $y . '"
								AND units.unit_id = "' . $unit . '"
								GROUP BY users.name
								ORDER BY users.name;';

					$view_resume = mysqli_query($connection, $sql_resume);
					if (mysqli_num_rows($view_resume) != 0) {
						$no = 0;
						while ($row = mysqli_fetch_assoc($view_resume)) {
							$no++;
							echo "<tr>\n";
							echo "<td style=\"vertical-align: middle;\">" . $no . "</td>\n";
							echo "<td style=\"vertical-align: middle;\"><a href=\"index.php?m=" . $m . "&y=" . $y . "&u=" . $row['username'] . "\">" . $row['name'] . "</a></td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['hadir'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['izin'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['sakit'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['sppd'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['cuti'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['datang_telat'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['jml_jam_kerja'] . "</td>\n";
							echo "<td></td>";
							echo "</tr>\n";
						}
					} else {
					?>
						<tr>
							<td colspan="11">
								Report tidak ditemukan
							</td>
						</tr>
					<?php
					}
					?>
				</table>
			</div>
		</div>
	</div>
</div>
</div>
<?php
include '../../view/layout/footer.php';
?>
</body>

</html>