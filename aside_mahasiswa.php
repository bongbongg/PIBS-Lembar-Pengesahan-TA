<?php
// Ambil data pengajuan berdasarkan NIM mahasiswa yang login
$query = "SELECT * FROM pengajuan WHERE nim = '$NIM' ORDER BY tanggal_pengajuan DESC LIMIT 1";
$result = $conn->query($query);

$data = $result->fetch_assoc();
?>

<link rel="stylesheet" href="css/styles.css">
<aside class="aside-mahasiswa">
    <h2>Ringkasan Pengajuan</h2>

    <?php if ($data): ?>
        <!-- Ringkasan Status Pengajuan -->
        <div class="ringkasan-status">
            <h3>Judul:</h3>
            <p><?= htmlspecialchars($data['judul']); ?></p>

            <h3>Tanggal Pengajuan:</h3>
            <p><?= htmlspecialchars($data['tanggal_pengajuan']); ?></p>

            <h3>Status:</h3>
            <p class="status <?= strtolower($data['status']); ?>">
                <?= htmlspecialchars($data['status']); ?>
            </p>
        </div>

        <!-- Progress Persetujuan -->
        <div class="progress-persetujuan">
            <h3>Progress Persetujuan</h3>
            <ul>
                <li>Dosen Pembimbing: <span class="status <?= strtolower($data['status_pembimbing']); ?>"> <?= htmlspecialchars($data['status_pembimbing']); ?></span></li>
                <li>Dosen Penguji 1: <span class="status <?= strtolower($data['status_penguji1']); ?>"> <?= htmlspecialchars($data['status_penguji1']); ?></span></li>
                <li>Dosen Penguji 2: <span class="status <?= strtolower($data['status_penguji2']); ?>"> <?= htmlspecialchars($data['status_penguji2']); ?></span></li>
                <li>Ka Prodi: <span class="status <?= strtolower($data['status_ka_prodi']); ?>"> <?= htmlspecialchars($data['status_ka_prodi']); ?></span></li>
                <li>Dekan: <span class="status <?= strtolower($data['status_dekan']); ?>"> <?= htmlspecialchars($data['status_dekan']); ?></span></li>
            </ul>
        </div>

        <!-- File Preview -->
        <div class="file-preview">
            <h3>File Pengajuan:</h3>
            <?php if (!empty($data['file'])): ?>
                <a href="uploads/<?= htmlspecialchars($data['file']); ?>" target="_blank">Lihat File</a>
            <?php else: ?>
                <p>File belum diunggah.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <p>Belum ada data pengajuan.</p>
    <?php endif; ?>
</aside>
