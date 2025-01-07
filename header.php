<?php
// Retrieve header data from the database
$stmt = $conn->prepare("SELECT * FROM header LIMIT 1");
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<link rel="stylesheet" href="css/styles.css">

<header class="header">
    <div class="logo-container">
        <!-- Displaying the logo -->
        <img src="<?php echo htmlspecialchars($header['logo_path']); ?>" alt="Logo UPJ" class="logo">
    </div>
    
    <div class="header-info">
        <!-- Displaying the title -->
        <h1 class="title"><?php echo htmlspecialchars($header['title']); ?></h1>
        
        <!-- Displaying the slogan -->
        <p class="slogan"><?php echo htmlspecialchars($header['slogan']); ?></p>
        
        <!-- Displaying the address -->
        <p class="address"><?php echo htmlspecialchars($header['address']); ?></p>
    </div>
</header>
