

<?php $__env->startSection('title', 'Detail Pengguna'); ?>

<?php $__env->startSection('header', 'Detail Pengguna'); ?>

<?php $__env->startSection('navigation'); ?>
    <li>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-users text-lg w-6"></i>
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
        <a href="<?php echo e(route('admin.announcements.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-bullhorn text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengumuman</span>
        </a>
    </li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="bg-white rounded-xl shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Profil Pengguna</h1>
                <div class="flex space-x-2">
                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="flex flex-col md:flex-row">
                <!-- User Avatar and Basic Info -->
                <div class="w-full md:w-1/3 flex flex-col items-center mb-6 md:mb-0">
                    <div class="mb-4">                        <?php if($user->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>" class="w-48 h-48 rounded-full object-cover border-4 border-indigo-100">
                        <?php else: ?>
                            <div class="w-48 h-48 rounded-full bg-blue-500 flex items-center justify-center text-white text-5xl font-bold">
                                <?php echo e(substr($user->name, 0, 1)); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo e($user->name); ?></h2>
                    <span class="mt-2 px-4 py-1 text-sm rounded-full <?php echo e($user->role_id == 1 ? 'bg-red-100 text-red-800' : ($user->role_id == 2 ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800')); ?>">
                        <?php echo e($user->role->name ?? 'Unknown'); ?>

                    </span>
                </div>

                <!-- Detailed User Information -->
                <div class="w-full md:w-2/3 md:pl-6">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Akun</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Username</p>
                                <p class="font-medium"><?php echo e($user->username); ?></p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium"><?php echo e($user->email); ?></p>
                            </div>
                              <?php if($user->nip || $user->nisn): ?>
                            <div>
                                <p class="text-sm text-gray-500"><?php echo e($user->role_id == 2 ? 'NIP' : ($user->role_id == 3 ? 'NISN' : 'ID Pengguna')); ?></p>
                                <p class="font-medium"><?php echo e($user->id_number); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div>
                                <p class="text-sm text-gray-500">Bergabung Pada</p>
                                <p class="font-medium"><?php echo e($user->created_at->format('d F Y')); ?></p>
                            </div>
                        </div>
                        
                        <?php if($user->classroom_id): ?>
                        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-4 border-b pb-2">Informasi Kelas</h3>
                        <div>
                            <p class="text-sm text-gray-500">Kelas</p>
                            <p class="font-medium"><?php echo e($user->classroom->name ?? 'Tidak Ada'); ?></p>
                        </div>
                        <?php endif; ?>
                          <!-- Display classrooms for student -->
                        
                        
                        <!-- Display subjects for teacher -->
                        <?php if($user->role_id == 2 && count($user->subjects) > 0): ?>
                        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-4 border-b pb-2">Mata Pelajaran yang Diampu</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <?php $__currentLoopData = $user->subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm">
                                <?php echo e($subject->name); ?>

                            </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Log Section - Optional, if you're tracking user activity -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-medium mb-4">Riwayat Aktivitas</h2>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-gray-500">Fitur riwayat aktivitas belum tersedia</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Add any specific scripts for this page if needed
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/admin/users/show.blade.php ENDPATH**/ ?>