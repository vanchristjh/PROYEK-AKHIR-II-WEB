@extends('layouts.dashboard')

@section('title', 'Buat Tugas Baru')

@section('header', 'Buat Tugas Baru')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.assignments.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar tugas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Form Pembuatan Tugas</h3>
            <p class="mt-1 text-sm text-gray-500">
                Isi detail tugas yang ingin diberikan kepada siswa.
            </p>
        </div>

        <form action="{{ route('guru.assignments.store') }}" method="POST" enctype="multipart/form-data" class="p-6" id="assignment-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Tugas <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
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
                              required>{{ old('description') }}</textarea>
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
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                    <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" 
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
                            <div class="md:col-span-2 mb-2 pb-2 border-b border-gray-200">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" id="select-all-classes" 
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="select-all-classes" class="font-medium text-gray-700">Pilih Semua Kelas</label>
                                    </div>
                                </div>
                            </div>
                            @foreach($classes as $class)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="classes[]" id="class-{{ $class->id }}" value="{{ $class->id }}" 
                                            class="class-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            {{ in_array($class->id, old('classes', [])) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="class-{{ $class->id }}" class="font-medium text-gray-700">{{ $class->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="classes-error" class="mt-1 text-sm text-red-600 hidden">Pilih minimal satu kelas untuk tugas ini</div>
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
                </div>

                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700">Nilai Maksimal</label>
                            <input type="number" name="max_score" id="max_score" 
                                   value="{{ old('max_score', 100) }}"
                                   min="0" max="100"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <div class="flex items-start mt-5">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="allow_late_submission" id="allow_late_submission" 
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                           {{ old('allow_late_submission') ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="allow_late_submission" class="font-medium text-gray-700">
                                        Izinkan Pengumpulan Terlambat
                                    </label>
                                    <p class="text-gray-500">Siswa dapat mengumpulkan setelah deadline</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="late_submission_penalty" class="block text-sm font-medium text-gray-700">
                                Penalti Keterlambatan (%)
                            </label>
                            <input type="number" name="late_submission_penalty" id="late_submission_penalty" 
                                   value="{{ old('late_submission_penalty', 0) }}"
                                   min="0" max="100"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-1 text-xs text-gray-500">
                                Persentase pengurangan nilai untuk pengumpulan terlambat
                            </p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Lampiran</label>
                    <div class="mt-1 flex items-center">
                        <span class="inline-block h-12 w-12 overflow-hidden bg-gray-100 rounded-lg">
                            <i class="fas fa-file-upload text-gray-400 flex items-center justify-center h-full text-xl"></i>
                        </span>
                        <div class="ml-5 flex-1">
                            <input type="file" name="file" id="file" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, JPG, JPEG, PNG (Maks. 100MB)</p>
                        </div>
                    </div>
                    <div id="selected-file" class="hidden mt-3 p-2 bg-gray-50 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i id="file-icon" class="fas fa-file text-blue-500 mr-2"></i>
                                <span id="file-name" class="text-sm text-gray-700"></span>
                                <span id="file-size" class="ml-2 text-xs text-gray-500"></span>
                            </div>
                            <div class="flex items-center">
                                <button type="button" id="preview-file" class="text-blue-500 hover:text-blue-700 mr-3" title="Preview file">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" id="remove-file" class="text-red-500 hover:text-red-700" title="Hapus file">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>                <!-- is_active checkbox removed - column doesn't exist in database -->
                
            </div>

            <div class="mt-6 flex items-center justify-end">
                <button type="button" onclick="window.history.back()" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Buat Tugas
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const selectedFile = document.getElementById('selected-file');
        const fileName = document.getElementById('file-name');
        const fileSizeElement = document.getElementById('file-size');
        const removeFile = document.getElementById('remove-file');
        const previewFile = document.getElementById('preview-file');
        const fileIcon = document.getElementById('file-icon');
        const fileError = document.getElementById('file-error');
        const classesError = document.getElementById('classes-error');
        
        // File upload handling
        if(fileInput) {
            fileInput.addEventListener('change', function() {
                if(this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileSizeMB = Math.round((file.size / 1024 / 1024) * 100) / 100; // MB
                    
                    // Check file size
                    if(fileSizeMB > 100) {
                        fileError.textContent = `File terlalu besar (${fileSizeMB}MB). Ukuran maksimal yang diizinkan adalah 100MB.`;
                        fileError.classList.remove('hidden');
                        this.value = '';
                        selectedFile.classList.add('hidden');
                        return false;
                    }
                    
                    // Check file type
                    const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png'];
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    
                    if(!allowedTypes.includes(fileExt)) {
                        fileError.textContent = `Jenis file "${fileExt}" tidak diizinkan. Gunakan format yang diizinkan.`;
                        fileError.classList.remove('hidden');
                        this.value = '';
                        selectedFile.classList.add('hidden');
                        return false;
                    }
                    
                    // Set appropriate icon based on file type
                    let iconClass = 'fa-file-alt text-blue-500';
                    
                    switch(fileExt) {
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
                    
                    fileIcon.className = `fas ${iconClass} mr-2`;
                    
                    // Show selected file info
                    fileError.classList.add('hidden');
                    fileName.textContent = file.name;
                    fileSizeElement.textContent = `(${fileSizeMB.toFixed(2)} MB)`;
                    selectedFile.classList.remove('hidden');
                    
                    // Enable/disable preview button based on file type
                    if(['pdf', 'jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                        previewFile.classList.remove('hidden');
                    } else {
                        previewFile.classList.add('hidden');
                    }
                } else {
                    selectedFile.classList.add('hidden');
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
            
            // Preview file button
            if(previewFile) {
                previewFile.addEventListener('click', function() {
                    if(fileInput.files && fileInput.files[0]) {
                        const file = fileInput.files[0];
                        const fileExt = file.name.split('.').pop().toLowerCase();
                        
                        if(['jpg', 'jpeg', 'png', 'gif', 'pdf'].includes(fileExt)) {
                            const fileUrl = URL.createObjectURL(file);
                            openFilePreview(fileUrl, fileExt, file.name);
                        }
                    }
                });
            }
        }
        
        // Class selection handling
        const selectAllCheckbox = document.getElementById('select-all-classes');
        const classCheckboxes = document.querySelectorAll('.class-checkbox');
        
        if(selectAllCheckbox && classCheckboxes.length > 0) {
            // Set initial state of select all checkbox
            const allChecked = Array.from(classCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(classCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
            
            // Add functionality to select all classes
            selectAllCheckbox.addEventListener('change', function() {
                classCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                
                if(this.checked) {
                    classesError.classList.add('hidden');
                }
            });
            
            // Update select all when individual checkboxes change
            classCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(classCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(classCheckboxes).some(cb => cb.checked);
                    
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                    
                    if(someChecked) {
                        classesError.classList.add('hidden');
                    }
                });
            });
        }
        
        // Form validation with improved error handling
        const form = document.getElementById('assignment-form');
        if(form) {
            form.addEventListener('submit', function(e) {
                let hasError = false;
                const errorMessages = [];
                
                // Required field validation
                const title = document.getElementById('title');
                const description = document.getElementById('description');
                const subject = document.getElementById('subject_id');
                const deadline = document.getElementById('deadline');
                
                if(!title.value.trim()) {
                    errorMessages.push('Judul tugas tidak boleh kosong');
                    hasError = true;
                }
                
                if(!description.value.trim()) {
                    errorMessages.push('Deskripsi tugas tidak boleh kosong');
                    hasError = true;
                }
                
                if(!subject.value) {
                    errorMessages.push('Mata pelajaran harus dipilih');
                    hasError = true;
                }
                
                // Check if at least one class is selected
                const checkedClassCheckboxes = document.querySelectorAll('.class-checkbox:checked');
                if(checkedClassCheckboxes.length === 0) {
                    classesError.classList.remove('hidden');
                    errorMessages.push('Pilih minimal satu kelas');
                    hasError = true;
                } else {
                    classesError.classList.add('hidden');
                }
                
                // Validate deadline - make sure it's in the future
                if(deadline && deadline.value) {
                    const deadlineDate = new Date(deadline.value);
                    const now = new Date();
                    
                    if(deadlineDate <= now) {
                        errorMessages.push('Deadline harus di masa depan');
                        hasError = true;
                    }
                } else {
                    errorMessages.push('Deadline harus ditentukan');
                    hasError = true;
                }
                
                // File validation
                if(fileInput && fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    const fileSizeMB = Math.round((file.size / 1024 / 1024) * 100) / 100;
                    
                    if(fileSizeMB > 100) {
                        fileError.textContent = `File terlalu besar (${fileSizeMB}MB). Ukuran maksimal yang diizinkan adalah 100MB.`;
                        fileError.classList.remove('hidden');
                        errorMessages.push(`File terlalu besar (${fileSizeMB}MB)`);
                        hasError = true;
                    }
                    
                    const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png'];
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    
                    if(!allowedTypes.includes(fileExt)) {
                        fileError.textContent = `Jenis file "${fileExt}" tidak diizinkan. Gunakan format yang diizinkan.`;
                        fileError.classList.remove('hidden');
                        errorMessages.push(`Jenis file "${fileExt}" tidak diizinkan`);
                        hasError = true;
                    }
                }
                
                if(hasError) {
                    e.preventDefault();
                    if(errorMessages.length > 0) {
                        alert('Periksa form Anda:\n- ' + errorMessages.join('\n- '));
                    }
                    return false;
                }
            });
        }
    });
    
    // Create a simple file preview overlay
    function openFilePreview(fileUrl, fileType, fileName) {
        // Create overlay elements if they don't exist
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
                    <span id="preview-filename">${fileName}</span>
                </h3>
                <button type="button" onclick="closeFilePreview()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            `;
            
            const body = document.createElement('div');
            body.className = 'flex-1 overflow-auto p-4';
            body.innerHTML = `
                <div id="file-content-container" class="h-full flex items-center justify-center">
                    <div id="image-preview" class="h-full flex items-center justify-center ${fileType !== 'pdf' ? '' : 'hidden'}">
                        <img id="preview-image" src="${fileType !== 'pdf' ? fileUrl : ''}" alt="File Preview" class="max-h-full max-w-full object-contain">
                    </div>
                    <div id="pdf-preview" class="h-full w-full ${fileType === 'pdf' ? '' : 'hidden'}"></div>
                </div>
            `;
            
            content.appendChild(header);
            content.appendChild(body);
            overlay.appendChild(content);
            document.body.appendChild(overlay);
            
            // Set up PDF preview if needed
            if (fileType === 'pdf' && window.pdfjsLib) {
                const pdfPreview = document.getElementById('pdf-preview');
                
                // Load PDF.js if not already loaded
                if (!window.pdfjsLib) {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js';
                    script.onload = function() {
                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
                        loadPdf(fileUrl, pdfPreview);
                    };
                    document.head.appendChild(script);
                } else {
                    loadPdf(fileUrl, pdfPreview);
                }
            }
        } else {
            // Update existing overlay
            document.getElementById('preview-filename').textContent = fileName;
            
            if (fileType !== 'pdf') {
                document.getElementById('image-preview').classList.remove('hidden');
                document.getElementById('pdf-preview').classList.add('hidden');
                document.getElementById('preview-image').src = fileUrl;
            } else {
                document.getElementById('image-preview').classList.add('hidden');
                document.getElementById('pdf-preview').classList.remove('hidden');
                if (window.pdfjsLib) {
                    loadPdf(fileUrl, document.getElementById('pdf-preview'));
                }
            }
            
            overlay.style.display = 'flex';
        }
    }
    
    function closeFilePreview() {
        const overlay = document.getElementById('file-preview-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
    
    function loadPdf(url, container) {
        // Clear container
        container.innerHTML = '';
        
        // Load the PDF
        pdfjsLib.getDocument(url).promise.then(function(pdf) {
            // Create container for all pages
            const wrapper = document.createElement('div');
            wrapper.className = 'pdf-container';
            container.appendChild(wrapper);
            
            // Load first page for preview
            pdf.getPage(1).then(function(page) {
                const scale = 1.5;
                const viewport = page.getViewport({ scale });
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.className = 'shadow-md mx-auto';
                
                wrapper.appendChild(canvas);
                
                // Render PDF page to canvas
                page.render({
                    canvasContext: context,
                    viewport: viewport
                });
                
                // Add page info
                const pageInfo = document.createElement('p');
                pageInfo.className = 'text-center text-sm text-gray-500 mt-2';
                pageInfo.textContent = `Halaman 1 dari ${pdf.numPages}`;
                wrapper.appendChild(pageInfo);
                
                // Add note about preview
                if (pdf.numPages > 1) {
                    const note = document.createElement('p');
                    note.className = 'text-center text-sm text-gray-500 mt-4';
                    note.textContent = 'Ini hanya pratinjau halaman pertama.';
                    wrapper.appendChild(note);
                }
            });
        }).catch(function(error) {
            container.innerHTML = `
                <div class="text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                    <p>Gagal memuat dokumen PDF. Silahkan coba lagi.</p>
                </div>
            `;
            console.error('Error loading PDF:', error);
        });
    }
</script>

<!-- Add this at the end of the file to create the global functions -->
<script>
    window.closeFilePreview = function() {
        const overlay = document.getElementById('file-preview-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    };
</script>
@endpush
@endsection
