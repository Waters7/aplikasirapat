# Dashboard Rapat – Poltekkes Kemenkes Manado

Aplikasi ini menggunakan Laravel 12, PHP 8.2 atau lebih baru, dan MySQL. Sumber aplikasi PHP lama sudah dihapus. Folder `uploads/` di root proyek tetap dipakai Laravel untuk menyimpan berkas rapat lama dan unggahan baru.

## Menjalankan di komputer ini

### Uji lokal Windows tanpa MySQL

Klik dua kali `JALANKAN-LOKAL.cmd` di folder proyek, lalu buka http://127.0.0.1:8000.
Biarkan jendela terminal terbuka; tekan Ctrl+C untuk menghentikan server.
Jika port tersebut sudah digunakan, jalankan `JALANKAN-LOKAL.cmd -Port 8001`.

Peluncur mendeteksi PHP Laragon, PHP di PATH, atau XAMPP. Lokasi lain dapat dipilih
dengan `-PhpPath "C:\lokasi\php.exe"`. Mode ini memakai `.env.preview` dan database
`laravel-app/database/preview.sqlite` terpisah; konfigurasi `.env` MySQL tetap disimpan.
Pada instalasi baru, login `admin` / `password`. Mode ini untuk pengujian lokal;
data pegawai dan rapat lama tidak disertakan dalam ZIP dan memerlukan backup MySQL.
Menjalankan ulang peluncur mempertahankan database dan kunci aplikasi uji.

Arsip ini sudah menyertakan dependensi dan aset. Untuk pemasangan dari kode sumber,
jalankan `composer install`, `npm ci`, dan `npm run build` di `laravel-app` dahulu.
PHP minimal 8.2 dengan `pdo_sqlite` diperlukan untuk mode uji. Aset saat ini memerlukan
Node.js 20.19+ pada seri 20, atau 22.12+; pemeriksaan dilakukan dengan Node.js 22.23.2.

### Menggunakan database MySQL

1. Jalankan MySQL melalui Laragon atau XAMPP. Apache tidak diperlukan untuk `artisan serve`.
2. Buka terminal di folder `laravel-app/`.
3. Jika dependensi belum tersedia, jalankan `composer install` dan `npm install`.
4. Buat database `rapat_poltekkes` jika belum tersedia, atau pulihkan backup database lama. Pastikan kredensial di `.env` sesuai MySQL Anda (contoh XAMPP: host `127.0.0.1`, port `3306`, user `root`, password kosong). Pada instalasi baru, salin `.env.example` menjadi `.env` dan jalankan `php artisan key:generate` hanya jika `APP_KEY` masih kosong.
5. Jalankan `php artisan migrate --force`, `php artisan db:seed --force`, dan `npm run build`.
6. Jalankan `php artisan serve --host=127.0.0.1 --port=8000`, lalu buka `http://127.0.0.1:8000/`.

`composer dev` juga menjalankan server PHP tersebut. Jalankan `npm run build` setelah
mengubah aset, atau `npm run dev` di terminal kedua untuk pengembangan tampilan.

### Hasil troubleshooting 26 Oktober 2026

- `composer dev` diperbaiki: sebelumnya memanggil `artisan dev`, yang tidak tersedia.
- PHP 8.4.25 Laragon dapat menjalankan aplikasi; PHP/Composer belum tersedia di PATH terminal biasa.
- Build Vite berhasil. Semua 44 pengujian yang disertakan lulus (401 assertions), menggunakan SQLite untuk pengujian.
- Peluncur Windows berhasil menjalankan migrasi dan seeder SQLite, termasuk saat dijalankan ulang tanpa mengganti kunci aplikasi. Pemeriksaan HTTP dashboard, login, Live Board, data Live Board, health check, dan aset CSS/JS semuanya menghasilkan status 200. Login akun awal dan akses halaman admin juga berhasil.
- MySQL di `127.0.0.1:3306` belum aktif saat pemeriksaan; koneksi ditolak. Integrasi dengan database MySQL lama belum diverifikasi.
- Error izin tulis cache/log saat pengujian awal berasal dari sandbox pemeriksaan; pengujian berhasil di luar sandbox tanpa perubahan izin folder.
- PHP 8.3 Laragon mengeluarkan peringatan `mysqli is already loaded`; peluncur di komputer ini memilih PHP 8.4 yang tidak mengeluarkan peringatan tersebut.

Migrasi tidak menghapus atau mengisi ulang data aplikasi yang sudah ada. Seeder hanya menambahkan nilai pengaturan/ruang yang belum tersedia dan akun Super Admin awal jika tabel akun lama kosong. Pada instalasi baru, login awal adalah username `admin` / kata sandi `password`; segera ganti sebelum aplikasi dipakai.

Waktu mulai dan selesai rapat ditulis dalam format 24 jam `HH:MM` (misalnya `13:30` atau `16:00`). Live Board menampilkan durasi rapat berjalan dalam jam, menit, dan detik (misalnya `30 menit 20 detik` atau `1 jam 20 menit 20 detik`).

