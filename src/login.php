<?php
include 'include/https.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>POP | PINS</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="images/favicon.png?v1" />
    <style>
	@font-face {
	    font-family: 'Nunito-Bold';
	    src: url('css/Nunito/Nunito-Bold.ttf') format('truetype');
	    font-weight: normal;
	    font-weight: normal;
	}
	
	@font-face {
	    font-family: 'Nunito-ExtraBold';
	    src: url('css/Nunito/Nunito-ExtraBold.ttf') format('truetype');
	    font-weight: normal;
	    font-weight: normal;
	}
	
	html, body {
	    height: 100%;
	    font-family: 'Nunito-Bold', sans-serif;
	}
	
	h1, h2, h3, h4, h5 {
	    font-family: 'Nunito-ExtraBold', sans-serif;
	}
	.footer {
	   left: 0;
	   bottom: 0;
	   width: 100%;
	   text-align: center;
	}
	</style>
</head>
<body onload="myLocation()">

	<script>
		$('body').css('overflow-y', 'hidden');
		$('body').css('background-size', 'cover');
		$('body').css('background-image', 'linear-gradient(to bottom right, #FF416C, #FF4B2B)');
	</script>

	<script>
	function absenLokasi(position){
		var loc = position.coords.latitude+','+position.coords.longitude;
		$('#txt_loc').val(loc);
	}
	</script>

	<script>
	function myLocation(){
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(absenLokasi);
		} else {
			alert('Kami tidak memiliki ijin untuk mengetahui lokasi anda ATAU browser yang anda pakai tidak mendukung fitur ini');
		}
	}
	</script>

	<div class="panel panel-default" style="width: 350px; padding-top: 50px; padding-bottom: 50px; margin-left: auto; margin-right: auto; margin-top: 100px; background: rgba(255, 255, 255, 0.5)!important; border: none;">
		<div class="panel-body">
			<form action="include/auth.php" method="post">
				<div clss="row">
					<div class="col-sm-12" style="text-align: center; font-size: 20px;" align="center">
						<img src="images/pins_logo.png" height="72px">
						<h4 style="color: white;">PRESENSI ONLINE PEGAWAI</h4>
						<input type="text" name="txt_user" class="form-control" style="margin-top: 5px; margin-bottom: 5px; margin-left: auto; margin-right: auto; width: 200px;" placeholder="Usename">
						<input type="password" name="txt_pass" class="form-control" style="margin-top: 5px; margin-bottom: 5px; margin-left: auto; 	margin-right: auto; width: 200px;" placeholder="Password">
						<input type="hidden" name="txt_loc" id="txt_loc" value="">
					</div>
				</div>
				<div clss="row">
					<div class="col-sm-12" style="text-align: center; font-size: 20px; margin-top: 20px;">
						<button type="submit" name="submit" class="btn btn-success btn-md">Sign In</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php
	include 'view/layout/footer.php';
	?>
</body>
</html>