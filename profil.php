<?php  # Script detail_data_pk.php
// Script untuk menampilkan profil penghuni.

require('config.php');
require(FUNGSI);

$page_title .= 'Data Penyewa Kontrakan';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

echo '<div class="container">';

if (validasiUser()) {
    require(MYSQLI);
} else {
    showErrorPage(1);
}

if (isset($_SESSION['nama'])){
  $nama = $_SESSION['nama'];
} else {
  $nama = 'Super Admin';
}

echo '<div class="page-header"><h3><span class="fas fa-user-alt"></span> Profil</h3></div>';

if ($_SESSION['hak_akses'] == 1){
    $id = mysqli_real_escape_string($dbc, trim(strip_tags($_SESSION['kode_akun'])));
    $q = "SELECT * FROM tbl_akun WHERE kode_akun = $id";
} else {
    $id = mysqli_real_escape_string($dbc, trim(strip_tags($_SESSION['kode_pk'])));
    $q = "SELECT a.*, b.*, c.* FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b USING (kode_akun) INNER JOIN tbl_wilayah AS c USING (kode_wilayah) WHERE kode_pk = $id";
}

if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        mysqli_free_result($r);

        if ($_SESSION['hak_akses'] == 1){
            showProfilSuperAdmin($row);
        } else {
            showProfilPenghuni($dbc, $row);
        }
    } else {
        showErrorPage(3);
    }
} else {
    showSqlError($dbc);
}
mysqli_close($dbc);

echo '</div><br>';
include('includes/footer.html');
?>