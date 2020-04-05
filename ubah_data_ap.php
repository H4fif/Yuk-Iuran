<?php  # Script ubah_data_ap.php
/*
- Script untuk mengubah data penghuni.
- I.S. : data awal atribut pembayaran sebelum diubah.
- F.S. : data baru atribut pembayaran.

*/

require('config.php');
require(FUNGSI);

$page_title .= 'Ubah Data Atribut Pembayaran';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser() || !in_array($_SESSION['hak_akses'], [1, 2])){
    showErrorPage();
}

echo '<div class="page-header"><h3><span class="fas fa-clipboard"></span> <span class="glyphicon glyphicon-edit"></span> Ubah Data Atribut Pembayaran</h3></div>';

// Validasi KODE ATRIBUT PEMBAYARAN
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
  $id = $_GET['id'];   
} elseif (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) { 
  $id = $_POST['id'];
} else {  // Bila user tidak valid :
  $id = null;
  showErrorPage();
}  // Akhir validasi KODE ATRIBUT PEMBAYARAN

require(MYSQLI);
$id = mysqli_real_escape_string($dbc, $id);
// Validasi kode customer dengan database :
$q = "SELECT * FROM tbl_atribut_pembayaran WHERE kode_ap = $id";
if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
        $id = $data['kode_ap'];
    } else {
        showErrorPage();
    }
} else {
    showSqlError($dbc);
}
mysqli_free_result($r);

$val = [];
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
        $q = "SELECT nama FROM tbl_atribut_pembayaran WHERE (nama = '$nama') AND (kode_ap != $id)";
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
    }  // Akhir validasi nama atribut pembayaran

    // Validasi harga:
    if (empty($val['harga'])){
        $errors[] = 'Harga tidak boleh kosong!';
    } elseif (!is_numeric($val['harga'])) { 
        $errors[] = 'Harga hanya boleh mengandung angka (0-9)';
    } elseif ($val['harga'] < 100) {
        $errors[] = 'Harga per bulan terlalu rendah! Minimal Rp. 100';
    }  // Akhir validasi harga

    if (empty($errors)) {
        $nama = ucwords(strtolower(mysqli_real_escape_string($dbc, $val['nama'])));
        $harga = mysqli_real_escape_string($dbc, $val['harga']);
        
        $q = "UPDATE tbl_atribut_pembayaran SET nama = LOWER('$nama'), harga = $harga WHERE kode_ap = $id";
        if ($r = @mysqli_query($dbc, $q)) {
            if (mysqli_affected_rows($dbc) == 1) {
                echo '<div class="alert alert-success">
                        <p>Perubahan berhasil disimpan</p>';
            } else {
                echo '<div class="alert alert-warning">
                        <p>Tidak ada perubahan yang disimpan</p>';
            }
            echo '</div>';
        } else {
            showSqlError($dbc);
        }
        echo '<a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>';
    } else {
        showErrorInput($errors);
        showUbahAP($id, $data, $val);
    }
} else {
    showUbahAP($id, $data, $val);
}

echo '</div><br>';
include('includes/footer.html');
?>