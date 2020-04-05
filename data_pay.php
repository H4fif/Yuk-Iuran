<?php  # Script data_pay.php
/* 
  - Script untuk menampilkan semua data pembayaran oleh penyewa kontrakan.
  - I.S. : -
  - F.S. : Menampilkan data pembayaran penyewa kontrakan
 */

// 2 options available for searching :
$options = ['Nama Penyewa', 'Tanggal'];
sort($options);
 
require('config.php');
require(FUNGSI);

$page_title .= 'Pembayaran';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser()) {
    showErrorPage();
}

// Validasi halaman :
if (isset($_GET['display'])) {
    $display = validasiHalaman(MAKS_TAMPILAN, $_GET['display']);
} else {
    $display = KELIPATAN;
}

require(MYSQLI);

if (isset($_GET['start'])) {
    $start = validasiRid(KELIPATAN, $_GET['start'], 'tbl_pembayaran', $dbc);
} else {
    $start = 0;
}

$colUrutDef = $urutDef = null;
$urut = 'ASC';
$colUrut = 'nama';
// Validasi pengurutan :
if (isset($_GET['urut'], $_GET['colUrut']) && in_array(filter_var($_GET['urut'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 2))), range(1, 2)) && in_array(filter_var($_GET['colUrut'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 2))), range(1, 2))){
    
    if ($_GET['urut'] == 2){
        $urut = 'DESC';
    }

    switch ($_GET['colUrut']) {
        case 1 :
            $colUrut = 'nama';
            break;
        case 2 :
            $colUrut = 'tanggal_bayar';
            break;
        default: showErrorPage();
    }  // Akhir SWITCH ($_GET['colUrut']).

    $colUrutDef = $_GET['colUrut'];
    $urutDef = $_GET['urut'];
}

// Validasi pencarian :
if (isset($_GET['colCari'], $_GET['keywordCari']) && ($_GET['colCari'] > 0) && ($_GET['colCari'] <= count($options)) && (strlen(trim($_GET['keywordCari'])) >= 1)) {
    $column = trim(htmlentities(strip_tags(urldecode($_GET['colCari']))));
    $keyword = trim(htmlentities(strip_tags(urldecode($_GET['keywordCari']))));
    $colCari = $column;
    $keywordCari = $keyword;
} else {
    $column = $keyword = false;
    $colCari = $keywordCari = null;
}

// Validasi pencarian kolom untuk query database :
if (($column !== false) && $keyword) {
    switch ($column) {
      case 1 :
          $colKunci = 'nama';
          break;
      case 2 :
          $colKunci = 'tanggal_bayar';
          break;
        default: showErrorPage();
    }  // Akhir SWITCH ($column).
    
    $keyword = mysqli_real_escape_string($dbc, $keyword);
    $queryCari = "WHERE LOWER($colKunci) LIKE LOWER('%$keyword%')";
    $search = true;
} else {
    $queryCari = '';
    $search = false;
}  // Akhir validasi query pencarian.

echo '<div class="page-header"><h3><span class="fas fa-money-check-alt"></span> Pembayaran</h3></div>';

$q_user = '';
if ($_SESSION['hak_akses'] == 3){
    if (!empty($queryCari)){
        $q_user = "AND";
    } else {
        $q_user = "WHERE";
    }
    $q_user .= " kode_akun = {$_SESSION['kode_akun']}";
}

$q = "SELECT COUNT(*) FROM tbl_pembayaran INNER JOIN tbl_penyewa_kontrakan USING (kode_pk) $q_user";

