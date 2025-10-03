<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
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
<body>

<header class="ilm-bg-blue p-4 flex justify-between items-center fixed top-0 w-full z-50 shadow-lg">
    
    <div class="flex items-center space-x-3">
        <img src="assets/images/logo.png" alt="Logo" class="h-10">
        <div class="flex flex-col">
            <span class="text-white text-lg font-bold tracking-tight leading-tight" id="main-title">At-Tatweer</span>
            <span class="text-white text-sm font-semibold mt-1 whitespace-nowrap" id="sub-title">
                International Institute
            </span>
        </div>
    </div>

    <nav class="hidden md:flex space-x-6 text-white font-medium opacity-90 absolute left-1/2 transform -translate-x-1/2">
        <a href="index.php#home" class="hover:ilm-text-gold transition">Home</a>
        <a href="index.php#courses" class="hover:ilm-text-gold transition">Courses</a>
        <a href="mentors.php" class="hover:ilm-text-gold transition">Mentors</a>
        <a href="index.php#board" class="hover:ilm-text-gold transition">Board</a>
        <a href="index.php#gallery" class="hover:ilm-text-gold transition">Video & Gallery</a>
        <a href="index.php#e-books" class="hover:ilm-text-gold transition">E-books</a>
    </nav>

    <div class="hidden md:flex items-center space-x-4">
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="relative group">
                <button class="ilm-bg-gold text-ilm-blue px-4 py-2 rounded-full font-bold shadow-lg hover:opacity-90 transition flex items-center">
                    Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-300">
                    <a href="my-courses.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Courses</a>
                    <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <button id="joinNowBtn" class="ilm-bg-gold text-ilm-blue px-4 py-2 rounded-full font-bold shadow-lg hover:opacity-90 transition">
                Join Now
            </button>
        <?php endif; ?>
    </div>

    <button id="menu-toggle" class="text-white focus:outline-none p-2 rounded-md hover:bg-gray-700 md:hidden" aria-label="Toggle menu">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
    </button>
</header>

<div id="mobile-menu" class="fixed inset-0 ilm-bg-blue z-40 transform translate-x-full transition-transform duration-300 ease-in-out pt-20 md:hidden">
    <div class="p-8 flex flex-col space-y-6">
        <nav class="flex flex-col space-y-4">
            <a href="index.php#home" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Home</a>
            <a href="index.php#courses" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Courses</a>
            <a href="mentors.php" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Mentors</a>
            <a href="index.php#board" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Board</a>
            <a href="index.php#gallery" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">Video & Gallery</a>
            <a href="index.php#e-books" class="text-white text-xl p-2 rounded-lg hover:ilm-bg-gold transition">E-books</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="ml-4 pt-4 font-semibold text-gray-300 border-t border-gray-700 mt-4">Welcome - <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="ilm-bg-gold text-ilm-blue px-4 py-2 rounded-full font-bold shadow-lg hover:opacity-90 transition w-fit">Logout</a>
            <?php endif; ?>
        </nav>

        <?php if(!isset($_SESSION['user_id'])): ?>
            <button id="joinNowBtnMobile" class="ilm-bg-gold text-ilm-blue mt-8 px-6 py-3 rounded-full font-bold shadow-xl hover:opacity-90 transition w-full">Join Now</button>
        <?php endif; ?>
    </div>
</div>


