

<?php $__env->startSection('title', 'Edit Materi Pelajaran'); ?>

<?php $__env->startSection('header', 'Edit Materi Pelajaran'); ?>

<?php $__env->startSection('navigation'); ?>
    <?php echo $__env->make('guru.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-edit text-9xl"></i>
        </div>
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold mb-2">Edit Materi Pelajaran</h2>
                <p class="text-indigo-100">Perbarui materi pembelajaran untuk siswa.</p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('guru.materials.show', $material)); ?>" class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg font-medium shadow-sm hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                </a>
                <a href="<?php echo e(route('guru.materials.index')); ?>" class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg font-medium shadow-sm hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
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
    
    <?php if(session('info')): ?>
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
        <p><?php echo e(session('info')); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="<?php echo e(route('guru.materials.update', $material)); ?>" method="POST" enctype="multipart/form-data" class="animate-fade-in" id="materialForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title field -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Materi <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title', $material->title)); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['title'];
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
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('description', $material->description)); ?></textarea>
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
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subject->id); ?>" <?php echo e((old('subject_id', $material->subject_id) == $subject->id) ? 'selected' : ''); ?>>
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
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-shadow duration-300 <?php $__errorArgs = ['classroom_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($classroom->id); ?>" <?php echo e((in_array($classroom->id, old('classroom_id', $selectedClassrooms))) ? 'selected' : ''); ?>>
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
                    
                    <!-- Active status -->
                    <div class="form-group mb-5">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" 
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    <?php echo e($material->is_active ? 'checked' : ''); ?>>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Materi Aktif</label>
                                <p class="text-gray-500">Materi yang tidak aktif tidak akan ditampilkan kepada siswa.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current file info -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Materi Saat Ini</label>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="<?php echo e($material->getFileColorAttribute()); ?> bg-opacity-20 p-3 rounded-full mr-3">
                                <i class="fas <?php echo e($material->getFileIconAttribute()); ?> <?php echo e($material->getFileColorAttribute()); ?>"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?php echo e(basename($material->file_path)); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($material->getFileType()); ?></p>
                            </div>
                            <a href="<?php echo e(route('guru.materials.download', $material)); ?>" class="ml-auto inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-download mr-1"></i> Unduh
                            </a>
                        </div>
                    </div>
                    
                    <!-- New file upload field -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="material_file" class="block text-sm font-medium text-gray-700 mb-1">Unggah File Baru <span class="text-gray-500">(opsional)</span></label>
                        <div class="mt-1">
                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg <?php echo e($errors->has('material_file') ? 'border-red-500' : 'border-gray-300'); ?>">
                                <div class="space-y-3 text-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="material_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Unggah file baru</span>
                                            <input id="material_file" name="material_file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.mp4,.zip,.rar">
                                        </label>
                                        <p class="pl-1">atau seret dan lepas</p>
                                    </div>
                                    <p class="text-xs text-gray-500" id="file-name">
                                        Kosongkan jika tidak ingin mengganti file
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
                    <a href="<?php echo e(route('guru.materials.show', $material)); ?>" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
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
                
                // Add file type icon
                const fileExtension = file.name.split('.').pop().toLowerCase();
                let fileIcon = 'fa-file';
                let iconColor = 'text-gray-500';
                
                switch(fileExtension) {
                    case 'pdf':
                        fileIcon = 'fa-file-pdf';
                        iconColor = 'text-red-500';
                        break;
                    case 'doc':
                    case 'docx':
                        fileIcon = 'fa-file-word';
                        iconColor = 'text-blue-500';
                        break;
                    case 'xls':
                    case 'xlsx':
                        fileIcon = 'fa-file-excel';
                        iconColor = 'text-green-500';
                        break;
                    case 'ppt':
                    case 'pptx':
                        fileIcon = 'fa-file-powerpoint';
                        iconColor = 'text-orange-500';
                        break;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        fileIcon = 'fa-file-image';
                        iconColor = 'text-purple-500';
                        break;
                    case 'zip':
                    case 'rar':
                        fileIcon = 'fa-file-archive';
                        iconColor = 'text-yellow-500';
                        break;
                    case 'mp4':
                    case 'avi':
                    case 'mov':
                        fileIcon = 'fa-file-video';
                        iconColor = 'text-pink-500';
                        break;
                }
                
                fileNameDisplay.innerHTML = `<i class="fas ${fileIcon} ${iconColor} mr-1"></i> ${file.name} (${fileSize} MB)`;
                
                // Show warning if file is too large
                if (file.size > 20 * 1024 * 1024) { // 20MB
                    fileNameDisplay.innerHTML += ' <span class="text-red-500">(File terlalu besar, maks 20MB)</span>';
                }
            } else {
                fileNameDisplay.textContent = 'Kosongkan jika tidak ingin mengganti file';
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
            
            // File size check
            if (fileInput.files && fileInput.files.length > 0 && fileInput.files[0].size > 20 * 1024 * 1024) { // 20MB
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

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/materials/edit.blade.php ENDPATH**/ ?>