<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once('../includes/img_viewer.php');

$currentProductId = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$productIds = getAllProductIds();
$currentIndex = array_search($currentProductId, $productIds);

if ($currentIndex === false) {
    $base64Image = getImageAsBase64($currentProductId);
    if ($base64Image === false) {
        echo "Product not found.";
        exit();
    }
} else {
    $prevProductId = ($currentIndex > 0) ? $productIds[$currentIndex - 1] : null;
    $nextProductId = ($currentIndex < count($productIds) - 1) ? $productIds[$currentIndex + 1] : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Image Viewer - Galactic Federation Shop System</title>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Main CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* New Image Viewer UI styles */
    .image-viewer {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(77,238,234,0.3);
      padding: 1.5rem;
      margin: 20px auto;
      max-width: 800px;
      position: relative;
      overflow: hidden;
    }
    .image-viewer-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    .image-viewer-title {
      font-size: 1.5rem;
      color: var(--neon-blue);
      text-shadow: 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue);
    }
    .back-link {
      color: #007BFF;
      text-decoration: none;
      font-weight: bold;
      font-size: 0.875rem;
    }
    .back-link:hover {
      text-decoration: underline;
      color: #0056b3;
    }
    .image-container {
      position: relative;
      margin-bottom: 1.5rem;
      border-radius: 0.5rem;
      overflow: hidden;
      border: 1px solid rgba(77,238,234,0.3);
      background-color: rgba(10,10,10,0.5);
      z-index: 1;
    }
    .image-glow {
      position: absolute;
      inset: 0;
      background: linear-gradient(to right, rgba(77,238,234,0.1), rgba(57,255,20,0.1));
      filter: blur(20px);
      z-index: -1;
    }
    .img-viewer-image {
      display: block;
      width: 100%;
      height: auto;
      max-height: 80vh;
      object-fit: contain;
    }
    .navigation-buttons {
      display: flex;
      justify-content: space-between;
      margin: 20px 0;
    }
    .navigation-buttons a {
      padding: 10px 20px;
      background-color: #333;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }
    .navigation-buttons a:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <header>
    <div class="container">
      <nav>
        <div class="nav-container">
          <div class="logo">
            <h1 class="logo-text">Galactic Federation</h1>
          </div>
          <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="catalogue.php" class="nav-link">Catalogue</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="home.php" class="nav-link">Dashboard</a>
            <a href="admin.php" class="nav-link">Council of Ricks</a>
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="logout.php" class="nav-link">Dimension Exit</a>
            <?php else: ?>
              <a href="register.php" class="nav-link">Register</a>
              <a href="login.php" class="nav-link">Login</a>
            <?php endif; ?>
          </div>
        </div>
      </nav>
    </div>
  </header>


  <!-- Main content -->
  <main>
    <div class="container">
      <div class="image-viewer fade-in">
        <div class="image-viewer-header">
          <h2 class="image-viewer-title">
            <?php echo ($currentIndex !== false) ? "Product #".htmlspecialchars($currentProductId) : "Image Viewer"; ?>
          </h2>
          <a href="catalogue.php" class="back-link">← Back to Catalogue</a>
        </div>
        <div class="image-container">
          <div class="image-glow"></div>
          <?php if ($currentIndex === false): ?>
            <img src="<?= $base64Image ?>" alt="Product Image" class="img-viewer-image">
          <?php else: ?>
            <img src="assets/images/<?= htmlspecialchars($currentProductId) ?>.png" alt="Product Image" class="img-viewer-image">
          <?php endif; ?>
        </div>
        <?php if ($currentIndex !== false): ?>
        <div class="navigation-buttons">
            <?php if ($prevProductId !== null): ?>
              <a href="img_viewer.php?product_id=<?= htmlspecialchars($prevProductId) ?>">&laquo; Previous</a>
            <?php else: ?>
              <span style="visibility: hidden;">Placeholder</span>
            <?php endif; ?>
            <?php if ($nextProductId !== null): ?>
              <a href="img_viewer.php?product_id=<?= htmlspecialchars($nextProductId) ?>">Next &raquo;</a>
            <?php else: ?>
              <span style="visibility: hidden;">Placeholder</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p class="footer-text">&copy; Galactic Federation Shop System</p>
    </div>
  </footer>

  <script src="assets/js/img_viewer.js"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>
