<?php  // Script hapus_pk.php
/*
  - Script untuk menghapus data pk.
  - Input : kode pk.
  - Output : status.
 */
 
require('config.php');
require(FUNGSI);
 
$page_title .= 'Hapus Data Penyewa';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser() || !in_array($_SESSION['hak_akses'], [1, 2])) {
    showErrorPage(1);
}
?>

<div class="page-header">
  <h3><span class="glyphicon glyphicon-trash"></span> Hapus Data Penyewa</h3>
</div>

<?php
// Validasi Kode Penyewa
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) && ($_SESSION['hak_akses'] == 2)) {
    $id = $_GET['id'];   
} elseif (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) { 
    $id = $_POST['id'];
} else {  // Bila user tidak valid :
    $id = null;
    showErrorPage(2);
}  // Akhir validasi KODE CUSTOMER

require(MYSQLI);
$id = mysqli_real_escape_string($dbc, $id);
// Validasi kode customer dengan database :
$q = "SELECT * FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b ON b.kode_akun = a.kode_akun INNER JOIN tbl_wilayah AS c ON b.kode_wilayah = c.kode_wilayah WHERE kode_pk = $id";
if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
    } else {
        showErrorPage(3);
    }
}
mysqli_free_result($r);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {       
    $q = "SELECT kode_akun FROM tbl_penyewa_kontrakan WHERE kode_pk = $id";
    if ($r = @mysqli_query($dbc, $q)) {
      if (mysqli_num_rows($r) == 1){
          $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
          $q = "SELECT kode_ps FROM tbl_pembayaran INNER JOIN tbl_penyewa_kontrakan USING (kode_pk) WHERE kode_pk = $id";
          $r = mysqli_query($dbc, $q);
          if (!$r) showSqlError($dbc);
          if (mysqli_num_rows($r) >= 1) {
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
                  $data_pay[] = $row['kode_ps'];
              }
              
              $q = "DELETE FROM tbl_rincian_pembayaran WHERE kode_ps IN(" . implode(', ', $data_pay) . ")";
              $r = mysqli_query($dbc, $q);
              if (!$r) showSqlError($dbc);
              
              $q = "SELECT kode_ps FROM tbl_pembayaran INNER JOIN tbl_penyewa_kontrakan USING (kode_pk) WHERE kode_pk = $id";
              $r = mysqli_query($dbc, $q);
              if (!$r) showSqlError($dbc);
          }
          
          $q = "DELETE FROM tbl_penyewa_kontrakan WHERE kode_pk = $id";
          $r = mysqli_query($dbc, $q);
          if (!$r) showSqlError($dbc);
          
          $q = "DELETE FROM tbl_akun WHERE kode_akun = {$data['kode_akun']}";
          $r = mysqli_query($dbc, $q);
          if (!$r) showSqlError($dbc);
          
          //if ($delete){
              /* if (mysqli_affected_rows($dbc) == 1) {
                  $q = "DELETE FROM tbl_akun WHERE kode_akun = {$data[0]}";
                  if ($r = @mysqli_query($dbc, $q)){
                      $q = "DELETE FROM tbl_rincian_pembayaran WHERE kode_ps = $id";
                      if ($r = @mysqli_query($dbc, $q)){
                          $q = "DELETE FROM tbl_pembayaran WHERE kode_ps = $id";  
                          if ($r = @mysqli_query($dbc, $q)){  */
                      echo '<br><div class="alert alert-success"><p>Data berhasil dihapus</p></div>';
                      /*} else {
                          echo '<p>Tidak ada data yang dihapus</p>';
                      }
                      echo '</div>';
                  } else {
                      showSqlError($dbc);
                  }
              } else {
                  showErrorPage(4);
              }*/
          /*} else {  
            //showSqlError($dbc);
            showErrorPage();
          }*/
      } else {
          showErrorPage(5);
      }
    } else {
        showSqlError($dbc);
    }
   
    echo '<br><a class="btn btn-default" href="data_pk.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a></div><br>';
} else {
?>
  <table style="border: 0" class="table table-responsive">
    <tr>
      <th>Foto</th>
      <td><img style="width: 100px; height: 100px;" src="data:image/jpeg;base64,<?= base64_encode($data['foto']); ?>" alt="Foto <?>=ucwords(strtolower($data['nama']));?>" /></td>
    </tr>
    <tr>
      <th>Nomor KTP</th>
      <td><?php echo $data['no_ktp']; ?></td>
    </tr>
    <tr>
      <th>Nama Lengkap</th>
      <td><?php echo ucwords(strtolower($data['nama'])); ?></td>
    </tr>
    <tr>
      <th>Jenis Kelamin</th>
      <td><?php echo (($data['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'); ?></td>
    </tr>
    <tr>
      <th>Tanggal Lahir</th>
      <td><?php echo kalender_indo($data['tanggal_lahir']); ?></td>
    </tr>
    <tr>
      <th>Pekerjaan</th>
      <td><?php echo ucwords(strtolower($data['pekerjaan'])); ?></td>
    </tr>
    <tr>
      <th>ID Kamar</th>
      <td><?php echo strtoupper($data['id_kamar']); ?></td>
    </tr>
    <tr>
      <th>Nomor HP</th>
      <td><?php echo $data['no_hp']; ?></td>
    </tr>
    <tr>
      <th>Email</th>
      <td><?php echo $data['email']; ?></td>
    </tr>
    <tr>
      <th>Alamat</th>
      <td>
        <?php 
          $arr = array($data['alamat'], 'Kode Pos ' . $data['kodepos'], $data['kelurahan'], $data['kecamatan'], $data['kabupaten'], $data['provinsi']);
          echo ucwords(strtolower(implode(', ', $arr)));
        ?>
      </td>
    </tr>
  </table>
  <br><br>
  <form action="hapus_pk.php" method="post">
    <p class="alert alert-warning">Anda yakin akan menghapus data tersebut?</p>
    <br>
    <p class="alert alert-danger">Data yang telah dihapus tidak dapat kembali!</p>
    <br>
    <div class="text-center">
      <input type="hidden" name="id" value="<?php echo $id; ?>" />
      <button style="width: 100px;" class="btn btn-danger" type="submit">Hapus</button>
    </div>
  </form>
  
  <br><br>
  <a class="btn btn-default" href="data_pk.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
</div><br>

<?php } include('includes/footer.html'); ?>