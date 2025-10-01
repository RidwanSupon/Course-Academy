<?php
// includes/footer.php
?>
</main>

<footer class="ilm-bg-blue text-white text-center py-4">
  &copy; <?= date('Y') ?> ILM PATH NETWORK. All rights reserved.
</footer>

<script>
// Mobile menu toggle
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
if(menuBtn) {
  menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('translate-x-full'));
  document.querySelectorAll('#mobile-menu a').forEach(a =>
      a.addEventListener('click', () => mobileMenu.classList.add('translate-x-full')));
}
</script>

</body>
</html>
