

<?php $__env->startSection('title', 'Daftar Ujian'); ?>

<?php $__env->startSection('header', 'Ujian'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Welcome Banner with enhanced animated gradient and floating particles -->
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600 animate-gradient-x rounded-xl shadow-xl p-6 mb-8 text-white relative overflow-hidden">
        <div class="particles-container absolute inset-0 pointer-events-none"></div>
        <div class="absolute right-0 top-0 opacity-10 transform hover:scale-110 transition-transform duration-700">
            <i class="fas fa-file-alt text-9xl -mt-4 -mr-4"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-1/3 -top-12 w-36 h-36 bg-indigo-300/20 rounded-full blur-3xl"></div>
        <div class="relative animate-fade-in z-10">
            <h2 class="text-2xl font-bold mb-2">Ujian Siswa</h2>
            <p class="text-indigo-100">
                Daftar ujian yang tersedia untuk Anda
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="<?php echo e(route('siswa.dashboard')); ?>" class="btn-glass flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards with enhanced visuals -->
    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
        <div class="p-2 bg-indigo-100 rounded-lg mr-3">
            <i class="fas fa-clipboard-list text-indigo-600"></i>
        </div>
        <span>Ringkasan Akademik</span>
        <div class="ml-auto text-sm text-gray-500 flex items-center bg-white py-1 px-3 rounded-lg shadow-sm">
            <i class="fas fa-sync-alt mr-1 hover:rotate-180 transition-transform cursor-pointer" id="refresh-data-btn" title="Refresh data"></i>
            <span>Terakhir diperbarui: <?php echo e(now()->format('H:i')); ?></span>
        </div>
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Ujian Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-indigo-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 shadow-inner">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Ujian</h3>
                    <p class="card-number floating-element"><?php echo e($exams->count() ?? 0); ?></p>
                    <div class="mt-2">
                        <span class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center group">
                            <span>Semua ujian</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ujian Aktif Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-blue-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 shadow-inner">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Ujian Aktif</h3>
                    <p class="card-number floating-element"><?php echo e($exams->where('status', 'active')->where('start_date', '<=', now())->where('end_date', '>=', now())->count() ?? 0); ?></p>
                    <div class="mt-2">
                        <span class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center group">
                            <span>Tersedia sekarang</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ujian Selesai Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-green-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 shadow-inner">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Ujian Selesai</h3>
                    <p class="card-number floating-element"><?php echo e($exams->where('end_date', '<', now())->count() ?? 0); ?></p>
                    <div class="mt-2">
                        <span class="text-sm text-green-600 hover:text-green-800 inline-flex items-center group">
                            <span>Sudah berakhir</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ujian Mendatang Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-orange-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-orange-100 text-orange-600 shadow-inner">
                    <i class="fas fa-hourglass-start text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Ujian Mendatang</h3>
                    <p class="card-number floating-element"><?php echo e($exams->where('start_date', '>', now())->count() ?? 0); ?></p>
                    <div class="mt-2">                        
                        <span class="text-sm text-orange-600 hover:text-orange-800 inline-flex items-center group">
                            <span>Belum dimulai</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Exam List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/60 transform transition hover:shadow-lg mb-8">
        <div class="card-header flex items-center justify-between p-6 border-b border-gray-100">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                    <i class="fas fa-list-alt text-indigo-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Semua Ujian</h3>
            </div>
        </div>
        <div class="p-6">
            <?php if($exams->isEmpty()): ?>
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600 mb-4">
                        <i class="fas fa-clipboard-check text-2xl"></i>
                    </div>
                    <h4 class="text-base font-medium text-gray-800 mb-1">Belum ada ujian</h4>
                    <p class="text-gray-500 mb-0">Saat ini belum ada ujian yang tersedia</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-200 
                            <?php if($exam->end_date < now()): ?>
                                bg-gray-50 border-gray-200
                            <?php elseif($exam->start_date <= now() && $exam->end_date >= now()): ?>
                                bg-blue-50 border-blue-200
                            <?php else: ?>
                                bg-orange-50 border-orange-200
                            <?php endif; ?>
                            rounded-lg p-4 transition-all hover:shadow-md hover:-translate-y-1 duration-300" style="--animation-order: <?php echo e($index); ?>">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-xl 
                                        <?php if($exam->end_date < now()): ?>
                                            bg-gray-100 text-gray-600
                                        <?php elseif($exam->start_date <= now() && $exam->end_date >= now()): ?>
                                            bg-blue-100 text-blue-600
                                        <?php else: ?>
                                            bg-orange-100 text-orange-600
                                        <?php endif; ?>">
                                        <i class="fas fa-<?php echo e($exam->end_date < now() ? 'check-double' : ($exam->start_date <= now() && $exam->end_date >= now() ? 'file-alt' : 'hourglass-start')); ?> text-xl"></i>
                                    </span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex flex-wrap justify-between mb-1">
                                        <h4 class="text-base font-medium text-gray-800 mb-1"><?php echo e($exam->title); ?></h4>
                                        <span class="text-sm 
                                            <?php if($exam->end_date < now()): ?>
                                                text-gray-600
                                            <?php elseif($exam->start_date <= now() && $exam->end_date >= now()): ?>
                                                text-blue-600 font-medium
                                            <?php else: ?>
                                                text-orange-600
                                            <?php endif; ?>">
                                            <?php if($exam->end_date < now()): ?>
                                                Ujian telah berakhir
                                            <?php elseif($exam->start_date <= now() && $exam->end_date >= now()): ?>
                                                Ujian aktif
                                            <?php else: ?>
                                                Mulai: <?php echo e($exam->start_date->format('d M Y, H:i')); ?>

                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <i class="fas fa-book-open mr-2 text-gray-400"></i>
                                        <span>
                                            <?php if($exam->subject): ?>
                                                <?php echo e($exam->subject->name); ?>

                                            <?php else: ?>
                                                Mata pelajaran tidak tersedia
                                            <?php endif; ?>
                                        </span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                                        <span>
                                            <?php if($exam->teacher): ?>
                                                <?php echo e($exam->teacher->name); ?>

                                            <?php else: ?>
                                                Guru tidak tersedia
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                                        <span>Durasi: <?php echo e($exam->duration); ?> menit</span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <i class="fas fa-question-circle mr-2 text-gray-400"></i>
                                        <span><?php echo e($exam->total_questions ?? 'N/A'); ?> Pertanyaan</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <a href="<?php echo e(route('siswa.exams.show', $exam->id)); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 inline-flex items-center group">
                                            <span>Detail ujian</span>
                                            <i class="fas fa-chevron-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                                        </a>
                                        <span class="
                                            <?php if($exam->end_date < now()): ?>
                                                bg-gray-100 text-gray-700
                                            <?php elseif($exam->start_date <= now() && $exam->end_date >= now()): ?>
                                                bg-blue-100 text-blue-700
                                            <?php else: ?>
                                                bg-orange-100 text-orange-700
                                            <?php endif; ?>
                                            text-xs px-2.5 py-1 rounded-full">
                                            <?php if($exam->end_date < now()): ?>
                                                Selesai
                                            <?php elseif($exam->start_date <= now() && $exam->end_date >= now()): ?>
                                                Tersedia sekarang
                                            <?php else: ?>
                                                Mendatang
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation for cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animate-fade-in');
            }, index * 100);
        });

        // Animation for exam list items
        const examItems = document.querySelectorAll('[style*="--animation-order"]');
        examItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('animate-fade-in');
            }, 400 + (index * 100)); // Start after cards have begun animating
        });

        // Refresh button action
        document.getElementById('refresh-data-btn')?.addEventListener('click', function() {
            this.classList.add('animate-spin');
            setTimeout(() => {
                this.classList.remove('animate-spin');
                // Here you could implement an AJAX call to refresh data
                window.location.reload();
            }, 1000);
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes gradient-x {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Utils */
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
        opacity: 0;
    }
    
    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 15s ease infinite;
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Card number styling */
    .card-number {
        font-size: 28px;
        font-weight: 600;
        color: #4B5563;
        line-height: 1.2;
        margin: 4px 0;
        animation: float 3s ease-in-out infinite;
    }
    
    /* Button styling */
    .btn-glass {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-glass:hover {
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    /* Dashboard cards initial state */
    .dashboard-card {
        opacity: 0;
    }

    /* Exam items animation */
    [style*="--animation-order"] {
        opacity: 0;
    }
</style>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation for cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animate-fade-in');
            }, index * 100);
        });

        // Refresh button action
        document.getElementById('refresh-data-btn')?.addEventListener('click', function() {
            this.classList.add('animate-spin');
            setTimeout(() => {
                this.classList.remove('animate-spin');
                // Here you could implement an AJAX call to refresh data
            }, 1000);
        });
    });
</script>

<style>
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes gradient-x {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Utils */
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 15s ease infinite;
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Card number styling */
    .card-number {
        font-size: 28px;
        font-weight: 600;
        color: #4B5563;
        line-height: 1.2;
        margin: 4px 0;
        animation: float 3s ease-in-out infinite;
    }
    
    /* Button styling */
    .btn-glass {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-glass:hover {
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }
</style>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation for cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animate-fade-in');
            }, index * 100);
        });

        // Refresh button action
        document.getElementById('refresh-data-btn')?.addEventListener('click', function() {
            this.classList.add('animate-spin');
            setTimeout(() => {
                this.classList.remove('animate-spin');
                // Here you could implement an AJAX call to refresh data
            }, 1000);
        });
    });
</script>

<style>
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes gradient-x {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Utils */
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 15s ease infinite;
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Card number styling */
    .card-number {
        font-size: 28px;
        font-weight: 600;
        color: #4B5563;
        line-height: 1.2;
        margin: 4px 0;
        animation: float 3s ease-in-out infinite;
    }
    
    /* Button styling */
    .btn-glass {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-glass:hover {
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/siswa/exams/index.blade.php ENDPATH**/ ?>