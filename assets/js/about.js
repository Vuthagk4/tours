function openModal(name, position, from, gender, skill, email) {
  document.getElementById("modalName").innerText = name;
  document.getElementById("modalPosition").innerText = position;
  document.getElementById("modalFrom").innerText = from;
  document.getElementById("modalGender").innerText = gender;
  document.getElementById("modalSkill").innerText = skill;
  document.getElementById("modalEmail").innerText = email;
  document.getElementById("infoModal").style.display = "block";
}

function closeModal() {
  document.getElementById("infoModal").style.display = "none";
}

window.onclick = function (event) {
  const modal = document.getElementById("infoModal");
  if (event.target === modal) {
    closeModal();
  }
};

// Team member data array
const teamMembers = [
  {
    image: "../images/phorn.jpg",
    name: "John PHORN",
    description: "បុរសគុណភាព",
    modalName: "SO SAOPHORN 🐝",
    position: "Frontend Developer",
    from: "Kompong Chhnang Province",
    gender: "Male",
    skill: "Build UI Website",
    email: "sosaophorn3@gmail.com",
  },
  {
    image: "../images/sak.jpg",
    name: "John SAK",
    description: "បុរសជោគជាំ",
    modalName: "HANG OUDAMSAK",
    position: "Work Building Time Square3",
    from: "Kandal Province",
    gender: "Male",
    skill: "Support",
    email: "hangoudamsak@gmail.com",
  },
  {
    image: "../images/nang.jpg",
    name: "John NANG",
    description: "សុីអីអូ ជិកជាំ",
    modalName: "VEN SOMNANG",
    position: "Work IT Center",
    from: "Kompong Chhnang Province",
    gender: "Male",
    skill: "Support",
    email: "vensonang@gmail.com",
  },
  {
    image:
      "http://127.0.0.1:5500/ceo-quality/images/Lonely%20anime%20lovers.jpg",
    name: "John VUTHA",
    description: "បងចេកស្មោះស្នេហ៏",
    modalName: "ORN VUTHA",
    position: "Work Backend Developer",
    from: "Kampong Cham",
    gender: "Male",
    skill: "Connect Between And Backend",
    email: "vutha@gmail.com",
  },
  {
    image: "../images/Lonely anime lovers.jpg",
    name: "Jonh THANY",
    description: "បុរសអៀនស្រី",
    modalName: "ORN THANY",
    position: "Work Backend Developer",
    from: "Kampong Cham",
    gender: " mûle",
    skill: "Connect Between And Backend",
    email: "thany@gmail.com",
  },
  {
    image: "../images/sal.JPG",
    name: "John VISAL",
    description: "បុរសរ៉ោករ៉ាក",
    modalName: "SUN VISAL",
    position: "WORK HR",
    from: "Kampong Cham",
    gender: "Male",
    skill: "Do Book",
    email: "sunvisal@gmail.com",
  },
  {
    image: "../images/lokrong.jpg",
    name: "John Likorng",
    description: "បុរស សាច់កាំ",
    modalName: "MORN LIKORNG",
    position: "WORK SALE",
    from: "Kampong Cham",
    gender: "Male",
    skill: "Support",
    email: "likorng@gmail.com",
  },
];

// Generate team member HTML using map
const teamContainer = document.getElementById("about");
teamContainer.innerHTML = `
    ${teamMembers
      .map(
        (member) => `
        <div class="team-member">
            <img src="${member.image}" alt="${member.name} portrait" />
            <h4>${member.name}</h4>
            <p>${member.description}</p>
            <button class="view-btn" onclick="openModal('${member.modalName}', '${member.position}', '${member.from}', '${member.gender}', '${member.skill}', '${member.email}')">Views Info...</button>
        </div>
    `
      )
      .join("")}
    <div id="infoModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2 id="modalName">Name</h2>
            <p><strong>Position:</strong> <span id="modalPosition"></span></p>
            <p><strong>From:</strong> <span id="modalFrom"></span></p>
            <p><strong>Gender:</strong> <span id="modalGender"></span></p>
            <p><strong>Skill:</strong> <span id="modalSkill"></span></p>
            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        </div>
    </div>
`;

// Modal functions
function openModal(name, position, from, gender, skill, email) {
  document.getElementById("modalName").textContent = name;
  document.getElementById("modalPosition").textContent = position;
  document.getElementById("modalFrom").textContent = from;
  document.getElementById("modalGender").textContent = gender;
  document.getElementById("modalSkill").textContent = skill;
  document.getElementById("modalEmail").textContent = email;
  document.getElementById("infoModal").style.display = "block";
}

function closeModal() {
  document.getElementById("infoModal").style.display = "none";
}
// Count-up animation for stats
function countUp(element, target, duration) {
  let start = 0;
  const increment = target / (duration / 16); // 60 FPS (16ms per frame)
  const updateCount = () => {
    start += increment;
    if (start >= target) {
      element.textContent = `${Math.floor(target)}+`;
      return;
    }
    element.textContent = `${Math.floor(start)}+`;
    requestAnimationFrame(updateCount);
  };
  updateCount();
}

// Intersection Observer for stats section
const statsSection = document.querySelector(".stats-section");
const statNumbers = document.querySelectorAll(".stat-box h3");
let hasAnimated = false;

if (statsSection && statNumbers.length) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && !hasAnimated) {
          statNumbers.forEach((number) => {
            const target = parseInt(number.getAttribute("data-target"), 10);
            countUp(number, target, 5000); // 2 seconds duration
          });
          hasAnimated = true; // Prevent re-animation
          observer.disconnect(); // Stop observing
        }
      });
    },
    { threshold: 0.5 }
  ); // Trigger when 50% of section is visible

  observer.observe(statsSection);
} else {
  console.error("Stats section or numbers not found");
}
