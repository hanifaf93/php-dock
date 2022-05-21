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
	 	$('#navbar-unit').addClass('active');
	});
	</script>

	<script>
	function createUnit(){
		if($('#txt_unit_id').val() != '' 
			&& $('#txt_name').val() != '') {
			var unit_id = $('#txt_unit_id').val();
			var name = $('#txt_name').val();
			var token = '<?php echo Token::generate() ?>';
			var ajaxurl = 'create.php',
			data =  {
				'unit_id': unit_id,
				'name': name,
				'token': token
			};
			if(confirm('Buat unit ini?')){
				$.post(ajaxurl, data, function (response) {
				    alert('Data unit berhasil dibuat.');
				    location.reload();
				});
			}
		} else {
			alert('Field * tidak boleh kosong.');
		}
	}
	</script>
	<script>
		function deleteUnit(unit_id){
		var ajaxurl = 'delete.php',
		data =  {
			'unit_id': unit_id
		};
		if(confirm('Hapus unit '+unit_id+'?')){
			$.post(ajaxurl, data, function (response) {
			    alert('Data unit '+unit_id+' dihapus.');
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
			<div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> UNIT</div>
				<div class="panel-body">
					<div style="overflow-x: auto;">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Unit ID</th>
									<th>Unit</th>
									<th>#</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><input type="text" class="form-control" name="txt_unit_id" id="txt_unit_id" value="" placeholder="Unit ID *"></td>
									<td><input type="text" class="form-control" name="txt_name" id="txt_name" value="" placeholder="Unit *"></td>
									<td style="vertical-align: middle;">
										<button type="submit" class="btn btn-success btn-xs" onclick="createUnit();">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</td>
								</tr>
	
								<tr>
									<form action="" method="post">
										<td></td>
										<td><input type="text" class="form-control" name="fnd_unit_id" id="fnd_unit_id" value=""></td>
										<td><input type="text" class="form-control" name="fnd_name" id="fnd_name" value=""></td>
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
							if (isset($_POST['fnd_unit_id'])) {
							    $fnd_unit_id = $_POST['fnd_unit_id'];
							} else {
								$fnd_unit_id = '';
							}
							if (isset($_POST['fnd_name'])) {
							    $fnd_name = $_POST['fnd_name'];
							} else {
								$fnd_name = '';
							}
							$sql_holiday = 'SELECT * FROM units
								WHERE unit_id LIKE "%'. $fnd_unit_id .'%" AND name LIKE "%'. $fnd_name .'%"
								ORDER BY unit_id ASC';
							$view_holiday = mysqli_query($connection, $sql_holiday);
				
							/*
							PAGINATION
							*/
							$sql_holiday_page = $sql_holiday.' LIMIT ' .$start_from. ', ' .$result_per_page. ';';
							$view_holiday_page = mysqli_query($connection, $sql_holiday_page);
				
							$total_user = mysqli_num_rows($view_holiday);
							$total_page = ceil($total_user / $result_per_page);
				
							if(mysqli_num_rows($view_holiday_page) == 0) {
								echo '<tr><td colspan="6">Data unit tidak ditemukan.</td></tr>'."\n";
							} else {
								while($row = mysqli_fetch_assoc($view_holiday_page)) {
						
									$start_from++;
	
							?>
								<tr>
									<td><?php echo $start_from ?></td>
									<td><?php echo $row['unit_id'];?></td>
									<td><?php echo $row['name'];?></td>
									<td>
										<button type="button" class="btn btn-danger btn-xs" onclick="deleteUnit('<?php echo $row['unit_id'];?>');">
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