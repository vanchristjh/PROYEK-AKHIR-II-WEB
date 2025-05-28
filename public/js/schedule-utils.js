/**
 * Helper function to check schedule conflicts
 * 
 * This function can be used in create/edit forms for schedules to check
 * for conflicts before submitting the form.
 * 
 * @param {Object} data - Schedule data to check
 * @param {string} apiUrl - URL to API endpoint for conflict checking
 * @param {Function} callback - Callback function to receive results
 */
function checkScheduleConflicts(data, apiUrl, callback) {
    // Show loading indicator if needed
    if (document.getElementById('conflict-check-loading')) {
        document.getElementById('conflict-check-loading').style.display = 'block';
    }
    
    // Clear previous conflicts
    if (document.getElementById('conflicts-list')) {
        document.getElementById('conflicts-list').innerHTML = '';
    }
    
    // Make API request
    fetch(apiUrl + '?' + new URLSearchParams({
        classroom_id: data.classroom_id,
        teacher_id: data.teacher_id,
        day: data.day,
        start_time: data.start_time,
        end_time: data.end_time,
        schedule_id: data.schedule_id || '', // Optional for edit case
    }))
    .then(response => response.json())
    .then(result => {
        // Hide loading indicator
        if (document.getElementById('conflict-check-loading')) {
            document.getElementById('conflict-check-loading').style.display = 'none';
        }
        
        // Handle results
        if (result.hasConflicts) {
            // Display conflicts
            if (document.getElementById('conflicts-list')) {
                let conflictsHtml = '<div class="bg-red-50 border border-red-200 rounded-md p-3 mt-3">';
                conflictsHtml += '<div class="font-medium text-red-700 mb-2">Konflik jadwal terdeteksi:</div>';
                conflictsHtml += '<ul class="list-disc list-inside text-sm text-red-600">';
                
                result.conflicts.forEach(conflict => {
                    conflictsHtml += `<li>${conflict.message}</li>`;
                });
                
                conflictsHtml += '</ul></div>';
                document.getElementById('conflicts-list').innerHTML = conflictsHtml;
            }
        }
        
        // Call the callback with results
        if (typeof callback === 'function') {
            callback(result);
        }
    })
    .catch(error => {
        console.error('Error checking schedule conflicts:', error);
        
        // Hide loading indicator
        if (document.getElementById('conflict-check-loading')) {
            document.getElementById('conflict-check-loading').style.display = 'none';
        }
        
        // Show error message
        if (document.getElementById('conflicts-list')) {
            document.getElementById('conflicts-list').innerHTML = 
                '<div class="bg-red-50 border border-red-200 rounded-md p-3 mt-3">' + 
                '<div class="text-red-700">Terjadi kesalahan saat memeriksa konflik jadwal. Silakan coba lagi.</div>' +
                '</div>';
        }
        
        // Call the callback with error
        if (typeof callback === 'function') {
            callback({ hasConflicts: false, error: error });
        }
    });
}

/**
 * Helper function to format time for display
 * 
 * @param {string} time - Time string in format HH:MM:SS
 * @returns {string} - Formatted time string HH:MM
 */
function formatTime(time) {
    return time.substring(0, 5);
}

/**
 * Helper function to determine if a schedule is currently active
 * 
 * @param {string} day - Day value (1-7 or day name)
 * @param {string} startTime - Start time string in format HH:MM:SS
 * @param {string} endTime - End time string in format HH:MM:SS
 * @returns {boolean} - True if schedule is currently active
 */
function isScheduleActive(day, startTime, endTime) {
    // Get current day number (1-7, Monday is 1)
    const currentDay = new Date().getDay() === 0 ? 7 : new Date().getDay();
    
    // Map day names to numbers if needed
    const dayMapping = {
        'Senin': 1,
        'Selasa': 2,
        'Rabu': 3,
        'Kamis': 4,
        'Jumat': 5,
        'Sabtu': 6,
        'Minggu': 7
    };
    
    // Convert day to number if it's a name
    const dayNum = isNaN(parseInt(day)) ? dayMapping[day] || -1 : parseInt(day);
    
    // If it's not today, it's not active
    if (dayNum !== currentDay) {
        return false;
    }
    
    // Get current time
    const now = new Date();
    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + 
                       now.getMinutes().toString().padStart(2, '0') + ':' + 
                       now.getSeconds().toString().padStart(2, '0');
    
    // Check if current time is within start and end time
    return currentTime >= startTime && currentTime <= endTime;
}

/**
 * Helper function to determine if a schedule is upcoming today
 * 
 * @param {string} day - Day value (1-7 or day name)
 * @param {string} startTime - Start time string in format HH:MM:SS
 * @returns {boolean} - True if schedule is upcoming today
 */
function isScheduleUpcoming(day, startTime) {
    // Get current day number (1-7, Monday is 1)
    const currentDay = new Date().getDay() === 0 ? 7 : new Date().getDay();
    
    // Map day names to numbers if needed
    const dayMapping = {
        'Senin': 1,
        'Selasa': 2,
        'Rabu': 3,
        'Kamis': 4,
        'Jumat': 5,
        'Sabtu': 6,
        'Minggu': 7
    };
    
    // Convert day to number if it's a name
    const dayNum = isNaN(parseInt(day)) ? dayMapping[day] || -1 : parseInt(day);
    
    // If it's not today, it's not upcoming
    if (dayNum !== currentDay) {
        return false;
    }
    
    // Get current time
    const now = new Date();
    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + 
                       now.getMinutes().toString().padStart(2, '0') + ':' + 
                       now.getSeconds().toString().padStart(2, '0');
    
    // Check if current time is before start time
    return currentTime < startTime;
}
