function openModal(name, position, from, gender,skill, email) {
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPosition').innerText = position;
    document.getElementById('modalFrom').innerText = from;
    document.getElementById('modalGender').innerText = gender;
    document.getElementById('modalSkill').innerText = skill;
    document.getElementById('modalEmail').innerText = email;
    document.getElementById('infoModal').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('infoModal').style.display = 'none';
  }

  window.onclick = function(event) {
    const modal = document.getElementById('infoModal');
    if (event.target === modal) {
      closeModal();
    }
  }