ATURAN

1. BATASAN AKSES PENGGUNA
  - ADMIN :
    - Data akun : Lihat, tambah, ubah, hapus hanya CUSTOMER
    - Data customer : Lihat, tambah, ubah, hapus semua
    - Data permintaan pengujian : Lihat, tambah, ubah, hapus semua
    - Data kaji ulang : Lihat, tambah, ubah, hapus semua
    - Data pengujian : Lihat, tambah, ubah, hapus semua
    - Data unsur kaji ulang : Lihat, tambah, ubah, hapus semua

  - CUSTOMER :
    - Data akun : Lihat, tambah, ubah, hapus milik sendiri
    - Data customer : Lihat, tambah, ubah, hapus milik sendiri
    - Data permintaan pengujian : Lihat, tambah, hapus milik sendiri

2. NILAI PENENTU HAK AKSES
  - Menggunakan angka
  - 1 -> admin
  - 2 -> customer

3. BATASAN INPUT
  - Nama lengkap :
    -> Hanya huruf a-z dan A-Z
    -> Min 3 karakter
    -> Maks 150 karakter
  
  - Nomor HP :
    -> Hanya angka 0-9
    -> Min 3 karakter
    -> Maks 20 karakter

  - Provinsi, kabupaten, kecamatan, kelurahan :
    -> Seperti di database
  
  - Kodepos :
    -> Hanya angka
    -> Min dan Maks 5 karakter

  - Alamat :
    -> Teks
    -> Min 5 karakter
    -> Maks 255 karakter
  
  -> Email :
    -> Teks
    -> Min 5 karakter (a@b.c)
    -> Maks 150 karakter
  
  -> Kata Sandi :
    -> Teks
    -> Min 4 karakter
    -> Maks 40 karakter