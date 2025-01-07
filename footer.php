<?php

// Ambil data footer dari tabel footer
$stmt = $conn->prepare("SELECT instagram, twitter, facebook, copyright_text, heading, tagline FROM footer WHERE id = 1");
if ($stmt === false) {
    die('Query preparation failed: ' . $conn->error);
}

$stmt->execute();
$footer_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>
<link rel="stylesheet" href="css/styles.css">

<footer class="footer">
    <div class="footer-content">
         <div class="social-links">
        <!-- Bagian Kiri: Social Links -->
           <p>
                Instagram: 
                <a href="<?php echo htmlspecialchars($footer_data['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">@upj_bintaro</a><br>
                Twitter: 
                <a href="<?php echo htmlspecialchars($footer_data['twitter']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter">@upj_bintaro</a><br>
                Facebook: 
                <a href="<?php echo htmlspecialchars($footer_data['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">@upj_Bintaro</a>
            </p>
        </div>

        <!-- Bagian Tengah: Copyright Information -->
        <div class="copyright">
            <p><?php echo htmlspecialchars($footer_data['copyright_text']); ?></p>
        </div>

        <!-- Bagian Kanan: Additional Footer Information -->
        <div class="footer-info">
            <h3><?php echo htmlspecialchars($footer_data['heading']); ?></h3>
            <p><?php echo htmlspecialchars($footer_data['tagline']); ?></p>
        </div>
    </div>
</footer>
