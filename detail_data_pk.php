<?php  # Script detail_data_pk.php
// Script untuk menampilkan profil penghuni.

require('config.php');
require(FUNGSI);

$page_title .= 'Penyewa Kontrakan';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

echo '<div class="container">';

if (validasiUser()) {
    require(MYSQLI);
} else {
    showErrorPage(1);
}

echo '<div class="page-header"><h3><span class="glyphicon glyphicon-user"></span> Penyewa Kontrakan</h3></div>';

if ((!isset($_GET['id']) || !is_numeric($_GET['id']) || ($_GET['id'] < 1))) {
    showErrorPage(2);
}
$id = mysqli_real_escape_string($dbc, trim(strip_tags($_GET['id'])));

$q = "SELECT a.*, b.*, c.* FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b USING (kode_akun) INNER JOIN tbl_wilayah AS c USING (kode_wilayah) WHERE b.kode_pk = $id";
if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        mysqli_free_result($r);

        showProfilPenghuni($dbc, $row);
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