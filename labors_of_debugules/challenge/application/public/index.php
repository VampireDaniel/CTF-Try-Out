<?php
require_once('../includes/db.php');
$db = Database::getInstance();
$db->addSampleData();
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galactic Federation Shop System</title>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link href="/assets/css/style.css" rel="stylesheet" />


  <!-- Tailwind CSS via CDN with inline configuration -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'neon-green': '#39ff14',
            'neon-blue': '#4deeea',
            'neon-purple': '#b44af7',
            'neon-pink': '#ff00ff',
            'dark-bg': '#0a0a0a',
            'space-blue': '#0f172a',
            'glass-bg': 'rgba(15, 23, 42, 0.7)'
          },
          fontFamily: {
            'space-mono': ['"Space Mono"', 'monospace'],
            'inter': ['Inter', 'sans-serif']
          },
          boxShadow: {
            'neon-green': '0 0 5px #39ff14, 0 0 10px #39ff14',
            'neon-blue': '0 0 5px #4deeea, 0 0 10px #4deeea',
            'neon-purple': '0 0 5px #b44af7, 0 0 10px #b44af7'
          },
          animation: {
            'spin-slow': 'spin 10s linear infinite'
          }
        }
      }
    }
  </script>

</head>
<body>
  <!-- Optional: Background elements -->
  <div class="fixed inset-0 z-0 pointer-events-none">
    <div class="absolute top-25 left-25 w-64 h-64 bg-purple-600 rounded-full opacity-5 blur-3xl"></div>
    <div class="absolute bottom-33 right-25 w-96 h-96 bg-blue-600 rounded-full opacity-5 blur-3xl"></div>
    <div class="absolute top-66 left-50 w-80 h-80 bg-green-600 rounded-full opacity-5 blur-3xl"></div>
    <div class="absolute inset-0 bg-grid"></div>
  </div>

  <!-- Navigation (you can include a header.php here if desired) -->
  <header>
    <div class="container">
      <nav>
        <div class="nav-container">
          <div class="logo">

            <h1 class="logo-text">Galactic Federation</h1>
          </div>
          <div class="nav-links">
            <a href="/" class="nav-link">
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
            <a href="register.php" class="nav-link">
              <i data-lucide="user-plus" width="16" height="16"></i>
              Register
            </a>
            <a href="login.php" class="nav-link active">
              <i data-lucide="log-in" width="16" height="16"></i>
              Login
            </a>
          </div>
        </div>
      </nav>
    </div>
  </header>


  <!-- Main content -->
  <main class="container flex-grow px-4 py-4 relative z-10">
    <!-- Hero Section -->
    <section class="bg-glass-bg backdrop-blur-sm rounded-lg p-6 mb-8 shadow-lg relative overflow-hidden">
      <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-500 rounded-full opacity-10 filter blur-3xl"></div>
      <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-green-500 rounded-full opacity-10 filter blur-3xl"></div>
      <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative">
        <div class="md:w-1/2">
          <h2 class="text-3xl md:text-4xl font-bold mb-3 neon-text text-white">Interdimensional Tech Emporium</h2>
          <p class="text-base text-blue-200 mb-4">Explore gadgets and artifacts from across the multiverse, curated by the Council of Ricks.</p>
          <a href="catalogue.php" class="inline-block bg-gradient-to-r from-neon-green to-neon-blue text-dark-bg font-bold py-2 px-4 rounded uppercase tracking-wider transition transform hover:-translate-y-1">Browse Catalogue</a>
        </div>
        <div class="md:w-1/2 flex justify-center">
          <div class="relative w-48 h-48">
            <div class="absolute inset-0 bg-gradient-to-r from-neon-green to-neon-blue rounded-full opacity-75 filter blur-lg animate-spin-slow"></div>
            <img src="/assets/images/logo.png" alt="Portal" class="absolute inset-0 w-full h-full object-cover rounded-full border-2 border-blue-400">
          </div>
        </div>
      </div>
    </section>

    <!-- (Additional content such as featured products or contact sections can go here) -->

    <!-- Example Contact Section -->
    <section class="bg-glass-bg backdrop-blur-sm rounded-lg p-6 shadow-lg">
      <h2 class="text-2xl font-bold mb-4 neon-text text-purple-400">Contact the Federation</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-start">
          <i data-lucide="mail" width="20" height="20" class="text-neon-purple mr-2"></i>
          <div>
            <h3 class="text-base font-bold mb-1 text-neon-blue">Interdimensional Email</h3>
            <p>rick_c137@citadel.dim</p>
          </div>
        </div>
        <div class="flex items-start">
          <i data-lucide="map-pin" width="20" height="20" class="text-neon-purple mr-2"></i>
          <div>
            <h3 class="text-base font-bold mb-1 text-neon-blue">Headquarters</h3>
            <p>42 Galactic Way, Citadel of Ricks</p>
          </div>
        </div>
        <div class="flex items-start">
          <i data-lucide="phone" width="20" height="20" class="text-neon-purple mr-2"></i>
          <div>
            <h3 class="text-base font-bold mb-1 text-neon-blue">Quantum Telephone</h3>
            <p>(137) 002-4567</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-glass-bg backdrop-blur-sm rounded-lg p-4 mt-8 shadow-lg">
    <div class="container text-center">
      <p class="text-xs text-blue-300">
        &copy; <span id="current-year"></span> Galactic Federation Shop System |
        <span class="text-green-400 font-bold">Dimension C-137</span>
      </p>
    </div>
  </footer>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Set current year in footer
    document.getElementById('current-year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
