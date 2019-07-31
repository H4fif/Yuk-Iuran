<?php  # Script functions.php

// Function untuk menampilkan pesan error saat input.
// Butuh 1 parameter, array berisi pesan-pesan error.
function showErrorInput($errors) {
    echo '<div class="alert alert-danger">
      <h3 class="alert-heading">Terjadi kesalahan!</h3><hr>
      <ul class="list-errors">';
    foreach ($errors as $v) {
        echo '<li><span class="glyphicon glyphicon-exclamation-sign"></span><span class="sr-only"></span> ' . $v . '</li>';
    }  // Akhir FOREACH LOOP.
    echo '</ul></div>';
}  // Akhir showError($errors) function.

// Function untuk menampilkan pesan error saat validasi user login gagal.
function showErrorPage($kode_kesalahan = null) {
    echo '<div class="alert alert-danger">
          <h3 class="alert-heading">Terjadi Kesalahan saat mencoba mengakses halaman ini!</h3><hr>
          <p>Silakan hubungi administrator bila Anda mengalami masalah untuk mengakses halaman ini.</p>';
    
    if (isset($kode_kesalahan)){
        echo '<p>Kode kesalahan : ' . $kode_kesalahan . '</p>';
    }
    echo '</div><br></div>';
    include('includes/footer.html');
    exit;
}  // Akhir function showErrorPage().

// Function untuk menampilkan pesan error saat menggunakan database.
// Butuh 1 parameter, koneksi database.
function showSqlError($dbc, $kode_kesalahan = null) {
  echo '<div class="alert alert-danger">
    <h3 class="alert-heading">Terjadi kesalahan sistem!</h3><hr>';
  
  if (isset($kode_kesalahan)){
      echo '<p>Kode kesalahan : ' . $kode_kesalahan . '</p>';
  }

  if (null != (mysqli_error($dbc))){
    echo '<p>Kesalahan saat terhubung ke database : ' . mysqli_error($dbc) . '</p>';
  }

  echo '</div><br></div>';
  include('includes/footer.html');
  exit;
}  // Akhir showSqlError($dbc) function.

// Function untuk menampilkan form login.
// Butuh 1 parameter, array input.
function showLoginForm($val = []) {
    echo '<form class="form-login form-horizontal" name="login" action="login.php" method="post">
        <div class="page-header">
          <h3 class="text-center" style="font-size:2em;">Masuk</h3>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <div class="input-group input-group-lg">
                <span class="input-group-addon" id="sizing-addon1"><i class="glyphicon glyphicon-envelope"></i></span>
                <input class="form-control" name="email" type="email" placeholder="Email" size="30" maxlength="150" required="required" aria-describedby="sizing-addon1" value="' . ((isset($val['email'])) ? $val['email'] : '') . '" />
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <div class="input-group input-group-lg">
                <span class="input-group-addon" id="sizing-addon2"><i class="glyphicon glyphicon-lock red"></i></span>
                <input class="form-control pwd" name="kata_sandi" type="password" placeholder="Kata Sandi" size="20" minlength="4" maxlength="40" aria-describedby="sizing-addon2" required="required" value="' . ((isset($val['kata_sandi'])) ? $val['kata_sandi'] : '') . '" />
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <div class="checkbox btn-lg">
                <label><input type="checkbox" id="ckPwd" /><small> Tampilkan Kata Sandi</small></label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="form-group">
            <div class="col-sm-12">
              <div class="button-group text-center">
                <button class="btn btn-default btn-lg" type="submit">
                  <span class="glyphicon glyphicon-log-in"></span> Masuk
                </button>
              </div>
            </div>
          </div>
        </div>

        <br><br>
        <div class="row">
          <div class="form-group">
            <i>Lupa kata sandi? Hubungi Admin</i>
          </div>
        </div>
      </form>';
}  // Akhir showLoginForm($val) function.

// Function untuk membuat form input alamat sticky.
function stickySelection($value1, $value2) {
    if ($value1 == $value2) {
        return ' selected="selected"';
    } else {
        return '';
    }
}  // Akhir function stickySelection($value1, $value2).

// Function untuk mendapatkan hasil query dari database untuk data ALAMAT.
// Butuh 4 parameter :
// (1) koneksi database
// (2) kolom yang di inginkan
// (3) kolom kondisi
// (4) kata kunci untuk membuat sticky form
function getAlamat($dbc, $colReturn, $colCondition = '', $keyword = '', $sticky = '') {
    if (!empty($colCondition) && !empty($keyword)) {
        $condition = "WHERE $colCondition = '$keyword'";
    } else {
        $condition = '';
    }

    $q = "SELECT DISTINCT $colReturn FROM tbl_wilayah $condition ORDER BY $colReturn";
    $r = @mysqli_query($dbc, $q);
    if (mysqli_num_rows($r) > 0) {
        while ($row = mysqli_fetch_array($r, MYSQLI_NUM)) {
            echo '<option value="' . $row[0] . '"';

            if (!empty($_POST)) {
                if ($row[0] == $_POST["$colReturn"]) {
                    echo ' selected="selected"';
                }
            } elseif (!empty($sticky)) {
                if ($row[0] == $sticky) {
                    echo ' selected="selected"';
                }
            }

            echo '>' . ucwords(strtolower($row[0])) . '</option>';
        }  // Akhir WHILE LOOP.
    }  // Akhir IF.
}  // Akhir function getAlamat.

// Function untuk validasi halaman.
// Butuh 2 parameter, (1) batasan data yang ditampilkan per halaman INT, (2) input halaman INT.
// Input : maksimum data yang ditampilkan per halaman, nomor halaman
// Output : halaman
function validasiHalaman($maksHalaman, $inputHalaman) {
    if (isset($_GET['display']) && filter_var($_GET['display'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => MAKS_TAMPILAN))) && (($_GET['display'] % KELIPATAN) == 0)) {
        return $_GET['display'];
    } else {
        if (isset($_SESSION['display'])) {
            return $_SESSION['display'];
        } else {
            return 5;
        }
    }
}

