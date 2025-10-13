<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header('Location: index.php');
}

require_once('../includes/db.php');
require_once('../includes/admin.php');
require_once('../includes/product.php');

$db = Database::getInstance();
$pdo = $db->getPdo();

// Load users using your predefined function
$users = getAllUsers();

// Load products using your predefined function
$products = getProducts();

// Load orders from the database
// (Since the Orders table in your db.php does not have a "status" column, we simulate one.)
$stmt = $pdo->query("SELECT o.order_id, o.user_id, u.name as user_name, o.product_id, p.name as product_name, p.price, o.order_date, 'completed' as status
                     FROM Orders o
                     JOIN Users u ON o.user_id = u.user_id
                     JOIN Products p ON o.product_id = p.product_id
                     ORDER BY o.order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compute stats
$totalUsers = count($users);
$totalProducts = count($products);
$totalOrders = count($orders);
$totalRevenue = 0;
foreach ($orders as $order) {
    $totalRevenue += $order['price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Galactic Federation Shop System</title>
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
      background-image: radial-gradient(circle at 20% 30%, rgba(77,238,234,0.05) 0%, transparent 50%),
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
    @media (min-width:768px){ .nav-container { flex-direction: row; } }
    .logo { display: flex; align-items: center; margin-bottom: 1rem; }
    @media (min-width:768px){ .logo { margin-bottom: 0; } }
    .logo-icon { position: relative; width: 2.5rem; height: 2.5rem; margin-right: 0.75rem; }
    .logo-glow { position: absolute; inset: 0; background: linear-gradient(to right, var(--neon-green), var(--neon-blue)); border-radius: 50%; opacity: 0.75; filter: blur(4px); }
    .logo-icon-inner { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: white; }
    .logo-text { font-size: 1.25rem; font-weight: bold; text-shadow: 0 0 5px var(--neon-green), 0 0 10px var(--neon-green); }
    .nav-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem; }
    .nav-link { display: flex; align-items: center; padding: 0.5rem 0.75rem; color: #63b3ed; text-decoration: none; font-weight: 500; transition: color 0.3s; position: relative; }
    .nav-link:hover { color: #4ade80; }
    .nav-link::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 1px; background: linear-gradient(90deg, var(--neon-blue), var(--neon-green)); transition: width 0.3s ease; }
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
    .bg-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 20px 20px; }
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
    @media (min-width:768px){ .dashboard-header-content { flex-direction: row; justify-content: space-between; align-items: center; } }
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
    @media (min-width:640px){ .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width:768px){ .stats-grid { grid-template-columns: repeat(3, 1fr); } }
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
    /* Data card */
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
    /* Table styles */
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
    /* Badge */
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
    /* Modal styles */
    .modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.7);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 100;
    }
    .modal {
      background-color: var(--glass-bg);
      padding: 1.5rem;
      border-radius: 0.5rem;
      width: 90%;
      max-width: 500px;
      position: relative;
    }
    .modal-header, .modal-footer { display: flex; justify-content: space-between; align-items: center; }
    .modal-title { font-size: 1.25rem; }
    .modal-close { background: none; border: none; color: #63b3ed; cursor: pointer; }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.75rem; color: #63b3ed; margin-bottom: 0.25rem; }
    .form-input, .form-select, textarea { width: 100%; padding: 0.5rem; border: 1px solid var(--neon-blue); border-radius: 0.25rem; background: rgba(10,10,10,0.7); color: white; }
    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
    }
    .btn-primary {
        background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
        color: var(--dark-bg);
        box-shadow: 0 0 10px var(--neon-green), 0 0 20px var(--neon-green);
    }
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 0 15px var(--neon-green), 0 0 30px var(--neon-green);
    }
    .btn-secondary {
        background: rgba(68, 68, 68, 0.7);
        color: white;
        box-shadow: 0 0 5px rgba(68, 68, 68, 0.5);
    }
    .btn-secondary:hover {
        background: rgba(68, 68, 68, 0.9);
        box-shadow: 0 0 10px rgba(68, 68, 68, 0.7);
    }
    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: all 0.6s ease;
    }
    .btn:hover::before {
        left: 100%;
    }
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
      <h1>Admin Panel</h1>
      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon-container stat-icon-blue">
            <i data-lucide="users" width="20" height="20"></i>
          </div>
          <div class="stat-content">
            <div class="stat-title">Total Users</div>
            <div id="totalUsers" class="stat-value"><?= htmlspecialchars($totalUsers) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-container stat-icon-green">
            <i data-lucide="box" width="20" height="20"></i>
          </div>
          <div class="stat-content">
            <div class="stat-title">Available Products</div>
            <div id="totalProducts" class="stat-value"><?= htmlspecialchars($totalProducts) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-container stat-icon-blue">
            <i data-lucide="shopping-cart" width="20" height="20"></i>
          </div>
          <div class="stat-content">
            <div class="stat-title">Total Orders</div>
            <div id="totalOrders" class="stat-value"><?= htmlspecialchars($totalOrders) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-container stat-icon-green">
            <i data-lucide="dollar-sign" width="20" height="20"></i>
          </div>
          <div class="stat-content">
            <div class="stat-title">Total Revenue</div>
            <div id="totalRevenue" class="stat-value">$<?= number_format($totalRevenue, 2) ?></div>
          </div>
        </div>
      </div>

      <!-- Admin Tabs -->
      <div class="admin-tabs" style="margin-bottom:1.5rem;">
        <button class="admin-tab active btn btn-primary" data-tab="usersTab">Users</button>
        <button class="admin-tab btn btn-primary" data-tab="productsTab">Products</button>
        <button class="admin-tab btn btn-primary" data-tab="ordersTab">Orders</button>
      </div>

      <!-- Users Tab -->
      <div id="usersTab" class="tab-content active">
        <h2>Manage Users</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersTable">
            <?php foreach($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars($user['user_id']) ?></td>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
              <td>
                <button class="btn btn-secondary" onclick="openEditUserModal(<?= $user['user_id'] ?>)">Edit</button>
                <button class="btn btn-secondary" onclick="openResetPasswordModal(<?= $user['user_id'] ?>)">Reset</button>
                <button class="btn btn-secondary" onclick="deleteUser(<?= $user['user_id'] ?>)">Delete</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <button class="btn btn-primary" onclick="openAddUserModal()">Add User</button>
      </div>

      <!-- Products Tab -->
      <div id="productsTab" class="tab-content" style="display: none;">
        <h2>Manage Products</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Admin Only</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="productsTable">
            <?php foreach($products as $prod): ?>
            <tr>
              <td><?= htmlspecialchars($prod['product_id']) ?></td>
              <td><?= htmlspecialchars($prod['name']) ?></td>
              <td>$<?= number_format($prod['price'], 2) ?></td>
              <td><?= $prod['admin_only'] ? 'Yes' : 'No' ?></td>
              <td>
                <button class="btn btn-secondary" onclick="openEditProductModal(<?= $prod['product_id'] ?>)">Edit</button>
                <button class="btn btn-secondary" onclick="deleteProduct(<?= $prod['product_id'] ?>)">Delete</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <button class="btn btn-primary" onclick="openAddProductModal()">Add Product</button>
      </div>

      <!-- Orders Tab -->
      <div id="ordersTab" class="tab-content" style="display: none;">
        <h2>Manage Orders</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>User</th>
              <th>Product</th>
              <th>Date</th>
              <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="ordersTable">
            <?php foreach($orders as $order): ?>
            <tr>
              <td><?= htmlspecialchars($order['order_id']) ?></td>
              <td><?= htmlspecialchars($order['user_name']) ?></td>
              <td><?= htmlspecialchars($order['product_name']) ?></td>
              <td><?= htmlspecialchars($order['order_date']) ?></td>
              <td>$<?= number_format($order['price'], 2) ?></td>
              <td><?= htmlspecialchars($order['status']) ?></td>
              <td>
                <button class="btn btn-secondary" onclick="openViewOrderModal(<?= $order['order_id'] ?>)">View</button>
                <button class="btn btn-secondary" onclick="deleteOrder(<?= $order['order_id'] ?>)">Delete</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p class="footer-text">&copy; <span id="current-year"></span> Galactic Federation Shop System | <span class="footer-highlight">Dimension C-137</span></p>
    </div>
  </footer>

  <!-- Modals -->

  <!-- Add Product Modal -->
  <div id="addProductModal" class="modal-backdrop" style="display: none;">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title">Add Product</h3>
        <button class="modal-close btn btn-secondary" onclick="closeModal('addProductModal')">
          <i data-lucide="x" width="20" height="20"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Product Name</label>
          <input type="text" class="form-input" id="newProductName">
        </div>
        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea class="form-input" id="newProductDescription"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Price</label>
          <input type="number" step="0.01" class="form-input" id="newProductPrice">
        </div>
        <div class="form-group">
          <label class="form-label">Image URL</label>
          <input type="text" class="form-input" id="newProductImage">
        </div>
        <div class="form-group">
          <label class="form-label">Admin Only</label>
          <select class="form-select" id="newProductAdminOnly">
            <option value="false">No</option>
            <option value="true">Yes</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal('addProductModal')">Cancel</button>
        <button class="btn btn-primary" onclick="addProduct()">Add Product</button>
      </div>
    </div>
  </div>

  <!-- View Order Modal -->
  <div id="viewOrderModal" class="modal-backdrop" style="display: none;">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title">Order Details</h3>
        <button class="modal-close btn btn-secondary" onclick="closeModal('viewOrderModal')">
          <i data-lucide="x" width="20" height="20"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Order ID</label>
          <input type="text" class="form-input" id="viewOrderId" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">User</label>
          <input type="text" class="form-input" id="viewOrderUser" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Product</label>
          <input type="text" class="form-input" id="viewOrderProduct" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Date</label>
          <input type="text" class="form-input" id="viewOrderDate" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Price</label>
          <input type="text" class="form-input" id="viewOrderPrice" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="viewOrderStatus">
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal('viewOrderModal')">Close</button>
        <button class="btn btn-primary" onclick="updateOrderStatus()">Update Status</button>
      </div>
    </div>
  </div>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Set current year in footer
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // Tab switching for admin panel
    const tabs = document.querySelectorAll('.admin-tab');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => content.style.display = 'none');
        const tabName = tab.getAttribute('data-tab');
        document.getElementById(tabName).style.display = 'block';
      });
    });

    // Modal functions
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'flex';
    }
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    // Dummy functions for product actions (replace with real AJAX calls as needed)
    function addProduct() {
      alert('Product added successfully.');
      closeModal('addProductModal');
    }
    function openEditProductModal(productId) {
      alert('Open edit modal for product ID: ' + productId);
    }
    function deleteProduct(productId) {
      if (confirm('Are you sure you want to delete this product?')) {
        alert('Product deleted.');
      }
    }

    // Dummy functions for user actions
    function openEditUserModal(userId) {
      alert('Open edit modal for user ID: ' + userId);
    }
    function openResetPasswordModal(userId) {
      alert('Open reset password modal for user ID: ' + userId);
    }
    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        alert('User deleted.');
      }
    }

    // Dummy functions for order actions
    function openViewOrderModal(orderId) {
      alert('Open view order modal for order ID: ' + orderId);
    }
    function updateOrderStatus() {
      alert('Order status updated.');
      closeModal('viewOrderModal');
    }
    function deleteOrder(orderId) {
      if (confirm('Are you sure you want to delete this order?')) {
        alert('Order deleted.');
      }
    }

    // Format date function
    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString();
    }
  </script>
</body>
</html>
