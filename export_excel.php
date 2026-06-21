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

// 2. Tangkap Keyword Pencarian (Jika ada)
$keyword = "";
$query = "SELECT * FROM surat_keluar ORDER BY id DESC"; 

if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    $query = "SELECT * FROM surat_keluar WHERE 
              nomor_surat_lengkap LIKE '%$keyword%' OR 
              perihal LIKE '%$keyword%' OR 
              tujuan LIKE '%$keyword%' OR 
              instansi LIKE '%$keyword%' 
              ORDER BY id DESC";
}
$baca_data = mysqli_query($koneksi, $query);

// 3. Set Header HTTP untuk memaksa Browser mendownload format Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Manifes_Surat_Keluar_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table border="1">
    <thead>
        <tr>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">No</th>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">No. Urut</th>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">Nomor Surat Lengkap</th>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">Perihal</th>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">Tujuan Lambung</th>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">Instansi</th>
            <th bgcolor="#1b3b6f" style="color:white; font-weight:bold;">Tgl Kirim</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (mysqli_num_rows($baca_data) > 0) {
            while ($row = mysqli_fetch_assoc($baca_data)) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                // Menggunakan x:str agar Excel membaca angka urut dengan teks 3 digit (tetap ada 00-nya)
                echo "<td style='background:#f4f7fa;' x:str>" . $row['nomor_urut'] . "</td>";
                echo "<td style='font-family:monospace;'>" . $row['nomor_surat_lengkap'] . "</td>";
                echo "<td>" . $row['perihal'] . "</td>";
                echo "<td>" . $row['tujuan'] . "</td>";
                echo "<td>" . $row['instansi'] . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['tanggal_kirim'])) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Data tidak ditemukan.</td></tr>";
        }
        ?>
    </tbody>
</table>