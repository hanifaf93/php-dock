<?php
include '../../include/token.php';
include '../../include/session.php';
include '../../view/layout/header.php';

date_default_timezone_set($_SESSION['timezone']);

$masuk = '';
$keluar = '';
$disable_btn_masuk = '';
$disable_btn_keluar = '';
$disable_opt_tipe = '';
$disable_txt_tgl_mulai = '';
$disable_txt_tgl_selesai = '';
$disable_txt_ket = '';
$str_jam_check_in_plus_8 = '';
$str_ket = '';
$tipe = '';
$tipe_reguler = '';
$ip = $_SERVER['REMOTE_ADDR'];

include '../../include/database.php';
$sql_check_masuk_keluar = 'SELECT *, DATE(check_in) AS tgl_masuk, DATE(check_out) AS tgl_keluar FROM logs WHERE username = "'. $_SESSION['username'] .'" ORDER BY id DESC LIMIT 1;';
$view_check_masuk_keluar = mysqli_query($connection, $sql_check_masuk_keluar);

if(mysqli_num_rows($view_check_masuk_keluar) != 0) { // jika ada data
	while($row = mysqli_fetch_assoc($view_check_masuk_keluar)) {
		if(
			// jika data bukan hari ini dan SUDAH cek out hari sebelumnya
			$row['tgl_masuk'] != date('Y-m-d') && $row['check_out'] != '0000-00-00 00:00:00'
		) {
			$str_ket = '';
			$masuk = date('Y-m-d H:i:s');
			$keluar = '0000-00-00 00:00:00';
			$disable_btn_masuk = '';
			$disable_btn_keluar = 'disabled';
			$disable_opt_tipe = '';
			$disable_txt_ket = '';
			$str_jam_check_in_plus_8 = '';
		} else if (
			// jika data hari ini dan SUDAH cek out hari ini juga
			($row['tgl_masuk'] == date('Y-m-d') && $row['check_out'] != '0000-00-00 00:00:00')
			// atau data tipe presensi BUKAN reguler
			|| ($row['type'] != 'reguler')
		) {
			$str_ket = $row['more_information'];
			$disable_btn_masuk = 'disabled';
			$disable_btn_keluar = 'disabled';
			$disable_opt_tipe = 'disabled';
			$disable_txt_tgl_mulai = 'disabled';
			$disable_txt_tgl_selesai = 'disabled';
			$disable_txt_ket = 'disabled';
			$str_jam_check_in_plus_8 = 'Terima kasih.';
		} else if (
			// jika data bukan hari ini TAPI BELUM cek out hari sebelumnya
			($row['tgl_masuk'] != date('Y-m-d') && $row['check_out'] == '0000-00-00 00:00:00')
			// ATAU jika data hari ini TAPI BELUM cek out hari ini
			|| ($row['tgl_masuk'] == date('Y-m-d') && $row['check_out'] == '0000-00-00 00:00:00')
		) {
			$str_ket = $row['more_information'];
			$masuk = $row['check_in'];
			$keluar = date('Y-m-d H:i:s');
			$disable_btn_masuk = 'disabled';
			$disable_btn_keluar = '';
			$disable_opt_tipe = 'disabled';
			$disable_txt_ket = 'disabled';

			$jam_check_in = new DateTime($row['check_in']);
			$jam_check_in_plus_8 = $jam_check_in->modify('+ 8 hour')->format('Y/m/d H:i');
			$str_jam_check_in_plus_8 = 'Perkiraan check out pukul '.$jam_check_in_plus_8;
		}
		$tipe = $row['type'];
	}
} else { // jika belum ada data sama sekali
	$str_ket = '';
	$masuk = date('Y-m-d H:i:s');
	$keluar = '0000-00-00 00:00:00';
	$disable_btn_masuk = '';
	$disable_btn_keluar = 'disabled';
	$disable_opt_tipe = '';
	$disable_txt_ket = '';
	$str_jam_check_in_plus_8 = '';
}

