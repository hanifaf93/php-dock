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
	 	$('#navbar-user').addClass('active');
	});
	</script>

	<script>
	function createUser(){
		if($('#txt_username').val() != '' 
			&& $('#txt_name').val() != '' 
			&& $('#opt_unit_id').val() != '' 
			&& $('#opt_role').val() != '' 
			&& $('#txt_password').val() != '' ) {
			var txt_username = $('#txt_username').val();
			var txt_name = $('#txt_name').val();
			var opt_unit_id = $('#opt_unit_id').val();
			var opt_role = $('#opt_role').val();
			var txt_password = $('#txt_password').val();
			var token = '<?php echo Token::generate() ?>';
			var ajaxurl = 'create.php',
			data =  {
				'txt_username': txt_username,
				'txt_name': txt_name,
				'opt_unit_id': opt_unit_id,
				'opt_role': opt_role,
				'txt_password': txt_password,
				'token': token
			};
			if(confirm('Buat User ini?')){
				$.post(ajaxurl, data, function (response) {
				    alert('Data User berhasil dibuat.');
				    location.reload();
				});
			}
		} else {
			alert('Field * tidak boleh kosong.');
		}
	}
	</script>
	<script>
		function deleteUser(username){
		var ajaxurl = 'delete.php',
		data =  {
			'username': username
		};
		if(confirm('Hapus user '+username+'?')){
			$.post(ajaxurl, data, function (response) {
			    alert('Data user '+username+' dihapus.');
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
			<div class="panel-heading"><span class="glyphicon glyphicon-user"></span> USER</div>
				<div class="panel-body">
					<div style="overflow-x: auto;">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Role</th>
									<th>Unit</th>
									<th>Fullname</th>
									<th>Username</th>
									<th>Password</th>
									<th>#</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td>
										<select class="form-control" name="opt_role" id="opt_role">
											<option value="">Role *</option>
											<option value="admin">Admin</option>
											<option value="user">User</option>
										</select>
									</td>
									<td>
										<select class="form-control" name="opt_unit_id" id="opt_unit_id">
											<option value="">Unit *</option>
											<?php
											$sql_unit = 'SELECT * FROM units ORDER BY name;';
											
											$view_unit = mysqli_query($connection, $sql_unit);
											if(mysqli_num_rows($view_unit) != 0) {
												while($row = mysqli_fetch_assoc($view_unit)) {
													?>
													<option value="<?php echo $row['unit_id'];?>"><?php echo $row['name'];?></option>
													<?php
												}
											}
											?>
										</select>
									</td>
									<td><input type="text" class="form-control" name="txt_name" id="txt_name" value="" placeholder="Fullname *"></td>
									<td><input type="text" class="form-control" name="txt_username" id="txt_username" value="" placeholder="Username *"></td>
									<td><input type="password" class="form-control" name="txt_password" id="txt_password" value="" placeholder="Password *"></td>
									<td style="vertical-align: middle;">
										<button type="submit" class="btn btn-success btn-xs" onclick="createUser();">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</td>
								</tr>
	
								<tr>
									<form action="" method="post">
										<td></td>
										<td><input type="text" class="form-control" name="fnd_role" id="fnd_role" value=""></td>
										<td><input type="text" class="form-control" name="fnd_unit_id" id="fnd_unit_id" value=""></td>
										<td><input type="text" class="form-control" name="fnd_name" id="fnd_name" value=""></td>
										<td><input type="text" class="form-control" name="fnd_username" id="fnd_username" value=""></td>
										<td><input type="text" class="form-control" name="fnd_password" id="fnd_password" value="" disabled=""></td>
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
							if (isset($_POST['fnd_role'])) {
							    $fnd_role = $_POST['fnd_role'];
							} else {
								$fnd_role = '';
							}
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
							if (isset($_POST['fnd_username'])) {
							    $fnd_username = $_POST['fnd_username'];
							} else {
								$fnd_username = '';
							}
							if (isset($_POST['fnd_password'])) {
							    $fnd_password = $_POST['fnd_password'];
							} else {
								$fnd_password = '';
							}
							$sql_user = 'SELECT 
								users.id AS id,
								users.role AS role,
								units.name AS unit_id,
								users.name AS name,
								users.username AS username,
								users.password AS password
								FROM users
								JOIN units ON users.unit_id = units.unit_id
								WHERE users.role LIKE "%'.$fnd_role.'%"
								AND units.name LIKE "%'.$fnd_unit_id.'%"
								AND users.name LIKE "%'.$fnd_name.'%"
								AND users.username LIKE "%'.$fnd_username.'%"
								ORDER BY users.role, units.name, users.name';
							$view_user = mysqli_query($connection, $sql_user);
				
							/*
							PAGINATION
							*/
							$sql_user_page = $sql_user.' LIMIT ' .$start_from. ', ' .$result_per_page. ';';
							$view_user_page = mysqli_query($connection, $sql_user_page);
				
							$total_user = mysqli_num_rows($view_user);
							$total_page = ceil($total_user / $result_per_page);
				
							if(mysqli_num_rows($view_user_page) == 0) {
								echo '<tr><td colspan="6">Data User tidak ditemukan.</td></tr>'."\n";
							} else {
								while($row = mysqli_fetch_assoc($view_user_page)) {
						
									$start_from++;
	
							?>
								<tr>
									<td><?php echo $start_from ?></td>
									<td><?php echo $row['role'];?></td>
									<td><?php echo $row['unit_id'];?></td>
									<td><?php echo $row['name'];?></td>
									<td><?php echo $row['username'];?></td>
									<td>******</td>
									<td>
										<button type="button" class="btn btn-danger btn-xs" onclick="deleteUser('<?php echo $row['username'];?>');">
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