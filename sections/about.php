<section id="about-us" class="bg-gray-50 py-16 px-4 sm:px-6 lg:px-8 text-gray-900">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-extrabold text-gray-900 mb-12 text-center opacity-0 animate-on-scroll" 
            data-animation="fade-up" 
            data-delay="0.1">
            About At-Tatweer Institute
        </h2>

        <div class="flex flex-col lg:flex-row lg:space-x-12 space-y-12 lg:space-y-0">

            <div 
                class="lg:w-1/2 flex flex-col justify-center p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 opacity-0 animate-on-scroll"
                data-animation="fade-right"
                data-delay="0.3"
            >
                <h3 class="text-2xl font-bold ilm-text-gold mb-4">Our Mission</h3>
                <p class="text-gray-700 mb-6">
                    At-Tatweer Institute is a trusted and pioneering online platform dedicated to academic Islamic education with a modern vision. Our mission is to **empower students with authentic Islamic knowledge** while equipping them with the intellectual and professional skills necessary to excel in today’s world.
                </p>
                <p class="text-gray-700">
                    Under the direct supervision of distinguished scholars from **Al-Azhar University**, we provide a well-structured learning environment where traditional Islamic sciences are taught alongside contemporary subjects. This unique approach ensures that students develop a strong foundation in faith, character, and knowledge — including recitation and memorization of the Holy Qur’an, Aqeedah, Hadith, Tafsir, Fiqh, Arabic literature, and spoken English — while also preparing them to meet the challenges of modern society.
                </p>
            </div>

            <div 
                class="lg:w-1/2 flex flex-col justify-center p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 opacity-0 animate-on-scroll"
                data-animation="fade-left"
                data-delay="0.5"
            >
                <h3 class="text-2xl font-bold ilm-text-gold mb-4">Our Commitment</h3>
                <ul class="list-disc pl-5 text-gray-700 mb-6 space-y-2 custom-marker">
                    <li>Authentic Islamic scholarship guided by world-renowned scholars</li>
                    <li>Modern academic and professional courses tailored to today’s needs</li>
                    <li>Well-structured online classes and resources accessible from anywhere in the world</li>
                    <li>A student-centered approach with personalized guidance and mentorship</li>
                    <li>A safe, reliable, and inspiring environment for learners of all ages</li>
                </ul>

                <h3 class="text-2xl font-bold ilm-text-gold mb-4">What Makes Us Different</h3>
                <ul class="list-disc pl-5 text-gray-700 space-y-2 custom-marker">
                    <li>A recognized and reliable source of **authentic Islamic education**</li>
                    <li>A unique balance between Islamic tradition and modern learning</li>
                    <li>Direct access to qualified, reputable, and internationally respected instructors</li>
                    <li>Opportunities for both children and adults to learn with excellence from the comfort of their homes</li>
                </ul>
            </div>
        </div>

        <p 
            class="mt-12 text-center text-gray-700 text-lg p-6 bg-white rounded-lg shadow-md opacity-0 animate-on-scroll"
            data-animation="fade-up"
            data-delay="0.7">
            At-Tatweer Institute is more than just an educational platform — it is a gateway to building a future rooted in knowledge, values, and excellence. <br>
            <strong>Join us today and take the first step toward a transformative learning experience that nurtures both faith and intellect.</strong>
        </p>
    </div>
</section>

<script>
    // ----------------------------------------------------------------------
    // 1. Custom CSS & Keyframes
    // ----------------------------------------------------------------------
    
    // Add these styles to your main <style> block or an external CSS file
    const customStyles = `
        :root {
            --ilm-blue: #0b1d3d;
            --ilm-gold: #f2a900;
        }
        .ilm-bg-blue { background-color: var(--ilm-blue); }
        .ilm-text-gold { color: var(--ilm-gold); }
        .ilm-bg-gold { background-color: var(--ilm-gold); }
        
        /* Custom Marker Color fallback for compatibility */
        .custom-marker li::marker {
            color: var(--ilm-gold);
        }

        /* Animation Keyframes */
        @keyframes custom-fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes custom-fade-right {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes custom-fade-left {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Animation Classes */
        .animate-show {
            animation-duration: 0.8s;
            animation-fill-mode: both;
        }
        .fade-up { animation-name: custom-fade-up; }
        .fade-right { animation-name: custom-fade-right; }
        .fade-left { animation-name: custom-fade-left; }
    `;

    // Inject styles into the document head
    const styleSheet = document.createElement("style");
    styleSheet.type = "text/css";
    styleSheet.innerText = customStyles;
    document.head.appendChild(styleSheet);


    // ----------------------------------------------------------------------
    // 2. Intersection Observer Logic (Replaces AOS.init)
    // ----------------------------------------------------------------------

    document.addEventListener('DOMContentLoaded', () => {
        const observerOptions = {
            root: null, // relative to the viewport
            rootMargin: '0px',
            threshold: 0.2 // Trigger when 20% of the element is visible
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animationType = element.getAttribute('data-animation');
                    const delay = element.getAttribute('data-delay');

                    // Apply the animation classes and delay
                    element.style.animationDelay = `${delay}s`;
                    element.classList.remove('opacity-0');
                    element.classList.add('animate-show', animationType);

                    // Stop observing after the animation has been triggered once
                    observer.unobserve(element);
                }
            });
        }, observerOptions);

        // Target all elements with the custom marker class
        document.querySelectorAll('.animate-on-scroll').forEach(element => {
            observer.observe(element);
        });
    });
</script>