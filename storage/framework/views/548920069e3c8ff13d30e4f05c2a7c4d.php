

<?php $__env->startSection('title', 'Buat Materi Pelajaran'); ?>

<?php $__env->startSection('header', 'Buat Materi Pelajaran'); ?>

<?php $__env->startSection('navigation'); ?>
    <?php echo $__env->make('guru.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-book-open text-9xl"></i>
        </div>
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold mb-2">Buat Materi Pelajaran</h2>
                <p class="text-blue-100">Bagikan materi pembelajaran kepada siswa.</p>
            </div>
            <a href="<?php echo e(route('guru.materials.index')); ?>" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg font-medium shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
      <?php if($errors->any()): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <h4 class="font-bold mb-2">Terjadi kesalahan:</h4>
        <ul class="list-disc list-inside">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if(session('warning')): ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded" role="alert">
        <h4 class="font-bold mb-2">Perhatian:</h4>
        <p><?php echo e(session('warning')); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="<?php echo e(route('guru.materials.store')); ?>" method="POST" enctype="multipart/form-data" class="animate-fade-in" id="materialForm">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title field -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Materi <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        </div>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Description field -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Materi <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="description" id="description" rows="6" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('description')); ?></textarea>
                        </div>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Jelaskan materi pembelajaran dengan detail agar siswa dapat memahami dengan baik.</p>
                    </div>
                    
                    <!-- Subject field -->
                    <div class="form-group mb-5">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            <select name="subject_id" id="subject_id" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subject->id); ?>" <?php echo e(old('subject_id') == $subject->id ? 'selected' : ''); ?>>
                                        <?php echo e($subject->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php $__errorArgs = ['subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Classrooms field -->
                    <div class="form-group mb-5">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <select name="classroom_id[]" id="classroom_id" multiple 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['classroom_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($classroom->id); ?>" <?php echo e((old('classroom_id') && in_array($classroom->id, old('classroom_id'))) ? 'selected' : ''); ?>>
                                        <?php echo e($classroom->name); ?> - <?php echo e($classroom->grade_level); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php $__errorArgs = ['classroom_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih beberapa kelas.</p>
                    </div>
                    
                    <!-- File upload field -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="material_file" class="block text-sm font-medium text-gray-700 mb-1">Unggah File Materi <span class="text-red-500">*</span></label>
                        <div class="mt-1">                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg <?php echo e($errors->has('material_file') ? 'border-red-500' : 'border-gray-300'); ?>">
                                <div class="space-y-3 text-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="material_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Unggah file</span>
                                            <input id="material_file" name="material_file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.mp4,.zip,.rar">
                                        </label>
                                        <p class="pl-1">atau seret dan lepas</p>
                                    </div>
                                    <p class="text-xs text-gray-500" id="file-name">
                                        Belum ada file yang dipilih
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        PDF, Word, PowerPoint, Excel, Image, Video, atau ZIP (maks. 20MB)
                                    </p>
                                </div>
                            </div>
                            <?php $__errorArgs = ['material_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="<?php echo e(route('guru.materials.index')); ?>" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Simpan Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('material_file');
        const fileNameDisplay = document.getElementById('file-name');
        
        // Display selected filename
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Convert to MB
                fileNameDisplay.textContent = `${file.name} (${fileSize} MB)`;
                
                // Add file type icon
                const fileExtension = file.name.split('.').pop().toLowerCase();
                let fileIcon = 'fa-file';
                
                switch(fileExtension) {
                    case 'pdf':
                        fileIcon = 'fa-file-pdf text-red-500';
                        break;
                    case 'doc':
                    case 'docx':
                        fileIcon = 'fa-file-word text-blue-500';
                        break;
                    case 'xls':
                    case 'xlsx':
                        fileIcon = 'fa-file-excel text-green-500';
                        break;
                    case 'ppt':
                    case 'pptx':
                        fileIcon = 'fa-file-powerpoint text-orange-500';
                        break;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        fileIcon = 'fa-file-image text-purple-500';
                        break;
                    case 'zip':
                    case 'rar':
                        fileIcon = 'fa-file-archive text-yellow-500';
                        break;
                    case 'mp4':
                    case 'avi':
                    case 'mov':
                        fileIcon = 'fa-file-video text-pink-500';
                        break;
                    default:
                        fileIcon = 'fa-file text-gray-500';
                }
                
                fileNameDisplay.innerHTML = `<i class="fas ${fileIcon} mr-1"></i> ${file.name} (${fileSize} MB)`;
                
                // Show warning if file is too large
                if (file.size > 20 * 1024 * 1024) { // 20MB
                    fileNameDisplay.innerHTML += ' <span class="text-red-500">(File terlalu besar, maks 20MB)</span>';
                }
            } else {
                fileNameDisplay.textContent = 'Belum ada file yang dipilih';
            }
        });
        
        // Form validation
        const materialForm = document.getElementById('materialForm');
        materialForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const subjectId = document.getElementById('subject_id').value;
            const classroomId = document.getElementById('classroom_id');
            const selectedClassrooms = Array.from(classroomId.selectedOptions).length;
            
            let isValid = true;
            
            if (!title) {
                isValid = false;
                document.getElementById('title').classList.add('border-red-300');
            }
            
            if (!description) {
                isValid = false;
                document.getElementById('description').classList.add('border-red-300');
            }
            
            if (!subjectId) {
                isValid = false;
                document.getElementById('subject_id').classList.add('border-red-300');
            }
            
            if (selectedClassrooms === 0) {
                isValid = false;
                document.getElementById('classroom_id').classList.add('border-red-300');
            }
            
            if (!fileInput.files || fileInput.files.length === 0) {
                isValid = false;
                fileNameDisplay.innerHTML = '<span class="text-red-500">File harus diunggah</span>';
            } else if (fileInput.files[0].size > 20 * 1024 * 1024) { // 20MB
                isValid = false;
                fileNameDisplay.innerHTML = `<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i> File terlalu besar (maks 20MB)`;
            }
            
            if (!isValid) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Show validation message
                if (!document.querySelector('.validation-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded';
                    errorDiv.innerHTML = '<p>Mohon lengkapi semua field yang diperlukan.</p>';
                    materialForm.parentNode.insertBefore(errorDiv, materialForm);
                    
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/materials/create.blade.php ENDPATH**/ ?>