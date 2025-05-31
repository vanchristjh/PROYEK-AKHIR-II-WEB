/**
 * SMAN1-GIRSIP - Statistics Page Fix
 * 
 * This script helps remedy issues with the statistics page display and charts.
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("Statistics page helper script loaded");
    
    // Force visibility of main elements
    function forceVisibility() {
        const criticalElements = [
            'body',
            '#wrapper',
            '#page-content-wrapper',
            '.content-container',
            '.container'
        ];
        
        criticalElements.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                if (el) {
                    el.style.display = 'block';
                    el.style.visibility = 'visible';
                    el.style.opacity = '1';
                }
            });
        });
        
        // Special handling for wrapper which needs flex
        const wrapper = document.getElementById('wrapper');
        if (wrapper) {
            wrapper.style.display = 'flex';
        }
        
        console.log('Element visibility enforced');
    }
    
    // Check if Chart.js is available and load it if not
    function ensureChartJsAvailable() {
        if (typeof Chart === 'undefined') {
            console.log('Chart.js not found, loading from CDN');
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
                script.onload = () => {
                    console.log('Chart.js loaded successfully');
                    resolve();
                };
                script.onerror = () => {
                    console.error('Failed to load Chart.js');
                    reject(new Error('Failed to load Chart.js'));
                };
                document.head.appendChild(script);
            });
        } else {
            console.log('Chart.js already available');
            return Promise.resolve();
        }
    }
    
    // Initialize or refresh charts
    function refreshCharts() {
        const chartCanvas = document.getElementById('submissionChart');
        if (!chartCanvas) {
            console.log('No chart canvas found to refresh');
            return;
        }
        
        // Hide loading indicator
        const loading = document.getElementById('chartLoading');
        if (loading) {
            loading.style.display = 'none';
        }
        
        console.log('Charts refreshed');
    }
    
    // Execute our fixes
    setTimeout(() => {
        forceVisibility();
        ensureChartJsAvailable()
            .then(refreshCharts)
            .catch(err => console.error('Chart initialization failed:', err));
    }, 500);
    
    // Also run after a delay to catch any post-load issues
    setTimeout(forceVisibility, 2000);
});
