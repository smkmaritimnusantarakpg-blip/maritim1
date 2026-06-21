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

// 2. Fungsi Mengubah Angka Bulan menjadi Romawi
function getRomawi($bln) {
    $romawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    return $romawi[(int)$bln];
}

// 3. Ambil Nomor Urut Terakhir Secara Otomatis dari Database
$query_cek = "SELECT MAX(CAST(nomor_urut AS UNSIGNED)) AS urutan_terakhir FROM surat_keluar";
$hasil_cek = mysqli_query($koneksi, $query_cek);
$data_cek  = mysqli_fetch_assoc($hasil_cek);

$angka_berikutnya = $data_cek['urutan_terakhir'] ? $data_cek['urutan_terakhir'] + 1 : 1;
$no_urut_otomatis = sprintf("%03s", $angka_berikutnya);


// 4. Logika Utama Saat Form di-Submit
$nomor_surat_final = "";
if (isset($_POST['generate_dan_simpan'])) {
    
    $no_urut        = sprintf("%03s", $_POST['no_urut']); 
    $perihal_pilihan= $_POST['perihal']; 
    $tujuan         = mysqli_real_escape_string($koneksi, $_POST['tujuan']); 
    $instansi       = $_POST['instansi'];
    $semester       = $_POST['semester'];
    $tanggal_kirim  = $_POST['tanggal_kirim'];

    $pecah_perihal  = explode('|', $perihal_pilihan);
    $nama_perihal   = $pecah_perihal[0];
    $singkatan_ph   = $pecah_perihal[1];

    $timestamp      = strtotime($tanggal_kirim);
    $bulan_romawi   = getRomawi(date('n', $timestamp));
    $tahun          = date('Y', $timestamp);

    $nomor_surat_final = "$no_urut/$singkatan_ph/$instansi/$semester/$bulan_romawi/$tahun";

    $query_simpan = "INSERT INTO surat_keluar (nomor_urut, nomor_surat_lengkap, perihal, tujuan, instansi, semester, tanggal_kirim) 
                     VALUES ('$no_urut', '$nomor_surat_final', '$nama_perihal', '$tujuan', '$instansi', '$semester', '$tanggal_kirim')";
    
    if (mysqli_query($koneksi, $query_simpan)) {
        echo "<script>alert('Sukses! Nomor Surat Berhasil Dibuat dan Disimpan: \\n$nomor_surat_final'); window.location='index.php';</script>";
    } else {
        echo "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Nomor Surat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #0f172a;
            --secondary-hover: #1e293b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --input-bg-readonly: #f1f5f9;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main);
            padding: 40px 20px; 
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container { 
            width: 100%;
            max-width: 540px; 
            background: var(--bg-card); 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .header-area {
            text-align: center;
            margin-bottom: 32px;
        }

        h2 { 
            margin: 0 0 8px 0; 
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
            letter-spacing: -0.5px;
        }

        .subtitle {
            margin: 0;
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-group { 
            margin-bottom: 20px; 
        }

        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 500; 
            font-size: 14px;
            color: var(--text-main); 
        }

        input[type="text"], input[type="date"], select { 
            width: 100%; 
            padding: 12px 16px; 
            border: 1px solid var(--border); 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 14px; 
            font-family: inherit;
            color: var(--text-main);
            background-color: #fff;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus, input[type="date"]:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        input[readonly] { 
            background-color: var(--input-bg-readonly); 
            color: var(--text-muted); 
            font-weight: 600; 
            cursor: not-allowed; 
        }

        /* Layout Tombol Modern */
        .btn-group { 
            display: flex; 
            flex-direction: column;
            gap: 12px; 
            margin-top: 32px; 
        }

        .btn-main { 
            width: 100%;
            padding: 14px; 
            background-color: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: 600; 
            font-family: inherit;
            cursor: pointer; 
            transition: background-color 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-main:hover { 
            background-color: var(--primary-hover); 
        }

        .btn-main:active {
            transform: scale(0.98);
        }

        .btn-navigasi { 
            width: 100%;
            text-align: center; 
            padding: 14px; 
            background-color: transparent; 
            color: var(--secondary); 
            border: 1px solid var(--border);
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: 600; 
            text-decoration: none; 
            box-sizing: border-box; 
            display: inline-block;
            transition: all 0.2s ease;
        }

        .btn-navigasi:hover { 
            background-color: #f8fafc;
            border-color: var(--text-muted);
        }

        /* Responsivitas untuk layar tablet ke atas (tombol sejajar kembali) */
        @media (min-width: 480px) {
            .btn-group {
                flex-direction: row;
            }
            .btn-main { flex: 1.4; }
            .btn-navigasi { flex: 1; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-area">
        <h2>Penomoran Surat</h2>
        <p class="subtitle">Generet Nomor Surat Maritim</p>
    </div>

    <form action="" method="POST">
        
        <div class="form-group">
            <label for="no_urut">Nomor Urut Sistem</label>
            <input type="text" id="no_urut" name="no_urut" value="<?php echo $no_urut_otomatis; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="perihal">Perihal & Singkatan</label>
            <select id="perihal" name="perihal" required>
                <option value="">Pilih klasifikasi perihal...</option>
                <option value="Surat Undangan|UND">Surat Undangan (UND)</option>
                <option value="Surat Keputusan|SK">Surat Keputusan (SK)</option>
                <option value="Surat Tugas|ST">Surat Tugas (ST)</option>
                <option value="Surat Keterangan|KET">Surat Keterangan (KET)</option>
                <option value="Nota Dinas|ND">Nota Dinas (ND)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="tujuan">Tujuan Surat</label>
            <input type="text" id="tujuan" name="tujuan" required placeholder="Nama instansi / jabatan / perorangan">
        </div>

        <div class="form-group">
            <label for="instansi">Instansi / Unit Pengirim</label>
            <select id="instansi" name="instansi" required>
                <option value="">Pilih unit pengirim...</option>
                <option value="DINKES">Dinas Kesehatan (DINKES)</option>
                <option value="DISDIK">Dinas Pendidikan (DISDIK)</option>
                <option value="KEC-SMG">Kecamatan Semarang (KEC-SMG)</option>
                <option value="INTERNAL-HRD">Internal HRD (HRD)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="semester">Semester</label>
            <select id="semester" name="semester" required>
                <option value="">Pilih rentang semester...</option>
                <option value="SMT1">Semester 1 (SMT1)</option>
                <option value="SMT2">Semester 2 (SMT2)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="tanggal_kirim">Tanggal Kirim</label>
            <input type="date" id="tanggal_kirim" name="tanggal_kirim" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="btn-group">
            <button type="submit" name="generate_dan_simpan" class="btn-main">Generate & Simpan</button>
            <a href="rekapan.php" class="btn-navigasi">Lihat Rekapan</a>
        </div>
    </form>
    </form> <hr style="margin-top: 30px; border: 0; border-top: 1px solid var(--border);">
    <div style="margin-top: 20px; text-align: center;">
        </form> <hr style="margin-top: 30px; border: 0; border-top: 1px solid var(--border);">
    <div style="margin-top: 20px; text-align: center;">
        <form action="aksi_surat.php" method="POST" onsubmit="return confirm('PERINGATAN! Tindakan ini akan menghapus SELURUH data surat secara permanen dan mengembalikan nomor ke 001. Apakah Anda yakin?');">
            <button type="submit" name="reset_total" style="background-color: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; width: 100%; transition: background 0.2s;">
                ⚠️ Reset Sistem & Mulai dari 001
            </button>
        </form>
    </div>