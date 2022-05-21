<?php
include '../../include/session.php';
include '../../include/database.php';
include '../../view/layout/header.php';

if($_SESSION['isFromLdap'] != 0){
	header('location: ../../index.php');
	exit();
}

date_default_timezone_set($_SESSION['timezone']);

if(isset($_POST['txt_curr_pass']) && $_POST['txt_curr_pass'] != '' && isset($_POST['txt_new_pass']) && $_POST['txt_new_pass'] != '' && isset($_POST['txt_confirm_new_pass']) && $_POST['txt_confirm_new_pass'] != ''){
	if($_POST['txt_new_pass'] != $_POST['txt_confirm_new_pass']){
		echo "<script>alert('Password baru dan Konfirmasi password baru harus sama');</script>";
	} else {
		$sql_chpass = 'UPDATE users SET password = SHA1("'.$_POST['txt_new_pass'].'") WHERE username = "'.$_SESSION['username'].'";';
		mysqli_query($connection, $sql_chpass);
		echo "<script>alert('Password berhasil diubah');window.location='../../index.php';</script>";
	}
}
?>

	<script>
	$(function() {
	 	$('#navbar-pass-reset').addClass('active');
	});
	</script>
	<br/>
	<br/>
	<br/>
	<br/>
	<div class="panel-group">
		<div class="panel panel-default" style="width: 95%; margin-left: auto; margin-right: auto;">
			<div class="panel-heading">UBAH PASSWORD</div>
				<div class="panel-body">
					<div class="container" style="margin-left: auto; margin-right: auto; max-width: 80%; width: 350px;">
						<form action="" method="post">
							<label for="txt_curr_pass">Password lama</label>
							<input type="password" class="form-control" style="margin-bottom: 20px" name="txt_curr_pass">
							<label for="txt_new_pass">Password baru</label>
							<input type="password" class="form-control" style="margin-bottom: 20px" name="txt_new_pass">
							<label for="txt_confirm_new_pass">Konfirmasi password baru</label>
							<input type="password" class="form-control" style="margin-bottom: 20px" name="txt_confirm_new_pass">
							<button type="submit" class="btn btn-warning" style="margin-bottom: 5px;">Ganti</button>
						</form>
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