<?php  # Script tambah_ap.php
// Script untuk tambah data atribut pembayaran

require('config.php');
require(FUNGSI);

$page_title .= 'Tambah Atribut Pembayaran';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

echo '<div class="container">';

if (!validasiUser() || !in_array($_SESSION['hak_akses'], [1, 2])) {
    showErrorPage();
}

echo '<div class="page-header">
      <h3><span class="glyphicon glyphicon-plus"></span><span class="fas fa-clipboard-list"></span> Tambah Atribut Pembayaran Baru</h3>
    </div>';

require(MYSQLI);
$val = [];

// Validasi form submission :
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $val = array_map('htmlentities', $_POST);
    $val = array_map('trim', $val);
    $errors = [];

    // Validasi nama atribut pembayaran:
    if (empty($val['nama'])){
        $errors[] = 'Nama atribut belum diisi';
    } elseif (strlen($val['nama']) > 50) {
        $errors[] = 'Nama atribut hanya 50 digit';
    } else {
        $nama = mysqli_real_escape_string($dbc, strtolower($val['nama']));
        $q = "SELECT nama FROM tbl_atribut_pembayaran WHERE nama = '$nama'";
        // Validasi query:
        if ($r = @mysqli_query($dbc, $q)) {
            // Validasi hasil query:
            if (mysqli_num_rows($r) > 0) {
                $errors[] = 'Nama atribut pembayaran tersebut sudah terdaftar!';
            } // Akhir IF (mysqli_num_rows($r)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
        mysqli_free_result($r);
    }  // Akhir validasi atribut pembayaran

    // Validasi harga:
    if (empty($val['harga'])){
        $errors[] = 'Harga tidak boleh kosong!';
    } elseif (!is_numeric($val['harga'])) { 
        $errors[] = 'Harga hanya boleh mengandung angka (0-9)';
    } elseif ($val['harga'] < 100) {
        $errors[] = 'Harga terlalu rendah! Minimal Rp. 100';
    }  // Akhir validasi harga

    // Validasi kesalahan input :
    if (empty($errors)) {
        $nama = mysqli_real_escape_string($dbc, $val['nama']);
        $harga = mysqli_real_escape_string($dbc, $val['harga']);
        
        $q = "INSERT INTO tbl_atribut_pembayaran (nama, harga) VALUES (LOWER('$nama'), $harga)";
        if ($r = @mysqli_query($dbc, $q)) {
            if (mysqli_affected_rows($dbc) == 1) {
                echo '<p class="alert alert-success">Data telah berhasil disimpan</p><br /><a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>';
            } else {
                showErrorPage();
            }  // Akhir IF (mysqli_affected_rows($dbc)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
    } else {
        showErrorInput($errors);
        showFormAddAP($dbc, $val);
    }  // Akhir validasi kesalahan input.
} else {
    showFormAddAP($dbc, $val);
}  // Akhir FORM SUBMISSION.

mysqli_close($dbc);
echo '</div><br>';
include('includes/footer.html');
?>