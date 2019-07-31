<?php  // Script hapus_ap.php
/*
  - Script untuk menghapus data pk.
  - Input : kode pk.
  - Output : status.
 */
 
require('config.php');
require(FUNGSI);
 
$page_title .= 'Hapus Atribut Pembayaran';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser() || (!in_array($_SESSION['hak_akses'], [1, 2]))) {
    showErrorPage(1);
}
?>

<div class="page-header">
  <h3><span class="glyphicon glyphicon-trash"></span> Hapus Atribut Pembayaran</h3>
</div>

<?php
// Validasi Kode Atribut Pembayaran
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
    $id = $_GET['id'];   
} elseif (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) { 
    $id = $_POST['id'];
} else {  // Bila tidak valid :
    $id = null;
    showErrorPage(2);
}  // Akhir validasi KODE ATRIBUT PEMBAYARAN

require(MYSQLI);
$id = mysqli_real_escape_string($dbc, $id);
// Validasi kode atribut dengan database :
$q = "SELECT * FROM tbl_atribut_pembayaran WHERE kode_ap = $id";
if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
    } else {
        showErrorPage(3);
    }
}
mysqli_free_result($r);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {       
    $q = "SELECT kode_ap FROM tbl_atribut_pembayaran WHERE kode_ap = $id";
    if ($r = @mysqli_query($dbc, $q)) {
        if (mysqli_num_rows($r) == 1){
            $data = mysqli_fetch_array($r);
            $q = "DELETE FROM tbl_atribut_pembayaran WHERE kode_ap = $id";
            if ($r = @mysqli_query($dbc, $q)){
                if (mysqli_affected_rows($dbc) == 1) {
                    echo '<p class="alert alert-success">Data berhasil dihapus</p>';
                } else {
                    echo '<p class="alert alert-warning">Tidak ada data yang dihapus</p>';
                }
            } else {  
              showSqlError($dbc);
            }
        } else {
            showErrorPage(5);
        }
    } else {
        showSqlError($dbc);
    }
   
    echo '<br><a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a></div><br>';
} else {
?>
  <table style="border: 0" class="table table-responsive">
    <tr>
      <th>Nama</th>
      <td><?php echo ucwords(strtolower($data['nama'])); ?></td>
    </tr>
    <tr>
      <th>Harga</th>
      <td><?php echo number_format($data['harga'], 2, ',', '.'); ?></td>
    </tr>
  </table>
  <br><br>
  <form action="hapus_ap.php" method="post">
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
  <a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
</div><br>

<?php } include('includes/footer.html'); ?>