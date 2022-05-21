<?php
if (isset($_GET['id'])) {
  $id = $_GET['id'];
} else {
  include '../../include/environment.php';
  echo "<script>alert('Id yang dipilih salah!');window.location='https://".$root_folder."/view/report/';</script>";
}

include '../../include/database.php';
$sql_log = 'SELECT * FROM logs WHERE id = "' .$id. '";';
$view_log = mysqli_query($connection, $sql_log);
if(mysqli_num_rows($view_log) != 0) {
  while($row = mysqli_fetch_assoc($view_log)) {
    ?>
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">EDIT REPORT ID <?php echo $id;?></h4>
    </div>
    <div class="modal-body">
      <form method="post" action="save.php">
        <div class="form-group">
          <label for="username">USERNAME</label>
          <input type="text" class="form-control" name="username" value="<?php echo $row['username'];?>" disabled/>
          <input type="hidden" name="id" value="<?php echo $id;?>">
        </div>
        <div class="form-group">
          <label for="type">TIPE</label>
          <select name="type" class="form-control">
            <option value="">Pilih tipe</option>
            <option value="reguler" <?php echo($row['type'] == 'reguler') ? 'selected' : '';?>>Reguler</option>
            <option value="cuti" <?php echo($row['type'] == 'cuti') ? 'selected' : '';?>>Cuti</option>
            <option value="ijin" <?php echo($row['type'] == 'ijin') ? 'selected' : '';?>>Izin</option>
            <option value="sakit" <?php echo($row['type'] == 'sakit') ? 'selected' : '';?>>Sakit</option>
            <option value="sppd" <?php echo($row['type'] == 'sppd') ? 'selected' : '';?>>SPPD</option>
          </select>
        </div>
        <div class="form-group">
          <label for="work_from">WFO/WFH</label>
          <select name="work_from" class="form-control">
            <option value="">Pilih kehadiran</option>
            <option value="wfo" <?php echo($row['work_from'] == 'wfo') ? 'selected' : '';?>>Kantor (WFO)</option>
            <option value="wfh" <?php echo($row['work_from'] == 'wfh') ? 'selected' : '';?>>Rumah (WFH)</option>
            <option value="satelit" <?php echo($row['work_from'] == 'satelit') ? 'selected' : '';?>>Satelit (Site)</option>
          </select>
        </div>
        <div class="form-group">
          <label for="check_in">CHECK IN</label>
          <input type="text" class="form-control" name="check_in" value="<?php echo $row['check_in'];?>"/>
        </div>
        <div class="form-group">
          <label for="check_out">CHECK OUT</label>
          <input type="text" class="form-control" name="check_out" value="<?php echo $row['check_out'];?>"/>
        </div>
        <div class="form-group">
          <label for="lat_lon">KOORDINAT CHEK IN</label>
          <input type="text" class="form-control" name="lat_lon" value="<?php echo $row['lat_lon'];?>" disabled/>
        </div>
        <div class="form-group">
          <label for="lat_lon_check_out">KOORDINAT CHECK OUT</label>
          <input type="text" class="form-control" name="lat_lon_check_out" value="<?php echo $row['lat_lon_check_out'];?>" disabled/>
        </div>
        <div class="form-group">
          <label for="location">LOKASI CHECK IN</label>
          <input type="text" class="form-control" name="location" value="<?php echo $row['location'];?>" disabled/>
        </div>
        <div class="form-group">
          <label for="location_check_out">LOKASI CHECK OUT</label>
          <input type="text" class="form-control" name="location_check_out" value="<?php echo $row['location_check_out'];?>" disabled/>
        </div>
        <div class="form-group">
          <label for="more_information">KETERANGAN</label>
          <input type="text" class="form-control" name="more_information" value="<?php echo $row['more_information'];?>"/>
        </div>
        <div class="form-group">
          <label for="ip_address">IP ADDRESS</label>
          <input type="text" class="form-control" name="ip_address" value="<?php echo $row['ip_address'];?>" disabled/>
        </div>
        <button type="submit" class="btn btn-primary">SAVE</button>
      </form>
    </div>
    <?php
  }
}
?>