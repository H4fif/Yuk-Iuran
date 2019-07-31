<?php  # Script ubah_data_pk.php
/*
- Script untuk mengubah data penghuni.
- I.S. : data awal penghuni sebelum diubah.
- F.S. : data baru penghuni.

*/

/* SESSION STATUS RETURN VALUES : 
    0 -> PHP_SESSION_DISABLED  (not started)
    1 -> PHP_SESSION_NONE  (started, but has no value)
    2 -> PHP_SESSION_ACTIVE (started, one exist)
*/

require('config.php');
require(FUNGSI);

$page_title .= 'Ubah Data Penyewa';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser() || !in_array($_SESSION['hak_akses'], [1, 2])){
    showErrorPage();
}

echo '<div class="page-header"><h3><span class="fas fa-users"></span> <span class="glyphicon glyphicon-edit"></span> Ubah Data Penyewa</h3></div>';

// Validasi KODE PENGHUNI
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
  $id = $_GET['id'];   
} elseif (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) { 
  $id = $_POST['id'];
} else {  // Bila user tidak valid :
  showErrorPage();
}  // Akhir validasi KODE CUSTOMER.

require(MYSQLI);
$id = mysqli_real_escape_string($dbc, $id);
// Validasi kode customer dengan database :
$q = "SELECT * FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b ON b.kode_akun = a.kode_akun INNER JOIN tbl_wilayah AS c ON b.kode_wilayah = c.kode_wilayah WHERE kode_pk = $id";
if ($r = @mysqli_query($dbc, $q)) {
    if (mysqli_num_rows($r) == 1) {
        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
        $id = $data['kode_pk'];
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

    $errAlamat = 0;

    if (!empty($_FILES['foto']["tmp_name"])){
        // Validasi foto 
        $foto = addslashes(file_get_contents($_FILES["foto"]["tmp_name"]));

        if ($_FILES['foto']['size'] > 500000){
            $errors[] = "Ukuran foto terlalu besar, maksimal 500 KB";
        }

        $filename = explode('.', basename($_FILES['foto']['name']));
        if (!in_array(strtolower($filename[1]), ['jpg', 'jpeg', 'png'])){
            $errors[] = 'Format gambar error, hanya menerima gambar dengan format .jpg, .jpeg dan .png';
        }
    }

    // Validasi nomor ktp:
    if (empty($val['no_ktp'])){
        $errors[] = 'Nomor KTP belum diisi';
    } elseif (!is_numeric($val['no_ktp'])) { 
        $errors[] = 'Nomor KTP hanya boleh mengandung angka (0-9)';
    } elseif (strlen($val['no_ktp']) > 16) {
        $errors[] = 'Nomor KTP hanya 16 digit';
    } else {
        $no_ktp = mysqli_real_escape_string($dbc, strtolower($val['no_ktp']));
        $q = "SELECT no_ktp FROM tbl_penyewa_kontrakan WHERE no_ktp = '$no_ktp' AND kode_pk != $id";
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

    // Validasi nama lengkap:
    if (empty($val['nama'])){
        $errors[] = 'Nama lengkap tidak boleh kosong!';
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
    }  // Akhir validasi jenis kelamin

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
    }

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
        $nohp = mysqli_real_escape_string($dbc, $val['no_hp']);
        $q = "SELECT kode_pk FROM tbl_penyewa_kontrakan WHERE no_hp = '$nohp' AND kode_pk != $id";
        if ($r = @mysqli_query($dbc, $q)) {
            if (mysqli_num_rows($r) > 0) {
                $errors[] = 'Nomor hp sudah terdaftar!';
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

            $q = "SELECT kodepos, kode_wilayah FROM tbl_wilayah WHERE provinsi = '$prov' AND kabupaten = '$kab' AND kecamatan = '$kec' AND kelurahan = '$kel'";
            // Validasi query:
            if ($r = @mysqli_query($dbc, $q)) {
                // Validasi hasil query:
                if (mysqli_num_rows($r) == 1) {
                    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                    // Validasi input kodepos dengan database:
                    if ($row['kodepos'] != $val['kodepos']) {
                        $errors[] = 'Kodepos salah!';
                    } else {
                        $kode_wilayah = $row['kode_wilayah'];
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
        $email = mysqli_real_escape_string($dbc, strtolower($val['email']));
        $q = "SELECT email FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b USING(kode_akun) WHERE LOWER(email) = LOWER('$email') AND b.kode_pk != $id";
        // Validasi query:
        if ($r = @mysqli_query($dbc, $q)) {
            // Validasi hasil query:
            if (mysqli_num_rows($r) > 0) {
                $errors[] = 'Email tersebut sudah terdaftar!';
            } // Akhir IF (mysqli_num_rows($r)).
        } else {
            showSqlError($dbc);
        }  // Akhir IF ($r).
        mysqli_free_result($r);
    }  // Akhir validasi email.

    // Validasi kata sandi:
    if (!empty($val['kata_sandi_lama']) || !empty($val['kata_sandi_baru'])) {
        if (strlen($val['kata_sandi_baru']) < 4) {
            $errors[] = 'Kata sandi terlalu pendek! Minimal 4 karakter!';
        } elseif (strlen($val['kata_sandi_baru']) > 40) {
            $errors[] = 'Kata sandi terlalu panjang! Maksimal 40 karakter!';
        } elseif ($val['kata_sandi_baru'] != $val['kata_sandi2']) {
            $errors[] = 'Kata sandi tidak sama dengan konfirmasi kata sandi';
        } else {
            if ($_SESSION['hak_akses'] == 3){
                $kata_sandi_lama = mysqli_real_escape_string($dbc, $val['kata_sandi_lama']);
                $q = "SELECT kata_sandi FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b WHERE b.kode_pk = $id AND b.kode_pk IS NOT NULL AND kata_sandi = SHA1('$kata_sandi_lama')";

                // Validasi query:
                if ($r = @mysqli_query($dbc, $q)) {
                    // Validasi hasil query:
                    if (mysqli_num_rows($r) != 1) {
                        $errors[] = 'Kata sandi lama salah!';
                    }
                } else {
                    showSqlError($dbc);
                }
                mysqli_free_result($r);
            }
        }
    }  // Akhir validasi kata sandi.

    if (empty($errors)) {
        $nama = ucwords(strtolower(mysqli_real_escape_string($dbc, $val['nama'])));
        $tgl_lhr = mysqli_real_escape_string($dbc, $val['tanggal_lahir']);
        $jk = mysqli_real_escape_string($dbc, $val['jenis_kelamin']);
        $pekerjaan = mysqli_real_escape_string($dbc, $val['pekerjaan']);
        $id_kamar = mysqli_real_escape_string($dbc, $val['id_kamar']);
        $alamat = mysqli_real_escape_string($dbc, $val['alamat']);
        $kode_wilayah = mysqli_real_escape_string($dbc, $kode_wilayah);
        $kata_sandi_baru = mysqli_real_escape_string($dbc, $val['kata_sandi_baru']);
        $hak_akses = mysqli_real_escape_string($dbc, $val['hak_akses']);
        
        if (!empty($_FILES['foto']['tmp_name'])){
            $qfoto = "b.foto = '$foto',";
        } else {
            $qfoto = '';
        }
        
        if ($_SESSION['kode_akun'] == $data['kode_akun']){
            if ($_SESSION['hak_akses'] != $hak_akses){
                $_SESSION['hak_akses'];
            }
        }
        
        $q = "UPDATE tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b USING (kode_akun) SET $qfoto b.nama = LOWER('$nama'), b.alamat = LOWER('$alamat'), b.kode_wilayah = $kode_wilayah, tanggal_lahir = '$tgl_lhr', jenis_kelamin = UPPER('$jk'), pekerjaan = LOWER('$pekerjaan'), id_kamar = '$id_kamar', hak_akses = $hak_akses";
        // Validasi email :
        if (strtolower($data['email']) !== $email) {
            $q .= ", email = '$email'";
        }
        
        if (!empty($kata_sandi_baru)){
            if ($data['kata_sandi'] !== $kata_sandi_baru){
                $q .= ", kata_sandi = SHA1('$kata_sandi_baru')";
            }
          }

        if ($data['no_hp'] !== $nohp) {
            $q .= ", no_hp = '$nohp'";
        }

        $q .= " WHERE kode_pk = $id";
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
        echo '<a class="btn btn-default" href="data_pk.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>';
    } else {
        showErrorInput($errors);
        showUbahDataPK($dbc, $id, $data, $val);
    }
} else {
    showUbahDataPK($dbc, $id, $data, $val);
}

echo '</div><br>';
include('includes/footer.html');
?>