// Validasi hasil eksekusi query :
if ($r = @mysqli_query($dbc, $q)) {
    list($total_row) = mysqli_fetch_array($r, MYSQLI_NUM);
    mysqli_free_result($r);
    if ($total_row == 0) {
        echo '
        <div class="alert alert-warning">Tidak ada data</div>';
    } else {
        if ($total_row > 1) { formPencarian('data_pay.php', $options, $colUrutDef, $urutDef); }

        $q = "SELECT COUNT(*) FROM tbl_pembayaran INNER JOIN tbl_penyewa_kontrakan AS pk USING (kode_pk) $queryCari $q_user";
        if ($r = @mysqli_query($dbc, $q)) {
            list($total_row) = mysqli_fetch_array($r, MYSQLI_NUM);
        } else {  
            showSqlError($dbc);
        }
        mysqli_free_result($r);
        
        if ($total_row == 0) {
            echo '<div class="alert alert-warning">Tidak ada hasil</div>';
        } else {
            // Tampilkan semua data.
            $q = "SELECT * FROM tbl_pembayaran AS pay INNER JOIN tbl_penyewa_kontrakan AS pk USING (kode_pk) $queryCari $q_user ORDER BY $colUrut $urut LIMIT $start, $display";
        
            if ($r = @mysqli_query($dbc, $q)) {  // Eksekusi query :
                // Validasi jumlah data dari hasil pencarian
                if (mysqli_num_rows($r) == 0) {
                    echo '<div class="alert alert-warning">Tidak ada hasil</div>';
                } else {
                    // Table header :
                    echo '<div class="table-responsive">
                    <table class="table-customer table table-hover table-bordered">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-center">Nama Penyewa</th>
                          <th class="text-center">Tanggal Bayar</th>
                          <th class="text-center">Rincian</th>
                          ';
                    
                    if ($_SESSION['hak_akses'] != 3){
                        echo '<th class="text-center">Hapus</th>';
                    }

                    echo '</tr>
                      </thead>';

                    $no = $start + 1;
                    // Tampilkan semua record :
                    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                        echo '<tr>
                          <td class="text-center">' . $no . '</td>
                          <td>' . ucwords(strtolower($row['nama'])) . '</td>
                          <td class="text-center">' . kalender_indo($row['tanggal_bayar']) . '</td>';
                          $q = "SELECT * FROM tbl_rincian_pembayaran INNER JOIN tbl_atribut_pembayaran USING (kode_ap) WHERE kode_ps = {$row['kode_ps']}";
                          
                          $r2 = @mysqli_query($dbc, $q);
                          $data_ap = [];
                          while ($row2 = mysqli_fetch_array($r2, MYSQLI_ASSOC)){
                              $data_ap[] = ucwords(strtolower($row2['nama']));
                          }

                          echo '<td>' . implode(', ', $data_ap) . '</td>';
                        
                        if ($_SESSION['hak_akses'] != 3){
                            echo '<td class="text-center"><a href="hapus_pay.php?id=' . $row['kode_ps'] . '" class="btn"><big><span class="glyphicon glyphicon-trash"></span></big></a></td>';
                        }
                        
                        echo '</tr>';
                        $no++;
                    }  // Akhir WHILE  
                    echo '</tbody>
                        <tfoot>
                          <tr>
                            <td colspan="11">Total : ' . $total_row . ' data</td>
                          </tr>
                        </tfoot>
                      </table></div>';
    
                    if ($total_row > $display) {
                        $page = ceil($total_row / $display);
                    } else {
                        $page = 1;
                    }

                    // tampilkan nomor halaman disini !!!
                    if ($page > 1) {
                        nomorHalaman($start, $display, $page);
                    }
                }  // Akhir validasi jumlah data dari hasil pencarian.
            } else {
                showSqlError($dbc);
            }
        }
    }  // Akhir validasi jumlah data.*/
    
    if ($total_row > 1){
        form_pengurutan($options, null, $colCari, $keywordCari);
    }

    if ($search) {
        echo '<br><p><a class="btn btn-default" href="data_pay.php">Tampilkan Semua</a></p><br>';
    }

    if ($_SESSION['hak_akses'] != 3) {
        echo '<br><p><a class="btn btn-default" href="tambah_pay.php"><small><span class="glyphicon glyphicon-plus"></span> <span class="fas fa-money-check-alt"></span></small> Tambahkan Pembayaran Baru</a></p>';
    }
} else {
    showSqlError($dbc);
}  // Akhir validasi hasil eksekusi query.

echo '</div><br>';
include('includes/footer.html');
?>