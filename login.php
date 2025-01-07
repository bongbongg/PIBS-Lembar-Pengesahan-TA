<?php
// Memasukkan file koneksi
include 'koneksi.php';
include 'header.php';

// Inisialisasi pesan
$message = "";

// CSRF Token untuk menghindari serangan CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi token CSRF
    if ($_POST['csrf_token'] != $_SESSION['csrf_token']) {
        $message = "Token CSRF tidak valid!";
    } else {
        $identifier = $_POST['identifier'];
        $password = $_POST['password'];

        // Validasi input
        if (empty($identifier) || empty($password)) {
            $message = "Identitas dan Password wajib diisi.";
        } else {
            // Cek di tabel dosen terlebih dahulu
            $sql = "SELECT * FROM dosen WHERE idDosen = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                // Jika ditemukan di tabel dosen
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Simpan informasi dosen ke session
                    $_SESSION['idDosen'] = $row['idDosen'];
                    $_SESSION['nama_dosen'] = $row['nama_dosen'];
                    header("Location: verifikasi.php");
                    exit;
                } else {
                    $message = "Password salah.";
                }
            } else {
                // Jika tidak ditemukan di tabel dosen, cek tabel mahasiswa
                $sql = "SELECT * FROM mahasiswa WHERE nim = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $identifier);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    // Jika ditemukan di tabel mahasiswa
                    $row = $result->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        // Simpan informasi mahasiswa ke session
                        $_SESSION['nim'] = $row['nim'];
                        $_SESSION['nama'] = $row['nama'];
                        $_SESSION['program_studi'] = $row['program_studi'];
                        header("Location: pengajuan.php");
                        exit;
                    } else {
                        $message = "Password salah.";
                    }
                } else {
                    $message = "Identitas tidak ditemukan.";
                }
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2 style="text-align: center;">Login</h2>
    <form method="POST" action="">
        <label for="identifier">ID Dosen / NIM:</label>
        <input type="text" id="identifier" name="identifier" placeholder="Masukkan ID Dosen atau NIM" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="submit" value="Login">
    </form>
    <?php if (!empty($message)) { ?>
        <p class="message"><?php echo $message; ?></p>
    <?php } ?>
</body>
</html>

<?php
include 'footer.php';
// Tutup koneksi
$conn->close();
?>
