<?php  // Script hapus_pay.php
/*
  - Script untuk menghapus data pembayaran.
  - Input : kode pembayaran.
  - Output : status.
 */
 
require('config.php');
require(FUNGSI);
 
$page_title .= 'Hapus Pembayaran';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser() || (!in_array($_SESSION['hak_akses'], [1, 2]))) {
    showErrorPage(1);
}
?>

<div class="page-header">
  <h3><span class="glyphicon glyphicon-trash"></span> Hapus Pembayaran</h3>
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
$q = "SELECT ps.*, pk.nama AS nama_pk FROM tbl_pembayaran AS ps INNER JOIN tbl_penyewa_kontrakan AS pk USING (kode_pk) WHERE kode_ps = $id";
if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
    } else {
        showErrorPage(3);
    }
}
mysqli_free_result($r);

$q2 = "SELECT nama FROM tbl_rincian_pembayaran INNER JOIN tbl_atribut_pembayaran USING (kode_ap) WHERE kode_ps = $id";
if ($r2 = @mysqli_query($dbc, $q2)) {
    while ($row = mysqli_fetch_array($r2, MYSQLI_ASSOC)) {
        $data_ap[] = ucwords(strtolower($row['nama']));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {       
    $q = "SELECT kode_ps FROM tbl_pembayaran WHERE kode_ps = $id";
    if ($r = @mysqli_query($dbc, $q)) {
        if (mysqli_num_rows($r) == 1){
            $data = mysqli_fetch_array($r);
            $q = "DELETE FROM tbl_rincian_pembayaran WHERE kode_ps = $id";
            if ($r = @mysqli_query($dbc, $q)){
                if (mysqli_affected_rows($dbc) >= 1) {
                    $q = "DELETE FROM tbl_pembayaran WHERE kode_ps = $id";
                    if ($r = @mysqli_query($dbc, $q)){
                        echo '<p class="alert alert-success">Data berhasil dihapus</p>';
                    } else {
                        echo '<p class="alert alert-warning">Tidak ada data yang dihapus</p>';
                    }
                } else {
                    showSqlError($dbc);
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
   
    echo '<br><a class="btn btn-default" href="data_pay.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a></div><br>';
} else {
?>
  <table style="border: 0" class="table table-responsive">
    <tr>
      <th>Nama</th>
      <td><?php echo ucwords(strtolower($data['nama_pk'])); ?></td>
    </tr>
    <tr>
      <th>Tanggal</th>
      <td><?php echo kalender_indo($data['tanggal_bayar']); ?></td>
    </tr>
    <tr>
      <th>Atribut Bayar</th>
      <td><?php echo implode(', ', $data_ap); ?></td>
    </tr>
  </table>
  <br><br>
  <form action="hapus_pay.php" method="post">
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