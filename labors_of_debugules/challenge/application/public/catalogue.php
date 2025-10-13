<?php
require_once('../includes/db.php');
require_once('../includes/product.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$products = getUserProducts($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catalogue - Galactic Federation Shop System</title>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
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
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Space Mono', monospace;
      font-weight: 700;
    }
    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    /* Navigation */
    header {
      position: relative;
      z-index: 10;
    }
    nav {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      margin: 1rem auto;
      padding: 0.75rem 1rem;
    }
    .nav-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
    }
    @media (min-width: 768px) {
      .nav-container {
        flex-direction: row;
      }
    }
    .logo {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }
    @media (min-width: 768px) {
      .logo {
        margin-bottom: 0;
      }
    }
    .logo-icon {
      position: relative;
      width: 2.5rem;
      height: 2.5rem;
      margin-right: 0.75rem;
    }
    .logo-glow {
      position: absolute;
      inset: 0;
      background: linear-gradient(to right, var(--neon-green), var(--neon-blue));
      border-radius: 50%;
      opacity: 0.75;
      filter: blur(4px);
    }
    .logo-icon-inner {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    .logo-text {
      font-size: 1.25rem;
      font-weight: bold;
      text-shadow: 0 0 5px var(--neon-green), 0 0 10px var(--neon-green);
    }
    .nav-links {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0.75rem;
    }
    .nav-link {
      display: flex;
      align-items: center;
      padding: 0.5rem 0.75rem;
      color: #63b3ed;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
      position: relative;
    }
    .nav-link:hover {
      color: #4ade80;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 1px;
      background: linear-gradient(90deg, var(--neon-blue), var(--neon-green));
      transition: width 0.3s ease;
    }
    .nav-link:hover::after {
      width: 100%;
    }
    .nav-link.active {
      color: #4ade80;
    }
    .nav-link.active::after {
      width: 100%;
    }
    .nav-link svg {
      margin-right: 0.25rem;
    }
    /* Background elements */
    .bg-elements {
      position: fixed;
      inset: 0;
      z-index: 0;
      overflow: hidden;
      pointer-events: none;
    }
    .bg-blob {
      position: absolute;
      border-radius: 50%;
      opacity: 0.05;
      filter: blur(3rem);
    }
    .bg-blob-1 {
      top: 25%;
      left: 25%;
      width: 16rem;
      height: 16rem;
      background-color: #a855f7;
    }
    .bg-blob-2 {
      bottom: 33%;
      right: 25%;
      width: 24rem;
      height: 24rem;
      background-color: #3b82f6;
    }
    .bg-blob-3 {
      top: 66%;
      left: 50%;
      width: 20rem;
      height: 20rem;
      background-color: #22c55e;
    }
    .bg-grid {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
      background-size: 20px 20px;
    }
    /* Main content */
    main {
      flex-grow: 1;
      position: relative;
      z-index: 10;
      padding: 1rem 0;
    }
    /* Catalogue Header */
    .catalogue-header {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    @media (min-width: 768px) {
      .catalogue-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
      }
    }
    .catalogue-title {
      font-size: 1.875rem;
      text-shadow: 0 0 5px var(--neon-green), 0 0 10px var(--neon-green);
    }
    .view-controls {
      display: flex;
      gap: 0.5rem;
    }
    .view-button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      border-radius: 0.25rem;
      background-color: transparent;
      border: none;
      color: #63b3ed;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .view-button:hover {
      color: #4ade80;
      background-color: rgba(77,238,234,0.1);
    }
    .view-button.active {
      color: #4ade80;
      background-color: rgba(57,255,20,0.1);
    }
    /* Search and Filter */
    .search-filter {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    @media (min-width: 768px) {
      .search-filter {
        flex-direction: row;
      }
    }
    .search-container {
      position: relative;
      flex-grow: 1;
    }
    .search-input {
      width: 100%;
      background-color: rgba(10,10,10,0.7);
      border: 1px solid var(--neon-blue);
      color: white;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border-radius: 0.25rem;
      font-family: 'Inter', sans-serif;
      transition: all 0.3s ease;
    }
    .search-input:focus {
      outline: none;
      border-color: var(--neon-green);
      box-shadow: 0 0 10px var(--neon-green);
    }
    .search-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #63b3ed;
    }
    .filter-select {
      background-color: rgba(10,10,10,0.7);
      border: 1px solid var(--neon-blue);
      color: white;
      padding: 0.75rem 1rem;
      border-radius: 0.25rem;
      font-family: 'Inter', sans-serif;
      transition: all 0.3s ease;
      min-width: 12rem;
    }
    .filter-select:focus {
      outline: none;
      border-color: var(--neon-green);
      box-shadow: 0 0 10px var(--neon-green);
    }
    .filter-select option {
      background-color: var(--space-blue);
    }
    /* Product Grid */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(1, 1fr);
      gap: 1rem;
    }
    @media (min-width: 640px) {
      .product-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (min-width: 1024px) {
      .product-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    /* Product List (for list view) */
    .product-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    /* Product Card (for grid view) */
    .product-card {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(77,238,234,0.3);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.3), 0 0 15px var(--neon-blue);
    }
    .product-image {
      position: relative;
      height: 12rem;
      overflow: hidden;
    }
    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    .product-card:hover .product-image img {
      transform: scale(1.1);
    }
    .product-image-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }
    .product-admin-badge {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      background-color: rgba(180,74,247,0.8);
      color: white;
      font-size: 0.625rem;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    .product-info {
      padding: 1rem;
    }
    .product-title {
      font-size: 1.125rem;
      color: var(--neon-blue);
      margin-bottom: 0.25rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .product-price {
      font-size: 1.25rem;
      font-weight: bold;
      color: #4ade80;
      margin-bottom: 0.5rem;
    }
    .product-description {
      font-size: 0.875rem;
      color: #d1d5db;
      margin-bottom: 1rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .product-actions {
      display: flex;
      gap: 0.5rem;
    }
    .buy-button {
      flex-grow: 1;
      background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
      color: var(--dark-bg);
      font-family: 'Space Mono', monospace;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .buy-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 10px var(--neon-green), 0 0 20px var(--neon-green);
    }
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
    .buy-button:hover::before {
      left: 100%;
    }
    .buy-button svg {
      margin-right: 0.25rem;
    }
    .action-button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      background-color: rgba(59,130,246,0.2);
      border-radius: 0.25rem;
      border: none;
      color: #63b3ed;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .action-button:hover {
      background-color: rgba(59,130,246,0.3);
      color: #4ade80;
    }
    /* Empty state */
    .empty-state {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 2rem;
      text-align: center;
    }
    .empty-icon {
      color: #63b3ed;
      margin: 0 auto 1rem;
    }
    .empty-title {
      font-size: 1.25rem;
      color: #63b3ed;
      margin-bottom: 0.5rem;
    }
    .empty-text {
      font-size: 0.875rem;
      color: #9ca3af;
    }
    /* Footer */
    footer {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 1rem;
      margin-top: auto;
      text-align: center;
      position: relative;
      z-index: 10;
    }
    .footer-text {
      font-size: 0.75rem;
      color: #63b3ed;
    }
    .footer-highlight {
      color: #4ade80;
    }
    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
      animation: fadeIn 0.5s ease-out forwards;
    }
    .fade-in-1 { animation-delay: 0.1s; }
    .fade-in-2 { animation-delay: 0.2s; }
    .fade-in-3 { animation-delay: 0.3s; }
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
            <a href="catalogue.php" class="nav-link active">Catalogue</a>
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
      <!-- Catalogue Header -->
      <div class="catalogue-header">
        <h1 class="catalogue-title">Multiverse Catalogue</h1>
        <div class="view-controls">
          <button id="gridViewBtn" class="view-button active" aria-label="Grid view">
            <i data-lucide="layout-grid" width="16" height="16"></i>
          </button>
          <button id="listViewBtn" class="view-button" aria-label="List view">
            <i data-lucide="list" width="16" height="16"></i>
          </button>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="search-filter">
        <div class="search-container">
          <i data-lucide="search" class="search-icon" width="16" height="16"></i>
          <input type="text" id="searchInput" class="search-input" placeholder="Search products...">
        </div>
        <select id="sortSelect" class="filter-select">
          <option value="name">Name (A-Z)</option>
          <option value="price-asc">Price (Low to High)</option>
          <option value="price-desc">Price (High to Low)</option>
        </select>
      </div>

      <!-- Product grid view (default) -->
      <div id="productGrid" class="product-grid"></div>

      <!-- Product list view (hidden by default) -->
      <div id="productList" class="product-list" style="display: none;"></div>

      <!-- Empty state (hidden by default) -->
      <div id="emptyState" class="empty-state" style="display: none;">
        <i data-lucide="search-x" class="empty-icon" width="48" height="48"></i>
        <h3 class="empty-title">No products found</h3>
        <p class="empty-text">Try adjusting your search or filters</p>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p class="footer-text">
        &copy; <span id="current-year"></span> Galactic Federation Shop System |
        <span class="footer-highlight">Dimension C-137</span>
      </p>
    </div>
  </footer>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    // Set current year in footer
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // Get products from PHP backend
    const products = <?php echo json_encode($products); ?>;

    // DOM elements
    const productGrid = document.getElementById('productGrid');
    const productList = document.getElementById('productList');
    const emptyState = document.getElementById('emptyState');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');

    let currentView = 'grid';

    document.addEventListener('DOMContentLoaded', () => {
      renderProducts();
      gridViewBtn.addEventListener('click', () => setViewMode('grid'));
      listViewBtn.addEventListener('click', () => setViewMode('list'));
      searchInput.addEventListener('input', renderProducts);
      sortSelect.addEventListener('change', renderProducts);

      // Mark catalogue link active in nav (already set in HTML class "active")
    });

    function setViewMode(mode) {
      currentView = mode;
      if (mode === 'grid') {
        productGrid.style.display = 'grid';
        productList.style.display = 'none';
        gridViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
      } else {
        productGrid.style.display = 'none';
        productList.style.display = 'flex';
        gridViewBtn.classList.remove('active');
        listViewBtn.classList.add('active');
      }
    }

    function getFilteredProducts() {
      const searchTerm = searchInput.value.toLowerCase();
      const sortOption = sortSelect.value;

      let filtered = products.filter(product => {
        return (
          product.name.toLowerCase().includes(searchTerm) ||
          product.description.toLowerCase().includes(searchTerm)
        );
      });

      switch (sortOption) {
        case 'name':
          filtered.sort((a, b) => a.name.localeCompare(b.name));
          break;
        case 'price-asc':
          filtered.sort((a, b) => a.price - b.price);
          break;
        case 'price-desc':
          filtered.sort((a, b) => b.price - a.price);
          break;
      }

      return filtered;
    }

    function renderProducts() {
      const filteredProducts = getFilteredProducts();
      productGrid.innerHTML = '';
      productList.innerHTML = '';

      if (filteredProducts.length === 0) {
        emptyState.style.display = 'block';
        return;
      } else {
        emptyState.style.display = 'none';
      }

      filteredProducts.forEach((product, index) => {
        // Grid view item
        const gridItem = document.createElement('div');
        gridItem.className = `product-card fade-in fade-in-${(index % 3) + 1}`;
        gridItem.innerHTML = `
          <div class="product-image">
            <img src="${product.image_url}" alt="${product.name}">
            <div class="product-image-overlay"></div>
            ${product.admin_only ? '<span class="product-admin-badge">Admin Only</span>' : ''}
          </div>
          <div class="product-info">
            <h3 class="product-title">${product.name}</h3>
            <p class="product-price">$${product.price.toFixed(2)}</p>
            <p class="product-description">${product.description}</p>
            <div class="product-actions">
              <button class="buy-button" onclick="buyProduct(${product.product_id})">
                <i data-lucide="shopping-cart" width="14" height="14"></i>
                Buy Now
              </button>
              <button class="action-button" onclick="viewProduct(${product.product_id})" aria-label="View details">
                <i data-lucide="eye" width="14" height="14"></i>
              </button>
              <button class="action-button" onclick="viewImage(${product.product_id})" aria-label="View image">
                <i data-lucide="image" width="14" height="14"></i>
              </button>
            </div>
          </div>
        `;
        productGrid.appendChild(gridItem);

        // List view item
        const listItem = document.createElement('div');
        listItem.className = `product-list-item fade-in fade-in-${(index % 3) + 1}`;
        listItem.innerHTML = `
          <div class="product-list-image">
            <img src="${product.image_url}" alt="${product.name}">
          </div>
          <div class="product-list-content">
            <div class="product-list-header">
              <h3 class="product-list-title">${product.name}</h3>
              ${product.admin_only ? '<span class="product-admin-badge">Admin Only</span>' : ''}
            </div>
            <p class="product-list-description">${product.description}</p>
            <div class="product-list-actions">
              <p class="product-list-price">$${product.price.toFixed(2)}</p>
              <div class="product-list-buttons">
                <button class="buy-button" onclick="buyProduct(${product.product_id})">
                  <i data-lucide="shopping-cart" width="14" height="14"></i>
                  Buy Now
                </button>
                <button class="action-button" onclick="viewProduct(${product.product_id})" aria-label="View details">
                  <i data-lucide="eye" width="14" height="14"></i>
                </button>
                <button class="action-button" onclick="viewImage(${product.product_id})" aria-label="View image">
                  <i data-lucide="image" width="14" height="14"></i>
                </button>
              </div>
            </div>
          </div>
        `;
        productList.appendChild(listItem);
      });

      lucide.createIcons();
    }

    // Updated buyProduct function using AJAX
    function buyProduct(productId) {
      const formData = new FormData();
      formData.append('product_id', productId);
      formData.append('quantity', 1);

      fetch('order.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(result => {
        alert('Order placed successfully!');
      })
      .catch(error => {
        alert('Failed to place order. Please try again.');
        console.error('Error:', error);
      });
    }

    function viewImage(productId) {
      window.location.href = 'img_viewer.php?product_id=' + productId;
    }

    function viewProduct(productId) {
      const product = products.find(p => p.product_id === productId);
      if (product) {
        window.location.href = 'product_detail.php?product_id=' + productId;
      }
    }
  </script>
</body>
</html>
