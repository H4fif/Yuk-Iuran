

<!doctype html>
<html>
  <head>
    <title>Upload Gambar</title>
  </head>
  <body>
    <form method="post" enctype="multipart/form-data">
      Pilih gambar untuk diupload :
      <input type="file" name="input_gambar" id="input_gambar" />
      <button type="submit" name="submit">Upload Gambar</button>
    </form>
    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // $errors = [];

            // $target_dir = "C:/xampp/htdocs/yuk_iuran/gambar/";
            // $filename = explode('.', basename($_FILES['input_gambar']['name']));
            // $nama_baru = 'tes' . '.' . end($filename);
            // $target_file = $target_dir . $nama_baru;

            // $tipe_file = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // if ($_FILES['input_gambar']['size'] > 1000000){
            //     $errors[] = "Ukuran gambar terlalu besar, maksimal 500 KB";
            // }

            // if (!in_array($tipe_file, ['jpg', 'jpeg', 'png'])){
            //     $errors[] = 'Format gambar error, hanya menerima gambar dengan format .jpg, .jpeg dan .png';
            // }

            // if (file_exists($target_file)){
            //     $errors[] = 'Duplikat file';
            // }
            

            // if (empty($errors)){
            //     if (move_uploaded_file($_FILES['input_gambar']['tmp_name'], $target_file)){
            //         echo 'File telah berhasil diupload';
            //     } else {
            //         echo 'File gagal diupload';
            //     }
            // } else {
            //     echo '<ul type="none">';
            //     foreach($errors as $text){
            //         echo '<li>' . $text . '</li>';
            //     }
            //     echo '</ul>';
            // }

            $dbc = mysqli_connect('localhost', 'root', '', 'tes_gambar');

            $file = addslashes(file_get_contents($_FILES["input_gambar"]["tmp_name"]));

            if ($_FILES['input_gambar']['size'] > 500000){
                $errors[] = "Ukuran gambar terlalu besar, maksimal 500 KB";
            }

            $filename = explode('.', basename($_FILES['input_gambar']['name']));

            var_dump($filename);  exit;

            
            if (!in_array($filename[1], ['jpg', 'jpeg', 'png'])){
                $errors[] = 'Format gambar error, hanya menerima gambar dengan format .jpg, .jpeg dan .png';
            }

            $q = "INSERT INTO gambar (gambar) VALUES ('$file')";
            if (mysqli_query($dbc, $q)){
                echo 'Data telah disimpan';
            }

            $q = "SELECT * FROM gambar ORDER BY id";
            $r = mysqli_query($dbc, $q);
            
            echo '<table>';
            while($row = mysqli_fetch_array($r)){
                echo '<tr>
                      <td><img src="data:image/jpeg;base64,'.base64_encode($row['gambar']) . '" /></td></tr>';
            }

        }
    ?>
  </body>
</html>