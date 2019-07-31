<?php  # Script opsi_wilayah.php
// Script untuk mengambil isi form alamat dari database.

require('config.php');
require(FUNGSI);
require(MYSQLI);

if (isset($_GET['p'])) {
    $keyword = $_GET['p'];
    $col = 'kabupaten';
    $colW = 'provinsi';
} elseif (isset($_GET['kab'])) {
    $keyword = $_GET['kab'];
    $col = 'kecamatan';
    $colW = 'kabupaten';
} elseif (isset($_GET['kec'])) {
    $keyword = $_GET['kec'];
    $col = 'kelurahan';
    $colW = 'kecamatan';
} else {
  $keyword = $col = $colW = '';
}

$q = "SELECT DISTINCT $col FROM tbl_wilayah WHERE $colW = '$keyword' ORDER BY $col";
if ($r = @mysqli_query($dbc, $q)) {
    echo '<option value="">-- Pilih salah satu --</option>';

    if (!empty(mysqli_num_rows($r))) {
        while ($row = mysqli_fetch_array($r, MYSQLI_NUM)) {
            echo '<option value="' . $row[0] . '">' . ucwords(strtolower($row[0])) . '</option>';
        }
    }
} else {
    showSqlError($dbc);
}

mysqli_free_result($r);
mysqli_close($dbc);
?>