/**
 * Batch Submission Grading Module
 * 
 * This module handles batch grading of student submissions with improved UI/UX
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const batchGradeForm = document.getElementById('batch-grade-form');
    const selectAllCheckbox = document.getElementById('select-all-submissions');
    const submissionCheckboxes = document.querySelectorAll('.submission-checkbox');
    const selectedCountBadge = document.getElementById('selected-count');
    const batchGradeBtn = document.getElementById('batch-grade-btn');
    const submissionRows = document.querySelectorAll('.submission-row');
    
    // Batch grading modal elements
    const batchModal = document.getElementById('batch-grade-modal');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const batchGradeSubmitBtn = document.getElementById('submit-batch-grades');
    const defaultScoreInput = document.getElementById('default-score');
    const defaultFeedbackInput = document.getElementById('default-feedback');
    
    // Progress elements
    const progressModal = document.getElementById('progress-modal');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    // Assignment status elements
    const assignmentId = document.getElementById('assignment-id')?.value;
      /**
     * Fetch submission status data for the assignment
     */
    function fetchSubmissionStatus() {
        if (!assignmentId) return;
        
        const statusElement = document.getElementById('submission-status');
        if (!statusElement) return;
        
        fetch(`/guru/assignments/${assignmentId}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSubmissionStatusUI(data.data);
                }
            })
            .catch(error => {
                console.error('Error fetching assignment status:', error);
            });
    }
    
    /**
     * Update the UI with submission status data
     */
    function updateSubmissionStatusUI(stats) {
        const elements = {
            totalStudents: document.getElementById('total-students'),
            submitted: document.getElementById('submitted-count'),
            graded: document.getElementById('graded-count'),
            late: document.getElementById('late-count'),
            notSubmitted: document.getElementById('not-submitted-count')
        };
        
        // Update stats if elements exist
        if (elements.totalStudents) elements.totalStudents.textContent = stats.total_students;
        if (elements.submitted) elements.submitted.textContent = stats.submitted;
        if (elements.graded) elements.graded.textContent = stats.graded;
        if (elements.late) elements.late.textContent = stats.late;
        if (elements.notSubmitted) elements.notSubmitted.textContent = stats.not_submitted;
        
        // Update progress bars
        const submittedPercent = stats.total_students > 0 ? 
            Math.round((stats.submitted / stats.total_students) * 100) : 0;
        const gradedPercent = stats.submitted > 0 ? 
            Math.round((stats.graded / stats.submitted) * 100) : 0;
            
        const submittedBar = document.getElementById('submitted-progress');
        const gradedBar = document.getElementById('graded-progress');
        
        if (submittedBar) submittedBar.style.width = `${submittedPercent}%`;
        if (gradedBar) gradedBar.style.width = `${gradedPercent}%`;
    }

    /**
     * Initialize the batch grading functionality
     */
    function init() {
        if (!batchGradeForm) return;
        
        // Setup event listeners
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', toggleSelectAll);
        }
        
        submissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        if (batchGradeBtn) {
            batchGradeBtn.addEventListener('click', showBatchGradeModal);
        }
        
        if (closeModalBtns) {
            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', closeModals);
            });
        }
        
        if (batchGradeSubmitBtn) {
            batchGradeSubmitBtn.addEventListener('click', submitBatchGrades);
        }
        
        // Fetch submission status when page loads
        fetchSubmissionStatus();
        
        // Apply default score/feedback to all selected items
        if (defaultScoreInput) {
            defaultScoreInput.addEventListener('input', function() {
                document.querySelectorAll('.batch-score-input').forEach(input => {
                    if (input.closest('tr').querySelector('.submission-checkbox').checked) {
                        input.value = this.value;
                    }
                });
            });
        }
        
        if (defaultFeedbackInput) {
            defaultFeedbackInput.addEventListener('input', function() {
                document.querySelectorAll('.batch-feedback-input').forEach(input => {
                    if (input.closest('tr').querySelector('.submission-checkbox').checked) {
                        input.value = this.value;
                    }
                });
            });
        }
        
        // Initial count update
        updateSelectedCount();
        
        // Enable row highlighting
        submissionRows.forEach(row => {
            const checkbox = row.querySelector('.submission-checkbox');
            if (checkbox) {
                row.addEventListener('click', function(e) {
                    // Don't toggle if clicking on form elements
                    if (
                        e.target.tagName === 'INPUT' || 
                        e.target.tagName === 'BUTTON' || 
                        e.target.tagName === 'A' || 
                        e.target.tagName === 'TEXTAREA' ||
                        e.target.closest('button') ||
                        e.target.closest('a')
                    ) {
                        return;
                    }
                    
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                    updateRowHighlight(row, checkbox.checked);
                });
                
                // Initial highlighting
                updateRowHighlight(row, checkbox.checked);
            }
        });
    }
    
    /**
     * Toggle select all checkboxes
     */
    function toggleSelectAll() {
        const isChecked = selectAllCheckbox.checked;
        
        submissionCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
            updateRowHighlight(checkbox.closest('tr'), isChecked);
        });
        
        updateSelectedCount();
    }
    
    /**
     * Update the selected count badge
     */
    function updateSelectedCount() {
        const selectedCount = Array.from(submissionCheckboxes).filter(checkbox => checkbox.checked).length;
        
        if (selectedCountBadge) {
            selectedCountBadge.textContent = selectedCount;
            
            // Show/hide batch buttons based on selection
            if (selectedCount > 0) {
                batchGradeBtn.classList.remove('hidden');
            } else {
                batchGradeBtn.classList.add('hidden');
            }
        }
    }
    
    /**
     * Update row highlighting based on selection
     */
    function updateRowHighlight(row, isSelected) {
        if (!row) return;
        
        if (isSelected) {
            row.classList.add('bg-blue-50');
        } else {
            row.classList.remove('bg-blue-50');
        }
    }
    
    /**
     * Show the batch grade modal
     */
    function showBatchGradeModal() {
        if (!batchModal) return;
        
        // Clear any previous form data
        const batchTable = document.getElementById('batch-grade-table');
        const tableBody = batchTable.querySelector('tbody');
        tableBody.innerHTML = '';
        
        // Add selected submissions to the modal table
        let hasSelected = false;
        
        submissionCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                hasSelected = true;
                const submissionId = checkbox.value;
                const row = checkbox.closest('tr');
                const studentName = row.querySelector('[data-student-name]').getAttribute('data-student-name');
                const submittedDate = row.querySelector('[data-submitted-date]').getAttribute('data-submitted-date');
                
                // Create table row for this submission
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="border px-4 py-2">
                        <input type="hidden" name="submissions[${submissionId}][id]" value="${submissionId}">
                        ${studentName}
                    </td>
                    <td class="border px-4 py-2">${submittedDate}</td>
                    <td class="border px-4 py-2">
                        <input type="number" name="submissions[${submissionId}][score]" 
                               class="batch-score-input rounded border-gray-300 w-full" 
                               min="0" max="100" required>
                    </td>
                    <td class="border px-4 py-2">
                        <textarea name="submissions[${submissionId}][feedback]" 
                                  class="batch-feedback-input rounded border-gray-300 w-full" 
                                  rows="2"></textarea>
                    </td>
                `;
                
                tableBody.appendChild(tr);
            }
        });
        
        if (hasSelected) {
            batchModal.classList.remove('hidden');
        } else {
            alert('Pilih minimal satu tugas untuk dinilai secara batch.');
        }
    }
    
    /**
     * Close modals
     */
    function closeModals() {
        if (batchModal) batchModal.classList.add('hidden');
        if (progressModal) progressModal.classList.add('hidden');
    }
    
    /**
     * Submit batch grades to the server
     */
    function submitBatchGrades() {
        // Show progress modal
        if (progressModal) {
            progressModal.classList.remove('hidden');
            progressBar.style.width = '0%';
            progressText.textContent = 'Memproses...';
        }
        
        // Collect form data
        const formData = new FormData(batchGradeForm);
        
        // Convert FormData to JSON
        const jsonData = {};
        jsonData.submissions = [];
        
        const submissionIds = new Set();
        
        for (const [key, value] of formData.entries()) {
            const match = key.match(/submissions\[(\d+)\]\[([^\]]+)\]/);
            
            if (match) {
                const submissionId = match[1];
                const field = match[2];
                
                submissionIds.add(submissionId);
                
                // Find or create submission entry
                let submission = jsonData.submissions.find(s => s.id === submissionId);
                
                if (!submission) {
                    submission = { id: submissionId };
                    jsonData.submissions.push(submission);
                }
                
                submission[field] = value;
            }
        }
          // AJAX submission
        fetch('/guru/submissions/mass-grade', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update progress
                progressBar.style.width = '100%';
                progressText.textContent = data.message;
                  // Update assignment status
                if (assignmentId) {
                    fetchSubmissionStatus();
                }
                
                // Reload page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menyimpan nilai.');
            }
        })
        .catch(error => {
            progressBar.style.width = '100%';
            progressBar.classList.remove('bg-blue-500');
            progressBar.classList.add('bg-red-500');
            progressText.textContent = `Error: ${error.message}`;
            
            setTimeout(() => {
                closeModals();
            }, 3000);
        });
    }
    
    // Initialize the module
    init();
});
