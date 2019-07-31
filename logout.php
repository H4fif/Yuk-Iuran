<?php  # Script logout.php
// Script untuk keluar.

require('config.php');
require(FUNGSI);
session_start();

if (isset($_SESSION['kode_akun'], $_SESSION['agent']) && (md5($_SERVER['HTTP_USER_AGENT']) == $_SESSION['agent'])) {
    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time()-3600);
//} else {
//    showErrorPage();
}

session_start();
$_SESSION['basename'] = basename(__FILE__, '.php');

header('Location: index.php');
?>