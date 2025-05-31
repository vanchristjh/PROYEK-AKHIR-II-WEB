

<?php $__env->startSection('title', $material->title); ?>

<?php $__env->startSection('header', 'Detail Materi Pembelajaran'); ?>

<?php $__env->startSection('navigation'); ?>
    <?php echo $__env->make('guru.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <a href="<?php echo e(route('guru.materials.index')); ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Daftar Materi</span>
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>

    <!-- Material Header -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100/50 mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex items-start">
                    <div class="<?php echo e($material->getFileColorAttribute()); ?> bg-opacity-20 p-3 rounded-full mr-4">
                        <i class="fas <?php echo e($material->getFileIconAttribute()); ?> <?php echo e($material->getFileColorAttribute()); ?> text-xl"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-medium uppercase tracking-wider <?php echo e($material->getFileColorAttribute()); ?> bg-<?php echo e(strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION))); ?>-100 bg-opacity-50 rounded-md px-2.5 py-0.5"><?php echo e($material->getFileTypeShort()); ?></span>
                            
                            <?php if($material->isNew()): ?>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-md">Baru</span>
                            <?php endif; ?>
                            
                            <?php if($material->is_active): ?>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-md">Aktif</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-md">Nonaktif</span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo e($material->title); ?>

                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-book mr-1"></i>
                                <span><?php echo e($material->subject->name ?? 'Tidak ada mata pelajaran'); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <span>Diterbitkan: <?php echo e($material->publish_date->format('d M Y, H:i')); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-clock mr-1"></i>
                                <span>Diperbarui: <?php echo e($material->updated_at->format('d M Y, H:i')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
                    <a href="<?php echo e(route('guru.materials.edit', $material)); ?>" 
                       class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-1.5"></i> Edit
                    </a>
                    
                    <a href="<?php echo e(route('guru.materials.download', $material)); ?>" 
                       class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-download mr-1.5"></i> Unduh
                    </a>
                    
                    <form action="<?php echo e(route('guru.materials.toggle-active', $material)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" 
                                class="<?php echo e($material->is_active ? 'bg-gray-600' : 'bg-green-600'); ?> inline-flex items-center px-3 py-2 text-white text-sm font-medium rounded-md shadow-sm hover:<?php echo e($material->is_active ? 'bg-gray-700' : 'bg-green-700'); ?> focus:outline-none focus:ring-2 focus:ring-offset-2 focus:<?php echo e($material->is_active ? 'ring-gray-500' : 'ring-green-500'); ?>">
                            <i class="fas <?php echo e($material->is_active ? 'fa-toggle-off mr-1.5' : 'fa-toggle-on mr-1.5'); ?>"></i> 
                            <?php echo e($material->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>

                        </button>
                    </form>
                    
                    <form action="<?php echo e(route('guru.materials.destroy', $material)); ?>" method="POST" class="inline delete-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash-alt mr-1.5"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Material Content -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left column - Description -->
            <div class="md:col-span-2">
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i> Deskripsi Materi
                    </h2>
                    <div class="text-gray-700 prose max-w-none">
                        <?php echo nl2br(e($material->description)); ?>

                    </div>
                </div>
            </div>
            
            <!-- Right column - File and Classroom info -->
            <div class="md:col-span-1">
                <!-- File preview card -->
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file mr-2 <?php echo e($material->getFileColorAttribute()); ?>"></i> File Materi
                    </h2>
                    
                    <div class="flex flex-col items-center justify-center p-5 border-2 border-dashed rounded-lg border-gray-300 bg-gray-50">
                        <div class="<?php echo e($material->getFileColorAttribute()); ?> bg-opacity-20 p-5 rounded-full mb-3">
                            <i class="fas <?php echo e($material->getFileIconAttribute()); ?> <?php echo e($material->getFileColorAttribute()); ?> text-3xl"></i>
                        </div>
                        <p class="text-sm text-gray-600 mb-1"><?php echo e(basename($material->file_path)); ?></p>
                        <p class="text-xs text-gray-500 mb-3"><?php echo e($material->getFileType()); ?></p>
                        <a href="<?php echo e(route('guru.materials.download', $material)); ?>" 
                           class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-download mr-1.5"></i> Unduh File
                        </a>
                    </div>
                </div>
                
                <!-- Classroom info card -->
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-indigo-600"></i> Kelas yang Menerima
                    </h2>
                    
                    <?php if($material->classrooms->isEmpty()): ?>
                        <div class="text-gray-500 text-center py-4">
                            Tidak ada kelas yang ditambahkan
                        </div>
                    <?php else: ?>
                        <ul class="space-y-2">
                            <?php $__currentLoopData = $material->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-center p-2 rounded-lg hover:bg-gray-50">
                                    <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($classroom->name); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($classroom->grade_level); ?> · <?php echo e($classroom->academic_year); ?></p>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Confirmation dialog for delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.querySelector('.delete-form');
        
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus materi ini? Tindakan ini tidak dapat dibatalkan.')) {
                    this.submit();
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/materials/show.blade.php ENDPATH**/ ?>