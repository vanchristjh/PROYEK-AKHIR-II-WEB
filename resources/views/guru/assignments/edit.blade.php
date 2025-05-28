@extends('layouts.dashboard')

@section('title', 'Edit Tugas')

@section('header', 'Edit Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.assignments.show', $assignment) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke detail tugas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Form Edit Tugas</h3>
            <p class="mt-1 text-sm text-gray-500">
                Update detail tugas yang telah diberikan kepada siswa.
            </p>
        </div>

        <form action="{{ route('guru.assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Tugas <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Tugas <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="6" 
                              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                              required>{{ old('description', $assignment->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select name="subject_id" id="subject_id" 
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            required>
                        <option value="">Pilih mata pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="deadline" id="deadline" 
                           value="{{ old('deadline', $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '') }}" 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                           required>
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="classes" class="block text-sm font-medium text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                    
                    @if(count($classes) > 0)
                        <div class="mt-1 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($classes as $class)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="classes[]" value="{{ $class->id }}" id="class-{{ $class->id }}"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            {{ in_array($class->id, old('classes', $selectedClasses)) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="class-{{ $class->id }}" class="font-medium text-gray-700">{{ $class->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>
                                            Tidak ada kelas yang tersedia. Silakan hubungi administrator untuk menambahkan kelas terlebih dahulu.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @error('classes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Lampiran Saat Ini</label>
                    @if($assignment->file)
                        <div class="flex items-center bg-gray-50 p-4 rounded-md border border-gray-200 mb-4">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-md mr-3">
                                @php
                                    $fileExt = pathinfo($assignment->file, PATHINFO_EXTENSION);
                                    $iconClass = 'fa-file-alt';
                                    $iconColor = 'text-blue-600';
                                    
                                    switch(strtolower($fileExt)) {
                                        case 'pdf':
                                            $iconClass = 'fa-file-pdf';
                                            $iconColor = 'text-red-600';
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            $iconClass = 'fa-file-word';
                                            $iconColor = 'text-blue-600';
                                            break;
                                        case 'xls':
                                        case 'xlsx':
                                            $iconClass = 'fa-file-excel';
                                            $iconColor = 'text-green-600';
                                            break;
                                        case 'ppt':
                                        case 'pptx':
                                            $iconClass = 'fa-file-powerpoint';
                                            $iconColor = 'text-orange-600';
                                            break;
                                        case 'zip':
                                        case 'rar':
                                            $iconClass = 'fa-file-archive';
                                            $iconColor = 'text-yellow-600';
                                            break;
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                        case 'gif':
                                            $iconClass = 'fa-file-image';
                                            $iconColor = 'text-purple-600';
                                            break;
                                    }
                                @endphp
                                <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-gray-800 font-medium">{{ Str::afterLast($assignment->file, '/') }}</span>
                                @if(Storage::exists($assignment->file) && ($size = Storage::size($assignment->file)))
                                    <span class="text-xs text-gray-500 ml-2">({{ round($size / 1024 / 1024, 2) }} MB)</span>
                                @endif
                            </div>
                            <a href="{{ Storage::url($assignment->file) }}" target="_blank" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm transition-colors mr-2">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                            <a href="#" onclick="previewFile('{{ Storage::url($assignment->file) }}', '{{ strtolower($fileExt) }}')" class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-sm transition-colors">
                                <i class="fas fa-eye mr-1"></i> Preview
                            </a>
                        </div>
                        <div class="mt-2">
                            <label class="inline-flex items-center text-sm text-red-600">
                                <input type="checkbox" name="delete_file" id="delete_file" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 h-4 w-4">
                                <span class="ml-2">Hapus file lampiran saat ini</span>
                            </label>
                        </div>
                    @else
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mb-4">
                            <span class="text-gray-500">Tidak ada file lampiran</span>
                        </div>
                    @endif

                    <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Ganti File Lampiran (Opsional)</label>
                    <div class="mt-1 flex items-center">
                        <span class="inline-block h-12 w-12 overflow-hidden bg-gray-100 rounded-lg">
                            <i class="fas fa-file-upload text-gray-400 flex items-center justify-center h-full text-xl"></i>
                        </span>
                        <div class="ml-5 flex-1">
                            <input type="file" name="file" id="file" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                {{ isset($assignment->file) && $assignment->file ? '' : '' }}>
                            <p class="mt-1 text-xs text-gray-500">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, JPG, JPEG, PNG (Maks. 100MB)</p>
                        </div>
                    </div>                    <div id="selected-file" class="hidden mt-3 p-2 bg-gray-50 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i id="file-icon" class="fas fa-file text-blue-500 mr-2"></i>
                                <span id="file-name" class="text-sm text-gray-700"></span>
                                <span id="file-size" class="ml-2 text-xs text-gray-500"></span>
                            </div>
                            <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="file-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>                <!-- is_active checkbox removed - column doesn't exist in database -->
                
            </div>

            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('guru.assignments.show', $assignment) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Perbarui Tugas
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const selectedFile = document.getElementById('selected-file');
        const fileName = document.getElementById('file-name');
        const fileSizeElement = document.getElementById('file-size');
        const fileIcon = document.getElementById('file-icon');
        const removeFile = document.getElementById('remove-file');
        const fileError = document.getElementById('file-error');
        const deleteFileCheckbox = document.getElementById('delete_file');
        const form = document.querySelector('form[action*="assignments/update"]');
        
        function validateFile(input) {
            if(input && input.files && input.files[0]) {
                const file = input.files[0];
                const fileSizeMB = Math.round((file.size / 1024 / 1024) * 100) / 100; // MB
                
                if(fileSizeMB > 100) {
                    fileError.textContent = `File terlalu besar (${fileSizeMB}MB). Ukuran maksimal yang diizinkan adalah 100MB.`;
                    fileError.classList.remove('hidden');
                    input.value = '';
                    selectedFile.classList.add('hidden');
                    return false;
                }
                
                const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png'];
                const fileExt = file.name.split('.').pop().toLowerCase();
                
                if(!allowedTypes.includes(fileExt)) {
                    fileError.textContent = `Jenis file "${fileExt}" tidak diizinkan. Gunakan format yang diizinkan.`;
                    fileError.classList.remove('hidden');
                    input.value = '';
                    selectedFile.classList.add('hidden');
                    return false;
                }
                
                // Hide error message if previously shown
                fileError.classList.add('hidden');
                
                // Show selected file info
                if(fileName && fileSizeElement) {
                    fileName.textContent = file.name;
                    fileSizeElement.textContent = `(${fileSizeMB.toFixed(2)} MB)`;
                    selectedFile.classList.remove('hidden');
                    
                    // Update file icon based on extension
                    updateFileIcon(fileIcon, fileExt);
                }
                return true;
            }
            return false;
        }
        
        // Function to update file icon based on file extension
        function updateFileIcon(iconElement, extension) {
            if (!iconElement) return;
            
            // Reset classes
            iconElement.className = 'fas mr-2';
            
            // Set icon based on file type
            switch(extension) {
                case 'pdf':
                    iconElement.classList.add('fa-file-pdf', 'text-red-500');
                    break;
                case 'doc':
                case 'docx':
                    iconElement.classList.add('fa-file-word', 'text-blue-600');
                    break;
                case 'xls':
                case 'xlsx':
                    iconElement.classList.add('fa-file-excel', 'text-green-600');
                    break;
                case 'ppt':
                case 'pptx':
                    iconElement.classList.add('fa-file-powerpoint', 'text-orange-500');
                    break;
                case 'zip':
                case 'rar':
                    iconElement.classList.add('fa-file-archive', 'text-yellow-600');
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    iconElement.classList.add('fa-file-image', 'text-purple-500');
                    break;
                default:
                    iconElement.classList.add('fa-file', 'text-blue-500');
            }
        }
        
        if(fileInput) {
            fileInput.addEventListener('change', function() {
                validateFile(this);
                
                // If a file is selected, uncheck the delete file checkbox
                if(this.files && this.files[0] && deleteFileCheckbox) {
                    deleteFileCheckbox.checked = false;
                }
            });
            
            // Remove file button
            if(removeFile) {
                removeFile.addEventListener('click', function() {
                    fileInput.value = '';
                    selectedFile.classList.add('hidden');
                    fileError.classList.add('hidden');
                });
            }
        }
        
        // If delete_file is checked, disable file upload
        if(deleteFileCheckbox) {
            deleteFileCheckbox.addEventListener('change', function() {
                if(fileInput) {
                    fileInput.disabled = this.checked;
                    if(this.checked && fileInput.value) {
                        fileInput.value = '';
                        if(selectedFile) selectedFile.classList.add('hidden');
                        if(fileError) fileError.classList.add('hidden');
                    }
                }
            });
        }
        
        // Form validation before submit with better error handling
        if(form) {
            form.addEventListener('submit', function(e) {                // Validate required fields
                const title = document.getElementById('title');
                const description = document.getElementById('description');
                const subject = document.getElementById('subject_id');
                const deadline = document.getElementById('deadline');
                const classes = document.querySelectorAll('input[name="classes[]"]:checked');
                const classesError = document.getElementById('classes-error');
                
                let hasError = false;
                const errorMessages = [];
                
                // Clear previous error states
                const resetErrorState = (element) => {
                    if (element) {
                        element.classList.remove('border-red-500', 'ring-red-500');
                        element.classList.add('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');
                        
                        // Find and hide error message if exists
                        const errorElement = element.parentNode.querySelector('.validation-error');
                        if (errorElement) {
                            errorElement.remove();
                        }
                    }
                };
                
                // Set error state
                const setErrorState = (element, message) => {
                    if (element) {
                        resetErrorState(element);
                        element.classList.remove('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');
                        element.classList.add('border-red-500', 'ring-red-500');
                        
                        // Add error message below the element
                        const errorElement = document.createElement('p');
                        errorElement.className = 'validation-error mt-1 text-sm text-red-600';
                        errorElement.innerText = message;
                        element.parentNode.appendChild(errorElement);
                    }
                };
                
                // Reset all error states first
                resetErrorState(title);
                resetErrorState(description);
                resetErrorState(subject);
                resetErrorState(deadline);
                
                if(!title.value.trim()) {
                    setErrorState(title, 'Judul tugas tidak boleh kosong');
                    errorMessages.push('Judul tugas tidak boleh kosong');
                    hasError = true;
                }
                
                if(!description.value.trim()) {
                    setErrorState(description, 'Deskripsi tugas tidak boleh kosong');
                    errorMessages.push('Deskripsi tugas tidak boleh kosong');
                    hasError = true;
                }
                
                if(!subject.value) {
                    setErrorState(subject, 'Mata pelajaran harus dipilih');
                    errorMessages.push('Mata pelajaran harus dipilih');
                    hasError = true;
                }
                
                if(!deadline.value) {
                    setErrorState(deadline, 'Deadline harus ditentukan');
                    errorMessages.push('Deadline harus ditentukan');
                    hasError = true;
                } else {
                    // Validate deadline is in the future
                    const deadlineDate = new Date(deadline.value);
                    const now = new Date();
                    
                    if(deadlineDate <= now) {
                        setErrorState(deadline, 'Deadline harus di masa depan');
                        errorMessages.push('Deadline harus di masa depan');
                        hasError = true;
                    }
                }
                
                if(classes.length === 0) {
                    errorMessages.push('Pilih minimal satu kelas');
                    hasError = true;
                    
                    // Display error message for classes
                    if (classesError) {
                        classesError.classList.remove('hidden');
                    }
                }
                
                // Validate deadline is in the future for new deadlines
                if(deadline.value) {
                    const originalDeadline = "{{ $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '' }}";
                    const newDeadline = new Date(deadline.value);
                    const now = new Date();
                    
                    // Only validate if deadline is changed
                    if(deadline.value !== originalDeadline && newDeadline <= now) {
                        errorMessages.push('Deadline baru harus di masa depan');
                        hasError = true;
                    }
                }
                
                // Validate file if one is selected
                if(fileInput && fileInput.files && fileInput.files[0]) {
                    if(!validateFile(fileInput)) {
                        hasError = true;
                        errorMessages.push('File tidak valid');
                    }
                }
                
                if(hasError) {
                    e.preventDefault();
                    if(errorMessages.length > 0) {
                        alert('Periksa form Anda:\n- ' + errorMessages.join('\n- '));
                    }
                    return false;
                }
                
                // Special warnings for changes that might impact students
                @if($assignment->submissions && $assignment->submissions->count() > 0)
                const deadlineElement = document.getElementById('deadline');
                const originalDeadline = "{{ $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '' }}";
                
                // If deadline is being changed to an earlier date and submissions exist
                if(deadlineElement.value && originalDeadline && new Date(deadlineElement.value) < new Date(originalDeadline)) {
                    if(!confirm('Mengubah deadline menjadi lebih cepat dapat memengaruhi siswa yang belum mengumpulkan. Lanjutkan?')) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                // If file is being deleted and submissions exist
                if(deleteFileCheckbox && deleteFileCheckbox.checked) {
                    if(!confirm('Menghapus file lampiran dapat membingungkan siswa yang sudah melihat tugas ini. Lanjutkan?')) {
                        e.preventDefault();
                        deleteFileCheckbox.checked = false;
                        return false;
                    }
                }
                @endif
            });
        }
    });
</script>
<script>    
    document.addEventListener('DOMContentLoaded', function() {
        // File preview functionality
        function previewFile(fileUrl, fileType) {
            // Create overlay if it doesn't exist
            let overlay = document.getElementById('file-preview-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'file-preview-overlay';
                overlay.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center';
                
                const content = document.createElement('div');
                content.className = 'w-11/12 h-5/6 bg-white rounded-lg overflow-hidden relative max-w-5xl flex flex-col';
                
                const header = document.createElement('div');
                header.className = 'bg-gray-100 p-4 flex items-center justify-between border-b border-gray-200';
                header.innerHTML = `
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i id="preview-file-icon" class="fas fa-file mr-2"></i>
                        <span id="preview-filename">Preview File</span>
                    </h3>
                    <button type="button" onclick="closeFilePreview()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                `;
                
                const body = document.createElement('div');
                body.className = 'flex-1 overflow-auto p-4';
                body.innerHTML = `
                    <div id="file-content-container" class="h-full flex items-center justify-center">
                        <div id="pdf-preview" class="hidden h-full w-full"></div>
                        <div id="image-preview" class="hidden h-full flex items-center justify-center">
                            <img id="preview-image" src="" alt="File Preview" class="max-h-full max-w-full object-contain">
                        </div>
                        <div id="other-file-preview" class="hidden text-center p-8">
                            <div class="w-24 h-24 rounded-full bg-gray-100 mx-auto flex items-center justify-center mb-4">
                                <i id="other-preview-icon" class="fas fa-file-alt text-4xl text-blue-500"></i>
                            </div>
                            <h4 class="text-xl font-medium text-gray-800 mb-2">File tidak dapat ditampilkan</h4>
                            <p class="text-gray-600 mb-4">Tipe file ini tidak dapat ditampilkan secara langsung. Silakan download file untuk melihatnya.</p>
                            <a id="download-file-link" href="" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i> Download File
                            </a>
                        </div>
                    </div>
                `;
                
                content.appendChild(header);
                content.appendChild(body);
                overlay.appendChild(content);
                document.body.appendChild(overlay);
            }
            
            // Update overlay content based on file type
            const pdfPreview = document.getElementById('pdf-preview');
            const imagePreview = document.getElementById('image-preview');
            const otherFilePreview = document.getElementById('other-file-preview');
            const previewImage = document.getElementById('preview-image');
            const previewFilename = document.getElementById('preview-filename');
            const downloadLink = document.getElementById('download-file-link');
            const previewFileIcon = document.getElementById('preview-file-icon');
            const otherPreviewIcon = document.getElementById('other-preview-icon');
            
            // Set download link
            downloadLink.href = fileUrl;
            
            // Set filename
            previewFilename.textContent = fileUrl.split('/').pop();
            
            // Reset all preview containers
            pdfPreview.classList.add('hidden');
            imagePreview.classList.add('hidden');
            otherFilePreview.classList.add('hidden');
            
            // Set appropriate icon
            let iconClass = 'fa-file-alt text-blue-500';
            
            switch(fileType) {
                case 'pdf':
                    iconClass = 'fa-file-pdf text-red-500';
                    break;
                case 'doc':
                case 'docx':
                    iconClass = 'fa-file-word text-blue-500';
                    break;
                case 'xls':
                case 'xlsx':
                    iconClass = 'fa-file-excel text-green-500';
                    break;
                case 'ppt':
                case 'pptx':
                    iconClass = 'fa-file-powerpoint text-orange-500';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    iconClass = 'fa-file-image text-purple-500';
                    break;
                case 'zip':
                case 'rar':
                    iconClass = 'fa-file-archive text-yellow-500';
                    break;
            }
            
            previewFileIcon.className = `fas ${iconClass} mr-2`;
            otherPreviewIcon.className = `fas ${iconClass} text-4xl`;
            
            // Show appropriate preview based on file type
            if (fileType === 'pdf') {
                pdfPreview.classList.remove('hidden');
                loadPdfPreview(fileUrl, pdfPreview);
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                // For images, use img tag
                imagePreview.classList.remove('hidden');
                previewImage.src = fileUrl;
            } else {
                // For other file types, show download prompt
                otherFilePreview.classList.remove('hidden');
            }
            
            // Show the overlay
            overlay.style.display = 'flex';
        }
        
        window.previewFile = previewFile;
        
        function closeFilePreview() {
            const overlay = document.getElementById('file-preview-overlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }
        
        function loadPdfPreview(url, container) {
            // Check if PDF.js is loaded
            if (typeof pdfjsLib === 'undefined') {
                // Load PDF.js if not available
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js';
                script.onload = function() {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
                    renderPdf(url, container);
                };
                document.head.appendChild(script);
            } else {
                renderPdf(url, container);
            }
        }
        
        function renderPdf(url, container) {
            // Clear container
            container.innerHTML = '';
            
            // Add loading indicator
            const loadingEl = document.createElement('div');
            loadingEl.className = 'text-center py-4';
            loadingEl.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500 text-xl mr-2"></i> Memuat PDF...';
            container.appendChild(loadingEl);
            
            // Load the PDF document
            pdfjsLib.getDocument(url).promise.then(function(pdf) {
                // Remove loading indicator
                container.removeChild(loadingEl);
                
                // Create wrapper for all pages
                const wrapper = document.createElement('div');
                wrapper.className = 'flex flex-col items-center';
                container.appendChild(wrapper);
                
                // Load first page for preview
                pdf.getPage(1).then(function(page) {
                    const scale = 1.5;
                    const viewport = page.getViewport({ scale });
                    
                    // Create canvas for this page
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.className = 'shadow-md mx-auto';
                    
                    wrapper.appendChild(canvas);
                    
                    // Render PDF page into canvas context
                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                    
                    // Add page info
                    const pageInfo = document.createElement('p');
                    pageInfo.className = 'text-center text-sm text-gray-500 mt-3';
                    pageInfo.textContent = `Halaman 1 dari ${pdf.numPages}`;
                    wrapper.appendChild(pageInfo);
                    
                    // Add message if there are more pages
                    if (pdf.numPages > 1) {
                        const morePages = document.createElement('div');
                        morePages.className = 'mt-4 p-4 bg-yellow-50 border border-yellow-100 rounded-md text-center';
                        morePages.innerHTML = `
                            <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                            <span class="text-sm text-yellow-700">
                                Ini hanya pratinjau halaman pertama. Dokumen ini memiliki ${pdf.numPages} halaman.
                                Silakan download file untuk melihat seluruh dokumen.
                            </span>
                        `;
                        wrapper.appendChild(morePages);
                    }
                });
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                container.innerHTML = `
                    <div class="text-center p-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full mx-auto flex items-center justify-center mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-red-800 mb-2">Gagal memuat PDF</h4>
                        <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat dokumen PDF.</p>
                        <a href="${url}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i> Download File
                        </a>
                    </div>
                `;
            });
        }
    });
</script>
@endpush
@endsection