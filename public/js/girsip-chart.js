/**
 * GirsipChart - A simple chart wrapper for Chart.js
 * This library provides a safe wrapper for Chart.js with fallbacks for statistics
 */

(function(global) {
    'use strict';
    
    console.log('GirsipChart library loaded');
    
    // Check if Chart.js is available
    let chartJsLoaded = typeof Chart !== 'undefined';
    
    // We'll retry loading Chart.js if it's not available
    let retryCount = 0;
    const MAX_RETRIES = 3;
    
    // Store callbacks to execute when Chart.js is ready
    const callbacks = [];
    
    // Create the main object
    const girsipChart = {
        // Status
        isReady: chartJsLoaded,
        chartInstances: {},
        
        // Initialize the library
        init: function() {
            console.log('GirsipChart: Initializing...');
            if (!chartJsLoaded) {
                console.log('GirsipChart: Chart.js not detected, attempting to load it');
                this.loadChartJs();
            } else {
                console.log('GirsipChart: Chart.js already loaded');
            }
        },
        
        // Load Chart.js dynamically
        loadChartJs: function() {
            if (retryCount >= MAX_RETRIES) {
                console.error('GirsipChart: Failed to load Chart.js after multiple attempts');
                return;
            }
            
            try {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
                script.async = true;
                
                script.onload = function() {
                    console.log('GirsipChart: Chart.js loaded successfully');
                    chartJsLoaded = true;
                    girsipChart.isReady = true;
                    
                    // Execute all callbacks
                    callbacks.forEach(function(callback) {
                        try {
                            callback();
                        } catch (e) {
                            console.error('GirsipChart: Error executing callback', e);
                        }
                    });
                    
                    // Clear callbacks
                    callbacks.length = 0;
                };
                
                script.onerror = function() {
                    console.error('GirsipChart: Failed to load Chart.js');
                    retryCount++;
                    
                    // Try again after a delay
                    setTimeout(function() {
                        girsipChart.loadChartJs();
                    }, 1000 * retryCount);
                };
                
                document.head.appendChild(script);
            } catch (e) {
                console.error('GirsipChart: Error during Chart.js loading', e);
            }
        },
        
        // Wait for Chart.js to be available then execute callback
        waitForChart: function(callback) {
            if (this.isReady) {
                callback();
            } else {
                console.log('GirsipChart: Chart.js not ready, queuing callback');
                callbacks.push(callback);
            }
        },
        
        // Create a bar chart
        createBarChart: function(elementId, data, options) {
            this.waitForChart(function() {
                try {
                    const ctx = document.getElementById(elementId).getContext('2d');
                    
                    // Check if a chart instance already exists for this canvas
                    if (girsipChart.chartInstances[elementId]) {
                        girsipChart.chartInstances[elementId].destroy();
                    }
                    
                    // Create new chart
                    girsipChart.chartInstances[elementId] = new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: options || {}
                    });
                    
                    console.log('GirsipChart: Bar chart created for', elementId);
                } catch (e) {
                    console.error('GirsipChart: Error creating bar chart', e);
                    
                    // Try to display error message on the canvas
                    try {
                        const canvas = document.getElementById(elementId);
                        const parent = canvas.parentElement;
                        
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'chart-error';
                        errorDiv.style.position = 'absolute';
                        errorDiv.style.top = '0';
                        errorDiv.style.left = '0';
                        errorDiv.style.width = '100%';
                        errorDiv.style.height = '100%';
                        errorDiv.style.display = 'flex';
                        errorDiv.style.alignItems = 'center';
                        errorDiv.style.justifyContent = 'center';
                        errorDiv.style.backgroundColor = 'rgba(255,255,255,0.8)';
                        
                        errorDiv.innerHTML = `
                            <div style="text-align:center; color:#e74c3c">
                                <i class="fas fa-exclamation-circle" style="font-size:24px; margin-bottom:8px"></i>
                                <p>Gagal memuat grafik</p>
                                <button id="retryChart_${elementId}" style="background:#3498db; color:white; border:none; padding:5px 10px; border-radius:4px; margin-top:8px;">
                                    Coba Lagi
                                </button>
                            </div>
                        `;
                        
                        parent.style.position = 'relative';
                        parent.appendChild(errorDiv);
                        
                        // Add retry handler
                        document.getElementById(`retryChart_${elementId}`).addEventListener('click', function() {
                            parent.removeChild(errorDiv);
                            girsipChart.createBarChart(elementId, data, options);
                        });
                    } catch (displayError) {
                        console.error('GirsipChart: Error displaying error message', displayError);
                    }
                }
            });
        },
        
        // Create a pie chart
        createPieChart: function(elementId, data, options) {
            this.waitForChart(function() {
                try {
                    const ctx = document.getElementById(elementId).getContext('2d');
                    
                    // Check if a chart instance already exists for this canvas
                    if (girsipChart.chartInstances[elementId]) {
                        girsipChart.chartInstances[elementId].destroy();
                    }
                    
                    // Create new chart
                    girsipChart.chartInstances[elementId] = new Chart(ctx, {
                        type: 'pie',
                        data: data,
                        options: options || {}
                    });
                    
                    console.log('GirsipChart: Pie chart created for', elementId);
                } catch (e) {
                    console.error('GirsipChart: Error creating pie chart', e);
                }
            });
        },
        
        // Create a line chart
        createLineChart: function(elementId, data, options) {
            this.waitForChart(function() {
                try {
                    const ctx = document.getElementById(elementId).getContext('2d');
                    
                    // Check if a chart instance already exists for this canvas
                    if (girsipChart.chartInstances[elementId]) {
                        girsipChart.chartInstances[elementId].destroy();
                    }
                    
                    // Create new chart
                    girsipChart.chartInstances[elementId] = new Chart(ctx, {
                        type: 'line',
                        data: data,
                        options: options || {}
                    });
                    
                    console.log('GirsipChart: Line chart created for', elementId);
                } catch (e) {
                    console.error('GirsipChart: Error creating line chart', e);
                }
            });
        },
        
        // Remove a chart instance
        destroyChart: function(elementId) {
            if (girsipChart.chartInstances[elementId]) {
                girsipChart.chartInstances[elementId].destroy();
                delete girsipChart.chartInstances[elementId];
                console.log('GirsipChart: Chart destroyed for', elementId);
            }
        }
    };
    
    // Initialize the library
    girsipChart.init();
    
    // Export to global scope
    global.girsipChart = girsipChart;
    
})(window);
