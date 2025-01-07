<?php
// Memasukkan file koneksi
include 'koneksi.php';
include 'header.php';

// Inisialisasi pesan
$message = "";

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role']; // Mahasiswa atau Dosen

    if ($role == 'mahasiswa') {
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $password = $_POST['password'];
        $program_studi = $_POST['program_studi'];

        // Validasi input
        if (empty($nim) || empty($nama) || empty($password) || empty($program_studi)) {
            $message = "Semua field wajib diisi.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Status bimbingan default 'belum selesai'
            $status_bimbingan = 'belum selesai';

            // Query untuk menyimpan data mahasiswa
            $sql = "INSERT INTO mahasiswa (nim, nama, password, program_studi, status_bimbingan) VALUES (?, ?, ?, ?, ?)";

            // Gunakan prepared statement untuk mencegah SQL Injection
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nim, $nama, $hashed_password, $program_studi, $status_bimbingan);

            if ($stmt->execute()) {
                // Jika berhasil, redirect ke login.php
                header("Location: login.php");
                exit;
            } else {
                $message = "Gagal menyimpan data: " . $conn->error;
            }

            $stmt->close();
        }
    } elseif ($role == 'dosen') {
        $idDosen = $_POST['idDosen'];
        $nama_dosen = $_POST['nama_dosen'];
        $password = $_POST['password'];

        // Validasi input
        if (empty($idDosen) || empty($nama_dosen) || empty($password)) {
            $message = "Semua field wajib diisi.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Query untuk menyimpan data dosen
            $sql = "INSERT INTO dosen (idDosen, nama_dosen, password) VALUES (?, ?, ?)";

            // Gunakan prepared statement untuk mencegah SQL Injection
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $idDosen, $nama_dosen, $hashed_password);

            if ($stmt->execute()) {
                // Jika berhasil, redirect ke login.php
                header("Location: login.php");
                exit;
            } else {
                $message = "Gagal menyimpan data: " . $conn->error;
            }

            $stmt->close();
        }
    } else {
        $message = "Role tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/register.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        form {
            max-width: 400px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"], input[type="password"], select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            color: red;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Form Registrasi</h2>
    <form method="POST" action="">
        <label for="role">Pilih Role:</label>
        <select id="role" name="role" required onchange="toggleFields(this.value)">
            <option value="" disabled selected>Pilih Role</option>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
        </select>

        <div id="mahasiswaFields" style="display: none;">
            <label for="nim">NIM:</label>
            <input type="text" id="nim" name="nim" placeholder="Masukkan NIM">

            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan Nama">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Masukkan Password">

            <label for="program_studi">Program Studi:</label>
            <select id="program_studi" name="program_studi">
                <option value="" disabled selected>Pilih Program Studi</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Teknik Komputer">Teknik Komputer</option>
            </select>
        </div>

        <div id="dosenFields" style="display: none;">
            <label for="idDosen">ID Dosen:</label>
            <input type="text" id="idDosen" name="idDosen" placeholder="Masukkan ID Dosen">

            <label for="nama_dosen">Nama Dosen:</label>
            <input type="text" id="nama_dosen" name="nama_dosen" placeholder="Masukkan Nama Dosen">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Masukkan Password">
        </div>

        <input type="submit" value="Daftar">
    </form>
    <?php if (!empty($message)) { ?>
        <p class="message"><?php echo $message; ?></p>
    <?php } ?>

    <script>
        function toggleFields(role) {
            document.getElementById('mahasiswaFields').style.display = (role === 'mahasiswa') ? 'block' : 'none';
            document.getElementById('dosenFields').style.display = (role === 'dosen') ? 'block' : 'none';
        }
    </script>
</body>
</html>

<?php
include 'footer.php'; 
// Tutup koneksi
$conn->close();
?>
