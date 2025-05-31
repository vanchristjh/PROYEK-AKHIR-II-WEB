// Main JavaScript file for the application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application loaded successfully');
    
    // Toggle sidebar functionality
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const wrapper = document.getElementById('wrapper');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            wrapper.classList.toggle('toggled');
        });
    }
    
    // Initialize any Chart.js charts
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js loaded and ready');
    }
});
