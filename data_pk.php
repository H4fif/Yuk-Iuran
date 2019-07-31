<?php  # Script data_pk.php
/* 
  - Script untuk menampilkan semua data penyewa kontrakan.
  - I.S. : -
  - F.S. : Menampilkan data penyewa kontrakan
 */

// 4 options available for searching :
$options = ['Nama', 'Nomor HP', 'ID Kamar', 'Jenis User'];
sort($options);
 
require('config.php');
require(FUNGSI);

$page_title .= 'Penyewa Kontrakan';
include('includes/header.html');
echo '<div class="container">';

// Validasi user :
if (!validasiUser()){
    showErrorPage();
}

if (!in_array($_SESSION['hak_akses'], [1, 2])){
    header('Location: index.php');
    exit;
}

// Validasi halaman :
if (isset($_GET['display'])) {
    $display = validasiHalaman(MAKS_TAMPILAN, $_GET['display']);
} else {
    $display = KELIPATAN;
}

require(MYSQLI);

if (isset($_GET['start'])) {
    $start = validasiRid(KELIPATAN, $_GET['start'], 'tbl_penyewa_kontrakan', $dbc);
} else {
    $start = 0;
}

$colUrutDef = $urutDef = null;
$urut = 'ASC';
$colUrut = 'nama';
// Validasi pengurutan :
if (isset($_GET['urut'], $_GET['colUrut']) && in_array(filter_var($_GET['urut'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 2))), [1, 2]) && in_array(filter_var($_GET['colUrut'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 4))), range(1, 4))){
    
    if ($_GET['urut'] == 2){
        $urut = 'DESC';
    }

    switch ($_GET['colUrut']) {
        case 1 :
            $colUrut = 'id_kamar';
            break;
        case 2 :
            $colUrut = 'hak_akses';
            break;
        case 3 :
            $colUrut = 'nama';
            break;
        case 4 :
            $colUrut = 'no_hp';
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
            $colKunci = 'id_kamar';
            break;
        case 2 :
            $colKunci = 'hak_akses';
            break;
        case 3 :
            $colKunci = 'nama';
            break;
        case 4 :
            $colKunci = 'no_hp';
            break;
        default: showErrorPage();
    }  // Akhir SWITCH ($column).
    
    $keyword = mysqli_real_escape_string($dbc, $keyword);
    if ($colKunci == 'hak_akses'){
        $a = stripos(strtolower($keyword), 'admin');
        if (is_int($a) && ($a >= 0)){
            $queryCari = "WHERE hak_akses = 2";
        }
        
        $b = stripos(strtolower($keyword), 'user');
        if (is_int($b) && ($b >= 0)){
            $queryCari = "WHERE hak_akses = 3";
        }
    } else {
        $queryCari = "WHERE LOWER($colKunci) LIKE LOWER('%$keyword%')";
    }
      
    $search = true;
} else {
    $queryCari = '';
    $search = false;
}  // Akhir validasi query pencarian.

echo '<div class="page-header"><h3><span class="fas fa-users"></span> Penyewa Kontrakan</h3></div>';

$q = "SELECT COUNT(*) FROM tbl_penyewa_kontrakan";
// Validasi hasil eksekusi query :
if ($r = @mysqli_query($dbc, $q)) {
    list($total_row) = mysqli_fetch_array($r, MYSQLI_NUM);
    mysqli_free_result($r);
    if ($total_row == 0) {
        echo '
        <div class="alert alert-warning">Tidak ada data</div>';
    } else {
        if ($total_row > 1) { formPencarian('data_pk.php', $options, $colUrutDef, $urutDef); }
        
        $q = "SELECT COUNT(*) FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b ON b.kode_akun = a.kode_akun INNER JOIN tbl_wilayah AS c ON b.kode_wilayah = c.kode_wilayah $queryCari";
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
            $q = "SELECT * FROM tbl_akun AS a INNER JOIN tbl_penyewa_kontrakan AS b ON b.kode_akun = a.kode_akun INNER JOIN tbl_wilayah AS c ON b.kode_wilayah = c.kode_wilayah $queryCari ORDER BY $colUrut $urut LIMIT $start, $display";
        
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
                          <th class="text-center">Foto</th>
                          <th class="text-center">Nama</th>
                          <th class="text-center">Nomor HP</th>
                          <th class="text-center">ID Kamar</th>
                          <th class="text-center">Jenis User</th>
                          <th class="text-center">Lihat Rincian</th>
                          <th class="text-center">Ubah</th>
                          <th class="text-center">Hapus</th>
                        </tr>
                      </thead>';

                    $no = $start + 1;
                    // Tampilkan semua record :
                    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                        echo '<tr>
                          <td>' . $no . '</td>
                          <td class="text-center"><img style="width: 100px; height: 100px;" src="data:image/jpeg;base64,'. base64_encode($row['foto']) . '" alt="Foto ' . ucwords(strtolower($row['nama'])) . '" /></td>
                          <td>' . ucwords(strtolower($row['nama'])) . '</td>
                          <td>' . $row['no_hp'] . '</td>
                          <td class="text-center">' . strtoupper($row['id_kamar']) . '</td>';
                          // <td>' . ucwords($row['alamat']) . ' Kode Pos ' . $row['kodepos'] . ', Kel.' . ucwords($row['kelurahan']) . ', Kec.' . ucwords($row['kecamatan']) . ', Kab. ' . ucwords($row['kabupaten']) . ', Prov. ' . ucwords($row['provinsi']) . '</td>';
                        
                        echo '<td class="text-center">' . (($row['hak_akses'] == 2) ? 'Admin' : 'User') . '</td>
                          <td class="text-center"><a href="detail_data_pk.php?id=' . $row['kode_pk'] . '" class="btn btn-default">Rincian</a></td>
                          
                          <td class="text-center"><a href="ubah_data_pk.php?id=' . $row['kode_pk'] . '" class="btn"><big><span class="glyphicon glyphicon-pencil"></span></big></a></td><td class="text-center">';
                        if ($_SESSION['kode_akun'] != $row['kode_akun']){
                            echo '<a href="hapus_pk.php?id=' . $row['kode_pk'] . '" class="btn"><big><span class="glyphicon glyphicon-trash"></span></big></a>';
                        }
                        
                        echo '</td>';
                        
                        echo '</tr>';
                        $no++;
                    }  // Akhir WHILE ($row = mysqli_fetch_array). 
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
        echo '<br><p><a class="btn btn-default" href="data_pk.php">Tampilkan Semua</a></p><br>';
    }
    echo '<br><p><a class="btn btn-default" href="registrasi_penghuni.php"><small><span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span></small> Tambahkan Penyewa baru</a></p>';
    
} else {
    showSqlError($dbc);
}  // Akhir validasi hasil eksekusi query.

echo '</div><br>';
include('includes/footer.html');
?>