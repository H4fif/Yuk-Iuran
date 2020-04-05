<?php  # Script mysqli_connect.php
// File ini butuh config.php dan functions.php di deklarasikan di included file.

if (!($dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME))) {
    showSqlError($dbc);
}

mysqli_set_charset($dbc, 'utf8');