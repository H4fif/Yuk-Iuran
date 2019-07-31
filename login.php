<?php  # Script login.php
// Script untuk masuk.

require('config.php');
require(FUNGSI);

$page_title .= 'Masuk';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

echo '<div class="container">';

// Validate if the user has logged in, redirect to user's homepage

// var_dump(validasiUser());  exit;
if (validasiUser()) {
    header('Location: index.php');
    exit;
} else {
    echo '<div class="page-header">
        <h3><span class="glyphicon glyphicon-log-in"></span> Masuk</h3>
      </div>';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];
        $val = array_map('strip_tags', $_POST);
        $val = array_map('trim', $val);

        if (!empty($val)) {

            // Validate email:
            if (!filter_var($val['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Mohon isi email Anda sesuai format. Contoh: contoh@email.com';
            }  // End email validation.

            // Validate kata_sandi:
            if (empty($val['kata_sandi']) || (strlen($val['kata_sandi'] < 4)) || strlen($val['kata_sandi'] > 40)) {
                $errors[] = 'Kata sandi minimal terdiri dari 4 karakter';
            }  // End kata_sandi validation.

            require(MYSQLI);

            $e = mysqli_real_escape_string($dbc, $val['email']);
            $ks = mysqli_real_escape_string($dbc, $val['kata_sandi']);

            $q = "SELECT * FROM tbl_akun LEFT JOIN tbl_penyewa_kontrakan USING (kode_akun) WHERE email = '$e' AND kata_sandi = SHA1('$ks')";
            if ($r = @mysqli_query($dbc, $q)) {
                if (mysqli_num_rows($r) == 1) {
                    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                    $_SESSION['kode_akun'] = $row['kode_akun'];
                    $_SESSION['hak_akses'] = $row['hak_akses'];
                    $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
                    if (!is_null($row['kode_pk'])) {
                        $_SESSION['kode_pk'] = $row['kode_pk'];
                    }

                    if (!empty($row['nama'])){
                        $nama = $row['nama'];
                        $pos = strpos($nama, chr(32));
                        if (empty($pos)){
                            $pos = strlen($nama);
                        }
                        $new_nama = substr($nama, 0, $pos);
                        $_SESSION['nama'] = $new_nama;
                    }

                    echo '<p class="alert alert-success">Login berhasil!</p><p>Bila browser Anda tidak mendukung fitur peralihan otomatis, maka klik link berikut <a href="index.php">Homepage</a></p>';
                    
                    header('Location: index.php');  // Change this to user's homepage based on their hak_akses

                    while (ob_get_level > 0) {
                        ob_end_flush();
                    }  // End WHILE LOOP.

                    exit();
                } else {
                    $errors = [];
                    $errors[] = 'Periksa kembali email dan kata sandi Anda';
                    showErrorInput($errors);
                }  // End IF (mysqli_num_rows($r)).
            } else {
                showSqlError($dbc);
            }  // End IF ($r).
            mysqli_free_result($r);
            mysqli_close($dbc);
        }  // End IF (!empty($val)).

    } else {
        if (!empty($errors)) {
            showErrorInput($errors);
        }
    }  // End FORM SUBMISSION.
    (isset($val) ? $val : $val = []);
    showLoginForm($val);
}  // End login validation.

echo '</div>';
include('includes/footer.html');
?>