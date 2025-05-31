

<?php $__env->startSection('title', 'Edit Jadwal'); ?>

<?php $__env->startSection('header', 'Edit Jadwal Pelajaran'); ?>

<?php $__env->startSection('navigation'); ?>
    <?php echo $__env->make('admin.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <a href="<?php echo e(route('admin.schedule.index')); ?>" class="text-blue-600 hover:text-blue-800 flex items-center text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i> Kembali ke Daftar Jadwal
        </a>
    </div>
    
    <!-- Flash Messages -->
    <?php if(session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                <p class="font-medium"><?php echo e(session('error')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Edit Schedule Form -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit mr-2 text-blue-500"></i>
                Form Edit Jadwal
            </h3>
        </div>
        
        <form action="<?php echo e(route('admin.schedule.update', $schedule)); ?>" method="POST" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Classroom -->
                    <div class="form-group">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-school absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="classroom_id" name="classroom_id" class="form-select pl-10 pr-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['classroom_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Kelas --</option>
                                <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($classroom->id); ?>" <?php echo e(old('classroom_id', $schedule->classroom_id) == $classroom->id ? 'selected' : ''); ?>>
                                        <?php echo e($classroom->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <?php $__errorArgs = ['classroom_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <!-- Visibility Notification -->
                        <div id="classroom-visibility" class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded-lg border border-blue-100">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                <span>Jadwal ini akan terlihat oleh <span id="student-count" class="font-medium">0</span> siswa di kelas ini.</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Subject -->
                    <div class="form-group">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="subject_id" name="subject_id" class="form-select pl-10 pr-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subject->id); ?>" <?php echo e(old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : ''); ?>>
                                        <?php echo e($subject->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <?php $__errorArgs = ['subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Teacher -->
                    <div class="form-group">
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Guru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-user-tie absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="teacher_id" name="teacher_id" class="form-select pl-10 pr-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['teacher_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Guru --</option>
                                <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($teacher->id); ?>" <?php echo e(old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : ''); ?>>
                                        <?php echo e($teacher->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <p id="teacher-hint" class="mt-1 text-xs text-gray-500 italic">Guru yang mengajar mata pelajaran ini akan ditampilkan setelah memilih mata pelajaran.</p>
                        <?php $__errorArgs = ['teacher_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <!-- Teacher Visibility Notification -->
                        <div id="teacher-visibility" class="mt-2 text-xs text-gray-500 bg-green-50 p-2 rounded-lg border border-green-100">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-green-500 mr-1"></i>
                                <span>Jadwal ini akan muncul di dashboard guru yang dipilih.</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Day -->
                    <div class="form-group">
                        <label for="day" class="block text-sm font-medium text-gray-700 mb-1">
                            Hari <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-calendar-day absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="day" name="day" class="form-select pl-10 pr-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Hari --</option>
                                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($day); ?>" <?php echo e(old('day', $schedule->day) == $day ? 'selected' : ''); ?>>
                                        <?php echo e($day); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <?php $__errorArgs = ['day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Time Slots -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Start Time -->
                        <div class="form-group">
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">
                                Waktu Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="far fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="time" id="start_time" name="start_time" value="<?php echo e(old('start_time', $schedule->start_time)); ?>" 
                                    class="form-input pl-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            </div>
                            <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <!-- End Time -->
                        <div class="form-group">
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                                Waktu Selesai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="far fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="time" id="end_time" name="end_time" value="<?php echo e(old('end_time', $schedule->end_time)); ?>" 
                                    class="form-input pl-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            </div>
                            <div id="time-error" class="hidden mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> Waktu selesai harus setelah waktu mulai
                            </div>
                            <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <!-- School Year -->
                    <div class="form-group">
                        <label for="school_year" class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun Ajaran
                        </label>
                        <div class="relative">
                            <i class="fas fa-graduation-cap absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="school_year" name="school_year" value="<?php echo e(old('school_year', $schedule->school_year)); ?>"
                                class="form-input pl-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan
                </label>
                <div class="relative">
                    <i class="fas fa-sticky-note absolute left-3 top-3 text-gray-400"></i>
                    <textarea id="notes" name="notes" rows="3" placeholder="Tambahkan catatan tambahan jika diperlukan..."
                        class="form-textarea pl-10 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full transition-all duration-200 <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $schedule->notes)); ?></textarea>
                </div>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div id="schedule-conflict" class="hidden mt-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i>
                    <div>
                        <p class="font-medium">Peringatan: Mungkin terjadi konflik jadwal. Silakan periksa kembali.</p>
                        <div id="conflict-details" class="mt-1 text-sm"></div>
                    </div>
                </div>
            </div>
            
            <!-- Visibility Summary -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Ringkasan Visibilitas Jadwal:</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-white p-3 rounded-lg border border-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-lg text-blue-600 mr-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="text-gray-600">Siswa</p>
                                <p id="student-visibility-summary"><?php echo e($schedule->classroom->name); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-3 rounded-lg border border-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex items-center justify-center bg-green-100 rounded-lg text-green-600 mr-3">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <p class="text-gray-600">Guru</p>
                                <p id="teacher-visibility-summary"><?php echo e($schedule->teacher_name); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 text-xs text-gray-500 flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                    <p>Jadwal yang dibuat akan langsung terlihat pada akun guru dan siswa yang terkait dengan jadwal ini.</p>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="<?php echo e(route('admin.schedule.index')); ?>" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow-sm">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow-sm">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const subjectSelect = document.getElementById('subject_id');
        const teacherSelect = document.getElementById('teacher_id');
        const classroomSelect = document.getElementById('classroom_id');
        const daySelect = document.getElementById('day');
        const form = document.querySelector('form');
        const scheduleId = <?php echo e($schedule->id); ?>;
        
        const timeErrorDiv = document.getElementById('time-error');
        const scheduleConflictDiv = document.getElementById('schedule-conflict');
        const conflictDetailsDiv = document.getElementById('conflict-details');
        const teacherHint = document.getElementById('teacher-hint');
        const classroomVisibility = document.getElementById('classroom-visibility');
        const teacherVisibility = document.getElementById('teacher-visibility');
        const studentCount = document.getElementById('student-count');
        const studentVisibilitySummary = document.getElementById('student-visibility-summary');
        const teacherVisibilitySummary = document.getElementById('teacher-visibility-summary');
        
        // Validate start and end times
        function validateTimes() {
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            if (startTime && endTime) {
                if (startTime >= endTime) {
                    timeErrorDiv.classList.remove('hidden');
                    endTimeInput.classList.add('border-red-300');
                    endTimeInput.setCustomValidity('Waktu selesai harus setelah waktu mulai');
                    return false;
                } else {
                    timeErrorDiv.classList.add('hidden');
                    endTimeInput.classList.remove('border-red-300');
                    endTimeInput.setCustomValidity('');
                    return true;
                }
            }
            return true;
        }
        
        // Update available teachers when subject is changed
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            if (subjectId) {
                // Clear the teacher select
                teacherSelect.innerHTML = '<option value="">-- Memuat Data Guru --</option>';
                teacherSelect.disabled = true;
                
                // Fetch teachers for the selected subject
                fetch(`/admin/subjects/${subjectId}/teachers`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response failed');
                    }
                    return response.json();
                })
                .then(data => {
                    teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
                    teacherSelect.disabled = false;
                    
                    if (data.length === 0) {
                        teacherSelect.innerHTML += '<option value="" disabled>Tidak ada guru untuk mata pelajaran ini</option>';
                    } else {
                        data.forEach(teacher => {
                            const option = document.createElement('option');
                            option.value = teacher.id;
                            option.textContent = teacher.name;
                            teacherSelect.appendChild(option);
                        });
                    }
                    
                    // Re-select current teacher if applicable
                    const currentTeacherId = '<?php echo e($schedule->teacher_id); ?>';
                    if (currentTeacherId) {
                        const option = teacherSelect.querySelector(`option[value="${currentTeacherId}"]`);
                        if (option) {
                            option.selected = true;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching teachers:', error);
                    teacherSelect.innerHTML = '<option value="">-- Error loading data --</option>';
                    teacherSelect.disabled = false;
                    
                    // Fallback to loading all teachers
                    setTimeout(() => {
                        teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
                        
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            teacherSelect.innerHTML += `<option value="<?php echo e($teacher->id); ?>" <?php echo e($schedule->teacher_id == $teacher->id ? 'selected' : ''); ?>><?php echo e($teacher->name); ?></option>`;
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    }, 500);
                });
            } else {
                teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
                teacherSelect.disabled = false;
            }
        });
          // Check for schedule conflicts
        function checkScheduleConflicts() {
            const classroomId = classroomSelect.value;
            const teacherId = teacherSelect.value;
            const day = daySelect.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            if (classroomId && teacherId && day && startTime && endTime) {
                // Show a loading indicator
                scheduleConflictDiv.classList.remove('hidden');
                conflictDetailsDiv.innerHTML = '<div class="flex justify-center"><i class="fas fa-circle-notch fa-spin text-amber-500"></i> <span class="ml-2">Memeriksa kemungkinan konflik jadwal...</span></div>';
                
                const params = new URLSearchParams({
                    classroom_id: classroomId,
                    teacher_id: teacherId,
                    day: day,
                    start_time: startTime,
                    end_time: endTime,
                    schedule_id: scheduleId // Exclude current schedule
                });
                
                fetch(`/api/schedule/check-conflicts?${params.toString()}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.conflicts && data.conflicts.length > 0) {
                            // Show conflict warning
                            scheduleConflictDiv.classList.remove('hidden');
                            scheduleConflictDiv.querySelector('h4').innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Konflik Jadwal Terdeteksi';
                            scheduleConflictDiv.classList.add('border-red-300', 'bg-red-50');
                            scheduleConflictDiv.classList.remove('border-green-300', 'bg-green-50');
                            
                            // Create conflict details message
                            let conflictMessage = '<ul class="list-disc ml-5 mt-2">';
                            data.conflicts.forEach(conflict => {
                                conflictMessage += `<li>${conflict.message}</li>`;
                            });
                            conflictMessage += '</ul>';
                            
                            conflictDetailsDiv.innerHTML = conflictMessage;
                        } else {
                            // Show success message
                            scheduleConflictDiv.classList.remove('hidden');
                            scheduleConflictDiv.querySelector('h4').innerHTML = '<i class="fas fa-check-circle mr-2"></i> Jadwal Tersedia';
                            scheduleConflictDiv.classList.add('border-green-300', 'bg-green-50');
                            scheduleConflictDiv.classList.remove('border-red-300', 'bg-red-50');
                            conflictDetailsDiv.innerHTML = '<p>Tidak ada konflik jadwal, waktu tersedia untuk kelas dan guru yang dipilih.</p>';
                            
                            // Auto-hide after 5 seconds
                            setTimeout(() => {
                                scheduleConflictDiv.classList.add('hidden');
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking schedule conflicts:', error);
                        // Show error message
                        scheduleConflictDiv.classList.remove('hidden');
                        scheduleConflictDiv.querySelector('h4').innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Gagal Memeriksa Konflik';
                        conflictDetailsDiv.innerHTML = '<p>Gagal memeriksa konflik jadwal. Silakan periksa koneksi dan coba lagi.</p>';
                    });
            }
        }
        
        // Show visibility info when classroom is selected
        classroomSelect.addEventListener('change', function() {
            if (this.value) {
                const classroomName = this.options[this.selectedIndex].text;
                classroomVisibility.classList.remove('hidden');
                
                // In a real app you would fetch this data
                const count = Math.floor(Math.random() * 20) + 20; // Mock data
                studentCount.textContent = count;
                
                studentVisibilitySummary.textContent = `${count} siswa kelas ${classroomName}`;
            } else {
                classroomVisibility.classList.add('hidden');
                studentVisibilitySummary.textContent = 'Belum dipilih';
            }
        });
        
        // Show teacher visibility when teacher is selected
        teacherSelect.addEventListener('change', function() {
            if (this.value) {
                const teacherName = this.options[this.selectedIndex].text;
                teacherVisibility.classList.remove('hidden');
                teacherVisibilitySummary.textContent = teacherName;
            } else {
                teacherVisibility.classList.add('hidden');
                teacherVisibilitySummary.textContent = 'Belum dipilih';
            }
        });
        
        // Event listeners
        startTimeInput.addEventListener('change', function() {
            validateTimes();
            checkScheduleConflicts();
        });
        
        endTimeInput.addEventListener('change', function() {
            validateTimes();
            checkScheduleConflicts();
        });
        
        classroomSelect.addEventListener('change', checkScheduleConflicts);
        teacherSelect.addEventListener('change', checkScheduleConflicts);
        daySelect.addEventListener('change', checkScheduleConflicts);
        
        // Form validation
        form.addEventListener('submit', function(e) {
            // Reset custom validity
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => input.setCustomValidity(''));
            
            // Validate times
            if (!validateTimes()) {
                e.preventDefault();
                alert('Waktu selesai harus setelah waktu mulai');
                return;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            submitBtn.disabled = true;
        });
        
        // Run validation on page load
        validateTimes();
        checkScheduleConflicts();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/admin/schedule/edit.blade.php ENDPATH**/ ?>