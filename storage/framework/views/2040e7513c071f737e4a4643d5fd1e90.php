

<?php $__env->startSection('content'); ?>
<div class="min-h-screen p-4 sm:p-6 bg-gray-50">
    <!-- Profile Header -->
    <div class="mb-8 overflow-hidden bg-white shadow-lg rounded-2xl">
        <div class="relative p-8 overflow-hidden bg-gradient-to-r from-purple-600 to-indigo-600">
            <div class="absolute inset-0 bg-black opacity-10 pattern-dots"></div>
            <div class="relative z-10 grid items-center grid-cols-1 gap-8 md:grid-cols-12">
                <!-- Avatar & Basic Info -->
                <div class="flex flex-col items-center md:col-span-3 md:items-start">
                    <div class="relative w-36 h-36">
                        <?php if($user->avatar): ?>
                            <img src="<?php echo e(Storage::url($user->avatar)); ?>" alt="<?php echo e($user->name); ?>" 
                                class="object-cover w-full h-full transition-transform duration-300 border-4 border-white rounded-full shadow-xl hover:scale-105">
                        <?php else: ?>
                            <div class="flex items-center justify-center w-full h-full transition-transform duration-300 border-4 border-white rounded-full shadow-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:scale-105">
                                <span class="text-5xl font-bold text-white"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></span>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo e(route('guru.profile.edit')); ?>" 
                           class="absolute bottom-0 right-0 p-3 transition duration-200 transform bg-white rounded-full shadow-lg hover:bg-gray-50 hover:scale-110">
                            <i class="text-gray-600 fas fa-camera"></i>
                        </a>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="text-center md:col-span-9 md:text-left">
                    <h1 class="mb-3 text-3xl font-bold text-white md:text-4xl"><?php echo e($user->name); ?></h1>
                    <div class="flex flex-wrap justify-center gap-6 md:justify-start text-white/90">
                        <?php if($user->teacher && $user->teacher->nip): ?>
                            <div class="flex items-center px-4 py-2 space-x-2 rounded-full backdrop-blur-sm bg-white/10">
                                <i class="fas fa-id-card"></i>
                                <span>NIP: <?php echo e($user->teacher->nip); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex items-center px-4 py-2 space-x-2 rounded-full backdrop-blur-sm bg-white/10">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo e($user->email); ?></span>
                        </div>
                        <?php if($user->teacher && $user->teacher->phone): ?>
                            <div class="flex items-center px-4 py-2 space-x-2 rounded-full backdrop-blur-sm bg-white/10">
                                <i class="fas fa-phone"></i>
                                <span><?php echo e($user->teacher->phone); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="p-4 bg-white border-t border-gray-100">
            <div class="max-w-4xl mx-auto">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium <?php echo e(isset($user->teacher) && isset($user->teacher->profile_completed) && $user->teacher->profile_completed ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'); ?> transition-all duration-200 hover:shadow-md">
                            <?php if(isset($user->teacher) && isset($user->teacher->profile_completed) && $user->teacher->profile_completed): ?>
                                <i class="mr-2 fas fa-check-circle"></i> Profil Lengkap
                            <?php else: ?>
                                <i class="mr-2 fas fa-exclamation-circle"></i> Perlu Dilengkapi
                            <?php endif; ?>
                        </span>
                        <?php if(!isset($user->teacher) || !($user->teacher->profile_completed ?? false)): ?>
                            <a href="<?php echo e(route('guru.profile.edit')); ?>" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition duration-200 bg-purple-600 rounded-full hover:bg-purple-700 hover:shadow-lg transform hover:-translate-y-0.5">
                                <i class="mr-2 fas fa-edit"></i> Lengkapi Profil
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center">
                        <a href="<?php echo e(route('guru.profile.edit')); ?>" 
                           class="flex items-center px-4 py-2 text-gray-600 transition duration-200 rounded-full hover:bg-purple-50 hover:text-purple-600">
                            <i class="mr-2 fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Left Column - Profile Info -->
        <div class="space-y-8 lg:col-span-4">
            <!-- About Section -->
            <div class="p-6 transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl">
                <h3 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <i class="mr-3 text-2xl text-purple-600 fas fa-user-circle"></i>
                    Tentang Saya
                </h3>
                <div class="space-y-6">
                    <?php if($user->teacher && $user->teacher->address): ?>
                        <div class="flex items-start group">
                            <i class="w-5 mt-1 text-gray-400 transition-colors duration-200 fas fa-map-marker-alt group-hover:text-purple-600"></i>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-900">Alamat</h4>
                                <p class="mt-1 text-gray-600"><?php echo e($user->teacher->address); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($user->teacher && $user->teacher->bio): ?>
                        <div class="flex items-start group">
                            <i class="w-5 mt-1 text-gray-400 transition-colors duration-200 fas fa-info-circle group-hover:text-purple-600"></i>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-900">Bio</h4>
                                <p class="mt-1 text-gray-600"><?php echo e($user->teacher->bio); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="pt-6 mt-6 border-t border-gray-100">
                    <a href="<?php echo e(route('guru.profile.edit')); ?>" 
                       class="inline-flex items-center px-4 py-2 text-sm text-purple-600 transition duration-200 rounded-lg hover:bg-purple-50">
                        <i class="mr-2 fas fa-pen"></i>
                        Edit Informasi
                    </a>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="p-6 transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl">
                <h3 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <i class="mr-3 text-2xl text-purple-600 fas fa-address-book"></i>
                    Informasi Kontak
                </h3>
                <div class="space-y-6">
                    <div class="flex items-center group">
                        <i class="w-5 text-gray-400 transition-colors duration-200 fas fa-envelope group-hover:text-purple-600"></i>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Email</h4>
                            <p class="mt-1 text-gray-600"><?php echo e($user->email); ?></p>
                        </div>
                    </div>
                    <?php if($user->teacher && $user->teacher->phone): ?>
                        <div class="flex items-center group">
                            <i class="w-5 text-gray-400 transition-colors duration-200 fas fa-phone group-hover:text-purple-600"></i>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-900">Telepon</h4>
                                <p class="mt-1 text-gray-600"><?php echo e($user->teacher->phone); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Schedule & Activities -->
        <div class="space-y-8 lg:col-span-8">
            <!-- Today's Schedule -->
            <div class="transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="flex items-center text-xl font-semibold text-gray-900">
                        <i class="mr-3 text-2xl text-purple-600 fas fa-calendar-day"></i>
                        Jadwal Hari Ini
                    </h3>
                </div>
                <div class="p-6">
                    <?php if($todaySchedules->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $todaySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-start p-5 transition-all duration-200 rounded-xl bg-gray-50 hover:bg-purple-50 hover:shadow-md group">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center transition-colors duration-200 bg-purple-100 w-14 h-14 rounded-xl group-hover:bg-purple-200">
                                            <i class="text-2xl text-purple-600 fas fa-book"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 ml-5">
                                        <h4 class="text-lg font-medium text-gray-900"><?php echo e($schedule->subject->name); ?></h4>
                                        <div class="flex flex-wrap gap-6 mt-2 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="mr-2 text-purple-500 fas fa-clock"></i>
                                                <?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('H:i')); ?>

                                            </div>
                                            <div class="flex items-center">
                                                <i class="mr-2 text-purple-500 fas fa-users"></i>
                                                Kelas <?php echo e($schedule->classroom->name); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="py-12 text-center text-gray-500">
                            <i class="mb-4 text-5xl text-gray-400 fas fa-calendar-xmark"></i>
                            <p class="text-lg">Tidak ada jadwal mengajar hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/profile/show_new.blade.php ENDPATH**/ ?>