<?php  # Script registrasi_penghuni.php
// Script untuk registrasi penghuni kontrakan.

require('config.php');
require(FUNGSI);

$page_title .= 'Tambah Penyewa Baru';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

echo '<div class="container">';

if (!validasiUser() || !in_array($_SESSION['hak_akses'], [1, 2])) {
    showErrorPage();
}

echo '<div class="page-header">
      <h3><span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span> Tambah Penyewa Baru</h3>
    </div>';

require(MYSQLI);
$val = [];

// Validasi form submission :
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $val = array_map('htmlentities', $_POST);
    $val = array_map('trim', $val);

    $errors = [];
    $errAlamat = 0;

    // Validasi nomor ktp:
    if (empty($val['no_ktp'])){
        $errors[] = 'Nomor KTP belum diisi';
    } elseif (!is_numeric($val['no_ktp'])) { 
        $errors[] = 'Nomor KTP hanya boleh mengandung angka (0-9)';
    } elseif (strlen($val['no_ktp']) != 16) {
        $errors[] = 'Nomor KTP hanya 16 digit';
    } else {
        $no_ktp = mysqli_real_escape_string($dbc, strtolower($val['no_ktp']));
        $q = "SELECT no_ktp FROM tbl_penyewa_kontrakan WHERE no_ktp = '$no_ktp'";
        // Validasi query:
        if ($r = @mysqli_query($dbc, $q)) {
            // Validasi hasil query:
            if (mysqli_num_rows($r) > 0) {
                $errors[] = 'Nomor KTP sudah terdaftar!';
            } // Akhir IF (mysqli_num_rows($r)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
        mysqli_free_result($r);
    }  // Akhir validasi nomor ktp

    // Validasi foto 
    $foto = addslashes(file_get_contents($_FILES["foto"]["tmp_name"]));

    if ($_FILES['foto']['size'] > 500000){
        $errors[] = "Ukuran foto terlalu besar, maksimal 500 KB";
    }

    $filename = explode('.', basename($_FILES['foto']['name']));
    if (!in_array(strtolower($filename[1]), ['jpg', 'jpeg', 'png'])){
        $errors[] = 'Format gambar error, hanya menerima gambar dengan format .jpg, .jpeg dan .png';
    }

    // Validasi nama lengkap:
    if (empty($val['nama'])){
        $errors[] = 'Nama tidak boleh kosong!';
    } elseif (!preg_match('/[a-zA-z\s]/', $val['nama'])) { 
        $errors[] = 'Nama hanya boleh mengandung huruf!';
    } elseif (strlen($val['nama']) < 3) {
        $errors[] = 'Nama terlalu pendek! Minimal 3 karakter!';
    } elseif (strlen($val['nama']) > 150) {
        $errors[] = 'Nama terlalu panjang! Maksimal 150 karakter!';
    }  // Akhir validasi nama lengkap.

    // Validasi tanggal lahir:
    if (empty($val['tanggal_lahir'])) {
        $errors[] = 'Tanggal lahir belum diisi';
    } else {
        $tahun_sekarang = date('Y');
        $minimal = ($tahun_sekarang - 17) . '-12-30';
        if (($val['tanggal_lahir'] > $minimal)) {
            $errors[] = 'Usia minimal 17 tahun';
        }
    }  // Akhir validasi tanggal lahir

    // Validasi jenis kelamin
    if (empty($val['jenis_kelamin'])){
        $errors[] = 'Jenis kelamin belum dipilih';
    } elseif (!in_array(strtolower($val['jenis_kelamin']), ['l', 'p'])){
        $errors[] = 'Jenis kelamin tidak valid';
    }  // Akhir validasi jeni kelamin

    // Validasi pekerjaan:
    if (empty($val['pekerjaan'])){
        $errors[] = 'Pekerjaan belum diisi';
    } elseif (!preg_match('/[a-zA-z\s]/', $val['pekerjaan'])) { 
        $errors[] = 'Pekerjaan hanya boleh mengandung huruf!';
    } elseif (strlen($val['pekerjaan']) < 4) {
        $errors[] = 'Pekerjaan terlalu pendek! Minimal 4 karakter!';
    } elseif (strlen($val['pekerjaan']) > 20) {
        $errors[] = 'Pekerjaan terlalu panjang! Maksimal 20 karakter!';
    }  // Akhir validasi pekerjaan

    // Validasi ID kamar:
    if (empty($val['id_kamar'])){
        $errors[] = 'ID Kamar belum diisi';
    } elseif (strlen($val['id_kamar']) != 2){
        $errors[] = 'ID Kamar tidak valid';
    }  // Akhir validasi ID kamar

    // Validasi nomor hp:
    if (empty($val['no_hp'])) {
        $errors[] = 'Nomor HP tidak boleh kosong!'; 
    } elseif (!preg_match('/(\d)/', $val['no_hp'])) {
        $errors[] = 'Nomor HP hanya boleh mengandung angka (0-9)!';
    } elseif (strlen($val['no_hp']) < 3) {
        $errors[] = 'Nomor HP terlalu pendek! Minimal 3 karaker!';
    } elseif (strlen($val['no_hp']) > 20) {
        $errors[] = 'Nomor HP terlalu panjang! Maksimal 20 karakter!';
    } else {
        $no_hp = mysqli_real_escape_string($dbc, $val['no_hp']);
        $q = "SELECT kode_pk FROM tbl_penyewa_kontrakan WHERE no_hp = '$no_hp'";
        if ($r = @mysqli_query($dbc, $q)) {
            if (mysqli_num_rows($r) == 1) {
                $errors[] = 'Nomor HP sudah terdaftar!';
            }
        } else {
            showSqlError($dbc);
        }
    }  // Akhir validasi nomor hp.

    // Validasi provinsi:
    if (empty($val['provinsi'])) {
        $errors[] = 'Anda belum memilih provinsi!';
        $errAlamat++;
    }

    // Validasi kabupaten / kota:
    if (empty($val['kabupaten'])) {
        $errors[] = 'Anda belum memilih kabupaten / kota!';
        $errAlamat++;
    }

    // Validasi kecamatan:
    if (empty($val['kecamatan'])) {
        $errors[] = 'Anda belum memilih kecamatan!';
        $errAlamat++;
    }

    // Validasi kelurahan:
    if (empty($val['kelurahan'])) {
        $errors[] = 'Anda belum memilih kelurahan!';
        $errAlamat++;
    }

    // Validasi kodepos:
    if (empty($val['kodepos'])) {
        $errors[] = 'Kodepos tidak boleh kosong!';
    } elseif (!preg_match('/(\d)/', $val['kodepos'])) {
        $errors[] = 'Kodepos hanya boleh mengandung angka!';
    } elseif (strlen($val['kodepos']) < 5) {
        $errors[] = 'Kodepos terlalu pendek! Minimal 5 karakter!';
    } elseif (strlen($val['kodepos']) > 5) {
        $errors[] = 'Kodepos terlalu panjang! Maksimal 5 karakter!';
    } else {
        // Validasi error pada input alamat:
        if (empty($errAlamat)) {  // Bila alamat yang dimasukkan benar
            $prov = mysqli_real_escape_string($dbc, $val['provinsi']);
            $kab = mysqli_real_escape_string($dbc, $val['kabupaten']);
            $kec = mysqli_real_escape_string($dbc, $val['kecamatan']);
            $kel = mysqli_real_escape_string($dbc, $val['kelurahan']);

            $q = "SELECT kodepos FROM tbl_wilayah WHERE provinsi = '$prov' AND kabupaten = '$kab' AND kecamatan = '$kec' AND kelurahan = '$kel'";
            // Validasi query:
            if ($r = @mysqli_query($dbc, $q)) {
                // Validasi hasil query:
                if (mysqli_num_rows($r) == 1) {
                    $row = mysqli_fetch_array($r, MYSQLI_NUM);
                    // Validasi input kodepos dengan database:
                    if ($row[0] != $val['kodepos']) {
                        $errors[] = 'Kodepos salah!';
                    }  // Akhir validasi input kodepos dengan database.
                } else {
                    $errors[] = 'Maaf, terjadi kesalahan pada sistem, silakan coba lagi.';
                }  // Akhir validasi hasil query.
            }  // Akhir validasi query.
        }  // Akhir validasi error pada input alamat.
    }  // Akhir validasi kodepos.

    // Validasi alamat:
    if (empty($val['alamat'])) {
        $errors[] = 'Alamat tidak boleh kosong!';
    } elseif (strlen($val['alamat']) < 5) {
        $errors[] = 'Alamat terlalu pendek! Minimal 5 karakter!';
    } elseif (strlen($val['alamat']) > 255) {
        $errors[] = 'Alamat terlalu panjang! Maksimal 255 karakter!';
    }  // Akhir validasi alamat.

    // Validasi email:
    if (empty($val['email'])) {
        $errors[] = 'Email tidak boleh kosong!';
    } elseif (!filter_var($val['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email salah! Contoh: akun@email.com';
    } elseif (strlen($val['email']) < 5) {
        $errors[] = 'Email terlalu pendek! Minimal 5 karakter!';
    } elseif (strlen($val['email']) > 150) {
        $errors[] = 'Email terlalu panjang! Maksimal 150 karakter!';
    } else {
        $e = mysqli_real_escape_string($dbc, strtolower($val['email']));
        $q = "SELECT email FROM tbl_akun WHERE email = '$e'";
        // Validasi query:
        if ($r = @mysqli_query($dbc, $q)) {
            // Validasi hasil query:
            if (mysqli_num_rows($r) > 0) {
                $errors[] = 'Email sudah terdaftar!';
            } // Akhir IF (mysqli_num_rows($r)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
        mysqli_free_result($r);
    }  // Akhir validasi email.

    // Validasi kata sandi:
    if (empty($val['kata_sandi'])) {
        $errors[] = 'Kata sandi tidak boleh kosong!';
    } elseif (strlen($val['kata_sandi']) < 4) {
        $errors[] = 'Kata sandi terlalu pendek! Minimal 4 karakter!';
    } elseif (strlen($val['kata_sandi']) > 40) {
        $errors[] = 'Kata sandi terlalu panjang! Maksimal 40 karakter!';
    } elseif ($val['kata_sandi'] != $val['kata_sandi2']){
        $errors[] = 'Kata sandi tidak sama dengan konfirmasi kata sandi!';
    }  // Akhir validasi kata sandi.

    // Validasi kesalahan input :
    if (empty($errors)) {
        // SIMPAN DATA PENDAFTARAN KE DATABASE :
        $nama = mysqli_real_escape_string($dbc, $val['nama']);
        $tgl_lhr = mysqli_real_escape_string($dbc, $val['tanggal_lahir']);
        $jk = mysqli_real_escape_string($dbc, $val['jenis_kelamin']);
        $pekerjaan = mysqli_real_escape_string($dbc, $val['pekerjaan']);
        $id_kamar = mysqli_real_escape_string($dbc, $val['id_kamar']);
        $kelurahan = mysqli_real_escape_string($dbc, $val['kelurahan']);
        $kodepos = mysqli_real_escape_string($dbc, $val['kodepos']);
        $alamat = mysqli_real_escape_string($dbc, $val['alamat']);
        $kata_sandi = mysqli_real_escape_string($dbc, $val['kata_sandi']);

        $simpan_akun = false;
        // SIMPAN AKUN :
        $q = "INSERT INTO tbl_akun (email, kata_sandi, hak_akses) VALUES (LOWER('$e'), SHA1('$kata_sandi'), 3)";
        if ($r = @mysqli_query($dbc, $q)) {
            if (mysqli_affected_rows($dbc) == 1) {
                $q = "SELECT LAST_INSERT_ID()";
                if ($r = mysqli_query($dbc, $q)) {
                    if (mysqli_num_rows($r) == 1) {
                        $simpan_akun = true;
                        $row = mysqli_fetch_array($r, MYSQLI_NUM);
                        $kode_akun = $row[0];
                    } else {
                        $errors[] = 'Maaf, terjadi kesalahan pada sistem, silakan coba lagi';
                    }  // Akhir IF (mysqli_num_rows($r)).
                } else {
                    showSqlError($dbc);
                }  // Akhir IF ($r).
            } else {
                $errors[] = 'Maaf, terjadi kesalahan pada sistem, silakan coba lagi.';
            }  // Akhir IF (mysqli_affected_rows($dbc)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).

        if (!empty($errors)) {
            showErrorInput($errors);
            showRegistrationForm($dbc, $val);
        } else {
            // SIMPAN DATA PENGHUNI :
            $q = "INSERT INTO tbl_penyewa_kontrakan (foto, nama, no_hp, kode_wilayah, alamat, kode_akun, no_ktp, tanggal_lahir, jenis_kelamin, pekerjaan, id_kamar) VALUES ('$foto', LOWER('$nama'), '$no_hp', (SELECT kode_wilayah FROM tbl_wilayah WHERE kodepos = '$kodepos' AND kelurahan = '$kelurahan'), LOWER('$alamat'), $kode_akun, '$no_ktp', '$tgl_lhr', UPPER('$jk'), LOWER('$pekerjaan'), '$id_kamar')";
            if ($r = @mysqli_query($dbc, $q)) {
                if (mysqli_affected_rows($dbc) == 1) {
                    echo '<p class="alert alert-success">Data telah berhasil disimpan</p><br /><a class="btn btn-default" href="data_pk.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>';
                } else {
                    $errors[] = 'Maaf, terjadi kesalahan pada sistem, silakan coba lagi.';
                }  // Akhir IF (mysqli_affected_rows($dbc)).
            } else {
                showSqlError($dbc);
            }  // Akhir IF ($r).
            
            if (!empty($errors)) {
                showErrorInput($errors);
                showRegistrationForm($dbc, $val);
            }
        }
    } else {
        showErrorInput($errors);
        showRegistrationForm($dbc, $val);
    }  // Akhir validasi kesalahan input.
} else {
    showRegistrationForm($dbc, $val);
}  // Akhir FORM SUBMISSION.

mysqli_close($dbc);
echo '</div><br>';
include('includes/footer.html');
?>