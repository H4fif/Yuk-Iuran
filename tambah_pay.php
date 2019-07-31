<?php  # Script tambah_pay.php
// Script untuk tambah data pembayaran

require('config.php');
require(FUNGSI);

$page_title .= 'Pembayaran';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

echo '<div class="container">';

if (!validasiUser() || ($_SESSION['hak_akses'] == 3)) {
    showErrorPage();
}

echo '<div class="page-header">
      <h3><span class="glyphicon glyphicon-plus"></span><span class="fas fa-money-check-alt"></span> Tambah Pembayaran Baru</h3>
    </div>';

require(MYSQLI);
$val = [];

// Validasi form submission :
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $val['kode_ap'] = array_map('htmlentities', $_POST['kode_ap']);
    $val['kode_ap'] = array_map('trim', $val['kode_ap']);
    $val['kode_pk'] = trim(strip_tags($_POST['kode_pk']));
    $errors = [];

    // Validasi kode penyewa kontrakan :
    if (empty($val['kode_pk'])){
        $errors[] = 'Penyewa belum dipilih';
    } else {
        $kode_pk = mysqli_real_escape_string($dbc, $val['kode_pk']);
        $q = "SELECT kode_pk FROM tbl_penyewa_kontrakan WHERE kode_pk = '$kode_pk'";
        // Validasi query:
        if ($r = @mysqli_query($dbc, $q)) {
            // Validasi hasil query:
            if (mysqli_num_rows($r) == 0) {
                $errors[] = 'Penyewa tersebut tidak terdaftar';
            } // Akhir IF (mysqli_num_rows($r)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
        mysqli_free_result($r);
    }  // Akhir validasi kode pk

    // var_dump($val['kode_ap']);  exit;


    // Validasi atribut pembayaran
    if (empty(count($val['kode_ap']))){
        $errors[] = 'Atribut pembayaran belum dipilih';
    } else {
        $kode_ap = implode(',', $val['kode_ap']);
        $q = "SELECT kode_ap FROM tbl_atribut_pembayaran WHERE kode_ap IN ($kode_ap)";
        // Validasi query:
        if ($r = @mysqli_query($dbc, $q)) {
            // Validasi hasil query:
            if (mysqli_num_rows($r) == 0) {
                $errors[] = 'Atribut pembayaran tersebut tidak valid';
            } // Akhir IF (mysqli_num_rows($r)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
        mysqli_free_result($r);
    }  // Akhir validasi atribut pembayaran


    // Validasi kesalahan input :
    if (empty($errors)) {
        // SIMPAN DATA PEMBAYARAN KE DATABASE :
        $kode_pk = mysqli_real_escape_string($dbc, $val['kode_pk']);

        $q = "INSERT INTO tbl_pembayaran (kode_pk, tanggal_bayar) VALUES ($kode_pk, NOW())";
        if ($r = @mysqli_query($dbc, $q)) {
            if (mysqli_affected_rows($dbc) == 1) {
                $id_pay = mysqli_insert_id($dbc);
                
                $q = "INSERT INTO tbl_rincian_pembayaran (kode_ps, kode_ap) VALUES";

                foreach ($val['kode_ap'] as $v){
                    $q .= "($id_pay, $v),";
                }

                $q = rtrim($q, ',');

                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) >= 1){
                    echo '<p class="alert alert-success">Data telah berhasil disimpan</p>';
                } else {
                    echo '<p class="alert alert-danger">Data gagal disimpan</p>';
                }
                echo '<br /><a class="btn btn-default" href="data_pay.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>';
            } else {
                showErrorPage();
            }  // Akhir IF (mysqli_affected_rows($dbc)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
    } else {
        showErrorInput($errors);
        FormPembayaran($dbc, $val);
    }  // Akhir validasi kesalahan input.
} else {
    FormPembayaran($dbc, $val);
}  // Akhir FORM SUBMISSION.

mysqli_close($dbc);
echo '</div><br>';
include('includes/footer.html');
?>