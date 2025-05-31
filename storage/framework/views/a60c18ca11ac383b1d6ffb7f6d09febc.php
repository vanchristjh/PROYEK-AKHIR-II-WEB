

<?php $__env->startSection('title', 'Edit Kuis'); ?>

<?php $__env->startSection('header', 'Edit Kuis'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-yellow-500 to-amber-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-edit text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Edit Kuis</h2>
            <p class="text-yellow-100">Perbarui pengaturan kuis untuk siswa.</p>
        </div>
    </div>

    <div class="mb-6">
        <a href="<?php echo e(route('guru.quizzes.show', $quiz->id)); ?>" class="inline-flex items-center text-yellow-600 hover:text-yellow-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Detail Kuis</span>
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="<?php echo e(route('guru.quizzes.update', $quiz->id)); ?>" method="POST" class="animate-fade-in" id="quizForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Kuis <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title', $quiz->title)); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
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
                    
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kuis</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="description" id="description" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300"><?php echo e(old('description', $quiz->description)); ?></textarea>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Berikan instruksi atau informasi tambahan untuk siswa mengenai kuis ini.</p>
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
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            <select name="subject_id" id="subject_id" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php $__currentLoopData = $subjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subject->id); ?>" <?php echo e((old('subject_id', $quiz->subject_id) == $subject->id) ? 'selected' : ''); ?>>
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
                    
                    <div class="form-group mb-5">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas (Pilih satu atau lebih) <span class="text-red-500">*</span></label>
                        <div class="mt-1 bg-white rounded-lg border border-gray-300 px-3 py-2 focus-within:ring focus-within:ring-yellow-200 focus-within:ring-opacity-50 focus-within:border-yellow-500">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                <?php $__currentLoopData = $classrooms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="classroom_<?php echo e($classroom->id); ?>" name="classroom_id[]" value="<?php echo e($classroom->id); ?>"
                                            class="rounded border-gray-300 text-yellow-600 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50"
                                            <?php echo e(in_array($classroom->id, old('classroom_id', $selectedClassrooms ?? [])) ? 'checked' : ''); ?>>
                                        <label for="classroom_<?php echo e($classroom->id); ?>" class="ml-2 text-sm text-gray-700"><?php echo e($classroom->name); ?></label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button type="button" id="select-all-classrooms" 
                                    class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                                    Pilih Semua
                                </button>
                            </div>
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
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Mulai <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="start_time" id="start_time" 
                                value="<?php echo e(old('start_time', $quiz->start_time ? date('Y-m-d\TH:i', strtotime($quiz->start_time)) : '')); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <?php $__errorArgs = ['start_time'];
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
                    
                    <div class="form-group mb-5">
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Berakhir <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="end_time" id="end_time" 
                                value="<?php echo e(old('end_time', $quiz->end_time ? date('Y-m-d\TH:i', strtotime($quiz->end_time)) : '')); ?>" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <?php $__errorArgs = ['end_time'];
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
                    
                    <div class="form-group mb-5">
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit) <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <input type="number" name="duration" id="duration" value="<?php echo e(old('duration', $quiz->duration)); ?>" min="1" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Waktu maksimal siswa mengerjakan kuis dalam menit.</p>
                        <?php $__errorArgs = ['duration'];
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
                    
                    <div class="form-group mb-5">
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1">Percobaan Maksimum</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-redo text-gray-400"></i>
                            </div>
                            <input type="number" name="max_attempts" id="max_attempts" value="<?php echo e(old('max_attempts', $quiz->max_attempts)); ?>" min="1" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Jumlah maksimal percobaan siswa mengerjakan kuis ini.</p>
                        <?php $__errorArgs = ['max_attempts'];
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
                    
                    <div class="form-group mb-5">
                        <label for="passing_grade" class="block text-sm font-medium text-gray-700 mb-1">Nilai Kelulusan</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-percentage text-gray-400"></i>
                            </div>
                            <input type="number" name="passing_grade" id="passing_grade" value="<?php echo e(old('passing_grade', $quiz->passing_grade)); ?>" min="0" max="100" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Nilai minimum untuk lulus kuis ini (0-100).</p>
                        <?php $__errorArgs = ['passing_grade'];
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
                    
                    <div class="form-group mb-5 md:col-span-2">
                        <div class="flex items-center space-x-2 mb-2">
                            <label for="show_answers" class="text-sm font-medium text-gray-700">Pengaturan Tambahan</label>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                Opsional
                            </span>
                        </div>
                        <div class="mt-1 bg-white rounded-lg border border-gray-300 px-4 py-3 space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_random" name="is_random" type="checkbox" value="1" <?php echo e(old('is_random', $quiz->is_random) ? 'checked' : ''); ?>

                                        class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_random" class="text-sm font-medium text-gray-700">Acak Urutan Soal</label>
                                    <p class="text-xs text-gray-500">Mengacak urutan soal untuk setiap siswa.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="show_answers" name="show_answers" type="checkbox" value="1" <?php echo e(old('show_answers', $quiz->show_answers) ? 'checked' : ''); ?>

                                        class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                </div>
                                <div class="ml-3">
                                    <label for="show_answers" class="text-sm font-medium text-gray-700">Tampilkan Jawaban Benar</label>
                                    <p class="text-xs text-gray-500">Siswa dapat melihat jawaban benar setelah mengerjakan kuis.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" <?php echo e(old('is_active', $quiz->is_active) ? 'checked' : ''); ?>

                                        class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktifkan Kuis</label>
                                    <p class="text-xs text-gray-500">Siswa dapat melihat dan mengakses kuis ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end">
                        <a href="<?php echo e(route('guru.quizzes.show', $quiz->id)); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-yellow-500 to-amber-600 text-white rounded-lg hover:from-yellow-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Perbarui Kuis
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .animate-fade-in {
        animation: fade-in 0.6s ease-in-out;
    }
    
    @keyframes fade-in {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-group:focus-within label {
        color: #F59E0B;
    }
    
    .form-group:focus-within i {
        color: #F59E0B;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkboxes functionality
        const selectAllBtn = document.getElementById('select-all-classrooms');
        const classroomCheckboxes = document.querySelectorAll('input[name="classroom_id[]"]');
        
        if (selectAllBtn && classroomCheckboxes.length > 0) {
            selectAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isAllSelected = Array.from(classroomCheckboxes).every(cb => cb.checked);
                
                classroomCheckboxes.forEach(checkbox => {
                    checkbox.checked = !isAllSelected;
                });
                
                this.textContent = isAllSelected ? 'Pilih Semua' : 'Batal Pilih';
            });
        }
        
        // Date validation
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');
        
        if (startTime && endTime) {
            endTime.addEventListener('change', function() {
                if (startTime.value && this.value) {
                    const startDate = new Date(startTime.value);
                    const endDate = new Date(this.value);
                    
                    if (endDate <= startDate) {
                        alert('Waktu berakhir harus setelah waktu mulai');
                        this.value = '';
                    }
                }
            });
            
            startTime.addEventListener('change', function() {
                if (endTime.value && this.value) {
                    const startDate = new Date(this.value);
                    const endDate = new Date(endTime.value);
                    
                    if (endDate <= startDate) {
                        alert('Waktu berakhir harus setelah waktu mulai');
                        endTime.value = '';
                    }
                }
            });
        }
        
        // Form validation
        const form = document.getElementById('quizForm');
        if (form) {
            form.addEventListener('submit', function(event) {
                // Basic validation
                const title = document.getElementById('title');
                const subjectId = document.getElementById('subject_id');
                const classroomCheckboxes = document.querySelectorAll('input[name="classroom_id[]"]:checked');
                const startTime = document.getElementById('start_time');
                const endTime = document.getElementById('end_time');
                const duration = document.getElementById('duration');
                
                let hasError = false;
                let errorMessage = '';
                
                if (!title.value.trim()) {
                    errorMessage += 'Judul kuis tidak boleh kosong\n';
                    hasError = true;
                }
                
                if (!subjectId.value) {
                    errorMessage += 'Pilih mata pelajaran\n';
                    hasError = true;
                }
                
                if (classroomCheckboxes.length === 0) {
                    errorMessage += 'Pilih minimal satu kelas\n';
                    hasError = true;
                }
                
                if (!startTime.value) {
                    errorMessage += 'Waktu mulai tidak boleh kosong\n';
                    hasError = true;
                }
                
                if (!endTime.value) {
                    errorMessage += 'Waktu berakhir tidak boleh kosong\n';
                    hasError = true;
                }
                
                if (parseInt(duration.value) <= 0) {
                    errorMessage += 'Durasi harus lebih dari 0 menit\n';
                    hasError = true;
                }
                
                if (hasError) {
                    event.preventDefault();
                    alert('Terdapat kesalahan pada formulir:\n\n' + errorMessage);
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/guru/quizzes/edit.blade.php ENDPATH**/ ?>