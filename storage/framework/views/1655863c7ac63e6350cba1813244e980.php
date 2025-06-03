

<?php $__env->startSection('title', $assignment->title); ?>

<?php $__env->startSection('heade                    <span class="flex items-center">
                        <i class="fas fa-book text-blue-500 mr-1"></i>
                        @if($assignment->subject)
                            <?php echo e($assignment->subject->name); ?>

                        <?php else: ?>
                            Mata pelajaran tidak tersedia
                        <?php endif; ?>
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-user text-green-500 mr-1"></i>
                        @if($assignment->teacher)
                            <?php echo e($assignment->teacher->name); ?>

                        @else
                            Guru tidak tersedia
                        @endif
                    </span>ail Tugas'); ?>

@section('navigation')
    <li>
        <a href="<?php echo e(route('siswa.dashboard')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('siswa.schedule.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-calendar-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Jadwal Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('siswa.assignments.index')); ?>" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-tasks text-lg w-6"></i>
            <span class="ml-3">Tugas</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('siswa.material.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-book text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Materi Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('siswa.grades.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-star text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Nilai</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('siswa.announcements.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-bullhorn text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengumuman</span>
        </a>
    </li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Add flash messages for success/error notifications -->
    <?php if(session('success')): ?>
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="<?php echo e(route('siswa.assignments.index')); ?>" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar tugas
        </a>
    </div>
    
    <!-- Assignment Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <!-- Header with status -->
        <div class="p-5 border-b border-gray-200 flex justify-between items-center
            <?php echo e($assignment->isExpired() ? 'bg-red-50' : 'bg-blue-50'); ?>">
            <div>
                <h3 class="text-xl font-semibold text-gray-900"><?php echo e($assignment->title); ?></h3>
                <div class="flex items-center mt-1 text-sm text-gray-600">
                    <span class="flex items-center mr-4">
                        <i class="fas fa-book text-blue-500 mr-1"></i>
                        <?php echo e($assignment->subject->name); ?>

                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-user text-green-500 mr-1"></i>
                        <?php echo e($assignment->teacher->name); ?>

                    </span>
                </div>
            </div>
            <!-- Status Badge -->
            <span class="px-4 py-2 rounded-full <?php echo e($assignment->isExpired() ? 'bg-red-500' : 'bg-blue-500'); ?> text-white text-sm font-semibold">
                <?php echo e($assignment->isExpired() ? 'Deadline Terlewati' : 'Aktif'); ?>

            </span>
        </div>
        
        <!-- Assignment Content -->
        <div class="p-5 bg-gray-50">
            <!-- Assignment Description -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Deskripsi Tugas</h4>
                <div class="bg-white p-3 rounded border border-gray-200">
                    <p class="text-gray-800 whitespace-pre-line"><?php echo e($assignment->description); ?></p>
                </div>
            </div>
            
            <!-- Assignment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Deadline</h4>
                    <div class="bg-white p-3 rounded border border-gray-200 flex items-center">
                        <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                        <span class="text-gray-800"><?php echo e($assignment->deadline ? $assignment->deadline->format('d M Y H:i') : 'No Deadline Set'); ?></span>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Status Pengumpulan</h4>
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <?php if($submission): ?>
                            <span class="text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Tugas sudah dikumpulkan pada <?php echo e($submission->created_at->format('d M Y H:i')); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-yellow-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Belum mengumpulkan tugas
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Assignment File Attachment (if any) -->
            <?php if($assignment->file): ?>
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Lampiran Tugas</h4>
                <div class="bg-white p-3 rounded border border-gray-200">
                    <a href="<?php echo e(asset('storage/' . $assignment->file)); ?>" target="_blank" class="flex items-center text-blue-500 hover:text-blue-700">
                        <i class="fas fa-file-download mr-2"></i>
                        <span>Download Lampiran Tugas</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Submission Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-5 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pengumpulan Tugas</h3>
            <p class="text-sm text-gray-600 mt-1">
                <?php echo e($submission ? 'Anda sudah mengumpulkan tugas ini. Anda dapat mengupdate pengumpulan tugas jika diperlukan.' 
                   : 'Upload file tugas Anda di bawah ini.'); ?>

            </p>
        </div>

        <!-- Show existing submission -->
        <?php if($submission): ?>
        <div class="p-5 bg-green-50 border-b border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-2">File yang Telah Diupload</h4>
            <div class="flex items-start bg-white p-3 rounded border border-gray-200">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-500">
                    <i class="fas fa-<?php echo e($submission->file_icon ?? 'file'); ?> text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h5 class="font-medium text-gray-800"><?php echo e($submission->file_name); ?></h5>
                    <div class="flex items-center text-sm text-gray-600 mt-1">
                        <span class="mr-3"><?php echo e($submission->file_size ?? 'Unknown size'); ?></span>
                        <span>Diupload: <?php echo e($submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : 'N/A'); ?></span>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <a href="<?php echo e(asset('storage/' . $submission->file_path)); ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-external-link-alt mr-1"></i> Lihat File
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>        <!-- Submission Form -->
        <div class="p-5">
            <?php if(!$assignment->isExpired() || ($submission && $assignment->allow_late_submission)): ?>
                <form action="<?php echo e($submission ? route('siswa.assignments.update-submission', $assignment) : route('siswa.assignments.submit', $assignment)); ?>" method="POST" enctype="multipart/form-data" class="space-y-4" id="submission-form">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Tugas <span class="text-red-500">*</span></label>
                        <div class="relative mt-1">
                            <div class="flex items-center">
                                <label class="cursor-pointer bg-white px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <span><i class="fas fa-upload mr-2"></i>Pilih File</span>
                                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.jpg,.jpeg,.png"
                                           class="hidden" onchange="handleFileSelect(event)">
                                </label>
                                <span id="file-name" class="ml-3 text-sm text-gray-500">Belum ada file dipilih</span>
                            </div>
                            
                            <!-- File Preview -->
                            <div id="file-preview" class="hidden mt-4 p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i id="file-icon" class="fas fa-file text-blue-500 text-xl mr-3"></i>
                                        <div>
                                            <h4 id="preview-name" class="text-sm font-medium text-gray-900"></h4>
                                            <p id="preview-size" class="text-xs text-gray-500"></p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <!-- File Type Validation Message -->
                                <div id="file-type-message" class="hidden mt-2 text-xs"></div>
                            </div>

                            <!-- Upload Progress Bar -->
                            <div id="upload-progress" class="hidden mt-4">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Mengunggah...</span>
                                    <span id="progress-percentage">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="progress-bar" class="bg-blue-500 h-2 rounded-full transition-all" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, JPG, PNG (Maks. 100MB)</p>
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="3" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Tambahkan catatan untuk tugas ini..."><?php echo e($submission->notes ?? ''); ?></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="reset" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Reset
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php echo e($submission ? 'Update Pengumpulan' : 'Kumpulkan Tugas'); ?>

                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Deadline telah terlewat</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Anda tidak dapat mengumpulkan tugas karena deadline telah terlewat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Grading Section (if graded) -->
    <?php if($submission && $submission->score !== null): ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-green-50 to-teal-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Hasil Penilaian</h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Nilai</h4>
                    <div class="bg-white p-4 rounded border border-gray-200 flex items-center">
                        <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <span class="text-xl font-bold text-green-600"><?php echo e($submission->score); ?></span>
                        </div>
                        <div>                            <p class="text-gray-900 font-medium">Nilai: <?php echo e($submission->score); ?>/100</p>
                            <p class="text-sm text-gray-500">Dinilai oleh: 
                                <?php if($assignment->teacher): ?>
                                    <?php echo e($assignment->teacher->name); ?>

                                <?php else: ?>
                                    Admin
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Dinilai Pada</h4>
                    <div class="bg-white p-3 rounded border border-gray-200 flex items-center">
                        <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                        <span class="text-gray-800"><?php echo e($submission->graded_at ? $submission->graded_at->format('d M Y H:i') : 'Belum dinilai'); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if($submission->feedback): ?>
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Feedback</h4>
                <div class="bg-white p-3 rounded border border-gray-200">
                    <p class="text-gray-800 whitespace-pre-line"><?php echo e($submission->feedback); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal to confirm when deadline is close -->
<div id="deadline-warning-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true" role="dialog">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="deadline-modal-backdrop"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Perhatian: Deadline Segera Berakhir</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Deadline pengumpulan tugas akan segera berakhir. Pastikan Anda telah memeriksa jawaban dengan teliti sebelum mengumpulkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" id="continue-submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                    Lanjutkan Pengumpulan
                </button>
                <button type="button" id="cancel-submit" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Periksa Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
    // Define allowed file types with their icons and colors
    const allowedFileTypes = {
        'pdf': { icon: 'fa-file-pdf', color: 'text-red-500' },
        'doc': { icon: 'fa-file-word', color: 'text-blue-500' },
        'docx': { icon: 'fa-file-word', color: 'text-blue-500' },
        'xls': { icon: 'fa-file-excel', color: 'text-green-500' },
        'xlsx': { icon: 'fa-file-excel', color: 'text-green-500' },
        'ppt': { icon: 'fa-file-powerpoint', color: 'text-orange-500' },
        'pptx': { icon: 'fa-file-powerpoint', color: 'text-orange-500' },
        'zip': { icon: 'fa-file-archive', color: 'text-yellow-500' },
        'rar': { icon: 'fa-file-archive', color: 'text-yellow-500' },
        'txt': { icon: 'fa-file-alt', color: 'text-gray-500' },
        'jpg': { icon: 'fa-file-image', color: 'text-purple-500' },
        'jpeg': { icon: 'fa-file-image', color: 'text-purple-500' },
        'png': { icon: 'fa-file-image', color: 'text-purple-500' }
    };

    function handleFileSelect(event) {
        const file = event.target.files[0];
        const fileNameDisplay = document.getElementById('file-name');
        const filePreview = document.getElementById('file-preview');
        const previewName = document.getElementById('preview-name');
        const previewSize = document.getElementById('preview-size');
        const fileIcon = document.getElementById('file-icon');
        const fileTypeMessage = document.getElementById('file-type-message');
        
        if (file) {
            const fileExt = file.name.split('.').pop().toLowerCase();
            const fileSize = formatFileSize(file.size);
            
            fileNameDisplay.textContent = file.name;
            fileNameDisplay.classList.add('text-blue-600', 'font-medium');
            
            // Show preview
            filePreview.classList.remove('hidden');
            previewName.textContent = file.name;
            previewSize.textContent = fileSize;
            
            // Set icon based on file type
            if (allowedFileTypes[fileExt]) {
                fileIcon.className = `fas ${allowedFileTypes[fileExt].icon} ${allowedFileTypes[fileExt].color} text-xl mr-3`;
            } else {
                fileIcon.className = 'fas fa-file text-blue-500 text-xl mr-3';
            }
            
            // Check file size
            if (file.size > 100 * 1024 * 1024) {
                fileTypeMessage.classList.remove('hidden');
                fileTypeMessage.classList.add('text-red-500');
                fileTypeMessage.innerHTML = `File terlalu besar (${fileSize}). Max: 100MB`;
            } else {
                fileTypeMessage.classList.add('hidden');
            }
        } else {
            fileNameDisplay.textContent = 'Belum ada file dipilih';
            fileNameDisplay.classList.remove('text-blue-600', 'font-medium');
            filePreview.classList.add('hidden');
        }
    }
    
    function removeFile() {
        const fileInput = document.getElementById('file');
        const fileNameDisplay = document.getElementById('file-name');
        const filePreview = document.getElementById('file-preview');
        
        fileInput.value = '';
        fileNameDisplay.textContent = 'Belum ada file dipilih';
        fileNameDisplay.classList.remove('text-blue-600', 'font-medium');
        filePreview.classList.add('hidden');
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('submission-form');
        const fileInput = document.getElementById('file');
        const fileNameDisplay = document.getElementById('file-name');
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        const uploadProgress = document.getElementById('upload-progress');

        // File input handling
        if (fileInput) {
        
        if (fileInput) {            fileInput.addEventListener('change', function(event) {
                handleFileSelect(event);
                
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileSize = formatFileSize(file.size);
                    
                    // Validate file size (max 100MB)
                    if (file.size > 100 * 1024 * 1024) {
                        document.getElementById('file-type-message').innerHTML = 
                            `<i class="fas fa-exclamation-circle text-red-500 mr-1"></i> File terlalu besar (${fileSize}). Ukuran maksimal yang diizinkan adalah 100MB.`;
                        removeFile();
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip'];
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExt)) {
                        alert(`Jenis file "${fileExt}" tidak diizinkan. Gunakan format yang diizinkan.`);
                        this.value = '';
                        fileNameDisplay.textContent = 'Belum ada file yang dipilih';
                        fileNameDisplay.classList.remove('text-blue-600', 'font-medium');
                        fileNameDisplay.classList.add('text-gray-500');
                        return;
                    }
                    
                    // Display file name and size
                    fileNameDisplay.textContent = `${file.name} (${fileSize})`;
                    fileNameDisplay.classList.add('text-blue-600', 'font-medium');
                    fileNameDisplay.classList.remove('text-gray-500');
                } else {
                    fileNameDisplay.textContent = 'Belum ada file yang dipilih';
                    fileNameDisplay.classList.remove('text-blue-600', 'font-medium');
                    fileNameDisplay.classList.add('text-gray-500');
                }
            });
        }
        
        // Deadline warning modal functionality
        const submissionForm = document.getElementById('submission-form');
        const deadlineWarningModal = document.getElementById('deadline-warning-modal');
        const continueSubmitBtn = document.getElementById('continue-submit');
        const cancelSubmitBtn = document.getElementById('cancel-submit');
        const deadlineModalBackdrop = document.getElementById('deadline-modal-backdrop');
        
        if (submissionForm) {
            submissionForm.addEventListener('submit', function(e) {
                // Only if not triggered by deadline warning modal
                if (!e.submittedViaModal) {
                    const fileInput = this.querySelector('input[type="file"]');
                    if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                        alert('Anda harus mengunggah file tugas');
                        e.preventDefault();
                        return false;
                    }
                    
                    <?php if(!isset($hoursRemaining) || $hoursRemaining >= 24): ?>
                    if (!confirm('Apakah Anda yakin ingin mengumpulkan tugas ini?')) {
                        e.preventDefault();
                        return false;
                    }
                    <?php endif; ?>
                }
            });
        }
        
        if (continueSubmitBtn) {
            continueSubmitBtn.addEventListener('click', function() {
                if (submissionForm) {
                    // Set flag to avoid re-triggering confirmation
                    const submitEvent = new Event('submit', {
                        bubbles: true,
                        cancelable: true
                    });
                    submitEvent.submittedViaModal = true;
                    submissionForm.dispatchEvent(submitEvent);
                    
                    if (!submitEvent.defaultPrevented) {
                        submissionForm.submit();
                    }
                }
                deadlineWarningModal.classList.add('hidden');
            });
        }
        
        if (cancelSubmitBtn) {
            cancelSubmitBtn.addEventListener('click', function() {
                deadlineWarningModal.classList.add('hidden');
            });
        }
        
        if (deadlineModalBackdrop) {
            deadlineModalBackdrop.addEventListener('click', function() {
                deadlineWarningModal.classList.add('hidden');
            });
        }
        
        // Helper to format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .prose {
        color: #374151;
        max-width: 65ch;
    }
    
    .prose p {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\NEW\PROYEK-AKHIR-II-WEB\resources\views/siswa/assignments/show.blade.php ENDPATH**/ ?>