<div id="authModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50 p-4">
  <div class="bg-white rounded-lg shadow-xl p-8 max-w-sm w-full relative">
    <button id="closeModal" class="absolute top-2 right-4 text-3xl font-light text-gray-500 hover:text-gray-800" aria-label="Close modal">&times;</button>
    <h2 class="text-2xl font-bold ilm-text-gold mb-4 text-center">Login / Sign Up</h2>

    <form id="loginForm" class="space-y-4">
      <input type="hidden" name="action" value="login">
      <input type="email" name="email" placeholder="Email" required class="w-full border rounded-lg p-2 focus:ring-ilm-gold focus:border-ilm-gold">
      <input type="password" name="password" placeholder="Password" required class="w-full border rounded-lg p-2 focus:ring-ilm-gold focus:border-ilm-gold">
      <button type="submit" class="ilm-bg-gold text-ilm-blue w-full py-2 rounded-lg font-bold hover:opacity-90 transition">Login</button>
      <p class="text-sm mt-2 text-center">Don't have an account? <span id="showSignup" class="text-ilm-gold cursor-pointer font-medium">Sign Up</span></p>
    </form>

    <form id="signupForm" class="space-y-4 hidden">
      <input type="hidden" name="action" value="signup">
      <input type="text" name="name" placeholder="Full Name" required class="w-full border rounded-lg p-2 focus:ring-ilm-gold focus:border-ilm-gold">
      <input type="email" name="email" placeholder="Email" required class="w-full border rounded-lg p-2 focus:ring-ilm-gold focus:border-ilm-gold">
      <input type="text" name="phone" placeholder="Phone" required class="w-full border rounded-lg p-2 focus:ring-ilm-gold focus:border-ilm-gold">
      <input type="password" name="password" placeholder="Password" required class="w-full border rounded-lg p-2 focus:ring-ilm-gold focus:border-ilm-gold">
      <button type="submit" class="ilm-bg-gold text-ilm-blue w-full py-2 rounded-lg font-bold hover:opacity-90 transition">Sign Up</button>
      <p class="text-sm mt-2 text-center">Already have an account? <span id="showLogin" class="text-ilm-gold cursor-pointer font-medium">Login</span></p>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    // Improved accessibility and state management for mobile menu
    menuToggle?.addEventListener('click', () => {
        const isExpanded = mobileMenu.classList.contains('translate-x-full');
        mobileMenu.classList.toggle('translate-x-full');
        menuToggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
    });

    // Close mobile menu when a link is clicked
    document.querySelectorAll('#mobile-menu a').forEach(link => link.addEventListener('click', () => {
        mobileMenu.classList.add('translate-x-full');
        menuToggle.setAttribute('aria-expanded', 'false');
    }));

    // Auth modal toggle
    const authModal = document.getElementById('authModal');
    const joinNowBtn = document.getElementById('joinNowBtn');
    const joinNowBtnMobile = document.getElementById('joinNowBtnMobile');
    const closeModal = document.getElementById('closeModal');
    const loginFormEl = document.getElementById('loginForm');
    const signupFormEl = document.getElementById('signupForm');
    const showSignup = document.getElementById('showSignup');
    const showLogin = document.getElementById('showLogin');

    // Function to open modal
    const openAuthModal = () => {
        authModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Prevent scrolling body when modal is open
        loginFormEl.classList.remove('hidden');
        signupFormEl.classList.add('hidden');
    };

    // Function to close modal
    const closeAuthModal = () => {
        authModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    [joinNowBtn, joinNowBtnMobile].forEach(btn => btn?.addEventListener('click', openAuthModal));
    closeModal.addEventListener('click', closeAuthModal);
    
    // Close modal when clicking outside
    authModal.addEventListener('click', (e) => {
        if (e.target === authModal) {
            closeAuthModal();
        }
    });


    showSignup.addEventListener('click', () => {
        loginFormEl.classList.add('hidden');
        signupFormEl.classList.remove('hidden');
    });

    showLogin.addEventListener('click', () => {
        signupFormEl.classList.add('hidden');
        loginFormEl.classList.remove('hidden');
    });

    // AJAX submission for login & signup (Logic remains correct)
    async function handleAuthSubmit(form) {
        const formData = new FormData(form);
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                // On success redirect to index page
                window.location.href = 'index.php';
            } else {
                alert(result.message || 'Something went wrong.');
            }
        } catch (error) {
            console.error(error);
            alert('Server error. Please try again.');
        }
    }

    loginFormEl.addEventListener('submit', e => {
        e.preventDefault();
        handleAuthSubmit(loginFormEl);
    });

    signupFormEl.addEventListener('submit', e => {
        e.preventDefault();
        handleAuthSubmit(signupFormEl);
    });
});
</script>

</body>
</html>