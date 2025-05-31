

<?php $__env->startSection('title', 'Kelola Kuis'); ?>

<?php $__env->startSection('header', 'Kelola Kuis'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-yellow-500 to-amber-600 animate-gradient-x rounded-xl shadow-xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform hover:scale-110 transition-transform duration-700">
            <i class="fas fa-question-circle text-9xl -mt-4 -mr-4"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-1/3 -top-12 w-36 h-36 bg-yellow-300/20 rounded-full blur-3xl"></div>
        <div class="relative animate-fade-in z-10">
            <h2 class="text-2xl font-bold mb-2">Kuis dan Evaluasi</h2>
            <p class="text-yellow-100">Buat dan kelola kuis untuk mengevaluasi pemahaman siswa secara interaktif.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="<?php echo e(route('guru.quizzes.create')); ?>" class="bg-white/15 backdrop-blur-sm hover:bg-white/25 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center shadow-md hover:shadow-lg hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> Buat Kuis Baru
                </a>
                <a href="#analytics" class="bg-yellow-700/80 text-white hover:bg-yellow-800 px-4 py-2 rounded-lg inline-flex items-center text-sm font-medium transition-all duration-300 shadow-md shadow-yellow-900/30 backdrop-blur-sm hover:shadow-xl hover:-translate-y-1">
                    <i class="fas fa-chart-bar mr-2"></i> Lihat Analitik
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Quizzes -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-indigo-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 shadow-inner">
                    <i class="fas fa-tasks text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Kuis</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($quizzes->count()); ?></p>
                    <div class="mt-2">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i> 
                            <?php echo e($activeQuizzes ?? 0); ?> Kuis Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Average Score -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-green-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 shadow-inner">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Rata-rata Nilai</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($averageScore ?? '-'); ?></p>
                    <div class="mt-2">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-user-graduate text-blue-500 mr-1"></i> 
                            Dari <?php echo e($totalParticipants ?? 0); ?> Siswa
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Completed Quizzes -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-blue-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 shadow-inner">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Selesai Dikerjakan</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($completedQuizzes ?? 0); ?></p>
                    <div class="mt-2">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-clock text-amber-500 mr-1"></i> 
                            <?php echo e($pendingQuizzes ?? 0); ?> Kuis Tertunda
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100/50">
        <form action="<?php echo e(route('guru.quizzes.index')); ?>" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari kuis..." 
                       class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
            </div>
            <div class="flex flex-wrap gap-3">
                <select name="subject" class="rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                    <option value="">Semua Mata Pelajaran</option>
                    <?php $__currentLoopData = $subjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subject->id); ?>" <?php echo e(request('subject') == $subject->id ? 'selected' : ''); ?>>
                            <?php echo e($subject->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="status" class="rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                    <option value="">Semua Status</option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Aktif</option>
                    <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Selesai</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Quizzes List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
        <?php $__empty_1 = true; $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100/60 overflow-hidden group">
                <div class="p-5 relative">
                    <div class="absolute top-0 right-0 pt-4 pr-4 flex space-x-1">
                        <?php if($quiz->is_active): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></div>
                                Aktif
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Draft
                            </span>
                        <?php endif; ?>
                        
                        <?php if($quiz->end_time && \Carbon\Carbon::parse($quiz->end_time)->isPast()): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Selesai
                            </span>
                        <?php elseif($quiz->start_time && \Carbon\Carbon::parse($quiz->start_time)->isFuture()): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Terjadwal
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600 mr-4">
                            <i class="fas fa-question-circle text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-yellow-600 transition-colors mb-1">
                                <?php echo e($quiz->title); ?>

                            </h3>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-book mr-1"></i> <?php echo e($quiz->subject->name ?? 'Tidak ada mata pelajaran'); ?>

                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-clock mr-1"></i> <?php echo e($quiz->duration ?? 0); ?> menit
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-question mr-1"></i> <?php echo e($quiz->questions_count ?? 0); ?> Soal
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-users mr-1"></i> <?php echo e($quiz->attempts_count ?? 0); ?> Mengerjakan
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                <?php echo e($quiz->description ?? 'Tidak ada deskripsi'); ?>

                            </p>
                            <div class="flex items-center text-xs text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span>Mulai: <?php echo e($quiz->start_time ? \Carbon\Carbon::parse($quiz->start_time)->format('d M Y, H:i') : 'Tidak diatur'); ?></span>
                                </div>
                                <div class="w-1 h-1 rounded-full bg-gray-300 mx-2"></div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    <span>Berakhir: <?php echo e($quiz->end_time ? \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') : 'Tidak diatur'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <a href="<?php echo e(route('guru.quizzes.show', $quiz->id)); ?>" class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline transition-colors">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>
                        <a href="<?php echo e(route('guru.quizzes.results', $quiz->id)); ?>" class="ml-3 text-sm text-green-600 hover:text-green-800 font-medium hover:underline transition-colors">
                            <i class="fas fa-chart-bar mr-1"></i> Hasil
                        </a>
                    </div>
                    <div class="flex space-x-2">
                        <a href="<?php echo e(route('guru.quizzes.edit', $quiz->id)); ?>" class="p-2 text-gray-500 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('guru.quizzes.destroy', $quiz->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kuis ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="p-2 text-gray-500 hover:text-red-600 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100/60">
                <div class="mx-auto w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-4 text-yellow-500">
                    <i class="fas fa-question-circle text-3xl"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-800 mb-2">Belum ada kuis</h3>
                <p class="text-gray-500 mb-6">Anda belum membuat kuis untuk siswa.</p>
                <a href="<?php echo e(route('guru.quizzes.create')); ?>" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i> Buat Kuis Baru
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if(isset($quizzes) && $quizzes->hasPages()): ?>
        <div class="px-4">
            <?php echo e($quizzes->links()); ?>

        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .animate-gradient-x {
        background-size: 400% 400%;
        animation: gradient-x 15s ease infinite;
    }
    
    @keyframes gradient-x {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .dashboard-card:hover .floating-element {
        animation: float 1s ease-in-out infinite;
    }
    
    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-5px);
        }
        100% {
            transform: translateY(0px);
        }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/quizzes/index.blade.php ENDPATH**/ ?>