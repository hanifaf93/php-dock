<!DOCTYPE html>
<html lang="en">

<head>
	<title>POP | PINS</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/highcharts-3d.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/buttons/1.6.2/css/buttons.bootstrap.min.css" type="text/javascript"></script>

	<link rel="shortcut icon" href="../../images/favicon.png?v1" />
	<style>
		@font-face {
			font-family: 'Nunito-Bold';
			src: url('../../css/Nunito/Nunito-Bold.ttf') format('truetype');
			font-weight: normal;
			font-weight: normal;
		}

		@font-face {
			font-family: 'Nunito-ExtraBold';
			src: url('../../css/Nunito/Nunito-ExtraBold.ttf') format('truetype');
			font-weight: normal;
			font-weight: normal;
		}

		html,
		body {
			height: 100%;
			font-family: 'Nunito-Bold', sans-serif;
		}

		h1,
		h2,
		h3,
		h4,
		h5 {
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

<body onload="startTime();gantiTipePresensi();">
	<nav class="navbar navbar-inverse navbar-fixed-top print-remove-element">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse-navbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">
					<span style="color: #fdfdfd"><b>Presensi Online Pegawai</b></span>
				</a>
			</div>
			<div class="collapse navbar-collapse" id="collapse-navbar">
				<ul class="nav navbar-nav navbar-right">
					<li id="clock1"><a href="#"></a></li>
					<li id="navbar-absensi"><a href="../main">Presensi</a></li>
					<li id="navbar-log"><a href="../log/index.php?m=<?php echo date('n'); ?>&y=<?php echo date('Y'); ?>&u=<?php echo $_SESSION['username']; ?>">Data</a></li>
					<?php
					if ($_SESSION['role'] == 'admin') {
					?>
						<li class="dropdown" id="navbar-admin">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"> Admin<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="navbar-dashboard"><a href="../../view/main/dashboard.php">Dashboard</a></li>
								<li id="navbar-report"><a href="../../view/report/index.php?m=<?php echo date('n'); ?>&y=<?php echo date('Y'); ?>&u=<?php echo $_SESSION['username']; ?>">Report</a></li>
								<li id="navbar-resume"><a href="../../view/report/resume.php?m=<?php echo date('n'); ?>&y=<?php echo date('Y'); ?>&unit=<?php echo $_SESSION['unit_id']; ?>">Resume</a></li>
								<li id="navbar-unit"><a href="../../view/unit/index.php">Unit</a></li>
								<li id="navbar-user"><a href="../../view/user/index.php">User</a></li>
								<li id="navbar-holiday"><a href="../../view/holiday/index.php">Hari libur</a></li>
							</ul>
						</li>
					<?php
					}
					?>
					<?php
					if ($_SESSION['isLeader'] == '1' && $_SESSION['role'] != 'admin') {
					?>
						<li class="dropdown" id="navbar-sl">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"> SL<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li id="navbar-report"><a href="../../view/report/index.php?m=<?php echo date('n'); ?>&y=<?php echo date('Y'); ?>&u=<?php echo $_SESSION['username']; ?>">Report</a></li>
								<li id="navbar-resume"><a href="../../view/report/resume.php?m=<?php echo date('n'); ?>&y=<?php echo date('Y'); ?>&unit=<?php echo $_SESSION['unit_id']; ?>">Resume</a></li>
							</ul>
						</li>
					<?php
					}
					?>
					<li class="dropdown" id="navbar-chpass">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['username'] . ' (' . $_SESSION['role'], ')'; ?>
							<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php
							if ($_SESSION['isFromLdap'] == 0) {
							?>
								<li id="navbar-pass-reset"><a href="../password-reset/index.php">Ubah password</a></li>
							<?php
							}
							?>
							<li><a href="../../logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>