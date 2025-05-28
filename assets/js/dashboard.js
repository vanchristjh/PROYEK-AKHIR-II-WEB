document.addEventListener('DOMContentLoaded', function() {
  // Toggle mobile menu
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.querySelector('.sidebar');
  
  if (menuToggle) {
    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('show-menu');
    });
  }
  
  // Current time display
  const timeDisplay = document.getElementById('time-display');
  if (timeDisplay) {
    updateTime();
    setInterval(updateTime, 1000);
  }
  
  function updateTime() {
    if (!timeDisplay) return;
    
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    timeDisplay.textContent = `${hours}:${minutes}:${seconds}`;
  }
});
