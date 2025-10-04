<?php
// includes/footer.php
// (</main> tag এর পর থেকে শুরু)
?>
</main>

<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:gap-12 text-sm border-b border-gray-700 pb-10 mb-8">
            
            <div>
                <h3 class="text-xl font-bold text-green-400 mb-4">ILM PATH NETWORK</h3>
                <p class="text-gray-400 text-xs leading-relaxed">
                    আমাদের লক্ষ্য হলো **সহীহ ইলম ও প্রশিক্ষণের** মাধ্যমে নতুন প্রজন্মের জন্য একটি শক্তিশালী ইসলামী নেটওয়ার্ক তৈরি করা।
                </p>
                <div class="flex space-x-4 mt-4">
                    <a href="https://www.facebook.com/profile.php?id=61580549275686" target="_blank" class="text-blue-600 hover:text-green-900 transition duration-300" title="Facebook">
                        <svg class="w-5 h-5 fill-current" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M504 256C504 114.6 397.4 8 256 8S8 114.6 8 256c0 128.7 90.6 236.8 209.3 254.9v-197H166.7V256h50.6v-23.7c0-60.8 27.5-89.3 75.3-89.3 12.8 0 25 1.7 37.3 3.5V176c-13.7-1.3-25.2-2-36.8-2-33 0-53 14.8-53 47.9V256h88.6l-14 88.6H275.4V510.9C397.4 492.8 504 384 504 256z"/></svg>
                    </a>
                    <a href="mailto:YOUR_EMAIL_ADDRESS" class="text-red-500 hover:text-red-700 transition duration-300" title="Email/Gmail">
    <svg class="w-5 h-5 fill-current" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path d="M48 64C21.5 64 0 85.5 0 112V400c0 26.5 21.5 48 48 48H464c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48H48zM464 160.8V400H48V160.8l208 144L464 160.8zM256 262.5L464 128H48L256 262.5z"/>
    </svg>
</a>


                    </div>
            </div>

            <div>
                <h3 class="text-base font-semibold text-gray-300 mb-4 uppercase tracking-wider">Courses & Programs</h3>
                <ul class="space-y-2">
                    <li><a href="index.php#courses" class="text-gray-400 hover:text-green-400 transition duration-300">All Courses</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Free Resources</a></li>
                    <li><a href="mentors.php" class="text-gray-400 hover:text-green-400 transition duration-300">Our Teachers</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Student Testimonials</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-base font-semibold text-gray-300 mb-4 uppercase tracking-wider">Support & Contact</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Contact Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">FAQ & Help Center</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Enrollment Process</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Refund Policy</a></li>
                </ul>
            </div>

            <div class="md:text-right">
                <h3 class="text-base font-semibold text-gray-300 mb-4 uppercase tracking-wider">Legal & Development</h3>
                <ul class="space-y-2 mb-4">
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Terms of Service</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition duration-300">Accessibility Statement</a></li>
                </ul>

                <div class="mt-6">
                    <p class="text-gray-400 text-xs">
                        Developed by:
                    </p>
                    <a href="https://www.facebook.com/ridwanur.r.mazumder/" target="_blank" class="text-green-400 hover:text-green-300 transition duration-300 font-bold text-sm block md:text-right mt-1">
                        Ridwanur R. Mazumder 
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center text-gray-500 text-xs">
            &copy; <?= date('Y') ?> **At-Tatweer International**. All rights reserved.
        </div>
    </div>
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