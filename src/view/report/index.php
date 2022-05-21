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
if (isset($_GET['u'])) {
	$u = $_GET['u'];
} else {
	$u = $_SESSION['username'];
}

?>
<script>
	$(function() {
		$('#navbar-report').addClass('active');
	});
</script>

<script>
	function search() {
		if ($('#opt_m').val() != '' && $('#opt_y').val() != '' && $('#opt_u').val() != '') {
			var m = $('#opt_m').val();
			var y = $('#opt_y').val();
			var u = $('#opt_u').val();
			var url = 'index.php?m=' + m + '&y=' + y + '&u=' + u;
			$(location).attr('href', url);
		}
	}
</script>
<br />
<br />
<br />
<br />
<!-- MODAL -->
<div class="modal fade" id="modal-default">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		</div>
	</div>
</div>
<div class="panel-group">
	<div class="panel panel-default" style="width: 95%; margin-left: auto; margin-right: auto;">
		<div class="panel-heading"><span class="glyphicon glyphicon-list-alt"></span> REPORT
			<span class="pull-right"><a target="_blank" href="../log/export.php?m=<?php echo $m; ?>&y=<?php echo $y; ?>&u=<?php echo $u; ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-cloud-download"></span> Export</a>
		</div>
		<div class="panel-body">
			<table>
				<tr>
					<td>Unit</td>
					<td>&nbsp;:&nbsp;</td>
					<td>
						<?php
						$sql_unit = 'SELECT units.name AS unit
									FROM users
									JOIN units ON users.unit_id = units.unit_id 
									WHERE users.username = "' . $u . '";';
						$view_unit = mysqli_query($connection, $sql_unit);
						if (mysqli_num_rows($view_unit) != 0) {
							while ($row = mysqli_fetch_assoc($view_unit)) {
								// echo $row['unit'];
								if ($_SESSION['isLeader'] == '1' && $row['unit'] != $_SESSION['unit']) {
									echo "<script>alert('Pilih user dengan unit yang sama!');window.location='index.php';</script>";
								} else {
									echo $row['unit'];
								}
							}
						}
						?>
					</td>
				</tr>
				<tr>
					<td>Nama</td>
					<td>&nbsp;:&nbsp;</td>
					<td>
						<select class="form-control" id="opt_u" style="margin-bottom: 5px; width: 200px;">
							<option value="">Pilih user</option>
							<?php
							$sql_leader_unit = '';
							if ($_SESSION['role'] != 'admin' && $_SESSION['isLeader'] == '1') {
								$sql_leader_unit = 'AND users.unit_id = ' . $_SESSION['unit_id'];
							} else {
								$sql_leader_unit = '';
							}
							$sql_user = 'SELECT logs.username AS username, users.name AS name 
										FROM logs
										JOIN users ON users.username = logs.username 
										WHERE logs.username NOT LIKE "%@pins.co.id" 
										' . $sql_leader_unit . '
										GROUP BY users.username ORDER BY users.username ASC;';
							$view_user = mysqli_query($connection, $sql_user);
							if (mysqli_num_rows($view_user) != 0) {
								while ($row = mysqli_fetch_assoc($view_user)) {
							?><option value="<?php echo $row['username']; ?>" <?php echo ($u == $row['username']) ? 'selected' : ''; ?>><?php echo $row['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
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
			<div style="overflow-x: auto;">
				<table class="table table-bordered">
					<tr>
						<th>No</th>
						<th>TIPE</th>
						<th>WFO/ WFH</th>
						<th>HARI LIBUR</th>
						<th>CHECK IN</th>
						<th>CHECK OUT</th>
						<th>TOTAL(JAM)</th>
						<th>LOKASI CHECK IN</th>
						<th>LOKASI CHECK OUT</th>
						<th>KETERANGAN</th>
						<th>IP ADDRESS</th>
						<th>#</th>
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

							if ($row['check_out'] == '0000-00-00 00:00:00') {
								$check_out = 'belum';
							} else {
								$check_out = $row['check_out'];
							}

							echo "<tr>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $start . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['type'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $row['work_from'] . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . getHolidayDate(date('Y-m-d', strtotime($row['check_in']))) . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">" . $row['check_in'] . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">" . $check_out . "</td>\n";
							echo "<td style=\"vertical-align: middle; text-align: center;\">" . $hour_total . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">" . $row['location'] . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">" . $row['location_check_out'] . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">" . $row['more_information'] . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">" . $row['ip_address'] . "</td>\n";
							echo "<td style=\"vertical-align: middle;\">\n";
							if ($_SESSION['role'] == 'admin') {
								echo "<a href=\"edit.php?id=" . $row['id'] . "\" class=\"btn btn-sm btn-primary\" data-toggle=\"modal\" data-target=\"#modal-default\"><span class=\"glyphicon glyphicon-pencil\"></span></a>\n";
							}
							echo "</td>\n";
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