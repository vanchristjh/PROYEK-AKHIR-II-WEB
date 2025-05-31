/**
 * Custom Chart.js implementation for SMAN1-GIRSIP
 * This provides better compatibility and error handling for charts
 */

// Make sure Chart.js is available or attempt to load it
(function() {
    if (typeof Chart === 'undefined') {
        console.log('[CustomChart] Loading Chart.js from CDN');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        document.head.appendChild(script);
    } else {
        console.log('[CustomChart] Chart.js already loaded');
    }
})();

/**
 * GIRSIP custom chart wrapper
 */
class GirsipChart {
    constructor() {
        this.charts = {};
        this.chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            }
        };
    }
    
    /**
     * Safely wait for Chart.js to be loaded
     * @param {function} callback - Function to run when ready
     * @param {number} maxAttempts - Maximum attempts to check for Chart
     */
    waitForChart(callback, maxAttempts = 10) {
        let attempts = 0;
        
        const checkChart = () => {
            attempts++;
            console.log(`[CustomChart] Checking for Chart.js (attempt ${attempts})`);
            
            if (typeof Chart !== 'undefined') {
                console.log('[CustomChart] Chart.js is loaded and available');
                callback();
                return;
            }
            
            if (attempts >= maxAttempts) {
                console.error('[CustomChart] Chart.js failed to load after multiple attempts');
                this.showError('chartLoading', 'chartError');
                return;
            }
            
            setTimeout(checkChart, 300);
        };
        
        checkChart();
    }
    
    /**
     * Show error in chart container
     * @param {string} loadingId - ID of loading element
     * @param {string} errorId - ID of error element
     */
    showError(loadingId, errorId) {
        const loading = document.getElementById(loadingId);
        const error = document.getElementById(errorId);
        
        if (loading) loading.style.display = 'none';
        if (error) error.style.display = 'flex';
    }
    
    /**
     * Create a bar chart 
     * @param {string} id - Canvas element ID
     * @param {object} data - Chart data
     * @param {object} options - Chart options
     * @returns {Chart} The chart instance
     */
    createBarChart(id, data, options = {}) {
        try {
            const canvas = document.getElementById(id);
            if (!canvas) {
                console.error(`[CustomChart] Canvas element with ID ${id} not found`);
                return null;
            }
            
            const ctx = canvas.getContext('2d');
            
            // If chart already exists for this ID, destroy it
            if (this.charts[id]) {
                this.charts[id].destroy();
            }
            
            // Merge default options with provided options
            const chartOptions = Object.assign({}, this.chartDefaults, options);
            
            // Create the chart
            this.charts[id] = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: chartOptions
            });
            
            return this.charts[id];
        } catch (error) {
            console.error('[CustomChart] Error creating bar chart:', error);
            return null;
        }
    }
    
    /**
     * Create a pie chart
     * @param {string} id - Canvas element ID
     * @param {object} data - Chart data
     * @param {object} options - Chart options
     * @returns {Chart} The chart instance
     */
    createPieChart(id, data, options = {}) {
        try {
            const canvas = document.getElementById(id);
            if (!canvas) {
                console.error(`[CustomChart] Canvas element with ID ${id} not found`);
                return null;
            }
            
            const ctx = canvas.getContext('2d');
            
            // If chart already exists for this ID, destroy it
            if (this.charts[id]) {
                this.charts[id].destroy();
            }
            
            // Default options for pie charts
            const pieDefaults = {
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            };
            
            // Merge default options with provided options
            const chartOptions = Object.assign({}, this.chartDefaults, pieDefaults, options);
            
            // Create the chart
            this.charts[id] = new Chart(ctx, {
                type: 'pie',
                data: data,
                options: chartOptions
            });
            
            return this.charts[id];
        } catch (error) {
            console.error('[CustomChart] Error creating pie chart:', error);
            return null;
        }
    }
    
    /**
     * Create a line chart
     * @param {string} id - Canvas element ID
     * @param {object} data - Chart data
     * @param {object} options - Chart options
     * @returns {Chart} The chart instance
     */
    createLineChart(id, data, options = {}) {
        try {
            const canvas = document.getElementById(id);
            if (!canvas) {
                console.error(`[CustomChart] Canvas element with ID ${id} not found`);
                return null;
            }
            
            const ctx = canvas.getContext('2d');
            
            // If chart already exists for this ID, destroy it
            if (this.charts[id]) {
                this.charts[id].destroy();
            }
            
            // Default options for line charts
            const lineDefaults = {
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            };
            
            // Merge default options with provided options
            const chartOptions = Object.assign({}, this.chartDefaults, lineDefaults, options);
            
            // Create the chart
            this.charts[id] = new Chart(ctx, {
                type: 'line',
                data: data,
                options: chartOptions
            });
            
            return this.charts[id];
        } catch (error) {
            console.error('[CustomChart] Error creating line chart:', error);
            return null;
        }
    }
    
    /**
     * Safely create any chart with error handling
     * @param {string} type - Chart type (bar, line, pie, etc)
     * @param {string} id - Canvas element ID
     * @param {object} data - Chart data
     * @param {object} options - Chart options
     * @returns {Chart} The chart instance
     */
    createChart(type, id, data, options = {}) {
        try {
            if (typeof Chart === 'undefined') {
                console.error('[CustomChart] Chart.js is not loaded');
                return null;
            }
            
            switch (type.toLowerCase()) {
                case 'bar':
                    return this.createBarChart(id, data, options);
                case 'pie':
                    return this.createPieChart(id, data, options);
                case 'line':
                    return this.createLineChart(id, data, options);
                default:
                    const canvas = document.getElementById(id);
                    if (!canvas) {
                        console.error(`[CustomChart] Canvas element with ID ${id} not found`);
                        return null;
                    }
                    
                    const ctx = canvas.getContext('2d');
                    
                    // If chart already exists for this ID, destroy it
                    if (this.charts[id]) {
                        this.charts[id].destroy();
                    }
                    
                    // Create the chart
                    this.charts[id] = new Chart(ctx, {
                        type: type,
                        data: data,
                        options: Object.assign({}, this.chartDefaults, options)
                    });
                    
                    return this.charts[id];
            }
        } catch (error) {
            console.error(`[CustomChart] Error creating ${type} chart:`, error);
            return null;
        }
    }
}

// Create global instance
window.girsipChart = new GirsipChart();
