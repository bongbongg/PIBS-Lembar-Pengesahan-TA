<?php

// Query untuk menghitung jumlah status 'Menunggu Verifikasi'
$sqlWaiting = "
    SELECT COUNT(*) AS count 
    FROM pengajuan 
    WHERE 
        (
            dosen_pembimbing = '$idDosen' AND status_pembimbing = 'menunggu' OR
            dosen_penguji1 = '$idDosen' AND status_penguji1 = 'menunggu' OR
            dosen_penguji2 = '$idDosen' AND status_penguji2 = 'menunggu' OR
            ka_prodi = '$idDosen' AND status_ka_prodi = 'menunggu' OR
            dekan = '$idDosen' AND status_dekan = 'menunggu'
        )
";

$sqlAccepted = "
    SELECT COUNT(*) AS count 
    FROM pengajuan 
    WHERE 
        (
            dosen_pembimbing = '$idDosen' AND status_pembimbing = 'disetujui' OR
            dosen_penguji1 = '$idDosen' AND status_penguji1 = 'disetujui' OR
            dosen_penguji2 = '$idDosen' AND status_penguji2 = 'disetujui' OR
            ka_prodi = '$idDosen' AND status_ka_prodi = 'disetujui' OR
            dekan = '$idDosen' AND status_dekan = 'disetujui'
        )
";

$sqlRejected = "
    SELECT COUNT(*) AS count 
    FROM pengajuan 
    WHERE 
        (
            dosen_pembimbing = '$idDosen' AND status_pembimbing = 'ditolak' OR
            dosen_penguji1 = '$idDosen' AND status_penguji1 = 'ditolak' OR
            dosen_penguji2 = '$idDosen' AND status_penguji2 = 'ditolak' OR
            ka_prodi = '$idDosen' AND status_ka_prodi = 'ditolak' OR
            dekan = '$idDosen' AND status_dekan = 'ditolak'
        )
";

// Eksekusi query
$resultWaiting = $conn->query($sqlWaiting)->fetch_assoc();
$resultAccepted = $conn->query($sqlAccepted)->fetch_assoc();
$resultRejected = $conn->query($sqlRejected)->fetch_assoc();

$waitingCount = $resultWaiting['count'];
$acceptedCount = $resultAccepted['count'];
$rejectedCount = $resultRejected['count'];
?>

<link rel="stylesheet" href="css/styles.css">
<aside class="aside-dosen">
  <h2>Dashboard Dosen</h2>
  <section class="info-box-wrapper">
    <div class="info-box waiting">
      <h3>Menunggu Verifikasi</h3>
      <p class="count"><?= $waitingCount ?> Orang</p>
    </div>
    <div class="info-box accepted">
      <h3>Diterima</h3>
      <p class="count"><?= $acceptedCount ?> Orang</p>
    </div>
    <div class="info-box rejected">
      <h3>Ditolak</h3>
      <p class="count"><?= $rejectedCount ?> Orang</p>
    </div>
  </section>
</aside>
