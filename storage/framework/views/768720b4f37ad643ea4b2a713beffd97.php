

<?php $__env->startSection('title', 'Kelola Soal Ujian'); ?>

<?php $__env->startSection('header', 'Kelola Soal Ujian'); ?>

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
            Kelola Soal: <?php echo e($exam->title); ?>

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

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="<?php echo e(route('guru.exams.questions.create', $exam->id)); ?>" 
               class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i> Tambah Soal Baru
            </a>
        </div>
        <div class="text-sm">
            Total Soal: <span class="font-medium"><?php echo e($exam->questions->count()); ?></span> | 
            Total Poin: <span class="font-medium"><?php echo e($exam->questions->sum('points')); ?></span>
        </div>
    </div>

    <?php if($exam->questions->count() > 0): ?>
        <div class="w-full overflow-hidden bg-white rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Soal</th>
                            <th class="px-4 py-3">Tipe Soal</th>
                            <th class="px-4 py-3">Poin</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm"><?php echo e($index + 1); ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-800 truncate max-w-xs">
                                                <?php echo \Illuminate\Support\Str::limit(strip_tags($question->content), 80); ?>

                                            </p>
                                            <?php if($question->question_type === 'multiple_choice'): ?>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <?php echo e($question->options->count()); ?> pilihan, <?php echo e($question->options->where('is_correct', true)->count()); ?> jawaban benar
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <?php if($question->question_type === 'multiple_choice'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">
                                            Pilihan Ganda
                                        </span>
                                    <?php elseif($question->question_type === 'true_false'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                            Benar/Salah
                                        </span>
                                    <?php elseif($question->question_type === 'essay'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-purple-700 bg-purple-100 rounded-full">
                                            Essay
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-medium"><?php echo e($question->points); ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?php echo e(route('guru.exams.questions.edit', ['exam' => $exam->id, 'question' => $question->id])); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                          <form action="<?php echo e(route('guru.questions.destroy', $question->id)); ?>" 
                                              method="POST" 
                                              class="inline-block" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center py-12 bg-white rounded-lg shadow-sm">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-question-circle text-gray-400 text-xl"></i>
            </div>
            <h3 class="mb-2 text-lg font-medium text-gray-900">Belum Ada Soal</h3>
            <p class="text-gray-500 text-center mb-4">Ujian ini belum memiliki soal. Tambahkan soal baru untuk memulai.</p>
            <a href="<?php echo e(route('guru.exams.questions.create', $exam->id)); ?>" 
               class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                Tambah Soal Pertama
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/questions/exam.blade.php ENDPATH**/ ?>