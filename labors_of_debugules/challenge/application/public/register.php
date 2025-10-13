<?php
require_once('../includes/db.php');
session_start();

$error = '';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name            = $_POST['name'] ?? '';
  $username        = $_POST['username'] ?? '';
  $email           = $_POST['email'] ?? '';
  $password        = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirmPassword'] ?? '';

  if ($password !== $confirmPassword) {
    $error = "Passwords do not match.";
  } else {
    if ($db->registerUser($name, $username, $password, $email)) {
      $_SESSION['success'] = "Registration successful! Please log in.";
      header('Location: login.php');
      exit();
    } else {
      $error = "Registration failed. Username may already exist.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Galactic Federation Shop System</title>
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
        radial-gradient(circle at 20% 30%, rgba(77, 238, 234, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(180, 74, 247, 0.05) 0%, transparent 50%);
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
      display: flex;
      justify-content: center;
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
      border: 1px solid rgba(255, 255, 255, 0.1);
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
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
      background-size: 20px 20px;
    }
    /* Main content */
    main {
      flex-grow: 1;
      position: relative;
      z-index: 10;
      padding: 1rem 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    /* Register Form */
    .register-container {
      width: 100%;
      max-width: 450px;
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(180, 74, 247, 0.3);
      box-shadow: 0 0 20px rgba(180, 74, 247, 0.1);
      padding: 2rem;
      position: relative;
      overflow: hidden;
      animation: fadeIn 0.5s ease-out forwards;
    }
    .register-blob-1 {
      position: absolute;
      top: -5rem;
      right: -5rem;
      width: 10rem;
      height: 10rem;
      background-color: #b44af7;
      border-radius: 50%;
      opacity: 0.1;
      filter: blur(3rem);
    }
    .register-blob-2 {
      position: absolute;
      bottom: -5rem;
      left: -5rem;
      width: 10rem;
      height: 10rem;
      background-color: #3b82f6;
      border-radius: 50%;
      opacity: 0.1;
      filter: blur(3rem);
    }
    .register-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 2rem;
      position: relative;
      z-index: 1;
    }
    .register-icon {
      position: relative;
      width: 4rem;
      height: 4rem;
      margin-bottom: 1rem;
    }
    .register-icon-glow {
      position: absolute;
      inset: 0;
      background: linear-gradient(to right, var(--neon-purple), var(--neon-blue));
      border-radius: 50%;
      opacity: 0.75;
      filter: blur(8px);
    }
    .register-icon-inner {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    .register-title {
      font-size: 1.5rem;
      text-align: center;
      color: var(--neon-purple);
      text-shadow: 0 0 5px var(--neon-purple), 0 0 10px var(--neon-purple);
    }
    .register-form {
      position: relative;
      z-index: 1;
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-label {
      display: block;
      font-size: 0.75rem;
      color: #63b3ed;
      margin-bottom: 0.25rem;
    }
    .input-wrapper {
      position: relative;
    }
    .form-input {
      width: 100%;
      background-color: rgba(10, 10, 10, 0.7);
      border: 1px solid var(--neon-purple);
      color: white;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border-radius: 0.25rem;
      font-family: 'Inter', sans-serif;
      transition: all 0.3s ease;
    }
    .form-input:focus {
      outline: none;
      border-color: var(--neon-blue);
      box-shadow: 0 0 10px var(--neon-blue);
    }
    .input-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #a78bfa;
    }
    .password-toggle {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #a78bfa;
      cursor: pointer;
    }
    .password-toggle:hover {
      color: #4ade80;
    }
    .register-button {
      width: 100%;
      background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
      color: white;
      font-family: 'Space Mono', monospace;
      font-weight: 600;
      padding: 0.75rem 1.5rem;
      border-radius: 0.25rem;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .register-button:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 10px var(--neon-purple), 0 0 20px var(--neon-purple);
    }
    .register-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: all 0.6s ease;
    }
    .register-button:hover::before {
      left: 100%;
    }
    .register-button svg {
      margin-right: 0.5rem;
    }
    .error-message {
      background-color: rgba(239, 68, 68, 0.1);
      border: 1px solid #ef4444;
      color: #ef4444;
      padding: 0.75rem;
      border-radius: 0.25rem;
      font-size: 0.875rem;
      text-align: center;
      margin-top: 1rem;
      display: <?php echo ($error !== '') ? 'block' : 'none'; ?>;
    }
    .success-message {
      background-color: rgba(34, 197, 94, 0.1);
      border: 1px solid #22c55e;
      color: #22c55e;
      padding: 0.75rem;
      border-radius: 0.25rem;
      font-size: 0.875rem;
      text-align: center;
      margin-top: 1rem;
      display: none;
    }
    .register-link {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.875rem;
      color: #63b3ed;
    }
    .register-link a {
      color: #a78bfa;
      text-decoration: none;
      transition: color 0.3s;
    }
    .register-link a:hover {
      color: var(--neon-purple);
    }
    /* Footer */
    footer {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
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
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
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
            <a href="home.php" class="nav-link">
              <i data-lucide="home" width="16" height="16"></i>
              Home
            </a>
            <a href="catalogue.php" class="nav-link">
              <i data-lucide="shopping-bag" width="16" height="16"></i>
              Catalogue
            </a>
            <a href="about.php" class="nav-link">
              <i data-lucide="info" width="16" height="16"></i>
              About
            </a>
            <a href="home.php" class="nav-link">
              <i data-lucide="layout-dashboard" width="16" height="16"></i>
              Dashboard
            </a>
            <a href="admin.php" class="nav-link">
              <i data-lucide="shield" width="16" height="16"></i>
              Council
            </a>
            <a href="register.php" class="nav-link active">
              <i data-lucide="user-plus" width="16" height="16"></i>
              Register
            </a>
            <a href="login.php" class="nav-link">
              <i data-lucide="log-in" width="16" height="16"></i>
              Login
            </a>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <!-- Main content -->
  <main>
    <div class="container">
      <div class="register-container">
        <div class="register-blob-1"></div>
        <div class="register-blob-2"></div>
        <div class="register-header">
          <div class="register-icon">
            <div class="register-icon-glow"></div>
            <div class="register-icon-inner">
              <i data-lucide="user-plus" width="24" height="24"></i>
            </div>
          </div>
          <h2 class="register-title">Register New Entity</h2>
        </div>
        <form id="registerForm" method="POST" class="register-form">
          <div class="form-group">
            <label for="name" class="form-label">Full Name:</label>
            <div class="input-wrapper">
              <i data-lucide="user" class="input-icon" width="16" height="16"></i>
              <input
                type="text"
                id="name"
                name="name"
                class="form-input"
                placeholder="Enter your full name"
                required
              >
            </div>
          </div>
          <div class="form-group">
            <label for="username" class="form-label">Username:</label>
            <div class="input-wrapper">
              <i data-lucide="at-sign" class="input-icon" width="16" height="16"></i>
              <input
                type="text"
                id="username"
                name="username"
                class="form-input"
                placeholder="Choose a username"
                required
              >
            </div>
          </div>
          <div class="form-group">
            <label for="email" class="form-label">Email:</label>
            <div class="input-wrapper">
              <i data-lucide="mail" class="input-icon" width="16" height="16"></i>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                placeholder="Enter your email address"
                required
              >
            </div>
          </div>
          <div class="form-group">
            <label for="password" class="form-label">Password:</label>
            <div class="input-wrapper">
              <i data-lucide="lock" class="input-icon" width="16" height="16"></i>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="Create a password"
                required
              >
              <button
                type="button"
                id="togglePassword"
                class="password-toggle"
                aria-label="Toggle password visibility"
              >
                <i data-lucide="eye" width="16" height="16" id="passwordIcon"></i>
              </button>
            </div>
          </div>
          <div class="form-group">
            <label for="confirmPassword" class="form-label">Confirm Password:</label>
            <div class="input-wrapper">
              <i data-lucide="check-circle" class="input-icon" width="16" height="16"></i>
              <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                class="form-input"
                placeholder="Confirm your password"
                required
              >
              <button
                type="button"
                id="toggleConfirmPassword"
                class="password-toggle"
                aria-label="Toggle password visibility"
              >
                <i data-lucide="eye" width="16" height="16" id="confirmPasswordIcon"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="register-button">
            <i data-lucide="user-plus" width="16" height="16"></i>
            Register
          </button>
          <div id="errorMessage" class="error-message"><?= htmlspecialchars($error) ?></div>
          <div id="successMessage" class="success-message"></div>
        </form>
        <div class="login-link">
          <p style="text-align: center;margin-top: 5px;">
            Already have an account?
            <a href="login.php">Login</a>
          </p>
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

    // Toggle password visibility for password field
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');

    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);

      if (type === 'text') {
        passwordIcon.setAttribute('data-lucide', 'eye-off');
      } else {
        passwordIcon.setAttribute('data-lucide', 'eye');
      }
      lucide.createIcons();
    });

    // Toggle password visibility for confirm password field
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');

    toggleConfirmPassword.addEventListener('click', function() {
      const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPassword.setAttribute('type', type);

      if (type === 'text') {
        confirmPasswordIcon.setAttribute('data-lucide', 'eye-off');
      } else {
        confirmPasswordIcon.setAttribute('data-lucide', 'eye');
      }
      lucide.createIcons();
    });

    // (Optional: Client-side validation can be added here)
    // Since the form now submits to the PHP backend, the PHP will handle registration.
  </script>
</body>
</html>
