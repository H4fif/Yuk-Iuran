<?php  # Script index.php
// Script untuk halaman awal.

require('config.php');
require(FUNGSI);

// Set the page title and include the header.
$page_title .= 'Beranda';
include('includes/header.html');
$_SESSION['basename'] = basename(__FILE__, '.php');

?>

<div class="container">
  <div class="page-header">
    <h3><span class="glyphicon glyphicon-home"></span> Beranda</h3>
  </div>

<?php    
if (isset($_SESSION['kode_akun'], $_SESSION['hak_akses'], $_SESSION['agent']) && ($_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT']))) {
    require(MYSQLI);
    if (($_SESSION['hak_akses'] == 1) || ($_SESSION['hak_akses'] == 2)) {
        // Total data penyewa kontrakan
        $q_pk = "SELECT COUNT(*) FROM tbl_penyewa_kontrakan";
        if (!$r = @mysqli_query($dbc, $q_pk)) {
            showSqlError($dbc);
        }
        list($total_pk) = mysqli_fetch_array($r, MYSQLI_NUM);

        // Total data pembayaran sewa
        $q_pay = "SELECT COUNT(*) FROM tbl_pembayaran";
        if (!$r = @mysqli_query($dbc, $q_pay)) {
            showSqlError($dbc);
        }
        list($total_pay) = mysqli_fetch_array($r, MYSQLI_NUM);

        // Total data atribut pembayaran
        $q_ap = "SELECT COUNT(*) FROM tbl_atribut_pembayaran";
        if (!$r = @mysqli_query($dbc, $q_ap)) {
            showSqlError($dbc);
        }
        list($total_ap) = mysqli_fetch_array($r, MYSQLI_NUM);
    ?>

    <div class="row">
      <!-- Kartu data atribut pembayaran -->
      <div class="col-sm-4">
        <a href="data_ap.php">
          <div class="panel panel-default kartu" style="width: 250px; height: 175px;">
            <div class="panel-heading">
                Atribut Pembayaran
                <span class="badge"><?php echo $total_ap; ?></span>
            </div>
            <div class="panel-body text-center">
              <span class="fas fa-clipboard-list"></span>
            </div>
          </div>
        </a>
      </div>
  
      <!-- Kartu data pembayaran -->
      <div class="col-sm-4">
        <a href="data_pay.php">
          <div class="panel panel-default kartu" style="width: 250px; height: 175px;">
            <div class="panel-heading">
                Pembayaran
                <span class="badge"><?php echo $total_pay; ?></span>
            </div>
            <div class="panel-body text-center">
              <span class="fas fa-money-check-alt"></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Kartu data penyewa kontrakan -->
      <div class="col-sm-4">
        <a href="data_pk.php">
          <div class="panel panel-default kartu" style="width: 250px; height: 175px;">
            <div class="panel-heading">
              Penyewa Kontrakan
              <span class="badge"><?php echo $total_pk; ?></span>
            </div>
            <div class="panel-body text-center">
              <span class="fa fa-users"></span>
            </div>
          </div>
        </a>
      </div>
    </div>

<?php
    } elseif ($_SESSION['hak_akses'] == 3) {
        // Total data pembayaran sewa
        $q_pay = "SELECT COUNT(*) FROM tbl_pembayaran WHERE kode_pk = {$_SESSION['kode_pk']}";
        if (!$r = @mysqli_query($dbc, $q_pay)) {
            showSqlError($dbc);
        }
        list($total_pay) = mysqli_fetch_array($r, MYSQLI_NUM);

        // Total data atribut pembayaran
        $q_ap = "SELECT COUNT(*) FROM tbl_atribut_pembayaran";
        if (!$r = @mysqli_query($dbc, $q_ap)) {
            showSqlError($dbc);
        }
        list($total_ap) = mysqli_fetch_array($r, MYSQLI_NUM);
?>

    <div class="row">
      <!-- Kartu data pembayaran -->
      <div class="col-sm-4">
        <a href="data_pay.php">
          <div class="panel panel-default kartu" style="width: 250px; height: 175px;">
            <div class="panel-heading">
                Pembayaran
                <span class="badge"><?php echo $total_pay; ?></span>
            </div>
            <div class="panel-body text-center">
              <span class="fas fa-money-check-alt"></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Kartu data atribut pembayaran -->
      <div class="col-sm-4">
        <a href="data_ap.php">
          <div class="panel panel-default kartu" style="width: 250px; height: 175px;">
            <div class="panel-heading">
                Atribut Pembayaran
                <span class="badge"><?php echo $total_ap; ?></span>
            </div>
            <div class="panel-body text-center">
              <span class="fas fa-clipboard-list"></span>
            </div>
          </div>
        </a>
      </div>
    </div>

<?php
    }
} else {
    if (isset($_SESSION['basename']) && ($_SESSION['basename'] == 'logout')) {
        echo 'Anda telah keluar. ';
    } else {
        header('Location: login.php');
    }
} ?>

</div>

<?php
include('includes/footer.html');  // Include the footer.
?>