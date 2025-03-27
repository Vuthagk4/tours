// slider 2
let currentIndex = 0;
const slidesToShow = 3;
const totalSlides = 11;
const slider = document.getElementById('slider');

function moveSlide(direction) {
    const maxIndex = totalSlides - slidesToShow;
    currentIndex += direction;
    if (currentIndex < 0) {
        currentIndex = maxIndex;
    } else if (currentIndex > maxIndex) {
        currentIndex = 0;
    }
    const translateX = -(currentIndex * (100 / slidesToShow)) + '%';
    slider.style.transform = 'translateX(' + translateX + ')';
}
// start class contact
function toggleContact() {
    const contactSection = document.getElementById('contactSection');
    contactSection.classList.toggle('show');
}
// end class contact