// Function untuk validasi user.
// Input : -
// Output : status
function validasiUser() {
    if (isset($_SESSION['kode_akun'], $_SESSION['hak_akses'], $_SESSION['agent']) && ($_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT']))) {
        return true;
    } else {
        return false;
    }
}

// Function untuk menampilkan form registrasi.
// Butuh 2 parameter, (1) koneksi database dan (2) array input.
function showRegistrationForm($dbc, $val) {
  echo '<form class="form-registrasi form-horizontal" name="registrasi" action="registrasi_penghuni.php" method="post" enctype="multipart/form-data">

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="foto">Foto</label>
        <div class="col-sm-5">
          <input id="foto" name="foto" type="file" required="required" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="no-ktp">Nomor KTP</label>
        <div class="col-sm-5">
          <input class="form-control" id="no-ktp" name="no_ktp" type="text" placeholder="Nomor KTP 16 digit" minlength="16" maxlength="16" size="16" required="required" value="' . (isset($val['no_ktp']) ? $val['no_ktp'] : '') . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nama-lengkap">Nama Lengkap</label>
        <div class="col-sm-5">
          <input class="form-control" id="nama-lengkap" name="nama" type="text" placeholder="min. 3 karakter, maks. 150 karakter" minlength="3" maxlength="150" size="30" required="required" value="' . (isset($val['nama']) ? $val['nama'] : '' ) . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="jenis_kelamin">Jenis Kelamin</label>
        <div class="col-sm-5">
          <div class="radio-inline">
            <label>
              <input name="jenis_kelamin" type="radio" value="L"';
            if (isset($val['jenis_kelamin']) && (strtoupper($val['jenis_kelamin']) == 'L')) {
                echo ' checked="checked"';
            }
            echo ' required="required" /> Laki-laki</label>
          </div>

          <div class="radio-inline">
            <label>
              <input name="jenis_kelamin" type="radio" value="P"';
            if (isset($val['jenis_kelamin']) && (strtoupper($val['jenis_kelamin']) == 'P')) {
                echo ' checked="checked"';
            }
            echo ' required="required" /> Perempuan</label>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="tanggal-lahir">Tanggal Lahir</label>
        <div class="col-sm-5">
          <input class="form-control" id="tanggal-lahir" type="date" name="tanggal_lahir" max="2002-12-30"  required="required" placeholder="Pilih tanggal" value="' . (isset($val['tanggal_lahir']) ? $val['tanggal_lahir'] : '') . '" />
        </div>
        <label><small><i>Maks. kelahiran tanggal 2002-12-30</i></small></label>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="pekerjaan">Pekerjaan</label>
        <div class="col-sm-5">
          <input class="form-control" id="pekerjaan" name="pekerjaan" type="text" placeholder="Karyawan / Mahasiswa" minlength="3" maxlength="20" size="20" required="required" value="' . (isset($val['pekerjaan']) ? $val['pekerjaan'] : '' ) . '" />
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="id-kamar">ID Kamar</label>
        <div class="col-sm-5">
          <input class="form-control" id="id_kamar" name="id_kamar" type="text" placeholder="Misal: A1" minlength="2" maxlength="2" size="2" required="required" value="' . (isset($val['id_kamar']) ? $val['id_kamar'] : '' ) . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="no-hp">Nomor HP</label>
        <div class="col-sm-5">
          <input class="form-control" id="no-hp" name="no_hp" type="text" placeholder="contoh: 08111xxx" minlength="3" maxlength="20" size="20" required="required" value="' . (isset($val['no_hp']) ? $val['no_hp'] : '') . '" />
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="email">Email</label>
        <div class="col-sm-5">
          <input class="form-control" id="email" name="email" type="email" placeholder="contoh: user@email.com" maxlength="150" size="30" required="required" value="' . (isset($val['email']) ? $val['email'] : '') . '" />
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kata-sandi">Kata Sandi</label>
        <div class="col-sm-5">
          <input class="form-control pwd" id="kata-sandi" name="kata_sandi" type="password" placeholder="********" size="20" minlength="4" maxlength="40" required="required" />
        </div>
        <div class="col-sm-2 checkbox">
          <label class="control-label"><input type="checkbox" id="ckPwd" /> Tampilkan Kata Sandi</label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kata-sandi2">Konfirmasi Kata Sandi</label>
        <div class="col-sm-5">
          <input class="form-control pwd" id="kata-sandi2" name="kata_sandi2" type="password" placeholder="********" size="20" minlength="4" maxlength="40" required="required" />
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_prov">Provinsi</label>
        <div class="col-sm-5">
          <select class="form-control" name="provinsi" id="op_prov" required="required">
            <option value="">-- Pilih salah satu --</option>';
            getAlamat($dbc, 'provinsi');
            echo '</select>
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_kabupaten">Kabupaten / Kota</label>
        <div class="col-sm-5">
          <select class="form-control" name="kabupaten" id="op_kabupaten" required="required">
            <option value="">-- Pilih salah satu --</option>';

            if (!empty($val['provinsi'])) {
                getAlamat($dbc, 'kabupaten', 'provinsi', $val['provinsi']);
            }
      
            echo '</select>
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_kecamatan">Kecamatan</label>
        <div class="col-sm-5">
          <select class="form-control" name="kecamatan" id="op_kecamatan" required="required">
            <option value="">-- Pilih salah satu --</option>';

            if (!empty($val['kabupaten'])) {
                getAlamat($dbc, 'kecamatan', 'kabupaten', $val['kabupaten']);
            }

            echo '</select>
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_kelurahan">Kelurahan</label>
        <div class="col-sm-5">
          <select class="form-control" name="kelurahan" id="op_kelurahan" required="required">
            <option value="">-- Pilih salah satu --</option>';

            if (!empty($val['kecamatan'])) {
                getAlamat($dbc, 'kelurahan', 'kecamatan', $val['kecamatan']);
            }

            echo '</select>
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kodepos">Kode Pos</label>
        <div class="col-sm-5">
          <input class="form-control" name="kodepos" placeholder="contoh: 12345" type="text" minlength="5" maxlength="5" size="10" required="required"  value="' . ((isset($val['kodepos'])) ? $val['kodepos'] : '') . '" />
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="alamat">Alamat</label>
        <div class="col-sm-5">
          <textarea class="form-control" id="alamat" name="alamat" placeholder="contoh: Jl. Abc No. 1, RT 01 / RW 01" cols="40" rows="5" minlength="5" maxlength="255" required="required">' . ((isset($val['alamat'])) ? $val['alamat'] : '') . '</textarea>
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="alert alert-warning col-sm-7">
        <strong>Catatan</strong> : Pastikan semua isian lengkap dan benar!
      </div>
    </div>
      
    <div class="row">
      <div class="form-group text-center">
        <div class="col-sm-7">
          <button class="btn btn-primary" type="submit">Simpan</button><span style="margin-right: 200px;"></span><button class="btn btn-danger" id="btnResetRegister" type="reset" title="Kosongkan semua isian">Reset</button>
        </div>
      </div>
    </div>
    <br><br>
    <div class="row">
      <div class="form-group">
        <a class="btn btn-default" href="data_pk.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
      </div>
    </div>
  </form>';
}  // Akhir showRegistrationForm($val).

// Function untuk menampilkan form pencarian
// Input : column, keyword
// Output : data setelah di cari
function formPencarian($filename, $options, $colUrut = null, $urut = null){
    if (empty($filename)) {
        showErrorPage();
    }
    if (!is_array($options) || empty($options)) {        
        showErrorPage();
    }
    echo '<form class="form-inline text-center" name="pencarian" action="' . $filename . '" method="get">

        <select class="form-control" name="colCari">
          <option value="">-- Cari berdasarkan --</option>';
    foreach ($options as $k => $v) {
        echo '<option value="' . ($k + 1) . '"';
        if (isset($_GET['colCari'])) {
            if (($k + 1) == $_GET['colCari']) {
                echo ' selected="selected"';
            }
        }
        echo '>' . $v . '</option>';
    }
    echo '</select>
        <input class="form-control" type="search" name="keywordCari" size="50" min="3" max="255" placeholder="Ketikkan kata kunci disini" value="' . ((isset($_GET['keywordCari'])) ? $_GET['keywordCari'] : '') . '" />
        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span> Cari</button>
        <a class="btn btn-default" href="' . $filename . '">Reset</a>';

    if (isset($colUrut, $urut)){
        echo '<input type="hidden" name="colUrut" value="' . $colUrut . '" />
              <input type="hidden" name="urut" value="' . $urut . '" />';
    }
    echo '</form><br />';
}  // Akhir formPencarian

// fungsi untuk mengurutkan data
// I. S. : nama kolom dan jenis urut (ASC / DESC)
// F. S. : data yang sudah terurut
function form_pengurutan($options, $id = null, $colCari = null, $keywordCari = null){
    GLOBAL $filename;
    $link = $filename;
    
    echo '<form class="form-inline" name="pengurutan" action="' . $link . '" method="get">
      <select class="form-control" name="colUrut">
        <option value="">-- Urut berdasarkan --</option>';
    foreach ($options as $k => $v) {
      echo '<option value="' . ($k + 1) . '"';
      if (isset($_GET['colUrut'])) {
          if (($k + 1) == $_GET['colUrut']) {
              echo ' selected="selected"';
          }
      }
      echo '>' . $v . '</option>';
    }
    echo '</select>
      <select class="form-control" name="urut">
        <option value="">-- Urutkan dari --</option>
        <option value="1"';
    
    if (isset($_GET['urut']) && ($_GET['urut'] == 1)) {
            echo ' selected="selected"';
    }
        
    echo '>A-Z</option>
      <option value="2"';
    
    if (isset($_GET['urut']) && ($_GET['urut'] == 2)) {
        echo ' selected="selected"';    
    }
    echo '>Z-A</option></select>';
    
    if (!empty($id)) {
        echo '<input type="hidden" name="id" value="' . $id . '" />';
    }

    if (isset($colCari, $keywordCari)){
        echo '<input type="hidden" name="colCari" value="' . $colCari . '" />
              <input type="hidden" name="keywordCari" value="' . $keywordCari . '" />';
    }
    
    echo '<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-refresh"></span> Segarkan laman</button>
    </form><br><br>';
}  // Akhir formPengurutan

// Function untuk menampilkan profil penghuni.
// Butuh 2 parameter, (1) koneksi database, (2) data (typeof array) diambil dari database.
// Input : koneksi database, data penghuni dari database
// Output : data penghuni
function showProfilPenghuni($dbc, $val) {

    $q = "SELECT foto FROM tbl_penyewa_kontrakan WHERE no_ktp = {$val['no_ktp']}";
    $r = mysqli_query($dbc, $q);
    $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
    $foto = $data['foto'];

    if (!empty($foto)) {
        $foto = 'src="data:image/jpeg;base64,' . base64_encode($foto) . '"';
    }

    echo '<form class="form-horizontal">
      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="foto1">Foto</label>
          <div class="col-sm-5">
            <img style="width: 100px; height: 100px;" alt="Foto ' . $val['nama'] . '" ' . $foto . ' />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="no-ktp">Nomor KTP</label>
          <div class="col-sm-5">
            <input class="form-control" id="no-ktp" name="no_ktp" type="text" placeholder="Nomor KTP 16 digit" minlength="16" maxlength="16" size="16" required="required" value="' . $val['no_ktp'] . '" readonly />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="nama-lengkap">Nama Lengkap</label>
          <div class="col-sm-5">
            <input class="form-control" id="nama-lengkap" name="nama" type="text" placeholder="min. 3 karakter, maks. 150 karakter" minlength="3" maxlength="150" size="30" required="required" value="' . $val['nama'] . '" readonly />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="jenis_kelamin">Jenis Kelamin</label>
          <div class="col-sm-5">
            <input class="form-control" type="text" value="';
              if (isset($val['jenis_kelamin']) && (strtoupper($val['jenis_kelamin']) == 'L')) {
                  echo 'Laki-laki';
              } else {
                  echo 'Perempuan';
              }
            echo '" readonly />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="tanggal-lahir">Tanggal Lahir</label>
          <div class="col-sm-5">
            <input class="form-control" id="tanggal-lahir" type="text" name="tanggal_lahir" max="2012-12-30"  required="required" placeholder="Pilih tanggal" value="' . kalender_indo($val['tanggal_lahir']) . '" readonly />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="pekerjaan">Pekerjaan</label>
          <div class="col-sm-5">
            <input class="form-control" id="pekerjaan" name="pekerjaan" type="text" placeholder="Karyawan / Mahasiswa" minlength="3" maxlength="20" size="20" required="required" value="' . $val['pekerjaan']. '" readonly />
          </div>
        </div>
      </div>
        
      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="id-kamar">ID Kamar</label>
          <div class="col-sm-5">
            <input class="form-control" id="id_kamar" name="id_kamar" type="text" placeholder="Misal: A1" minlength="2" maxlength="2" size="2" required="required" value="' . strtoupper($val['id_kamar']) . '" readonly />
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="no-hp">Nomor HP</label>
          <div class="col-sm-5">
            <input class="form-control" id="no-hp" name="no_hp" type="text" placeholder="contoh: 08111xxx" minlength="3" maxlength="20" size="20" required="required" value="' . $val['no_hp'] . '" readonly />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="email">Email</label>
          <div class="col-sm-5">
            <input class="form-control" id="email" name="email" type="email" placeholder="contoh: user@email.com" maxlength="150" size="30" required="required" value="' . $val['email']  . '" readonly />
          </div>
        </div>
      </div>';

      if (in_array($_SESSION['hak_akses'], [1])) {
        if ($val['hak_akses'] == 2){
            $user = 'admin';
        } else {
            $user = 'user';
        }

        echo '<div class="row">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="jenis_user">Jenis User</label>
            <div class="col-sm-5">
              <input class="form-control" id="jenis_user" name="jenis_user" type="text" size="30" value="' . ucwords(strtolower($user))  . '" readonly />
            </div>
          </div>
        </div>';
      }
        
      echo '<div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="alamat">Alamat</label>
          <div class="col-sm-5">
            <textarea class="form-control" id="alamat" name="alamat" placeholder="contoh: Jl. Abc No. 1" cols="40" rows="5" minlength="5" maxlength="255" required="required" readonly>' . $val['alamat'] . ' Kode Pos ' . $val['kodepos'] . ', Kel.' . $val['kelurahan'] . ', Kec.' . $val['kecamatan'] . ', Kab. ' . $val['kabupaten'] . ', Prov.' . $val['provinsi'] . '</textarea>
          </div>
        </div>
      </div>      
      <br>';
      
      if ($_SESSION['basename'] == 'profil' ){
          $href = 'ubah_profil.php';
      } else {
          $href = 'ubah_data_pk.php?id=' . $val['kode_pk'];
      }

      echo '<div class="row">
              <div class="col-sm-7 form-group text-center">
                <a class="btn btn-default" href="' . $href . '"><span class="glyphicon glyphicon-edit"></span> Ubah</a>
              </div>
            </div>
          </form>';
}  // Akhir function showProfilPenghuni()

// Function untuk menampilkan form update profil.
// Butuh 4 parameter, (1) koneksi database DATABASE OBJECT, (2) kode customer INT, (3) data awal sebelum perubahan ARRAY, (4) data saat update ARRAY.
function showUbahProfilForm($dbc, $row, $val) {
    if (empty($val)) {
        $email = $row['email'];
        if ($_SESSION['hak_akses'] != 1){
            $nama = $row['nama'];
            $no_hp = $row['no_hp'];
            $prov = $row['provinsi'];
            $kab = $row['kabupaten'];
            $kec = $row['kecamatan'];
            $kel = $row['kelurahan'];
            $kodepos = $row['kodepos'];
            $alamat = $row['alamat'];
            $pekerjaan = $row['pekerjaan'];
            $jenis_kelamin = $row['jenis_kelamin'];
            $id_kamar = $row['id_kamar'];
            $no_ktp = $row['no_ktp'];
            $tanggal_lahir = $row['tanggal_lahir'];
        }
    } else {
        $email = $val['email'];
        if ($_SESSION['hak_akses'] != 1){
            $nama = $val['nama'];
            $no_hp = $val['no_hp'];
            $prov = $val['provinsi'];
            $kab = $val['kabupaten'];
            $kec = $val['kecamatan'];
            $kel = $val['kelurahan'];
            $kodepos = $val['kodepos'];
            $alamat = $val['alamat'];
            $pekerjaan = $val['pekerjaan'];
            $jenis_kelamin = $val['jenis_kelamin'];
            $id_kamar = $val['id_kamar'];
            $no_ktp = $val['no_ktp'];
            $tanggal_lahir = $val['tanggal_lahir'];
        }
    }

    if ($_SESSION['hak_akses'] != 1){
        $q = "SELECT foto FROM tbl_penyewa_kontrakan WHERE kode_akun = {$_SESSION['kode_akun']}";
        $r = mysqli_query($dbc, $q);
        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
        $foto = $data['foto'];

        if (!empty($foto)) {
            $foto = 'src="data:image/jpeg;base64,' . base64_encode($foto) . '"';
        }
    }

    echo '<form class="form-horizontal" name="update" action="ubah_profil.php" method="post" enctype="multipart/form-data">';
    if ($_SESSION['hak_akses'] != 1){
        echo '<div class="row">
        <div class="row">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="foto1">Foto</label>
            <div class="col-sm-5">
              <img style="width: 100px; height: 100px;" alt="Foto ' . $nama . '" ' . $foto . ' />
            </div>
          </div>
        </div>
    
        <div class="row">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="foto">Ubah Foto</label>
            <div class="col-sm-5">
              <input id="foto" name="foto" type="file" />
            </div>
          </div>
        </div>

        <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="no-ktp">Nomor KTP</label>
              <div class="col-sm-5">
                <input class="form-control" id="no-ktp" name="no_ktp" type="text" placeholder="Nomor KTP 16 digit" minlength="16" maxlength="16" size="16" value="' . $no_ktp . '" />
              </div>
            </div>
          </div>
        
          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="nama-lengkap">Nama Lengkap</label>
              <div class="col-sm-5">
                <input class="form-control" id="nama-lengkap" name="nama" type="text" placeholder="min. 3 karakter, maks. 50 karakter" minlength="3" maxlength="50" size="30" value="' . $nama . '" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="jenis_kelamin">Jenis Kelamin</label>
              <div class="col-sm-5">
                <div class="radio-inline">
                  <label>
                    <input name="jenis_kelamin" type="radio" value="L"';
                  if (isset($jenis_kelamin) && (strtoupper($jenis_kelamin) == 'L')) {
                      echo ' checked="checked"';
                  }
                  echo ' /> Laki-laki</label>
                </div>

                <div class="radio-inline">
                  <label>
                    <input name="jenis_kelamin" type="radio" value="P"';
                    if (isset($jenis_kelamin) && (strtoupper($jenis_kelamin) == 'P')) {
                      echo ' checked="checked"';
                  }
                  echo ' /> Perempuan</label>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="tanggal-lahir">Tanggal Lahir</label>
              <div class="col-sm-5">
                <input class="form-control" id="tanggal-lahir" type="date" name="tanggal_lahir" max="2012-12-30" placeholder="Pilih tanggal" value="' . $tanggal_lahir . '" />
              </div>
              <label><small><i>Maks. kelahiran tanggal 2012-12-30</i></small></label>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="pekerjaan">Pekerjaan</label>
              <div class="col-sm-5">
                <input class="form-control" id="pekerjaan" name="pekerjaan" type="text" placeholder="Karyawan / Mahasiswa" minlength="3" maxlength="20" size="20" value="' . $pekerjaan . '" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="id-kamar">ID Kamar</label>
              <div class="col-sm-5">
                <input class="form-control" id="id_kamar" name="id_kamar" type="text" placeholder="Misal: A1" minlength="2" maxlength="2" size="2" required="required" value="' . $id_kamar . '" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="no-hp">Nomor HP</label>
              <div class="col-sm-5">
                <input class="form-control" id="no-hp" name="no_hp" type="text" placeholder="contoh: 08111xxx" minlength="3" maxlength="20" size="20" value="' . $no_hp . '" />
              </div>
            </div>
          </div>';
    }  // End If

      echo '<div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="email">Email</label>
          <div class="col-sm-5">
            <input class="form-control" id="email" name="email" type="email" placeholder="contoh: user@email.com" maxlength="150" value="' . $email . '" />
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="kata-sandi">Kata Sandi Lama</label>
          <div class="col-sm-5">
            <input class="form-control pwd" id="kata-sandi" name="kata_sandi_lama" type="password" placeholder="*****" maxlength="40" />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="kata-sandi-baru">Kata Sandi Baru</label>
          <div class="col-sm-5">
            <input class="form-control pwd" id="kata-sandi-baru" name="kata_sandi_baru" type="password" placeholder="*****" maxlength="40" />
          </div>
          <div class="col-sm-2 checkbox">
            <label class="control-label"><input type="checkbox" id="ckPwd" /> Tampilkan Kata Sandi</label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="kata-sandi2">Konfirmasi Kata Sandi Baru</label>
          <div class="col-sm-5">
            <input class="form-control pwd" id="kata-sandi2" name="kata_sandi2" type="password" placeholder="*****" maxlength="40" />
          </div>
        </div>
      </div>';

      if ($_SESSION['hak_akses'] != 1){
          echo '<div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="op_prov">Provinsi</label>
              <div class="col-sm-5">
                <select class="form-control" name="provinsi" id="op_prov">
                  <option value="">-- Pilih salah satu --</option>';

              getAlamat($dbc, 'provinsi', '', $prov, $prov);
              
            echo '</select></div></div></div>
            
          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="op_kabupaten">Kabupaten / Kota</label>
              <div class="col-sm-5">
                <select class="form-control" name="kabupaten" id="op_kabupaten">
                  <option value="">-- Pilih salah satu --</option>';
              
                  getAlamat($dbc, 'kabupaten', 'provinsi', $prov, $kab);
            
            echo '</select></div></div></div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="op_kecamatan">Kecamatan</label>
              <div class="col-sm-5">
                <select class="form-control" name="kecamatan" id="op_kecamatan">
                  <option value="">-- Pilih salah satu --</option>';

              getAlamat($dbc, 'kecamatan', 'kabupaten', $kab, $kec);

            echo '</select></div></div></div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="op_kelurahan">Kelurahan</label>
              <div class="col-sm-5">
                <select class="form-control" name="kelurahan" id="op_kelurahan">
                  <option value="">-- Pilih salah satu --</option>';

              getAlamat($dbc, 'kelurahan', 'kecamatan', $kec, $kel);

            echo '</select></div></div></div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="kodepos">Kode Pos</label>
              <div class="col-sm-5">
                <input class="form-control" id="kodepos" name="kodepos" placeholder="contoh: 12345" type="text" minlength="5" maxlength="5" size="10" value="' . $kodepos . '" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="alamat">Alamat</label>
              <div class="col-sm-5">
                <textarea class="form-control" id="alamat" name="alamat" placeholder="contoh: Jl. Abc No. 1, RT 01 / RW 01" cols="40" rows="5" minlength="5" maxlength="255">' . $alamat . '</textarea>
              </div>
            </div>
          </div>';
      }

      echo '     
      <br>
      <div class="row">
        <div class="form-group">
          <div class="col-sm-7 text-center">
            <button class="btn btn-danger" type="submit">Simpan</button>
            <span style="margin-right: 200px;"></span>
            <button class="btn btn-info" type="reset">Batal</button>
          </div>
        </div>
      </div>

      <br>
      <div class="row">
        <div class="alert alert-danger">Data lama akan diganti dengan data yang baru, pastikan semua isian lengkap dan benar!</div>
      </div>
      
      <br><br>

      <div class="row">
        <div class="form-group">
          <a class="btn btn-default" href="profil.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
        </div>
      </div>
    </form>';
}  // Akhir showUbahProfilForm($val).

// Function untuk menampilkan form tambah atribut pembayaran.
// Butuh 2 parameter, (1) koneksi database dan (2) array input.
function showFormAddAP($dbc, $val) {
  echo '<form class="form-registrasi form-horizontal" name="tambah_ap" action="tambah_ap.php" method="post">
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nama">Nama</label>
        <div class="col-sm-5">
          <input class="form-control" id="nama" name="nama" type="text" placeholder="Air / Listrik / Internet / dll" minlength="2" maxlength="50" size="30" required="required" value="' . (isset($val['nama']) ? $val['nama'] : '') . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="harga">Harga Per Bulan</label>
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon" id="addonrp"><b>Rp.</b></span><input class="form-control" id="harga" name="harga" type="number" placeholder="contoh: 50000" min="100" size="20" required="required" value="' . (isset($val['harga']) ? $val['harga'] : '' ) . '" aria-describedby="addonrp" />
          </div>
        </div>
      </div>
    </div>
      
    <br>
    <div class="row">
      <div class="alert alert-warning col-sm-7">
        <strong>Catatan</strong> : Pastikan semua isian lengkap dan benar!
      </div>
    </div>
    
    <br>
    <div class="row">
      <div class="form-group text-center">
        <div class="col-sm-7">
          <button class="btn btn-primary" type="submit">Simpan</button><span style="margin-right: 200px;"></span><button class="btn btn-danger" id="btnResetRegister" type="reset" title="Kosongkan semua isian">Reset</button>
        </div>
      </div>
    </div>
    <br><br>
    <div class="row">
      <div class="form-group">
        <a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
      </div>
    </div>
  </form>';
}  // Akhir showFormAddAP().

// Function untuk menampilkan form update atribut pembayaran.
// Butuh 4 parameter, (1) koneksi database DATABASE OBJECT, (2) kode customer INT, (3) data awal sebelum perubahan ARRAY, (4) data saat update ARRAY.
function showUbahAP($id, $row, $val) {
  if (empty($val)) {
      $nama = $row['nama'];
      $harga = $row['harga'];
  } else {
      $nama = $val['nama'];
      $harga = $val['harga'];
  }

  echo '<form class="form-horizontal" name="ubah_ap" action="ubah_data_ap.php" method="post">
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nama">Nama</label>
        <div class="col-sm-5">
          <input class="form-control" id="nama" name="nama" type="text" placeholder="Air / Listrik / Internet / dll" minlength="2" maxlength="50" size="30" required="required" value="' . $nama . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="harga">Harga Per Bulan</label>
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon" id="addonrp"><b>Rp.</b></span><input class="form-control" id="harga" name="harga" type="number" placeholder="contoh: 50000" min="100" size="20" required="required" value="' . $harga . '" aria-describedby="addonrp" />
          </div>
        </div>
      </div>
    </div>

    <input type="hidden" name="id" value="' . $id . '" />
    
    <br>
    <div class="row">
      <div class="form-group">
        <div class="col-sm-7 text-center">
          <button class="btn btn-danger" type="submit">Simpan</button>
          <span style="margin-right: 200px;"></span>
          <button class="btn btn-info" type="reset">Batal</button>
        </div>
      </div>
    </div>

    <br>
    <div class="row">
      <div class="alert alert-danger">Data lama akan diganti dengan data yang baru, pastikan semua isian lengkap dan benar!</div>
    </div>
    
    <br><br>
    <div class="row">
      <div class="form-group">
        <a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
      </div>
    </div>
  </form>';
}  // Akhir showUbahAP()

function kalender_indo($tanggal){
  $a = explode('-', $tanggal);
  $hari = $a[2];
  $bulan = $a[1];
  $tahun = $a[0];

  switch ($bulan) {
      case 1 : $bulan = 'Januari'; break;
      case 2 : $bulan = 'Februari'; break;
      case 3 : $bulan = 'Maret'; break;
      case 4 : $bulan = 'April'; break;
      case 5 : $bulan = 'Mei'; break;
      case 6 : $bulan = 'Juni'; break;
      case 7 : $bulan = 'Juli'; break;
      case 8 : $bulan = 'Agustus'; break;
      case 9 : $bulan = 'September'; break;
      case 10 : $bulan = 'Oktober'; break;
      case 11 : $bulan = 'November'; break;
      case 12 : $bulan = 'Desember'; break;
  }
  return $hari . ' ' . $bulan . ' ' . $tahun;
}


function showProfilSuperAdmin($val) {
  echo '<form class="form-horizontal">
      <div class="row">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="email">Email</label>
          <div class="col-sm-5">
            <input class="form-control" id="email" name="email" type="email" placeholder="contoh: user@email.com" maxlength="150" size="30" required="required" value="' . $val['email']  . '" readonly />
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-sm-7 form-group text-center">
          <a class="btn btn-default" href="ubah_profil.php"><span class="glyphicon glyphicon-edit"></span> Ubah</a>
        </div>
      </div>
    </form>';
}  // Akhir function showProfilSuperAdmin($val)


function showUbahDataPK($dbc, $id, $row, $val) {
  if (empty($val)) {
      $nama = $row['nama'];
      $no_hp = $row['no_hp'];
      $prov = $row['provinsi'];
      $kab = $row['kabupaten'];
      $kec = $row['kecamatan'];
      $kel = $row['kelurahan'];
      $kodepos = $row['kodepos'];
      $alamat = $row['alamat'];
      $email = $row['email'];
      $pekerjaan = $row['pekerjaan'];
      $jenis_kelamin = $row['jenis_kelamin'];
      $id_kamar = $row['id_kamar'];
      $no_ktp = $row['no_ktp'];
      $tanggal_lahir = $row['tanggal_lahir'];
      $hak_akses = $row['hak_akses'];
      $kode_akun = $row['kode_akun'];
  } else {
      $nama = $val['nama'];
      $no_hp = $val['no_hp'];
      $prov = $val['provinsi'];
      $kab = $val['kabupaten'];
      $kec = $val['kecamatan'];
      $kel = $val['kelurahan'];
      $kodepos = $val['kodepos'];
      $alamat = $val['alamat'];
      $email = $val['email'];
      $pekerjaan = $val['pekerjaan'];
      $jenis_kelamin = $val['jenis_kelamin'];
      $id_kamar = $val['id_kamar'];
      $no_ktp = $val['no_ktp'];
      $tanggal_lahir = $val['tanggal_lahir'];
      $hak_akses = $val['hak_akses'];
      $kode_akun = '';
  }

  $q = "SELECT foto FROM tbl_penyewa_kontrakan WHERE no_ktp = $no_ktp";
  $r = mysqli_query($dbc, $q);
  $data = mysqli_fetch_array($r, MYSQLI_ASSOC);
  $foto = $data['foto'];

  if (!empty($foto)) {
      $foto = 'src="data:image/jpeg;base64,' . base64_encode($foto) . '"';
  }

  echo '<form class="form-horizontal" name="update" action="ubah_data_pk.php" method="post" enctype="multipart/form-data">
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="foto1">Foto</label>
        <div class="col-sm-5">
          <img style="width: 100px; height: 100px;" alt="Foto ' . $nama . '" ' . $foto . ' />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="foto">Ubah Foto</label>
        <div class="col-sm-5">
          <input id="foto" name="foto" type="file" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="no-ktp">Nomor KTP</label>
        <div class="col-sm-5">
          <input class="form-control" id="no-ktp" name="no_ktp" type="text" placeholder="Nomor KTP 16 digit" minlength="16" maxlength="16" size="16" value="' . $no_ktp . '" />
        </div>
      </div>
    </div>
  
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nama-lengkap">Nama Lengkap</label>
        <div class="col-sm-5">
          <input class="form-control" id="nama-lengkap" name="nama" type="text" placeholder="min. 3 karakter, maks. 50 karakter" minlength="3" maxlength="50" size="30" value="' . $nama . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="jenis_kelamin">Jenis Kelamin</label>
        <div class="col-sm-5">
          <div class="radio-inline">
            <label>
              <input name="jenis_kelamin" type="radio" value="L"';
            if (isset($jenis_kelamin) && (strtoupper($jenis_kelamin) == 'L')) {
                echo ' checked="checked"';
            }
            echo ' /> Laki-laki</label>
          </div>

          <div class="radio-inline">
            <label>
              <input name="jenis_kelamin" type="radio" value="P"';
              if (isset($jenis_kelamin) && (strtoupper($jenis_kelamin) == 'P')) {
                echo ' checked="checked"';
            }
            echo ' /> Perempuan</label>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="tanggal-lahir">Tanggal Lahir</label>
        <div class="col-sm-5">
          <input class="form-control" id="tanggal-lahir" type="date" name="tanggal_lahir" max="2012-12-30" placeholder="Pilih tanggal" value="' . $tanggal_lahir . '" />
        </div>
        <label><small><i>Maks. kelahiran tanggal 2012-12-30</i></small></label>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="pekerjaan">Pekerjaan</label>
        <div class="col-sm-5">
          <input class="form-control" id="pekerjaan" name="pekerjaan" type="text" placeholder="Karyawan / Mahasiswa" minlength="3" maxlength="20" size="20" value="' . $pekerjaan . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="id-kamar">ID Kamar</label>
        <div class="col-sm-5">
          <input class="form-control" id="id_kamar" name="id_kamar" type="text" placeholder="Misal: A1" minlength="2" maxlength="2" size="2" required="required" value="' . $id_kamar . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="no-hp">Nomor HP</label>
        <div class="col-sm-5">
          <input class="form-control" id="no-hp" name="no_hp" type="text" placeholder="contoh: 08111xxx" minlength="3" maxlength="20" size="20" value="' . $no_hp . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="email">Email</label>
        <div class="col-sm-5">
          <input class="form-control" id="email" name="email" type="email" placeholder="contoh: user@email.com" maxlength="150" value="' . $email . '" />
        </div>
      </div>
    </div>';

    if ($_SESSION['hak_akses'] == 3){
        echo '<div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="kata-sandi">Kata Sandi Lama</label>
              <div class="col-sm-5">
                <input class="form-control pwd" id="kata-sandi" name="kata_sandi_lama" type="password" placeholder="*****" maxlength="40" />
              </div>
            </div>
          </div>';
    }
    
    echo'<div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kata-sandi-baru">Kata Sandi Baru</label>
        <div class="col-sm-5">
          <input class="form-control pwd" id="kata-sandi-baru" name="kata_sandi_baru" type="password" placeholder="*****" maxlength="40" />
        </div>
        <div class="col-sm-2 checkbox">
          <label class="control-label"><input type="checkbox" id="ckPwd" /> Tampilkan Kata Sandi</label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kata-sandi2">Konfirmasi Kata Sandi Baru</label>
        <div class="col-sm-5">
          <input class="form-control pwd" id="kata-sandi2" name="kata_sandi2" type="password" placeholder="*****" maxlength="40" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_prov">Provinsi</label>
        <div class="col-sm-5">
          <select class="form-control" name="provinsi" id="op_prov">
            <option value="">-- Pilih salah satu --</option>';

        getAlamat($dbc, 'provinsi', '', $prov, $prov);
        
      echo '</select></div></div></div>
      
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_kabupaten">Kabupaten / Kota</label>
        <div class="col-sm-5">
          <select class="form-control" name="kabupaten" id="op_kabupaten">
            <option value="">-- Pilih salah satu --</option>';
        
            getAlamat($dbc, 'kabupaten', 'provinsi', $prov, $kab);
      
      echo '</select></div></div></div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_kecamatan">Kecamatan</label>
        <div class="col-sm-5">
          <select class="form-control" name="kecamatan" id="op_kecamatan">
            <option value="">-- Pilih salah satu --</option>';

        getAlamat($dbc, 'kecamatan', 'kabupaten', $kab, $kec);

      echo '</select></div></div></div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="op_kelurahan">Kelurahan</label>
        <div class="col-sm-5">
          <select class="form-control" name="kelurahan" id="op_kelurahan">
            <option value="">-- Pilih salah satu --</option>';

        getAlamat($dbc, 'kelurahan', 'kecamatan', $kec, $kel);

      echo '</select></div></div></div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kodepos">Kode Pos</label>
        <div class="col-sm-5">
          <input class="form-control" id="kodepos" name="kodepos" placeholder="contoh: 12345" type="text" minlength="5" maxlength="5" size="10" value="' . $kodepos . '" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="alamat">Alamat</label>
        <div class="col-sm-5">
          <textarea class="form-control" id="alamat" name="alamat" placeholder="contoh: Jl. Abc No. 1, RT 01 / RW 01" cols="40" rows="5" minlength="5" maxlength="255">' . $alamat . '</textarea>
        </div>
      </div>
    </div>';

    if ($_SESSION['kode_akun'] != $kode_akun){
        echo '<div class="row">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="jenis_user">Jenis User</label>
            <div class="col-sm-5">
              <div class="radio-inline">
                <label>
                  <input name="hak_akses" type="radio" value="2"';
                if (strtoupper($hak_akses) == 2) {
                    echo ' checked="checked"';
                }
                echo ' /> Admin</label>
              </div>

              <div class="radio-inline">
                <label>
                  <input name="hak_akses" type="radio" value="3"';
                  if (strtoupper($hak_akses) == 3) {
                    echo ' checked="checked"';
                }
                echo ' /> User</label>
              </div>
            </div>
          </div>
        </div>';
    }

    echo '<input type="hidden" name="id" value="' . $id . '" />
    
    <br>
    <div class="row">
      <div class="form-group">
        <div class="col-sm-7 text-center">
          <button class="btn btn-danger" type="submit">Simpan</button>
          <span style="margin-right: 200px;"></span>
          <button class="btn btn-info" type="reset">Batal</button>
        </div>
      </div>
    </div>

    <br>
    <div class="row">
      <div class="alert alert-danger">Data lama akan diganti dengan data yang baru, pastikan semua isian lengkap dan benar!</div>
    </div>
    
    <br><br>

    <div class="row">
      <div class="form-group">
        <a class="btn btn-default" href="data_pk.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
      </div>
    </div>
  </form>';
}  // Akhir showUbahDataPK().

function FormPembayaran($dbc, $val) {
  echo '<form class="form-registrasi form-horizontal" name="pembayaran" action="tambah_pay.php" method="post">

    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="kode_pk">Nama Penyewa</label>
        <div class="col-sm-5">
          <select class="form-control" name="kode_pk" id="kode_pk">
            <option value="">-- Pilih salah satu --</option>';
            $q = "SELECT kode_pk, nama FROM tbl_penyewa_kontrakan";
            $r = mysqli_query($dbc, $q);
            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
                if ($val['kode_pk'] == $row['kode_pk']){
                    echo '<option value="'. $row['kode_pk'] . '" selected>';
                } else {
                    echo '<option value="'. $row['kode_pk'] . '">';
                }
                echo ucwords(strtolower($row['nama'])) . '</option>';
            }
      echo '</select></div>
      </div>
    </div>
  
    <div class="row">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="ap">Pembayaran Untuk : </label>
        <div class="col-sm-10">';
          
        $q = "SELECT * FROM tbl_atribut_pembayaran";
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
            echo '<div class="checkbox">
              <label>
                <input type="checkbox" name="kode_ap[]" value="' . $row['kode_ap'] . '"';
                if (isset($val['kode_ap'][$row['kode_ap']]) && ($val['kode_ap'][$row['kode_ap']] == $row['kode_ap'])){
                    echo 'checked';
                }
                echo ' />' . ucwords(strtolower($row['nama'])) . ' - Rp.' . number_format($row['harga'], 2, ',', '.') . '
              </label>
            </div>';
        }

        echo '</div>
      </div>
    </div>
      
    <br>
    <div class="row">
      <div class="alert alert-warning col-sm-7">
        <strong>Catatan</strong> : Pastikan semua isian lengkap dan benar!
      </div>
    </div>
    
    <br>
    <div class="row">
      <div class="form-group text-center">
        <div class="col-sm-7">
          <button class="btn btn-primary" type="submit">Bayar</button><span style="margin-right: 200px;"></span><button class="btn btn-danger" id="btnResetRegister" type="reset" title="Kosongkan semua isian">Reset</button>
        </div>
      </div>
    </div>
    <br><br>
    <div class="row">
      <div class="form-group">
        <a class="btn btn-default" href="data_ap.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Kembali</a>
      </div>
    </div>
  </form>';
}  // Akhir FormPembayaran().