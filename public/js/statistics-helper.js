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
    
    // Check for debug mode
    const isDebugMode = window.location.search.includes('debug=true');
    
    if (isDebugMode) {
        createDebugPanel();
    }
    
    // Add debug logging for chart rendering issues
    monitorChartRendering();
});

/**
 * Attempt to fix common chart rendering issues
 */
function monitorChartRendering() {
    // Check if charts are present
    const chartCanvas = document.getElementById('submissionChart');
    if (!chartCanvas) return;
    
    // Log chart dimensions
    console.log("Chart canvas dimensions:", {
        width: chartCanvas.width,
        height: chartCanvas.height,
        clientWidth: chartCanvas.clientWidth,
        clientHeight: chartCanvas.clientHeight,
        offsetWidth: chartCanvas.offsetWidth,
        offsetHeight: chartCanvas.offsetHeight
    });
    
    // Fix chart size issues
    setTimeout(function() {
        if (chartCanvas.clientWidth === 0 || chartCanvas.clientHeight === 0) {
            console.log("Chart canvas has zero dimensions, attempting to fix...");
            chartCanvas.style.width = '100%';
            chartCanvas.style.height = '250px';
            chartCanvas.height = 250;
            
            // Force chart redraw if possible
            if (window.girsipChart && chartCanvas.id) {
                console.log("Forcing chart redraw...");
                
                // Get chart data
                const chartContainer = chartCanvas.closest('.chart-container') || chartCanvas.parentElement;
                const loadingElement = chartContainer.querySelector('#chartLoading');
                if (loadingElement) loadingElement.style.display = 'flex';
                
                // Wait a bit and try to redraw
                setTimeout(function() {
                    const chartData = window.dummyStatisticsData || window.statisticsData;
                    if (chartData && chartData.submissionDates) {
                        const dates = Object.keys(chartData.submissionDates);
                        const counts = Object.values(chartData.submissionDates);
                        
                        window.girsipChart.createBarChart('submissionChart', {
                            labels: dates,
                            datasets: [{
                                label: 'Jumlah Pengumpulan',
                                data: counts,
                                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 1
                            }]
                        });
                    }
                }, 500);
            }
        }
    }, 1000);
}

/**
 * Create a debug panel for troubleshooting
 */
function createDebugPanel() {
    const panel = document.createElement('div');
    panel.className = 'debug-panel';
    panel.style.position = 'fixed';
    panel.style.bottom = '10px';
    panel.style.right = '10px';
    panel.style.backgroundColor = 'rgba(248, 250, 252, 0.95)';
    panel.style.border = '1px solid #cbd5e1';
    panel.style.borderRadius = '4px';
    panel.style.padding = '10px';
    panel.style.zIndex = '9999';
    panel.style.maxWidth = '400px';
    panel.style.maxHeight = '300px';
    panel.style.overflow = 'auto';
    panel.style.fontSize = '12px';
    
    // Add title and close button
    panel.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <strong>Debug Panel</strong>
            <button id="closeDebugPanel" style="background:none;border:none;cursor:pointer;color:#ef4444;">×</button>
        </div>
        <div id="debugContent">
            <p>Browser: ${navigator.userAgent}</p>
            <p>Screen: ${window.innerWidth}x${window.innerHeight}</p>
            <p>Charts loaded: ${typeof Chart !== 'undefined'}</p>
            <p>GirsipChart loaded: ${typeof window.girsipChart !== 'undefined'}</p>
            <button id="refreshCharts" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Refresh Charts</button>
        </div>
    `;
    
    document.body.appendChild(panel);
    
    // Add event listeners
    document.getElementById('closeDebugPanel').addEventListener('click', function() {
        panel.style.display = 'none';
    });
    
    document.getElementById('refreshCharts').addEventListener('click', function() {
        if (window.girsipChart && document.getElementById('submissionChart')) {
            try {
                window.girsipChart.destroyChart('submissionChart');
                
                const chartLoading = document.getElementById('chartLoading');
                if (chartLoading) chartLoading.style.display = 'flex';
                
                // Get data and recreate chart
                const submissionDates = window.dummyStatisticsData?.submissionDates || {};
                const dates = Object.keys(submissionDates);
                const counts = Object.values(submissionDates);
                
                window.girsipChart.createBarChart('submissionChart', {
                    labels: dates,
                    datasets: [{
                        label: 'Jumlah Pengumpulan',
                        data: counts,
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                });
                
                document.getElementById('debugContent').innerHTML += '<p class="text-green-500">Charts refreshed!</p>';
            } catch (e) {
                document.getElementById('debugContent').innerHTML += `<p class="text-red-500">Error: ${e.message}</p>`;
            }
        } else {
            document.getElementById('debugContent').innerHTML += '<p class="text-red-500">Chart libraries not available</p>';
        }
    });
}

/**
 * Export data to Excel (dummy implementation)
 */
function exportToExcel() {
    console.log("Export to Excel triggered");
    alert("Mengekspor data ke Excel... (dummy implementation)");
    
    // In a real implementation, this would trigger a download
    // For now, just simulate with a timeout
    setTimeout(function() {
        alert("Export selesai!");
    }, 1500);
}
