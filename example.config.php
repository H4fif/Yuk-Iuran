<?php  # Script config.php
// Script berisi pengaturan website, file paths, koneksi database, etc.

date_default_timezone_set('Asia/Jakarta');

define('MYSQLI', 'mysqli_connect.php');
define('FUNGSI', 'includes/functions.php');

// Persiapan koneksi database :
define('DB_USER', '');
define('DB_HOST', '127.0.0.1');
define('DB_PASS', '');
define('DB_NAME', '');

// Pengaturan Halaman :
define('MAKS_TAMPILAN', 50);
define('KELIPATAN', 25);

$page_title = 'Yuk Iuran - ';