?>
	
	<script>
		$('body').css('overflow-y', 'hidden');
		$('body').css('background-size', 'cover');
		$('body').css('background-image', 'linear-gradient(to bottom right, #FF416C, #FF4B2B)');
	</script>
	
	<script>
		$('#navbar-absensi').addClass('active');
	</script>

	<script>
	function startTime(){
	    var today = new Date();
	    var h = <?php echo date('H'); ?>;
	    var m = today.getMinutes();
	    var s = today.getSeconds();
	    m = checkTime(m);
	    s = checkTime(s);
	    document.getElementById('clock').innerHTML = h + ":" + m + ":" + s;
	    var t = setTimeout(startTime, 500);
	}
	function checkTime(i){
	    if (i < 10) {i = '0' + i};
	    return i;
	}
	</script>

	<script>
	var hm = '<?php echo date('H:i');?>';
	function gantiTipePresensi(){

		var sudah_masuk = '<?php echo $disable_btn_masuk;?>';
	  	if(sudah_masuk == 'disabled') { // jika sudah check in
			$(document).ready(function(){
				$('#div_tgl').hide();
				$('#div_ket').hide();
				$('#opt_tipe').hide();
				$('#div_tipe_izin').hide();
				$('#div_tipe_reguler').hide();
			});  
	  	}

		var jam_keluar = '<?php echo $keluar;?>';
    	var val = $('#opt_tipe').val();
    	if(val == 'ijin') { // jika tipe ijin
			$(document).ready(function(){
				$('#div_tgl').show();
				$('#div_ket').show();
				$('#div_tipe_izin').show();
				$('#div_tipe_reguler').hide();
			});  
    	} else if(val == 'cuti' || val == 'sppd') { // jika tipe cuti/sppd
			$(document).ready(function(){
				$('#div_tgl').show();
				$('#div_ket').show();
				$('#div_tipe_izin').hide();
				$('#div_tipe_reguler').hide();
			});  
    	} else if (val == 'reguler') { // jika tipe reguler
    		if(hm < '08:15' && jam_keluar == '0000-00-00 00:00:00'){ // jika tipe reguler di bawah jam 08.15 dan belum cek out
				$(document).ready(function(){
					$('#div_tgl').hide();
					$('#div_ket').hide();
					$('#div_tipe_izin').hide();
					$('#div_tipe_reguler').show();
				}); 
    		} else if(hm > '08:15' && jam_keluar == '0000-00-00 00:00:00') { // jika tipe reguler di atas jam 08.15 dan belum cek out
				$(document).ready(function(){
					$('#div_tgl').hide();
					$('#div_ket').show();
					$('#div_tipe_izin').hide();
					$('#div_tipe_reguler').show();
				}); 
    		} else {
				$(document).ready(function(){
					$('#div_tgl').hide();
					$('#div_ket').hide();
					$('#div_tipe_izin').hide();
					$('#div_tipe_reguler').hide();
				});     		
    		}
    	} 
	}
	</script>

	<script>
	function absenLokasi(position){
		var hm = '<?php echo date('H:i');?>';
		var id_user = '<?php echo $_SESSION['username'];?>';
		var tipe = $('#opt_tipe').val();
		var tipe_ijin = $('#opt_tipe_ijin').val();
		var tipe_reguler = $('#opt_tipe_reguler').val();
		var tgl_mulai = $('#txt_tgl_mulai').val();
		var tgl_selesai = $('#txt_tgl_selesai').val();
		var masuk = '<?php echo $masuk;?>';
		var keluar = '<?php echo $keluar;?>';
		var ket = $('#txt_ket').val();
		var ip = '<?php echo $ip;?>';
		var loc = position.coords.latitude+','+position.coords.longitude;
		var token = '<?php echo Token::generate() ?>'

		if((tipe == 'reguler' && hm >= '08:15' && ket == '') || (tipe == 'reguler' && tipe_reguler == '')){
			alert('Anda wajib menuliskan keterangan/ pilih kehadiran.');
		} else if ((tipe == 'cuti' || tipe == 'sppd') && (tgl_mulai == '' || tgl_selesai == '' || ket == '')){
			alert('Anda wajib menuliskan keterangan dan tanggal mulai hingga selesai.');
		} else if ((tipe == 'ijin') && (tgl_mulai == '' || tgl_selesai == '' || ket == '' || tipe_ijin == '')){
			alert('Anda wajib mengisi izin /menuliskan keterangan dan tanggal mulai hingga selesai.');
		} else if (id_user != '' && tipe != '' && masuk != '' && keluar != '' && ip != '') {
			var ajaxurl = '../log/create.php',
			data =  {
				'id_user': id_user,
				'tipe': tipe,
				'tipe_ijin': tipe_ijin,
				'tipe_reguler': tipe_reguler,
				'masuk': masuk,
				'keluar': keluar,
				'ket': ket,
				'ip': ip,
				'loc': loc,
				'tgl_mulai': tgl_mulai,
				'tgl_selesai': tgl_selesai,
				'token': token
			};
			if(confirm('Buat presensi hari ini? [check in]')){
				$.post(ajaxurl, data, function (response) {
				    alert('Anda berhasil check in');
					var url = '../log/index.php?m=<?php echo date('n');?>&y=<?php echo date('Y');?>';
				    $(location).attr('href', url);
				});
			}
		}
	}
	</script>

	<script>
	function absenLokasiKeluar(position){
		var id_user = '<?php echo $_SESSION['username'];?>';
		var masuk = '<?php echo $masuk;?>';
		var keluar = '<?php echo $keluar;?>';
		var loc = position.coords.latitude+','+position.coords.longitude;
		if(id_user != '' && masuk != '' && keluar != '' && loc != '' ) {
			var ajaxurl = '../log/update.php',
			data =  {
				'id_user': id_user,
				'masuk': masuk,
				'keluar': keluar,
				'loc': loc,
			};
			if(confirm('Update presensi hari ini? [check out]')){
				$.post(ajaxurl, data, function (response) {
				    alert('Anda berhasil check out');
					var url = '../log/index.php?m=<?php echo date('n');?>&y=<?php echo date('Y');?>';
				    $(location).attr('href', url);
				});
			}
		}
	}
	</script>

	<script>
	function absenMasuk(){
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(absenLokasi);
		} else {
			alert('Kami tidak memiliki ijin untuk mengetahui lokasi anda ATAU browser yang anda pakai tidak mendukung fitur ini');
		}
	}

	function absenKeluar(){
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(absenLokasiKeluar);
		} else {
			alert('Kami tidak memiliki ijin untuk mengetahui lokasi anda ATAU browser yang anda pakai tidak mendukung fitur ini');
		}
	}
	</script>

	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<div class="panel panel-default" style="width: 350px; padding-top: 50px; padding-bottom: 50px; margin-left: auto; margin-right: auto; background: rgba(255, 255, 255, 0.5)!important; border: none;">
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-12" style="text-align: center; font-size: 14px;"><?php echo $_SESSION['fullname'];?><br/><?php echo $_SESSION['unit'];?></div>
			</div>
			<br/>
			<div class="row">
				<div class="col-sm-12" style="text-align: center; font-size: 20px;"><?php echo date('D, d M Y');?></div>
				<div class="col-sm-12" id="clock" style="text-align: center; font-size: 42px;"></div>
				<div class="col-sm-12" style="text-align: center; font-size: 14px;">Timezone <?php echo $_SESSION['timezone']?></div>
				<br/>
				<div class="col-sm-12" style="text-align: center; font-size: 12px;"><?php echo $str_jam_check_in_plus_8;?></div>
			</div>
			<br/>
			<!-- KETERANGAN -->
			<div id="div_ket" class="row">
				<div class="col-sm-12">
					<textarea id="txt_ket" class="form-control" <?php echo $disable_txt_ket;?> style="width: 280px; margin-left: auto; margin-right: auto; margin-top: 5px; margin-bottom: 5px; resize: none;" placeholder="Isi keterangan informasi ketidak hadiran atau alasan keterlambatan"><?php echo $str_ket;?></textarea>
				</div>
			</div>
			<!-- TIPE -->
			<div id="div_tipe" class="row">
				<div class="col-sm-12">
					<select id="opt_tipe" name="opt_tipe" class="form-control" onchange="gantiTipePresensi();" <?php echo $disable_opt_tipe;?> style="width: 280px; margin-left: auto; margin-right: auto; margin-bottom: 5px;">
						<option value="reguler" <?php echo($tipe == 'reguler') ? 'selected' : '';?>>Hadir</option>
						<option value="cuti" <?php echo($tipe == 'cuti') ? 'selected' : '';?>>Cuti</option>
						<option value="ijin" <?php echo($tipe == 'ijin' || $tipe == 'sakit') ? 'selected' : '';?>>Izin</option>
						<option value="sppd" <?php echo($tipe == 'sppd') ? 'selected' : '';?>>SPPD</option>
					</select>
				</div>
			</div>
			<!-- IJIN -->
			<div id="div_tipe_reguler" class="row">
				<div class="col-sm-12">
					<select id="opt_tipe_reguler" name="opt_tipe_reguler" class="form-control" onchange="gantiTipePresensi();" <?php echo $disable_opt_tipe;?> style="width: 280px; margin-left: auto; margin-right: auto; margin-bottom: 5px;">
						<option value="">- Pilih Kehadiran -</option>
						<option value="wfo" <?php echo($tipe_reguler == 'wfo') ? 'selected' : '';?>>Kantor (WFO)</option>
						<option value="wfh" <?php echo($tipe_reguler == 'wfh') ? 'selected' : '';?>>Rumah (WFH)</option>
						<option value="satelit" <?php echo($tipe_reguler == 'satelit') ? 'selected' : '';?>>Satelit (Site)</option>
					</select>
				</div>
			</div>
			<!-- IJIN -->
			<div id="div_tipe_izin" class="row">
				<div class="col-sm-12">
					<select id="opt_tipe_ijin" name="opt_tipe_izin" class="form-control" onchange="gantiTipePresensi();" <?php echo $disable_opt_tipe;?> style="width: 280px; margin-left: auto; margin-right: auto; margin-bottom: 5px;">
						<option value="">- Pilih Izin -</option>
						<option value="ijin" <?php echo($tipe == 'ijin') ? 'selected' : '';?>>Izin Keperluan Lain</option>
						<option value="sakit" <?php echo($tipe == 'sakit') ? 'selected' : '';?>>Izin Sakit</option>
					</select>
				</div>
			</div>
			<!-- TANGGAL -->
			<div id="div_tgl" class="row">
				<div class="col-sm-12">
					<input type="text" id="txt_tgl_mulai" class="form-control" style="width: 280px; margin-left: auto; margin-right: auto; margin-bottom: 5px; resize: none;" placeholder="Tanggal mulai YYYY-MM-DD" value="<?php echo date('Y-m-d');?>" <?php echo $disable_txt_tgl_mulai;?>>
				</div>
				<div class="col-sm-12">
					<input type="text" id="txt_tgl_selesai" class="form-control" style="width: 280px; margin-left: auto; margin-right: auto; margin-bottom: 5px; resize: none;" placeholder="Tanggal selesai YYYY-MM-DD" value="<?php echo date('Y-m-d');?>" <?php echo $disable_txt_tgl_selesai;?>>
				</div>
			</div>
			<!-- BUTTON -->
			<div class="row">
				<div class="col-sm-12" style="text-align: center; font-size: 20px; margin-top: 20px;">
					<button type="button" class="btn btn-success btn-md" onclick="absenMasuk();" <?php echo $disable_btn_masuk;?>>Check in</button>
					<button type="button" class="btn btn-danger btn-md" onclick="absenKeluar();" <?php echo $disable_btn_keluar;?>>Check out</button>
				</div>
			</div>
		</div>
	</div>
	<?php
	include '../../view/layout/footer.php';
	?>
</body>
</html>
