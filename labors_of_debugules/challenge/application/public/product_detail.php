<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once('../includes/db.php');
require_once('../includes/product.php');

$id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
if (!ctype_digit($id)) {
    echo "Invalid product ID.";
    exit();
}

$product = getProductById($id);
if (!$product) {
    echo "Product not found.";
    exit();
}

// Build the current product data for the gallery
$currentProductData = array(
    "id"         => (int)$product["product_id"],
    "name"       => $product["name"],
    "description"=> $product["description"],
    "price"      => (float)$product["price"],
    "admin_only" => (bool)$product["admin_only"],
    "features"   => array(),
    "images"     => array($product["image_url"])
);

// For related products
$allProducts = getProducts();
$relatedProducts = array_filter($allProducts, function($p) use ($product) {
    return $p["product_id"] != $product["product_id"];
});
$relatedProductsData = array_map(function($p) {
    return array(
        "id"         => (int)$p["product_id"],
        "name"       => $p["name"],
        "price"      => (float)$p["price"],
        "admin_only" => (bool)$p["admin_only"],
        "images"     => array($p["image_url"])
    );
}, $relatedProducts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product["name"]) ?> - Galactic Federation Shop System</title>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Inline styles for the Product Detail page */
    :root {
      --neon-green: #39ff14;
      --neon-blue: #4deeea;
      --neon-purple: #b44af7;
      --neon-pink: #ff00ff;
      --dark-bg: #0a0a0a;
      --space-blue: #0f172a;
      --portal-glow: rgba(57, 255, 20, 0.6);
      --glass-bg: rgba(15, 23, 42, 0.7);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--dark-bg);
      color: #fff;
      background-image:
        radial-gradient(circle at 20% 30%, rgba(77,238,234,0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(180,74,247,0.05) 0%, transparent 50%);
      background-attachment: fixed;
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
    }
    h1, h2, h3, h4, h5, h6 { font-family: 'Space Mono', monospace; font-weight:700; }
    .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
    header { position: relative; z-index: 10; }
    nav {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      margin: 1rem auto;
      padding: 0.75rem 1rem;
    }
    /* (Navigation styles omitted for brevity; assume similar to other pages) */
    /* Product Detail Styles */
    .product-details { display: flex; flex-direction: column; gap: 1.5rem; }
    @media (min-width:768px){ .product-details { flex-direction: row; } }
    .product-gallery {
      flex: 1;
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(77,238,234,0.3);
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
    }
    .main-image-container {
      position: relative;
      margin-bottom: 1rem;
      border-radius: 0.5rem;
      overflow: hidden;
      border: 1px solid rgba(77,238,234,0.3);
      background-color: rgba(10,10,10,0.5);
      aspect-ratio: 4/3;
    }
    .image-glow { position: absolute; inset: 0; background: linear-gradient(to right, rgba(77,238,234,0.1), rgba(57,255,20,0.1)); filter: blur(20px); z-index: 0; }
    .main-image { position: relative; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
    .thumbnails { display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem; }
    .thumbnail { width: 4rem; height: 4rem; border-radius: 0.25rem; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: all 0.3s ease; flex-shrink: 0; }
    .thumbnail:hover { border-color: rgba(77,238,234,0.5); }
    .thumbnail.active { border-color: var(--neon-blue); box-shadow: 0 0 10px var(--neon-blue); }
    .gallery-controls { display: flex; justify-content: space-between; margin-top: 1rem; }
    .gallery-button {
      display: flex; align-items: center; padding: 0.5rem 0.75rem; background-color: rgba(59,130,246,0.2); border-radius: 0.25rem; border: none; color: #63b3ed; cursor: pointer; transition: all 0.3s ease; font-size: 0.875rem;
    }
    .gallery-button:hover { background-color: rgba(59,130,246,0.3); color: #4ade80; }
    .gallery-button:disabled { opacity: 0.5; cursor: not-allowed; }
    .product-info {
      flex: 1;
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(77,238,234,0.3);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
    }
    .product-header { margin-bottom: 1.5rem; }
    .product-title { font-size: 1.875rem; margin-bottom: 0.5rem; color: white; text-shadow: 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue); }
    .product-meta { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 0.5rem; }
    .product-price { font-size: 1.5rem; font-weight: bold; color: #4ade80; }
    .product-badge { display: inline-block; background-color: rgba(180,74,247,0.8); color: white; font-size: 0.625rem; padding: 0.25rem 0.5rem; border-radius: 9999px; margin-left: 0.5rem; }
    .product-rating { display: flex; align-items: center; gap: 0.25rem; }
    .rating-stars { display: flex; color: #fbbf24; }
    .rating-count { font-size: 0.75rem; color: #9ca3af; }
    .product-availability { display: flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; }
    .availability-icon { color: #4ade80; }
    .availability-text { color: #4ade80; }
    .product-description { margin-bottom: 1.5rem; }
    .description-title { font-size: 1rem; margin-bottom: 0.5rem; color: #63b3ed; }
    .description-text { color: #d1d5db; line-height: 1.6; font-size: 0.875rem; }
    .product-actions { display: flex; flex-direction: column; gap: 1rem; }
    .quantity-selector { display: flex; align-items: center; gap: 0.5rem; }
    .quantity-controls { display: flex; align-items: center; border: 1px solid rgba(77,238,234,0.3); border-radius: 0.25rem; overflow: hidden; }
    .quantity-button { display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: rgba(59,130,246,0.2); border: none; color: #63b3ed; cursor: pointer; transition: all 0.3s ease; }
    .quantity-input { width: 3rem; height: 2rem; background-color: rgba(10,10,10,0.7); border: none; color: white; text-align: center; font-family: 'Space Mono', monospace; }
    .action-buttons { display: flex; gap: 0.5rem; }
    <!-- Updated Buy Button uses buyProduct() with quantity from the input -->
    .buy-button {
      flex-grow: 1;
      background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
      color: var(--dark-bg);
      font-family: 'Space Mono', monospace;
      font-weight: 600;
      padding: 0.75rem 1rem;
      border-radius: 0.25rem;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .buy-button:hover { transform: translateY(-2px); box-shadow: 0 0 10px var(--neon-green), 0 0 20px var(--neon-green); }
    .buy-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: all 0.6s ease;
    }
    .buy-button:hover::before { left: 100%; }
    .wishlist-button { display: flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; background-color: rgba(59,130,246,0.2); border-radius: 0.25rem; border: none; color: #63b3ed; cursor: pointer; transition: all 0.3s ease; }
    .wishlist-button:hover { background-color: rgba(59,130,246,0.3); color: #4ade80; }
    .wishlist-button.active { color: #ef4444; }
  </style>
</head>
<body>
  <!-- Background elements -->
  <div class="bg-elements">
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>
    <div class="bg-grid"></div>
  </div>
  <!-- Navigation (for brevity, similar to other pages) -->
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
      <div class="product-details fade-in">
        <!-- Product Gallery -->
        <div class="product-gallery fade-in fade-in-1">
          <div class="gallery-blob-1"></div>
          <div class="gallery-blob-2"></div>
          <div class="main-image-container">
            <div class="image-glow"></div>
            <img id="mainImage" src="<?= htmlspecialchars($product["image_url"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?>" class="main-image">
          </div>
          <div id="thumbnails" class="thumbnails">
            <div class="thumbnail active">
              <img src="<?= htmlspecialchars($product["image_url"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?> - Image 1">
            </div>
          </div>
          <div class="gallery-controls">
            <button id="prevButton" class="gallery-button prev-button" disabled>
              <i data-lucide="chevron-left" width="16" height="16"></i>
              Previous
            </button>
            <button id="nextButton" class="gallery-button next-button" disabled>
              Next
              <i data-lucide="chevron-right" width="16" height="16"></i>
            </button>
          </div>
        </div>
        <!-- Product Info -->
        <div class="product-info fade-in fade-in-2">
          <div class="info-blob"></div>
          <div class="product-header">
            <h1 id="productTitle" class="product-title"><?= htmlspecialchars($product["name"]) ?></h1>
            <div class="product-meta">
              <p id="productPrice" class="product-price">$<?= number_format($product["price"], 2) ?></p>
              <?php if($product["admin_only"]): ?>
                <span id="adminBadge" class="product-badge">Admin Only</span>
              <?php endif; ?>
              <div class="product-rating">
                <div class="rating-stars">
                  <i data-lucide="star" width="16" height="16"></i>
                  <i data-lucide="star" width="16" height="16"></i>
                  <i data-lucide="star" width="16" height="16"></i>
                  <i data-lucide="star" width="16" height="16"></i>
                  <i data-lucide="star-half" width="16" height="16"></i>
                </div>
                <span class="rating-count">(42)</span>
              </div>
            </div>
            <div class="product-availability">
              <i data-lucide="check-circle" class="availability-icon" width="16" height="16"></i>
              <span class="availability-text">In Stock</span>
            </div>
          </div>
          <div class="product-description">
            <h2 class="description-title">Description</h2>
            <p id="productDescription" class="description-text"><?= htmlspecialchars($product["description"]) ?></p>
          </div>
          <div class="product-features">
            <h2 class="features-title">Key Features</h2>
            <ul id="featuresList" class="features-list">
              <li class="feature-item">
                <i data-lucide="check" class="feature-icon" width="16" height="16"></i>
                <span class="feature-text">No additional features provided.</span>
              </li>
            </ul>
          </div>
          <div class="product-specs">
            <h2 class="specs-title">Technical Specifications</h2>
            <div class="specs-grid">
              <div class="spec-item">
                <span class="spec-label">Dimension</span>
                <span class="spec-value">C-137</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Power Source</span>
                <span class="spec-value">Standard</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Warranty</span>
                <span class="spec-value">1 Galactic Year</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Certification</span>
                <span class="spec-value">Council of Ricks</span>
              </div>
            </div>
          </div>
          <div class="product-actions">
            <div class="quantity-selector">
              <span class="quantity-label">Quantity:</span>
              <div class="quantity-controls">
                <button class="quantity-button" onclick="updateQuantity(-1)">
                  <i data-lucide="minus" width="16" height="16"></i>
                </button>
                <input type="number" id="quantityInput" class="quantity-input" value="1" min="1" max="99">
                <button class="quantity-button" onclick="updateQuantity(1)">
                  <i data-lucide="plus" width="16" height="16"></i>
                </button>
              </div>
            </div>
            <div class="action-buttons">
              <!-- Updated Buy button passes the quantity from the input -->
              <button id="buyButton" class="buy-button" onclick="buyProduct(<?= $product['product_id'] ?>, document.getElementById('quantityInput').value)">
                <i data-lucide="shopping-cart" width="16" height="16"></i>
                Add to Cart
              </button>
              <button id="wishlistButton" class="wishlist-button" onclick="toggleWishlist()">
                <i data-lucide="heart" width="16" height="16"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Related products section (remains unchanged) -->
      <div class="related-products fade-in fade-in-3">
        <h2 class="related-title">Related Products</h2>
        <div id="relatedProducts" class="products-grid">
          <!-- Related products will be loaded via JavaScript -->
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p class="footer-text">&copy; <span id="current-year"></span> Galactic Federation Shop System | <span class="footer-highlight">Dimension C-137</span></p>
    </div>
  </footer>

  <!-- Include external JS files -->
  <script src="assets/js/order.js"></script>
  <script src="assets/js/script.js"></script>
  <script>
    // Initialize Lucide icons and set current year
    lucide.createIcons();
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // (Optional) Additional inline JS for product detail page (gallery, related products, etc.) can remain here.
  </script>
</body>
</html>
