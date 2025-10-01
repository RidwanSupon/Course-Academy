<?php
// header.php
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ILM PATH NETWORK</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --ilm-blue: #0b1d3d;
      --ilm-gold: #f2a900;
    }
    .ilm-bg-blue { background-color: var(--ilm-blue); }
    .ilm-text-gold { color: var(--ilm-gold); }
    .ilm-bg-gold { background-color: var(--ilm-gold); }
  </style>
</head>

<header class="ilm-bg-blue p-4 flex justify-between items-center fixed top-0 w-full z-50 shadow-lg">
  <!-- Logo -->
  <div class="flex items-center">
    <img src="assets/images/logo.png" alt="Logo" class="h-10 mr-3">
    <span class="text-white text-lg font-bold">ILM PATH NETWORK</span>
  </div>

<!-- Desktop Navigation -->
<nav class="hidden md:flex space-x-6 text-white font-semibold">
    <a href="index.php#home" class="hover:ilm-text-gold transition">Home</a>
    <a href="index.php#courses" class="hover:ilm-text-gold transition">Courses</a>
    <a href="index.php#mentors" class="hover:ilm-text-gold transition">Mentors</a>
    <a href="index.php#board" class="hover:ilm-text-gold transition">Board</a>
    <a href="index.php#gallery" class="hover:ilm-text-gold transition">Video & Gallery</a>
    <a href="index.php#e-books" class="hover:ilm-text-gold transition">E-books</a>
</nav>

  <!-- Mobile Menu Button -->
  <button id="menu-toggle" class="text-white focus:outline-none p-2 rounded-md hover:bg-gray-700 md:hidden">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
    </svg>
  </button>
</header>

<!-- Mobile Menu -->
<div id="mobile-menu" class="fixed inset-0 ilm-bg-blue z-40 transform translate-x-full transition-transform duration-300 ease-in-out pt-20 md:hidden">
  <div class="p-8 flex flex-col space-y-6">
    <nav class="flex flex-col space-y-4">
      <a href="index.php#home" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Home</a>
      <a href="index.php#courses" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Courses</a>
      <a href="index.php#mentors" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Mentors</a>
      <a href="index.php#board" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Board</a>
      <a href="index.php#gallery" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Video & Gallery</a>
      <a href="index.php#e-books" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">E-books</a>
    </nav>
    <a href="#enroll" class="inline-block ilm-bg-gold text-ilm-blue mt-8 px-6 py-3 rounded-full font-bold shadow-xl hover:opacity-90 transition">
      Join Now
    </a>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if(menuToggle && mobileMenu){
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('translate-x-full');
        });

        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => mobileMenu.classList.add('translate-x-full'));
        });
    }
});
</script>
