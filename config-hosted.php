<?php  # Script config.php
// Script berisi pengaturan website, file paths, koneksi database, etc.

date_default_timezone_set('Asia/Jakarta');

define('MYSQLI', 'mysqli_connect.php');
define('FUNGSI', 'includes/functions.php');

// Persiapan koneksi database :
define('DB_USER', 'epiz_23903826');
define('DB_HOST', 'sql100.epizy.com');
define('DB_PASS', 'QACZxnbgm');
define('DB_NAME', 'epiz_23903826_yuk_iuran');

// Pengaturan Halaman :
define('MAKS_TAMPILAN', 50);
define('KELIPATAN', 25);

$page_title = 'Yuk Iuran - ';