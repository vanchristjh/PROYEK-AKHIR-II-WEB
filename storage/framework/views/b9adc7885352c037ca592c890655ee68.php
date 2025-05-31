

<?php $__env->startSection('title', 'Buat Pengumuman Baru'); ?>

<?php $__env->startSection('header', 'Buat Pengumuman Baru'); ?>

<?php $__env->startSection('navigation'); ?>
    <li>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-users text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengguna</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.subjects.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-book text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Mata Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.classrooms.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-school text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Kelas</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.announcements.index')); ?>" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-bullhorn text-lg w-6"></i>
            <span class="ml-3">Pengumuman</span>
        </a>
    </li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Enhanced Header with animation and floating elements -->
    <div class="bg-gradient-to-r from-red-600 via-red-500 to-orange-500 animate-gradient-x rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden">
        <div class="particles-container absolute inset-0 pointer-events-none"></div>
        <div class="absolute -right-5 -top-5 opacity-20 transform group-hover:scale-110 transition-transform duration-700">
            <i class="fas fa-bullhorn text-9xl"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-1/3 -top-12 w-36 h-36 bg-orange-300/20 rounded-full blur-3xl"></div>
        <div class="relative z-10 animate-fade-in">
            <h2 class="text-2xl font-bold mb-2 flex items-center">
                <div class="h-10 w-10 rounded-lg bg-white/20 flex items-center justify-center mr-3 shadow-inner backdrop-blur-sm">
                    <i class="fas fa-bullhorn"></i>
                </div>
                Buat Pengumuman Baru
            </h2>
            <p class="text-red-100 ml-1">Sampaikan informasi penting kepada guru dan siswa dengan cepat dan mudah.</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200/50 transform transition-all duration-300 hover:shadow-md">
        <div class="p-6">
            <form action="<?php echo e(route('admin.announcements.store')); ?>" method="POST" enctype="multipart/form-data" class="animate-fade-in">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Form Title -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            Judul Pengumuman
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" 
                                placeholder="Masukkan judul pengumuman yang informatif"
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-all duration-300" required>
                        </div>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Form Content -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            Isi Pengumuman
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="content" id="content" rows="6" 
                                placeholder="Tulis isi pengumuman dengan jelas dan lengkap"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-all duration-300" required><?php echo e(old('content')); ?></textarea>
                        </div>
                        <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Audience Selector -->
                    <div class="form-group mb-5">
                        <label for="audience" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            Ditujukan Kepada
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <select name="audience" id="audience" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-all duration-300" required>
                                <option value="" disabled selected>-- Pilih Audiens --</option>
                                <option value="all" <?php echo e(old('audience') == 'all' ? 'selected' : ''); ?>>Semua</option>
                                <option value="teachers" <?php echo e(old('audience') == 'teachers' ? 'selected' : ''); ?>>Guru Saja</option>
                                <option value="students" <?php echo e(old('audience') == 'students' ? 'selected' : ''); ?>>Siswa Saja</option>
                            </select>
                        </div>
                        <?php $__errorArgs = ['audience'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Important Flag -->
                    <div class="form-group mb-5">
                        <label for="is_important" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            Prioritas
                        </label>
                        <div class="mt-1 relative p-4 border border-gray-200 rounded-lg bg-gray-50/50 hover:bg-gray-50 transition-colors duration-300">
                            <div class="flex items-center">
                                <div class="flex items-center h-6">
                                    <input id="is_important" name="is_important" type="checkbox" value="1" <?php echo e(old('is_important') ? 'checked' : ''); ?>

                                        class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded transition-all duration-300">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_important" class="font-medium text-gray-700">Tandai sebagai pengumuman penting</label>
                                    <p class="text-gray-500 mt-1">
                                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                        Pengumuman penting akan ditampilkan dengan highlight khusus
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php $__errorArgs = ['is_important'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Publish Date -->
                    <div class="form-group mb-5">
                        <label for="publish_date" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            <div class="flex items-center">
                                <span>Tanggal Publikasi</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Opsional
                                </span>
                            </div>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="publish_date" id="publish_date" value="<?php echo e(old('publish_date')); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-all duration-300">
                            <div class="text-xs text-gray-500 mt-1 flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mr-1 mt-0.5"></i>
                                <span>Kosongkan untuk publikasi langsung. Gunakan fitur ini untuk menjadwalkan pengumuman di masa depan.</span>
                            </div>
                        </div>
                        <?php $__errorArgs = ['publish_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Expiry Date -->
                    <div class="form-group mb-5">
                        <label for="expiry_date" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            <div class="flex items-center">
                                <span>Tanggal Kedaluwarsa</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Opsional
                                </span>
                            </div>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-times text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="expiry_date" id="expiry_date" value="<?php echo e(old('expiry_date')); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-all duration-300">
                            <div class="text-xs text-gray-500 mt-1 flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mr-1 mt-0.5"></i>
                                <span>Kosongkan jika tidak memiliki batas waktu. Pengumuman akan otomatis disembunyikan setelah tanggal ini.</span>
                            </div>
                        </div>
                        <?php $__errorArgs = ['expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- File Attachment with improved UI -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors duration-300">
                            <div class="flex items-center">
                                <span>Lampiran</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Opsional
                                </span>
                            </div>
                        </label>
                        <div class="mt-1 relative">
                            <div class="p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-300">
                                <div class="flex flex-col items-center space-y-3">
                                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                                        <i class="fas fa-paperclip text-red-500 text-2xl" id="attachment-icon"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-700">Drag & drop file atau</p>
                                        <label for="attachment" class="relative cursor-pointer rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                            <span>Pilih file</span>
                                            <input type="file" name="attachment" id="attachment" class="sr-only">
                                        </label>
                                    </div>
                                    <div class="text-xs text-gray-500 text-center">
                                        <p>Maksimal 20MB</p>
                                        <p>Format: PDF, Word, Excel, PowerPoint, Image, ZIP</p>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500" id="selected-file" style="display: none;">
                                        <i class="fas fa-file mr-2"></i>
                                        <span id="file-name"></span>
                                        <button type="button" id="remove-file" class="ml-2 text-red-500 hover:text-red-700">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end space-x-3">
                        <a href="<?php echo e(route('admin.announcements.index')); ?>" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                            <i class="fas fa-bullhorn mr-2"></i> Terbitkan Pengumuman
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .animate-fade-in {
        animation: fade-in 0.6s ease-in-out;
    }
    
    .animate-gradient-x {
        background-size: 300% 300%;
        animation: gradient-x 15s ease infinite;
    }
    
    @keyframes gradient-x {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes fade-in {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-group:focus-within label {
        color: #ef4444;
        font-weight: 600;
    }
    
    .form-group:focus-within i {
        color: #ef4444;
    }
    
    /* Particle effect */
    .particles-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create floating particles effect
        const particlesContainer = document.querySelector('.particles-container');
        if (particlesContainer) {
            for (let i = 0; i < 25; i++) {
                createParticle(particlesContainer);
            }
        }
        
        function createParticle(container) {
            const particle = document.createElement('div');
            
            // Style the particle
            particle.style.position = 'absolute';
            particle.style.width = Math.random() * 6 + 2 + 'px';
            particle.style.height = particle.style.width;
            particle.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
            particle.style.borderRadius = '50%';
            
            // Position the particle randomly within the container
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            
            // Add the particle to the container
            container.appendChild(particle);
            
            // Animate the particle
            const duration = Math.random() * 10000 + 5000; // Between 5-15s
            const xMove = Math.random() * 30 - 15;
            const yMove = Math.random() * 30 - 15;
            
            particle.animate(
                [
                    { transform: 'translate(0, 0)', opacity: 0 },
                    { transform: `translate(${xMove}px, ${yMove}px)`, opacity: 0.8, offset: 0.5 },
                    { transform: 'translate(0, 0)', opacity: 0 }
                ],
                {
                    duration,
                    iterations: Infinity,
                    direction: 'alternate',
                    easing: 'ease-in-out'
                }
            );
        }
        
        // Animate form groups when focused
        document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(element => {
            element.addEventListener('focus', function() {
                this.closest('.form-group').classList.add('focused');
            });
            
            element.addEventListener('blur', function() {
                this.closest('.form-group').classList.remove('focused');
            });
        });
        
        // Improved file handling
        const attachmentInput = document.getElementById('attachment');
        const attachmentIcon = document.getElementById('attachment-icon');
        const selectedFile = document.getElementById('selected-file');
        const fileName = document.getElementById('file-name');
        const removeFileBtn = document.getElementById('remove-file');
        
        attachmentInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileName.textContent = file.name;
                selectedFile.style.display = 'flex';
                
                const fileType = file.type;
                if (fileType.includes('pdf')) {
                    attachmentIcon.className = 'fas fa-file-pdf text-red-500 text-2xl';
                } else if (fileType.includes('word') || fileType.includes('doc')) {
                    attachmentIcon.className = 'fas fa-file-word text-blue-500 text-2xl';
                } else if (fileType.includes('spreadsheet') || fileType.includes('excel')) {
                    attachmentIcon.className = 'fas fa-file-excel text-green-500 text-2xl';
                } else if (fileType.includes('presentation') || fileType.includes('powerpoint')) {
                    attachmentIcon.className = 'fas fa-file-powerpoint text-orange-500 text-2xl';
                } else if (fileType.includes('image')) {
                    attachmentIcon.className = 'fas fa-file-image text-purple-500 text-2xl';
                } else if (fileType.includes('zip') || fileType.includes('archive')) {
                    attachmentIcon.className = 'fas fa-file-archive text-yellow-500 text-2xl';
                } else {
                    attachmentIcon.className = 'fas fa-file text-gray-500 text-2xl';
                }
            }
        });
        
        removeFileBtn.addEventListener('click', function() {
            attachmentInput.value = '';
            selectedFile.style.display = 'none';
            attachmentIcon.className = 'fas fa-paperclip text-red-500 text-2xl';
        });
        
        // Drag and drop functionality for file input
        const dropArea = attachmentInput.closest('.border-dashed');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropArea.classList.add('border-red-500', 'bg-red-50');
        }
        
        function unhighlight() {
            dropArea.classList.remove('border-red-500', 'bg-red-50');
        }
        
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                attachmentInput.files = files;
                attachmentInput.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/admin/announcements/create.blade.php ENDPATH**/ ?>