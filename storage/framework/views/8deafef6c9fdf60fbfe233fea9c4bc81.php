<?php
    use Illuminate\Support\Facades\Route;
?>

<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <?php if($announcements->count() > 0): ?>
        <div class="divide-y divide-gray-100">
            <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-6 hover:bg-gray-50 transition-colors <?php echo e($announcement->is_important ? 'bg-red-50' : ''); ?>">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-xl <?php echo e($announcement->is_important ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'); ?> flex items-center justify-center">
                                <i class="fas <?php echo e($announcement->is_important ? 'fa-exclamation-circle' : 'fa-bullhorn'); ?> text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <?php echo e($announcement->title); ?>

                                    <?php if($announcement->is_important): ?>
                                        <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded-full">Penting</span>
                                    <?php endif; ?>
                                    <?php if($announcement->isNew()): ?>
                                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">Baru</span>
                                    <?php endif; ?>
                                </h3>
                                <span class="text-sm text-gray-500"><?php echo e($announcement->publish_date->format('d M Y')); ?></span>
                            </div>
                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                <span class="mr-2">Oleh: <?php echo e($announcement->author->name); ?></span>
                                <span class="mr-2">•</span>
                                <span>Untuk: 
                                    <?php if($announcement->audience == 'all'): ?>
                                        Semua
                                    <?php elseif($announcement->audience == 'administrators'): ?>
                                        Admin
                                    <?php elseif($announcement->audience == 'teachers'): ?>
                                        Guru
                                    <?php else: ?>
                                        Siswa
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="mt-3">
                                <p class="text-gray-600 line-clamp-2"><?php echo e($announcement->excerpt()); ?></p>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <a href="<?php echo e(route($routePrefix . '.announcements.show', $announcement)); ?>" class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors flex items-center">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                                
                                <?php if($announcement->hasAttachment()): ?>
                                    <a href="<?php echo e(route($routePrefix . '.announcements.download', $announcement)); ?>" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors flex items-center">
                                        <i class="fas fa-download mr-1"></i> Lampiran
                                    </a>
                                <?php endif; ?>
                                
                                <?php if(isset($canEdit) && $canEdit && (auth()->user()->isAdmin() || auth()->id() === $announcement->author_id)): ?>
                                    <a href="<?php echo e(route($routePrefix . '.announcements.edit', $announcement)); ?>" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 transition-colors flex items-center">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    
                                    <form action="<?php echo e(route($routePrefix . '.announcements.destroy', $announcement)); ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors flex items-center">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
            <?php echo e($announcements->withQueryString()->links()); ?>

        </div>
    <?php else: ?>
        <div class="p-8 text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 text-gray-500 mb-4">
                <i class="fas fa-bullhorn text-2xl"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-800 mb-1">Tidak ada pengumuman ditemukan</h4>
            <p class="text-gray-500 mb-4">Belum ada pengumuman yang dibuat atau sesuai dengan filter yang dipilih.</p>
            <?php if(isset($canCreate) && $canCreate): ?>
                <a href="<?php echo e(route($routePrefix . '.announcements.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Buat Pengumuman Baru
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\NEW\PROYEK-AKHIR-II-WEB\resources\views/components/announcements-list.blade.php ENDPATH**/ ?>