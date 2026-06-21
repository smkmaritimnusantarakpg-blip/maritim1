<?php
// 1. Koneksi ke Database
$host     = "localhost";
$user     = "root";
$pass     = "";
$db       = "db_surat";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. Logika Pencarian
$keyword = "";
$query   = "SELECT * FROM surat_keluar ORDER BY id DESC"; // Default: Tampilkan semua, data terbaru di atas

if (isset($_POST['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_POST['keyword']);
    
    // Cari berdasarkan nomor surat, perihal, tujuan, atau instansi
    $query = "SELECT * FROM surat_keluar WHERE 
              nomor_surat_lengkap LIKE '%$keyword%' OR 
              perihal LIKE '%$keyword%' OR 
              tujuan LIKE '%$keyword%' OR 
              instansi LIKE '%$keyword%' 
              ORDER BY id DESC";
}

$baca_data = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Nomor Surat</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: #f4f6f9; 
            padding: 30px; 
            margin: 0; 
        }
        .container { 
            max-width: 1000px; 
            background: #fff; 
            margin: 0 auto; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        h2 { 
            color: #333; 
            margin-top: 0; 
        }
        
        /* Style Area Fitur Atas */
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            flex-wrap: wrap; 
            gap: 10px; 
        }
        .form-cari input[type="text"] { 
            padding: 8px 12px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            width: 250px; 
            font-size: 14px; 
        }
        .form-cari button { 
            padding: 8px 15px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 14px; 
        }
        .form-cari button:hover { 
            background-color: #0056b3; 
        }
        .btn-tambah { 
            padding: 9px 15px; 
            background-color: #28a745; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 14px;
            display: inluine-flex;
            align-items: center;
            transition: all 0.2s ease; 
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
        }
        .btn-tambah:hover { 
            background-color: #218838; 
        }
        .btn-excel {
            padding: 9px 15px; 
            background-color: #28a745; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 14px; 
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
        }
        .btn-excel:hover {
            background-color: #1b5e20;
            transform: translateY(-1px);
        }

        /* Style Tabel */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            font-size: 14px; 
        }
        table th, table td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
        }
        table th { 
            background-color: #f8f9fa; 
            color: #333; 
            font-weight: bold; 
        }
        table tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        table tr:hover { 
            background-color: #f1f1f1; 
        }
        
        .badge-nomor { 
            background-color: #e2f0d9; 
            color: #385723; 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-weight: bold; 
            font-family: monospace; 
            font-size: 13px; 
            border: 1px solid #bcd6ad; 
        }
        .text-kosong { 
            text-align: center; 
            color: #777; 
            font-style: italic; 
            padding: 20px; 
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Rekapan Nomor Surat Keluar</h2>
    
    <div class="top-bar">
        <a href="index.php" class="btn-tambah">+ Buat Nomor Surat Baru</a>
        <a href="export_excel.php?keyword=<?= urlencode($keyword); ?>" class="btn-excel">
            📥 Unduh Manifes Excel
        </a>
        <div class="form-cari">
            <form action="" method="POST">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari nomor, perihal, tujuan...">
                <button type="submit" name="cari">Cari</button>
                <?php if ($keyword != ""): ?>
                    <a href="rekapan.php" style="margin-left: 5px; color: #666; font-size: 13px;">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">No. Urut</th>
                <th width="35%">Nomor Surat Lengkap</th>
                <th width="15%">Perihal</th>
                <th width="15%">Tujuan</th>
                <th width="10%">Instansi</th>
                <th width="10%">Tgl Kirim</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if (mysqli_num_rows($baca_data) > 0):
                while ($row = mysqli_fetch_assoc($baca_data)):
                    $tanggal_indo = date('d-m-Y', strtotime($row['tanggal_kirim']));
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nomor_urut']; ?></td>
                        <td><span class="badge-nomor"><?= $row['nomor_surat_lengkap']; ?></span></td>
                        <td><?= $row['perihal']; ?></td>
                        <td><?= $row['tujuan']; ?></td>
                        <td><?= $row['instansi']; ?></td>
                        <td><?= $tanggal_indo; ?></td>
                    </tr>
                <?php 
                endwhile;
            else: 
                ?>
                <tr>
                    <td colspan="7" class="text-kosong">Belum ada data surat atau data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>