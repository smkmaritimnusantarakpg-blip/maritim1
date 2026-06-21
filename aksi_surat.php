<?php
// 1. Koneksi ke Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_surat";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. FITUR HAPUS DATA (Dijalankan via halaman rekapan.php)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = (int)$_GET['id'];

    $query_hapus = "DELETE FROM surat_keluar WHERE id = $id";
    
    if (mysqli_query($koneksi, $query_hapus)) {
        echo "<script>alert('Data surat berhasil dihapus!'); window.location='rekapan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); window.location='rekapan.php';</script>";
    }
}

// 3. FITUR RESET TOTAL (Dijalankan via tombol reset)
if (isset($_POST['reset_total'])) {
    // TRUNCATE akan mengosongkan tabel dan mengembalikan AUTO_INCREMENT serta nomor urut ke awal (001)
    $query_reset = "TRUNCATE TABLE surat_keluar";

    if (mysqli_query($koneksi, $query_reset)) {
        echo "<script>alert('Sistem Berhasil Direset! Semua data dihapus & nomor kembali ke 001.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mereset sistem: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
}
?>