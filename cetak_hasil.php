<?php
require 'koneksi.php'; // Pastikan file koneksi sudah benar

// Query untuk mendapatkan data pengajuan yang sudah disetujui
$query = "SELECT p.judul, p.file, p.tanggal_pengajuan, p.status, 
                 m.nim, m.nama AS nama_mahasiswa, m.program_studi, m.status_bimbingan, 
                 d1.nama_dosen AS dosen_pembimbing, d2.nama_dosen AS dosen_penguji1, 
                 d3.nama_dosen AS dosen_penguji2, d4.nama_dosen AS ka_prodi, 
                 d5.nama_dosen AS dekan, p.status_pembimbing, p.status_penguji1, 
                 p.status_penguji2, p.status_ka_prodi, p.status_dekan
          FROM pengajuan p
          JOIN mahasiswa m ON p.nim = m.nim
          JOIN dosen d1 ON p.dosen_pembimbing = d1.idDosen
          JOIN dosen d2 ON p.dosen_penguji1 = d2.idDosen
          JOIN dosen d3 ON p.dosen_penguji2 = d3.idDosen
          JOIN dosen d4 ON p.ka_prodi = d4.idDosen
          JOIN dosen d5 ON p.dekan = d5.idDosen
          WHERE p.status = 'Disetujui'";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengesahan Pengajuan</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="content">
        <div class="header">
            <h1>Surat Pengesahan Pengajuan</h1>
            <h2>Data Pengajuan yang Disetujui</h2>
        </div>

        <a href="#" class="btn-print" onclick="window.print()">Cetak Halaman</a>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='data'>";
            echo "<p><strong>Nomor Induk Mahasiswa (NIM):</strong> " . htmlspecialchars($row['nim']) . "</p>";
            echo "<p><strong>Nama Mahasiswa:</strong> " . htmlspecialchars($row['nama_mahasiswa']) . "</p>";
            echo "<p><strong>Program Studi:</strong> " . htmlspecialchars($row['program_studi']) . "</p>";
            echo "<p><strong>Status Bimbingan:</strong> " . htmlspecialchars($row['status_bimbingan']) . "</p>";
            echo "<p><strong>Judul Pengajuan:</strong> " . htmlspecialchars($row['judul']) . "</p>";
            echo "<p><strong>Dosen Pembimbing:</strong> " . htmlspecialchars($row['dosen_pembimbing']) . "</p>";
            echo "<p><strong>Dosen Penguji 1:</strong> " . htmlspecialchars($row['dosen_penguji1']) . "</p>";
            echo "<p><strong>Dosen Penguji 2:</strong> " . htmlspecialchars($row['dosen_penguji2']) . "</p>";
            echo "<p><strong>Kepala Program Studi:</strong> " . htmlspecialchars($row['ka_prodi']) . "</p>";
            echo "<p><strong>Dekan:</strong> " . htmlspecialchars($row['dekan']) . "</p>";
            echo "<p><strong>Tanggal Pengajuan:</strong> " . htmlspecialchars($row['tanggal_pengajuan']) . "</p>";
            echo "</div>";

            echo "<div class='signature'>";
            echo "<div class='row'>";
                echo "<div><strong>Dosen Pembimbing</strong><br><hr><p>" . htmlspecialchars($row['dosen_pembimbing']) . "</p></div>";
                echo "<div><strong>Dosen Penguji 1</strong><br><hr><p>" . htmlspecialchars($row['dosen_penguji1']) . "</p></div>";
                echo "<div><strong>Dosen Penguji 2</strong><br><hr><p>" . htmlspecialchars($row['dosen_penguji2']) . "</p></div>";
            echo "</div>";
            echo "<div class='row'>";
                echo "<div><strong>Kepala Program Studi</strong><br><hr><p>" . htmlspecialchars($row['ka_prodi']) . "</p></div>";
                echo "<div><strong>Dekan</strong><br><hr><p>" . htmlspecialchars($row['dekan']) . "</p></div>";
            echo "</div>";
        echo "</div>";
        
        }
        ?>
    </div>
</body>
</html>
