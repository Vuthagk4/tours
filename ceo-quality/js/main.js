document.addEventListener('DOMContentLoaded', function () {
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', function () {
            // Close all other open questions
            faqQuestions.forEach(q => {
                if (q !== question && q.classList.contains('active')) {
                    q.classList.remove('active');
                    q.nextElementSibling.style.maxHeight = '0';
                    q.nextElementSibling.style.paddingTop = '0';
                }
            });

            // Toggle current question
            this.classList.toggle('active');
            const answer = this.nextElementSibling;

            if (this.classList.contains('active')) {
                // Add delay before expanding
                setTimeout(() => {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    answer.style.paddingTop = '5px';

                    // Smooth scroll to the question
                    setTimeout(() => {
                        this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 300);
                }, 200);
            } else {
                answer.style.maxHeight = '0';
                answer.style.paddingTop = '0';
            }
        });
    });
});
// section photo
document.addEventListener('DOMContentLoaded', function () {
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');

    let currentIndex = 0;
    const slideCount = slides.length;

    // Clone first and last slides for infinite loop
    const firstClone = slides[0].cloneNode(true);
    const lastClone = slides[slideCount - 1].cloneNode(true);

    firstClone.id = 'first-clone';
    lastClone.id = 'last-clone';

    slider.appendChild(firstClone);
    slider.insertBefore(lastClone, slides[0]);

    // Adjust slider position to show the first real slide
    slider.style.transform = `translateX(-${100}%)`;

    // Handle navigation
    function goToSlide(index) {
        slider.style.transition = 'transform 0.5s ease';
        slider.style.transform = `translateX(-${(index + 1) * 100}%)`;
        currentIndex = index;
    }

    function handleTransitionEnd() {
        // If we're at the first clone (which is actually the last slide), jump to the real last slide
        if (currentIndex === -1) {
            slider.style.transition = 'none';
            currentIndex = slideCount - 1;
            slider.style.transform = `translateX(-${(currentIndex + 1) * 100}%)`;
        }
        // If we're at the last clone (which is actually the first slide), jump to the real first slide
        else if (currentIndex === slideCount) {
            slider.style.transition = 'none';
            currentIndex = 0;
            slider.style.transform = `translateX(-${(currentIndex + 1) * 100}%)`;
        }
    }

    prevBtn.addEventListener('click', function () {
        if (currentIndex <= -1) return;
        goToSlide(currentIndex - 1);
    });

    nextBtn.addEventListener('click', function () {
        if (currentIndex >= slideCount) return;
        goToSlide(currentIndex + 1);
    });

    slider.addEventListener('transitionend', handleTransitionEnd);

    // Auto-advance slides (optional)
    // setInterval(() => {
    //     nextBtn.click();
    // }, 5000);
});
// modal pop for class package list
// Open Modal with dynamic tour information
function openModal(destination) {
    const modal = document.getElementById("modal");
    const modalTitle = document.getElementById("modalTitle");
    const modalType = document.getElementById("modalType");
    const modalLocation = document.getElementById("modalLocation");
    const modalFeatures = document.getElementById("modalFeatures");
    const modalPrice = document.getElementById("modalPrice");
    const modalDescription = document.getElementById("modalDescription");
    const modalImage = document.getElementById("modalImage");

    if (destination === "Bali") {
        modalTitle.innerText = "Package Name : Soulmate Special Bali - 7 Night";
        modalType.innerText = "Package Type : Family Package"
        modalLocation.innerText = "Package Location : Indonesia(Ball)";
        modalFeatures.innerText = "Features : Free Pickup and drop facility. Wi-fi, Free Profesional Guide";
        modalPrice.innerText = "GenTotal : USD 5000";
        modalDescription.innerText = "The Soulmate Special Bali - 7 Night  package is a Bali honeymoon or romantic getaway package for couples, offering a 7-night stay with a focus on shared experiences. These packages often include accommodation, transfers, and sightseeing. Some popular honeymoon spots in Bali include Uluwatu Temple, Ubud's rice terraces, Nusa Dua's beaches, and the cliffs of Nusa Penida.";
        modalImage.src = "../images/bali.webp";
    }
    if (destination === "Guwahati") {
        modalTitle.innerText = "Package Name : 6 Day in Guwahati and Shillong With Cherrapunji Excursion";
        modalType.innerText = "Package Type : Family Package"
        modalLocation.innerText = "Package Location: Guwahati (Sikkim)";
        modalFeatures.innerText = "Features: Breakfast, Accommodation, Pick-up, Drop, Sightseeing";
        modalPrice.innerText = "GenTotal USD 4500";
        modalDescription.innerText = "After arrival at Guwahati airport, meet our representative & proceed for Shillong. Visit Barapani lake, Police bazar, and experience the serene beauty of Meghalaya.";
        modalImage.src = "../images/sikki.webp"; // Change to your Guwahati image path
    }
    if (destination === "Dubai") {
        modalTitle.innerText = "Package Name : Short Trip Dubai";
        modalType.innerText = "Package Type : Family Package"
        modalLocation.innerText = "Package Location: Dubai";
        modalFeatures.innerText = "Features: Free Pickup and drop facility. Wi-fi, Free Breakfast";
        modalPrice.innerText = "GenTotal USD 7000";
        modalDescription.innerText = "A Short Trip Dubai package is a travel itinerary designed for a brief visit to Dubai, typically lasting a few days. It offers a curated selection of attractions, activities, and accommodations to maximize your time in the city. Common components include city tours, visits to iconic landmarks like the Burj Khalifa and Dubai Mall, and potentially a Dhow cruise";
        modalImage.src = "../images/dubai.jpg"; // Change to your Guwahati image path
    }
    else if (destination === "Bhutan") {
        modalTitle.innerText = "Package Name : Bhutan Holiday - Thimphu and Para Special";
        modalType.innerText = "Package Type : Family Package"
        modalLocation.innerText = "Package Location: Bhutan";
        modalFeatures.innerText = "Features: Free Wi-fi, Free Breakfast, Free Pickup and Drop facility";
        modalPrice.innerText = "GenTotal USD 2500";
        modalDescription.innerText = "he package name 'Bhutan Holiday - Thimphu and Paro Special' indicates a travel package focused on exploring Thimphu and Paro, two prominent cities in Bhutan. It likely features sightseeing, cultural experiences, and potentially adventure activities within these two regions.";
        modalImage.src = "../images/bhutan.jpg"; // Change to your Guwahati image path
    }
    modal.style.display = "block";
}

// Close Modal
function closeModal() {
    document.getElementById("modal").style.display = "none";
}

// Close modal if clicked outside content
window.onclick = function (event) {
    const modal = document.getElementById("modal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
