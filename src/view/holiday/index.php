<?php
include '../../include/token.php';
include '../../include/session.php';
include '../../include/database.php';
include '../layout/header.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}
?>
	<script>
	$(function() {
	 	$('#navbar-holiday').addClass('active');
	});
	</script>

	<script>
	function createHoliday(){
		if($('#txt_tgl').val() != '' 
			&& $('#txt_nama').val() != '') {
			var tgl = $('#txt_tgl').val();
			var nama = $('#txt_nama').val();
			var token = '<?php echo Token::generate() ?>';
			var ajaxurl = 'create.php',
			data =  {
				'tgl': tgl,
				'nama': nama,
				'token': token
			};
			if(confirm('Buat hari libur ini?')){
				$.post(ajaxurl, data, function (response) {
				    alert('Data hari libur berhasil dibuat.');
				    location.reload();
				});
			}
		} else {
			alert('Field * tidak boleh kosong.');
		}
	}
	</script>
	<script>
		function deleteHoliday(tgl){
		var ajaxurl = 'delete.php',
		data =  {
			'tgl': tgl
		};
		if(confirm('Hapus hari libur '+tgl+'?')){
			$.post(ajaxurl, data, function (response) {
			    alert('Data hari libur '+tgl+' dihapus.');
			    location.reload();
			});
		}
	}
	</script>
	<br />
	<br />
	<br />
	<br />
	<div class="panel-group">
		<div class="panel panel-default" style="width: 95%; margin-left: auto; margin-right: auto;">
			<div class="panel-heading"><span class="glyphicon glyphicon-calendar"></span> HARI LIBUR</div>
				<div class="panel-body">
					<div style="overflow-x: auto;">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Tanggal</th>
									<th>Hari libur</th>
									<th>#</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><input type="text" class="form-control" name="txt_tgl" id="txt_tgl" value="" placeholder="Tanggal *"></td>
									<td><input type="text" class="form-control" name="txt_nama" id="txt_nama" value="" placeholder="Hari libur *"></td>
									<td style="vertical-align: middle;">
										<button type="submit" class="btn btn-success btn-xs" onclick="createHoliday();">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</td>
								</tr>
	
								<tr>
									<form action="" method="post">
										<td></td>
										<td><input type="text" class="form-control" name="fnd_tgl" id="fnd_tgl" value=""></td>
										<td><input type="text" class="form-control" name="fnd_nama" id="fnd_nama" value=""></td>
										<td style="vertical-align: middle;">
											<button type="submit" class="btn btn-primary btn-xs">
												<span class="glyphicon glyphicon-search"></span>
											</button>
										</td>
									</form>
								</tr>
	
							<?php
							if (isset($_GET['page'])) {
							    $page = $_GET['page'];
							} else {
							    $page = 1;
							}
							$result_per_page = 25;
							$start_from = ($page - 1) * $result_per_page;
				
							/*
							SELECT USER
							*/
							if (isset($_POST['fnd_tgl'])) {
							    $fnd_tgl = $_POST['fnd_tgl'];
							} else {
								$fnd_tgl = '';
							}
							if (isset($_POST['fnd_nama'])) {
							    $fnd_nama = $_POST['fnd_nama'];
							} else {
								$fnd_nama = '';
							}
							$sql_holiday = 'SELECT * FROM holidays
								WHERE holiday_date LIKE "%'. $fnd_tgl .'%" AND name LIKE "%'. $fnd_nama .'%"
								ORDER BY holiday_date ASC';
							$view_holiday = mysqli_query($connection, $sql_holiday);
				
							/*
							PAGINATION
							*/
							$sql_holiday_page = $sql_holiday.' LIMIT ' .$start_from. ', ' .$result_per_page. ';';
							$view_holiday_page = mysqli_query($connection, $sql_holiday_page);
				
							$total_user = mysqli_num_rows($view_holiday);
							$total_page = ceil($total_user / $result_per_page);
				
							if(mysqli_num_rows($view_holiday_page) == 0) {
								echo '<tr><td colspan="6">Data hari libur tidak ditemukan.</td></tr>'."\n";
							} else {
								while($row = mysqli_fetch_assoc($view_holiday_page)) {
						
									$start_from++;
	
							?>
								<tr>
									<td><?php echo $start_from ?></td>
									<td><?php echo $row['holiday_date'];?></td>
									<td><?php echo $row['name'];?></td>
									<td>
										<button type="button" class="btn btn-danger btn-xs" onclick="deleteHoliday('<?php echo $row['holiday_date'];?>');">
											<span class="glyphicon glyphicon-trash"></span>
										</button>
									</td>
								</tr>
	
						<?php		
							}
						}
			
						?>
							</tbody>
						</table>
					</div>
					<ul class="pagination pagination-sm">
					<?php
					for($i = 1; $i <= $total_page; $i++){
					    ?>
					    <li<?php echo ($i == $page) ? ' class="active"' : '';?>><a href="index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
					    <?php
					}
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
	include '../layout/footer.php';
	?>
</body>
</html>