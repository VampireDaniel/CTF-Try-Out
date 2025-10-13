<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once('../includes/db.php');
require_once('../includes/product.php');

// Fetch recent orders and all products
$recentOrders = getRecentOrders();
$products = getProducts();

// Compute stats (note: getRecentOrders() returns the 10 most recent orders for all users)
$totalOrders = count($recentOrders);
$totalSpent = 0;
foreach ($recentOrders as $order) {
    $totalSpent += $order['price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Galactic Federation Shop System</title>
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
    h1, h2, h3, h4, h5, h6 { font-family: 'Space Mono', monospace; font-weight: 700; }
    .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
    /* Navigation */
    header { position: relative; z-index: 10; }
    nav {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      margin: 1rem auto;
      padding: 0.75rem 1rem;
    }
    .nav-container { display: flex; flex-direction: column; align-items: center; justify-content: space-between; }
    @media (min-width:768px) { .nav-container { flex-direction: row; } }
    .logo { display: flex; align-items: center; margin-bottom: 1rem; }
    @media (min-width:768px) { .logo { margin-bottom: 0; } }
    .logo-icon { position: relative; width: 2.5rem; height: 2.5rem; margin-right: 0.75rem; }
    .logo-glow { position: absolute; inset: 0; background: linear-gradient(to right, var(--neon-green), var(--neon-blue)); border-radius: 50%; opacity: 0.75; filter: blur(4px); }
    .logo-icon-inner { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: white; }
    .logo-text { font-size: 1.25rem; font-weight: bold; text-shadow: 0 0 5px var(--neon-green), 0 0 10px var(--neon-green); }
    .nav-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem; }
    .nav-link {
      display: flex; align-items: center; padding: 0.5rem 0.75rem; color: #63b3ed; text-decoration: none; font-weight: 500; transition: color 0.3s; position: relative;
    }
    .nav-link:hover { color: #4ade80; }
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
    .nav-link:hover::after { width: 100%; }
    .nav-link.active { color: #4ade80; }
    .nav-link.active::after { width: 100%; }
    .nav-link svg { margin-right: 0.25rem; }
    /* Background elements */
    .bg-elements { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
    .bg-blob { position: absolute; border-radius: 50%; opacity: 0.05; filter: blur(3rem); }
    .bg-blob-1 { top: 25%; left: 25%; width: 16rem; height: 16rem; background-color: #a855f7; }
    .bg-blob-2 { bottom: 33%; right: 25%; width: 24rem; height: 24rem; background-color: #3b82f6; }
    .bg-blob-3 { top: 66%; left: 50%; width: 20rem; height: 20rem; background-color: #22c55e; }
    .bg-grid { position: absolute; inset: 0; background-image:
      linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
      background-size: 20px 20px;
    }
    /* Main content */
    main { flex-grow: 1; position: relative; z-index: 10; padding: 1rem 0; }
    /* Dashboard header */
    .dashboard-header {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 1rem;
      margin-bottom: 1.5rem;
    }
    .dashboard-header-content { display: flex; flex-direction: column; gap: 1rem; }
    @media (min-width:768px) { .dashboard-header-content { flex-direction: row; justify-content: space-between; align-items: center; } }
    .dashboard-title { font-size: 1.875rem; margin-bottom: 0.25rem; text-shadow: 0 0 5px var(--neon-green), 0 0 10px var(--neon-green); }
    .dashboard-welcome { color: #63b3ed; font-size: 0.875rem; }
    .dashboard-welcome-name { color: #4ade80; font-weight: bold; }
    .dashboard-action {
      display: inline-flex;
      align-items: center;
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
      font-size: 0.75rem;
      text-decoration: none;
    }
    .dashboard-action:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 10px var(--neon-green), 0 0 20px var(--neon-green);
    }
    .dashboard-action::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: all 0.6s ease;
    }
    .dashboard-action:hover::before { left: 100%; }
    .dashboard-action svg { margin-right: 0.5rem; }
    /* Stats grid */
    .stats-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    @media (min-width:640px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width:768px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
    .stat-card {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 1rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .stat-icon-container { width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon-blue { background-color: rgba(59,130,246,0.2); color: #63b3ed; }
    .stat-icon-green { background-color: rgba(34,197,94,0.2); color: #4ade80; }
    .stat-icon-purple { background-color: rgba(168,85,247,0.2); color: #a78bfa; }
    .stat-content { flex-grow: 1; }
    .stat-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #63b3ed; margin-bottom: 0.25rem; }
    .stat-value { font-size: 1.5rem; font-weight: bold; color: white; }
    /* Data card (Recent purchases and product list) */
    .data-card {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 1rem;
      margin-bottom: 1.5rem;
    }
    .data-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .data-card-title { font-size: 1.25rem; color: #63b3ed; text-shadow: 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue); }
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .data-table th {
      background-color: rgba(15,23,42,0.8);
      color: white;
      padding: 0.75rem 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
      text-align: left;
      font-size: 0.75rem;
    }
    .data-table td {
      background-color: rgba(15,23,42,0.4);
      padding: 0.75rem 1rem;
      border-bottom: 1px solid rgba(77,238,234,0.1);
    }
    .data-table tr:hover td { background-color: rgba(57,255,20,0.05); }
    .data-table-empty { text-align: center; color: #9ca3af; padding: 2rem 0; }
    /* Action buttons */
    .action-button {
      display: inline-flex;
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
    .action-button:hover { background-color: rgba(59,130,246,0.3); color: #4ade80; }
    .action-button-green {
      background-color: rgba(34,197,94,0.2);
      color: #4ade80;
    }
    .action-button-green:hover { background-color: rgba(34,197,94,0.3); }
    .badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.625rem;
      font-weight: 600;
      text-transform: uppercase;
    }
    .badge-blue { background-color: rgba(59,130,246,0.8); color: white; }
    .badge-purple { background-color: rgba(168,85,247,0.8); color: white; }
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
    .footer-text { font-size: 0.75rem; color: #63b3ed; }
    .footer-highlight { color: #4ade80; }
    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 0.5s ease-out forwards; }
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
      <div id="dashboardContent">
        <!-- Dashboard header -->
        <div class="dashboard-header">
          <div class="dashboard-header-content">
            <div>
              <h1 class="dashboard-title">Portal Dashboard</h1>
              <p class="dashboard-welcome">
                Welcome back, <span id="userName"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></span>
              </p>
            </div>
            <a href="catalogue.php" class="dashboard-action">
              <i data-lucide="shopping-bag" width="16" height="16"></i>
              View Catalogue
            </a>
          </div>
        </div>
        <!-- Stats grid -->
        <div class="stats-grid">
          <div class="stat-card fade-in fade-in-1">
            <div class="stat-icon-container stat-icon-blue">
              <i data-lucide="shopping-cart" width="20" height="20"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Total Orders</div>
              <div id="totalOrders" class="stat-value"><?php echo htmlspecialchars($totalOrders); ?></div>
            </div>
          </div>
          <div class="stat-card fade-in fade-in-2">
            <div class="stat-icon-container stat-icon-green">
              <i data-lucide="package" width="20" height="20"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Available Products</div>
              <div id="availableProducts" class="stat-value"><?php echo count($products); ?></div>
            </div>
          </div>
          <div class="stat-card fade-in fade-in-3">
            <div class="stat-icon-container stat-icon-purple">
              <i data-lucide="dollar-sign" width="20" height="20"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Total Spent</div>
              <div id="totalSpent" class="stat-value">$<?php echo number_format($totalSpent, 2); ?></div>
            </div>
          </div>
        </div>
        <!-- Recent purchases -->
        <div class="data-card fade-in fade-in-1">
          <div class="data-card-header">
            <h2 class="data-card-title">Recent Purchases</h2>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Order Date</th>
                </tr>
              </thead>
              <tbody id="recentOrdersTable">
                <?php if (count($recentOrders) > 0): ?>
                  <?php foreach($recentOrders as $order): ?>
                    <tr>
                      <td><?= htmlspecialchars($order['name']); ?></td>
                      <td><?= htmlspecialchars($order['product_name']); ?></td>
                      <td>$<?= number_format($order['price'], 2); ?></td>
                      <td><?= htmlspecialchars($order['order_date']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="data-table-empty">No recent orders found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- Product list -->
        <div class="data-card fade-in fade-in-2">
          <div class="data-card-header">
            <h2 class="data-card-title">Product List</h2>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Orders</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="productsTable">
                <?php foreach ($products as $prod): ?>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <?= htmlspecialchars($prod['name']); ?>
                        <?php if ($prod['admin_only']): ?>
                          <span class="badge badge-purple">Admin Only</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>$<?= number_format($prod['price'], 2); ?></td>
                    <td><?= htmlspecialchars($prod['order_count']); ?></td>
                    <td>
                      <div style="display: flex; gap: 0.5rem;">
                        <button class="action-button" onclick="viewProduct(<?= $prod['product_id']; ?>)" aria-label="View details">
                          <i data-lucide="eye" width="14" height="14"></i>
                        </button>
                        <!-- Updated Buy Now button -->
                        <button class="action-button action-button-green" onclick="buyProduct(<?= $prod['product_id']; ?>)" aria-label="Buy now">
                          <i data-lucide="shopping-cart" width="14" height="14"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
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

    // Updated buyProduct() function using AJAX
    function buyProduct(productId, quantity = 1) {
      // Create FormData and append required fields
      const formData = new FormData();
      formData.append('product_id', productId);
      formData.append('quantity', quantity);

      fetch('order.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(result => {
        // You may choose to parse result if needed
        alert('Order placed successfully!');
        // Optionally, update stats or refresh the page
      })
      .catch(error => {
        alert('Failed to place order. Please try again.');
        console.error('Error:', error);
      });
    }

    // Dummy viewProduct function (can redirect to product_detail.php)
    function viewProduct(productId) {
      window.location.href = 'product_detail.php?product_id=' + productId;
    }
  </script>
</body>
</html>
