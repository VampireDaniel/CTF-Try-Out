<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About - Galactic Federation Shop System</title>
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
    }
    /* About Section */
    .about-section {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 1.5rem;
      margin-bottom: 2rem;
      position: relative;
      overflow: hidden;
      animation: fadeIn 0.5s ease-out forwards;
    }
    .about-blob-1 {
      position: absolute;
      top: -5rem;
      right: -5rem;
      width: 16rem;
      height: 16rem;
      background-color: #3b82f6;
      border-radius: 50%;
      opacity: 0.1;
      filter: blur(3rem);
    }
    .about-blob-2 {
      position: absolute;
      bottom: -5rem;
      left: -5rem;
      width: 16rem;
      height: 16rem;
      background-color: #22c55e;
      border-radius: 50%;
      opacity: 0.1;
      filter: blur(3rem);
    }
    .about-content {
      position: relative;
      z-index: 1;
    }
    .about-title {
      font-size: 1.875rem;
      margin-bottom: 1rem;
      text-shadow: 0 0 5px var(--neon-green), 0 0 10px var(--neon-green);
    }
    .about-text {
      color: #d1d5db;
      line-height: 1.6;
      margin-bottom: 1rem;
    }
    /* Profile Section */
    .profile-section {
      background-color: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
      animation: fadeIn 0.5s ease-out forwards;
      animation-delay: 0.2s;
      opacity: 0;
    }
    .profile-blob {
      position: absolute;
      top: -5rem;
      right: -5rem;
      width: 16rem;
      height: 16rem;
      background-color: #a855f7;
      border-radius: 50%;
      opacity: 0.1;
      filter: blur(3rem);
    }
    .profile-content {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      position: relative;
      z-index: 1;
    }
    @media (min-width: 768px) {
      .profile-content {
        flex-direction: row;
      }
    }
    .profile-image-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }
    @media (min-width: 768px) {
      .profile-image-container {
        max-width: 33.333333%;
      }
    }
    .profile-image-wrapper {
      position: relative;
      width: 100%;
      max-width: 16rem;
      aspect-ratio: 1;
    }
    .profile-image-glow {
      position: absolute;
      inset: 0;
      background: linear-gradient(to right, var(--neon-blue), var(--neon-green));
      border-radius: 50%;
      opacity: 0.75;
      filter: blur(1rem);
    }
    .profile-image {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #3b82f6;
    }
    .profile-details {
      flex: 2;
    }
    .profile-name {
      font-size: 1.5rem;
      margin-bottom: 0.75rem;
      color: var(--neon-blue);
      text-shadow: 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue);
    }
    .profile-section-title {
      font-size: 1rem;
      font-weight: bold;
      color: #63b3ed;
      margin-bottom: 0.25rem;
      margin-top: 1rem;
    }
    .profile-section-title:first-child {
      margin-top: 0;
    }
    .contact-info {
      margin-bottom: 1rem;
    }
    .contact-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.25rem;
    }
    .contact-icon {
      color: #4ade80;
      flex-shrink: 0;
    }
    .contact-text {
      font-size: 0.875rem;
      color: #d1d5db;
    }
    .contact-link {
      color: #d1d5db;
      text-decoration: none;
      transition: color 0.3s;
    }
    .contact-link:hover {
      color: #4ade80;
    }
    .profile-text {
      font-size: 0.875rem;
      color: #d1d5db;
      line-height: 1.6;
    }
    .experience-item, .education-item {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
    }
    .experience-icon, .education-icon {
      color: #4ade80;
      flex-shrink: 0;
      margin-top: 0.125rem;
    }
    .experience-content, .education-content {
      flex: 1;
    }
    .experience-title, .education-title {
      font-weight: bold;
      color: #d1d5db;
      font-size: 0.875rem;
    }
    .experience-date, .education-date {
      font-size: 0.75rem;
      color: #9ca3af;
    }
    .skills-list {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .skill-tag {
      background-color: rgba(30, 58, 138, 0.5);
      color: #93c5fd;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
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
    /* Animations */
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
            <a href="about.php" class="nav-link active">
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
      <!-- About Section -->
      <section class="about-section">
        <div class="about-blob-1"></div>
        <div class="about-blob-2"></div>
        <div class="about-content">
          <h2 class="about-title">About Galactic Federation</h2>
          <p class="about-text">
            Galactic Federation Shop System is an interdimensional emporium where advanced science meets dazzling design. A renegade team of Ricks, armed with crystallized Mega Seeds and refined Fleeb juice, creates reality-twisting accessories fit for portal-hopping fashionistas. We rigorously test every piece across multiple universes to ensure its stability (and your safety), offering everything from meeseeks-blue diamonds to cosmic pendants that may open the odd wormhole.
          </p>
          <p class="about-text">
            Ethical sourcing is our top priority—no Cronenberg scenarios here. Whether you're a dimension-hopping daredevil or just a curious traveler, our collection promises vibrant style paired with ingenious tech. Ready to expand your wardrobe beyond the boundaries of known reality? Step through a portal and experience Galactic Federation Shop System, where each item is a brilliant fusion of science, style, and a dash of the bizarre.
          </p>
        </div>
      </section>

      <!-- Profile Section (Sample Resume for Rick Sanchez) -->
      <section class="profile-section">
        <div class="profile-blob"></div>
        <div class="profile-content">
          <div class="profile-image-container">
            <div class="profile-image-wrapper">
              <div class="profile-image-glow"></div>
              <img src="/assets/images/logo.png" alt="Rick Sanchez" class="profile-image">
            </div>
          </div>
          <div class="profile-details">
            <h2 class="profile-name">Rick Sanchez</h2>
            <h3 class="profile-section-title">Contact Information</h3>
            <div class="contact-info">
              <div class="contact-item">
                <i data-lucide="mail" class="contact-icon" width="16" height="16"></i>
                <a href="mailto:rick@c137.dim" class="contact-link">rick_c137@citadel.dim</a>
              </div>
              <div class="contact-item">
                <i data-lucide="phone" class="contact-icon" width="16" height="16"></i>
                <span class="contact-text">(137) 002-4567</span>
              </div>
            </div>
            <h3 class="profile-section-title">Objective</h3>
            <p class="profile-text">
              To leverage my extensive knowledge of interdimensional travel and scientific innovation to create groundbreaking technologies and solutions.
            </p>
            <h3 class="profile-section-title">Experience</h3>
            <div class="experience-list">
              <div class="experience-item">
                <i data-lucide="briefcase" class="experience-icon" width="16" height="16"></i>
                <div class="experience-content">
                  <div class="experience-title">Lead Scientist - Galactic Federation</div>
                  <div class="experience-date">2010 - Present</div>
                </div>
              </div>
              <div class="experience-item">
                <i data-lucide="lightbulb" class="experience-icon" width="16" height="16"></i>
                <div class="experience-content">
                  <div class="experience-title">Inventor - Self-employed</div>
                  <div class="experience-date">2000 - 2010</div>
                </div>
              </div>
            </div>
            <h3 class="profile-section-title">Education</h3>
            <div class="education-list">
              <div class="education-item">
                <i data-lucide="graduation-cap" class="education-icon" width="16" height="16"></i>
                <div class="education-content">
                  <div class="education-title">Ph.D. in Theoretical Physics</div>
                  <div class="education-date">University of Dimension C-137</div>
                </div>
              </div>
            </div>
            <h3 class="profile-section-title">Skills</h3>
            <div class="skills-list">
              <span class="skill-tag">Interdimensional Travel</span>
              <span class="skill-tag">Advanced Robotics</span>
              <span class="skill-tag">Quantum Mechanics</span>
              <span class="skill-tag">Portal Technology</span>
              <span class="skill-tag">Genetic Engineering</span>
            </div>
          </div>
        </div>
      </section>
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

    // Add active class to current page in navigation
    document.addEventListener('DOMContentLoaded', () => {
      const navLinks = document.querySelectorAll('.nav-link');
      navLinks.forEach(link => {
        if(link.getAttribute('href').includes('about')) {
          link.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>
