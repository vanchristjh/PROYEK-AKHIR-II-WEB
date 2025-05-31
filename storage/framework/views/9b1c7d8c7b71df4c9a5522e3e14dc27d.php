

<?php $__env->startSection('title', 'Hasil Ujian'); ?>

<?php $__env->startSection('header', 'Hasil Ujian'); ?>

<?php $__env->startSection('navigation'); ?>
    <?php echo $__env->make('guru.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="<?php echo e(route('guru.exams.show', $exam->id)); ?>" class="text-blue-600 hover:text-blue-900 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Ujian
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Hasil Ujian: <?php echo e($exam->title); ?>

        </h2>
    </div>

    <!-- Alert Success -->
    <?php if(session('success')): ?>
    <div class="mb-4 px-4 py-3 leading-normal text-green-700 bg-green-100 rounded-lg" role="alert">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Alert Error -->
    <?php if(session('error')): ?>
    <div class="mb-4 px-4 py-3 leading-normal text-red-700 bg-red-100 rounded-lg" role="alert">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <div class="grid gap-6 mb-8">
        <!-- Statistik Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <i class="fas fa-users text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Peserta</p>
                        <p class="text-lg font-semibold text-gray-700"><?php echo e($attempts->total()); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Nilai Tertinggi</p>
                        <p class="text-lg font-semibold text-gray-700">
                            <?php if($attempts->total() > 0): ?>
                                <?php echo e(number_format($attempts->max('score'), 1)); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 mr-4">
                        <i class="fas fa-times-circle text-red-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Nilai Terendah</p>
                        <p class="text-lg font-semibold text-gray-700">
                            <?php if($attempts->total() > 0): ?>
                                <?php echo e(number_format($attempts->min('score'), 1)); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <i class="fas fa-chart-line text-purple-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rata-rata Nilai</p>
                        <p class="text-lg font-semibold text-gray-700">
                            <?php if($attempts->total() > 0): ?>
                                <?php echo e(number_format($attempts->avg('score'), 1)); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Hasil Ujian Siswa -->
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Waktu Mulai</th>
                            <th class="px-4 py-3">Waktu Selesai</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3">Nilai</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">
                                <?php echo e($attempt->student->name); ?>

                            </td>
                            <td class="px-4 py-3">
                                <?php echo e($attempt->student->classroom ? $attempt->student->classroom->name : '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php echo e($attempt->start_time->format('d M Y, H:i:s')); ?>

                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php echo e($attempt->end_time ? $attempt->end_time->format('d M Y, H:i:s') : 'Belum selesai'); ?>

                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php if($attempt->end_time): ?>
                                    <?php
                                        $duration = $attempt->start_time->diffInMinutes($attempt->end_time);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    ?>
                                    <?php echo e($hours > 0 ? $hours . ' jam ' : ''); ?><?php echo e($minutes); ?> menit
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php if($attempt->is_submitted): ?>
                                    <?php echo e(number_format($attempt->score, 1)); ?>

                                <?php else: ?>
                                    <span class="text-gray-500">Belum dikumpulkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php if($attempt->is_submitted): ?>
                                    <?php if($attempt->score >= $exam->passing_score): ?>
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Lulus</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Tidak Lulus</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">Belum Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="<?php echo e(route('guru.exams.view-attempt', ['exam' => $exam->id, 'attempt' => $attempt->id])); ?>" 
                                   class="px-2 py-1 text-xs font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-center">
                                <p class="text-gray-500">Belum ada siswa yang mengerjakan ujian ini.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 border-t">
                <?php echo e($attempts->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/exams/results.blade.php ENDPATH**/ ?>