Semua akun masuk melalui satu halaman Login tersendiri menggunakan NIP (pegawai/Pengelola Rapat/Super Admin) atau username (akun Super Admin lama); tombol Login tersedia di bagian atas dashboard dan formulir tidak ditampilkan di dashboard. Tingkatan akses adalah **Pegawai → Pengelola Rapat → Super Admin**. Super Admin dapat mengatur role dari halaman **Pegawai**. Pada tab **Hak Akses**, daftar Super Admin ditampilkan terpisah dan centang fitur Pengelola Rapat berlaku global untuk semua akun Pengelola Rapat. Super Admin dapat mengakhiri rapat berjalan secara manual dari dashboard; tindakan tersebut langsung menutup rapat dan membuka kode absen pulang selama 30 menit. Pegawai yang hadir tetapi belum mengisi kode pulang akan berstatus **Belum Absen Pulang** dan dapat mengisi keterangan/alasan dari detail rapat; keterangan tersebut ditampilkan pada laporan Pengelola Rapat/Super Admin. Akun pegawai dengan role Pengelola Rapat dapat berpindah antara mode Pegawai dan Pengelola Rapat menggunakan tombol **Beralih ke mode**.

Saat membuat atau mengubah rapat, pilih **Rapat untuk semua pegawai** atau kosongkan pilihan tersebut dan centang pegawai yang wajib mengikuti. Jika memilih pegawai tertentu, minimal satu nama harus dicentang; daftar undangan ditampilkan di detail rapat. Hanya Super Admin yang dapat membuat, memperbarui, memperpanjang, atau menutup kode absen masuk dan kode absen pulang. Pengelola Rapat dengan izin presensi dapat memantau daftar hadir dan pulang, tetapi tidak dapat mengatur kodenya. Kode pulang biasa hanya dapat digunakan setelah rapat selesai, dengan masa berlaku yang dapat diatur Super Admin menjadi 15, 30, atau 60 menit. Pegawai biasa tidak dapat membuat rapat atau menambahkan materi, rekaman, maupun notulen. Di halaman **Pegawai**, Super Admin dapat memilih role Pegawai, Pengelola Rapat, atau Super Admin.

Untuk akun yang dibuat dari daftar pegawai maupun akun baru yang ditambahkan Pengelola Rapat, kata sandi awal adalah NIP dan wajib diganti pada login pertama. Setelah itu, pegawai tidak dapat mengubah kata sandi sendiri; Pengelola Rapat dapat mengatur ulang kata sandi melalui **Pegawai**, dan pegawai wajib menggantinya lagi setelah login.

## Menjalankan melalui Apache XAMPP

Arahkan DocumentRoot virtual host Apache ke `laravel-app/public/`, bukan ke root proyek, folder `laravel-app/`, atau `uploads/`. Aktifkan `mod_rewrite` dan ekstensi PHP `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`, serta `session`. Untuk unggahan video, atur `upload_max_filesize` dan `post_max_size` di `php.ini` sekurangnya 100 MB.

## Live Board publik

Di atas kalender pada dashboard tersedia **Semua ruang** dan tombol Live Board terpisah untuk setiap ruang rapat aktif (Bunaken, Dihaga, dan Siladen). Papan semua ruang menampilkan seluruh rapat aktif, sedangkan papan suatu ruang hanya menampilkan rapat yang dijadwalkan di ruang itu dengan kartu yang melebar penuh. Papan menampilkan waktu mulai, lama rapat berjalan, dan jumlah pegawai hadir; lama berjalan diperbarui setiap detik dan data rapat/presensi setiap 5 detik. Halaman dan data Live Board dapat diakses publik tanpa login dan hanya menampilkan jumlah hadir, bukan identitas pegawai. Tombol **Preview Live Board** pada kartu rapat yang sedang berlangsung hanya ditampilkan kepada pengunjung yang belum login. Gunakan **Tampilkan layar penuh** untuk presentasi; mode ini mempertahankan papan rapat dengan header ringkas berisi logo, judul, jam, dan tombol navigasi kecil. Pengelola Rapat dapat meninjau presensi rapat yang sudah selesai melalui **Kehadiran & Izin** atau **Rapat**; catatan rapat selesai hanya dapat ditinjau, bukan dibuka ulang untuk presensi. Pada tab **Rapat**, empat rapat selesai terbaru ditampilkan terlebih dahulu; gunakan kolom pencarian untuk mencari berdasarkan judul, tanggal, atau tempat. Tombol **Tampilkan rapat selesai lainnya** membuka daftar lengkap atau menciutkannya kembali.

## Data dan berkas

- Data rapat, pegawai, kehadiran, dan pengaturan disimpan di database MySQL `rapat_poltekkes`.
- Berkas rapat tersimpan di folder `uploads/` di root proyek. Folder dan berkas di dalamnya jangan dihapus; Laravel mengaksesnya melalui route yang memeriksa autentikasi.
- Folder `laravel-app/database/` berisi migrasi dan seeder Laravel, bukan salinan database MySQL.
- Cadangkan database dan folder `uploads/` secara berkala. Jangan menghapus database MySQL ketika membersihkan berkas proyek.
