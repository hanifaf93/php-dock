<?php
include '../../include/session.php';
include '../../include/database.php';
include '../../view/layout/header.php';

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
	<script>
	$(function() {
	 	$('#navbar-log').addClass('active');
	});
	</script>

	<script>
	function search(){
		if($('#opt_m').val() != '' && $('#opt_y').val() != '') {
			var m = $('#opt_m').val();
			var y = $('#opt_y').val();
			var url = 'index.php?m='+m+'&y='+y;
			$(location).attr('href', url);
		}
	}
	</script>
	<br/>
	<br/>
	<br/>
	<br/>
	<div class="panel-group">
		<div class="panel panel-default" style="width: 95%; margin-left: auto; margin-right: auto;">
			<div class="panel-heading"><span class="glyphicon glyphicon-time"></span> DATA
				<span class="pull-right"><a target="_blank" href="export.php?m=<?php echo $m;?>&y=<?php echo $y;?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-cloud-download"></span> Export</a>
			</div>
				<div class="panel-body">
					<table>
						<tr>
							<td>Nama</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<?php echo $_SESSION['fullname']; ?>
							</td>
						</tr>
						<tr>
							<td>Unit</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<?php echo $_SESSION['unit']; ?>
							</td>
						</tr>
						<tr>
							<td>Bulan</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<select class="form-control" id="opt_m" style="margin-bottom: 5px; width: 200px;" onchange="search()">
									<?php
									for ($i=1; $i <= 12; $i++) {
										?><option value="<?php echo $i;?>" <?php echo ($i == $m) ? 'selected' : '';?>><?php echo $i;?></option>
										<?php
									}
								?></select>
							</td>
						</tr>
						<tr>
							<td>Tahun</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<select class="form-control" id="opt_y" style="margin-bottom: 5px; width: 200px;" onchange="search()">
									<?php
									$year_init = 2020;
									$year_range = intval(date('Y')) + 3;
									for ($i=$year_init; $i < $year_range; $i++) { 
										?><option value="<?php echo $i;?>" <?php echo ($i == $y) ? 'selected' : '';?>><?php echo $i;?></option>
										<?php
									}
								?></select>
							</td>
						</tr>
					</table>
					<br/>			
					<?php
						$sql_log = 'SELECT 
						*
						FROM logs
						WHERE username = "' .$_SESSION['username']. '"
						AND MONTH(check_in) = "' .$m. '"
						AND YEAR(check_in) = "' .$y. '"
						ORDER BY id ASC;';
	
						$view_log = mysqli_query($connection, $sql_log);
						if(mysqli_num_rows($view_log) != 0) {
							$start = 0;
							while($row = mysqli_fetch_assoc($view_log)) {
								$start ++;
								$date_masuk = date('Y/m/d', strtotime($row['check_in']));
								$hour_keluar = date('H:i', strtotime($row['check_out']));
								$d1 = new DateTime($row['check_in']);
								if($row['check_out'] != '0000-00-00 00:00:00'){
									$d2 = new DateTime($row['check_out']);
									$check_out = date('d/m/Y H:i', strtotime($row['check_out']));
								} else {
									$d2 = $d1;
									$check_out = 'belum';
								}
								$hour_total = intval($d2->diff($d1)->format('%H'));
		
								?>
									<div class="panel panel-default">
										<div class="panel-body">
										<?php
										if($row['type'] != 'reguler'){
											$glyphicon = 'info-sign';
											$label = 'danger';
											if($row['type'] == 'sppd'){
												$glyphicon = 'briefcase';
												$label = 'primary';
											}
											else if($row['type'] == 'sakit')
												$glyphicon = 'heart';
											else if($row['type'] == 'ijin')
												$glyphicon = 'envelope';
											else if($row['type'] == 'cuti')
												$glyphicon = 'plane';
											else
												$glyphicon = 'info-sign';
										?>
											<span class="pull-right label label-<?php echo $label;?>" style="text-align: center; width: 75px;"><i class="glyphicon glyphicon-<?php echo $glyphicon;?>"></i> &nbsp;&nbsp;&nbsp;<?php echo $row['type'];?></span>
										<?php
										}
										?>
											<table>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-log-in"></i></td>
													<td><?php echo($row['type'] == 'reguler') ? date('d/m/Y H:i', strtotime($row['check_in'])) : date('d/m/Y', strtotime($row['check_in']));?></td>
												</tr>
											<?php
											if($row['type'] == 'reguler'){
											?>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-map-marker"></i></td>
													<td><?php echo $row['location'];?></td>
												</tr>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-log-out"></i></td>
													<td><?php echo $check_out;?></td>
												</tr>
											<?php
											if($row['location_check_out'] != ''){
											?>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-map-marker"></i></td>
													<td><?php echo $row['location_check_out'];?></td>
												</tr>
											<?php
											}
											if($row['work_from'] != ''){
											?>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-flash"></i></td>
													<td><?php echo strtoupper($row['work_from']);?></td>
												</tr>
											<?php
											}
											?>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-time"></i></td>
													<td><?php echo $hour_total;?> jam</td>
												</tr>
											<?php
											if($row['more_information'] != ''){
											?>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-comment"></i></td>
													<td><?php echo $row['more_information'];?></td>
												</tr>
											<?php
											}
										} else {
											?>
												<tr>
													<td style="width: 25px"><i class="glyphicon glyphicon-comment"></i></td>
													<td><?php echo $row['more_information'];?></td>
												</tr>
											<?php
										}
										?>
											</table>
											<i style="font-size: 10px;">IP address <?php echo $row['ip_address'];?></i>
										</div>
									</div>
								<?php
							}
						} else {
							?>
							Data tidak ditemukan
							<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
	include '../../view/layout/footer.php';
	?>
</body>
</html>