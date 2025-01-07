<?php
    date_default_timezone_set('Asia/Jakarta');
    require 'koneksi.php';
    include 'header.php';

    // Fitur Logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // Cek apakah dosen sudah login
    if (!isset($_SESSION['idDosen'])) {
        header('Location: login.php');
        exit;
    }

    $idDosen = $_SESSION['idDosen'];

    // Ambil data dosen
    $stmt = $conn->prepare("SELECT nama_dosen FROM dosen WHERE idDosen = ?");
    $stmt->bind_param('i', $idDosen);
    $stmt->execute();
    $result = $stmt->get_result();
    $dosen = $result->fetch_assoc();
    $stmt->close();

    // Ambil data pengajuan yang terkait dengan dosen login dengan status menunggu verifikasi
    $stmt = $conn->prepare(
        "SELECT p.idPengajuan, p.judul, p.tanggal_pengajuan, m.nama, 
        IF(p.dosen_pembimbing = ?, 'Pembimbing', 
        IF(p.dosen_penguji1 = ?, 'Penguji 1', 
        IF(p.dosen_penguji2 = ?, 'Penguji 2', 
        IF(p.ka_prodi = ?, 'Ka Prodi', 
        IF(p.dekan = ?, 'Dekan', NULL))))) AS peran,
        IF(p.dosen_pembimbing = ?, p.status_pembimbing, 
        IF(p.dosen_penguji1 = ?, p.status_penguji1, 
        IF(p.dosen_penguji2 = ?, p.status_penguji2, 
        IF(p.ka_prodi = ?, p.status_ka_prodi, 
        IF(p.dekan = ?, p.status_dekan, NULL))))) AS status_peran,
        p.status
        FROM pengajuan p 
        JOIN mahasiswa m ON p.NIM = m.NIM 
        WHERE (? IN (p.dosen_pembimbing, p.dosen_penguji1, p.dosen_penguji2, p.ka_prodi, p.dekan))
        AND (
            p.status != 'Ditolak' AND (
                (p.dosen_pembimbing = ? AND p.status_pembimbing = 'Menunggu') OR
                (p.dosen_penguji1 = ? AND p.status_penguji1 = 'Menunggu') OR
                (p.dosen_penguji2 = ? AND p.status_penguji2 = 'Menunggu') OR
                (p.ka_prodi = ? AND p.status_ka_prodi = 'Menunggu') OR
                (p.dekan = ? AND p.status_dekan = 'Menunggu')
            )
        )
        ORDER BY p.tanggal_pengajuan DESC"
    );
    $stmt->bind_param('iiiiiiiiiiiiiiii', $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen);
    $stmt->execute();
    $pengajuan_list = $stmt->get_result();
    $stmt->close();

    // Fungsi untuk menampilkan status
    function getStatus($status) {
        switch ($status) {
            case 'Disetujui':
                return '<span class="approved">Disetujui</span>';
            case 'Ditolak':
                return '<span class="rejected">Ditolak</span>';
            default:
                return '<span class="pending">Menunggu</span>';
        }
    }

    // Proses verifikasi pengajuan
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifikasi'])) {
        $idPengajuan = $_POST['id_pengajuan'];
        $status = $_POST['status'];

        $roles = [];

        $stmt = $conn->prepare("SELECT dosen_pembimbing, dosen_penguji1, dosen_penguji2, ka_prodi, dekan FROM pengajuan WHERE idPengajuan = ?");
        $stmt->bind_param('i', $idPengajuan);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            if ($result['dosen_pembimbing'] == $idDosen) {
                $roles[] = "UPDATE pengajuan SET status_pembimbing = ? WHERE idPengajuan = ?";
            }
            if ($result['dosen_penguji1'] == $idDosen) {
                $roles[] = "UPDATE pengajuan SET status_penguji1 = ? WHERE idPengajuan = ?";
            }
            if ($result['dosen_penguji2'] == $idDosen) {
                $roles[] = "UPDATE pengajuan SET status_penguji2 = ? WHERE idPengajuan = ?";
            }
            if ($result['ka_prodi'] == $idDosen) {
                $roles[] = "UPDATE pengajuan SET status_ka_prodi = ? WHERE idPengajuan = ?";
            }
            if ($result['dekan'] == $idDosen) {
                $roles[] = "UPDATE pengajuan SET status_dekan = ? WHERE idPengajuan = ?";
            }

            foreach ($roles as $query) {
                $stmt = $conn->prepare($query);
                $stmt->bind_param('si', $status, $idPengajuan);
                $stmt->execute();
                $stmt->close();
            }

            $stmt = $conn->prepare("SELECT status_pembimbing, status_penguji1, status_penguji2, status_ka_prodi, status_dekan FROM pengajuan WHERE idPengajuan = ?");
            $stmt->bind_param('i', $idPengajuan);
            $stmt->execute();
            $statuses = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (in_array('Ditolak', $statuses)) {
                $stmt = $conn->prepare("UPDATE pengajuan SET status = 'Ditolak' WHERE idPengajuan = ?");
                $stmt->bind_param('i', $idPengajuan);
                $stmt->execute();
                $stmt->close();
                $_SESSION['message'] = 'Status pengajuan diubah menjadi Ditolak karena salah satu status Ditolak.';
            } elseif (count(array_filter($statuses, function($s) { return $s === 'Disetujui'; })) === count($statuses)) {
                $stmt = $conn->prepare("UPDATE pengajuan SET status = 'Disetujui' WHERE idPengajuan = ?");
                $stmt->bind_param('i', $idPengajuan);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE mahasiswa m JOIN pengajuan p ON m.NIM = p.NIM SET m.status_bimbingan = 'Selesai' WHERE p.idPengajuan = ?");
                $stmt->bind_param('i', $idPengajuan);
                $stmt->execute();
                $stmt->close();

                $_SESSION['message'] = 'Pengajuan disetujui dan status mahasiswa diperbarui menjadi selesai.';
            } else {
                $_SESSION['message'] = 'Status pengajuan berhasil diperbarui!';
            }

            header('Location: verifikasi.php');
            exit;
        }
    }

    // Proses penghapusan laporan pengajuan
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_laporan'])) {
        $idPengajuan = $_POST['id_pengajuan'];

        // Hapus pengajuan dari tabel pengajuan
        $stmt = $conn->prepare("DELETE FROM pengajuan WHERE idPengajuan = ?");
        $stmt->bind_param('i', $idPengajuan);
        $stmt->execute();
        $stmt->close();

        // Kirim pesan ke mahasiswa bahwa laporan mereka telah dihapus
        $stmt = $conn->prepare("SELECT m.email FROM mahasiswa m JOIN pengajuan p ON m.NIM = p.NIM WHERE p.idPengajuan = ?");
        $stmt->bind_param('i', $idPengajuan);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $emailMahasiswa = $result['email'];
            // Kirim email atau pesan ke mahasiswa (gunakan library email seperti PHPMailer)
            // Misalnya menggunakan PHP mail() atau PHPMailer untuk mengirim pesan
            $subject = "Laporan Pengajuan Dihapus";
            $message = "Laporan pengajuan Anda dengan ID Pengajuan $idPengajuan telah dihapus oleh dosen. Silakan periksa status pengajuan Anda.";
            $headers = "From: no-reply@domain.com";

            // Mengirim email ke mahasiswa
            mail($emailMahasiswa, $subject, $message, $headers);
        }

        $_SESSION['message'] = 'Laporan pengajuan telah dihapus dan mahasiswa telah diberitahukan.';
        header('Location: verifikasi.php');
        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pengajuan</title>
    <link rel="stylesheet" href="css/verifikasi.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="main-content">
        <div class="content-section">
            <div class="container">
                <div class="welcome-box">
                    <p>SELAMAT DATANG</p><br>
                    <p>Nama Dosen: <?php echo htmlspecialchars($dosen['nama_dosen']); ?></p>
                </div>

                <a href="?logout=true" class="btn-logout">Logout</a>
                <?php if (isset($_SESSION['message'])): ?>
                    <p class="message"><?php echo $_SESSION['message']; ?></p>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <h3>Pengajuan Menunggu Verifikasi:</h3>
                <table>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <th>Judul</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Peran Anda</th>
                        <th>Status Anda</th>
                        <th>Verifikasi</th>
                        <th>Aksi</th>
                    </tr>
                    <?php if ($pengajuan_list && $pengajuan_list->num_rows > 0): ?>
                        <?php while ($row = $pengajuan_list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo date('H:i:s d-m-Y', strtotime($row['tanggal_pengajuan'])); ?></td>
                                <td><?php echo htmlspecialchars($row['peran']); ?></td>
                                <td><?php echo getStatus($row['status_peran']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id_pengajuan" value="<?php echo $row['idPengajuan']; ?>">
                                        <select name="status">
                                            <option value="Disetujui">Disetujui</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                        <button type="submit" name="verifikasi" class="btn">Verifikasi</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id_pengajuan" value="<?php echo $row['idPengajuan']; ?>">
                                        <button type="submit" name="hapus_laporan" class="btn btn-delete">Hapus Laporan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">Belum ada pengajuan menunggu verifikasi.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <aside>
            <?php include 'aside.php'; ?>
        </aside>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
