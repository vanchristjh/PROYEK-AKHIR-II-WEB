<!-- Dashboard -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-indigo-400 rounded-full"></span>
        Dashboard
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="<?php echo e(route('siswa.dashboard')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.dashboard') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.dashboard') ? 'bg-indigo-800' : 'bg-indigo-700/50 group-hover:bg-indigo-700'); ?> transition-all duration-200">
                    <i class="fas fa-tachometer-alt text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.dashboard') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Dashboard</span>
                <?php if(request()->routeIs('siswa.dashboard')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-indigo-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- Pembelajaran -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-blue-400 rounded-full"></span>
        Pembelajaran
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="<?php echo e(route('siswa.schedule.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.schedule.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.schedule.*') ? 'bg-blue-800' : 'bg-indigo-700/50 group-hover:bg-blue-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-calendar-alt text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.schedule.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Jadwal Pelajaran</span>
                <?php if(request()->routeIs('siswa.schedule.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-blue-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>        <li>
            <a href="<?php echo e(route('siswa.materials.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.materials.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.materials.*') ? 'bg-purple-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-book text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.materials.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Materi Pelajaran</span>
                <?php if(request()->routeIs('siswa.materials.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('siswa.assignments.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.assignments.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.assignments.*') ? 'bg-yellow-700' : 'bg-indigo-700/50 group-hover:bg-yellow-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-tasks text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.assignments.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Tugas</span>
                <?php if(request()->routeIs('siswa.assignments.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-yellow-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('siswa.quizzes.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.quizzes.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.quizzes.*') ? 'bg-orange-700' : 'bg-indigo-700/50 group-hover:bg-orange-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-question-circle text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.quizzes.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Quiz</span>
                <?php if(request()->routeIs('siswa.quizzes.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-orange-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('siswa.exams.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.exams.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.exams.*') ? 'bg-red-700' : 'bg-indigo-700/50 group-hover:bg-red-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-file-alt text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.exams.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Ujian</span>
                <?php if(request()->routeIs('siswa.exams.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-red-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- Pencapaian -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
        Pencapaian
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="<?php echo e(route('siswa.grades.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.grades.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.grades.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-green-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-star text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.grades.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Nilai</span>
                <?php if(request()->routeIs('siswa.grades.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-green-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- Informasi -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-red-400 rounded-full"></span>
        Informasi
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="<?php echo e(route('siswa.announcements.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 <?php echo e(request()->routeIs('siswa.announcements.*') ? 'sidebar-active' : ''); ?>">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.announcements.*') ? 'bg-red-700' : 'bg-indigo-700/50 group-hover:bg-red-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-bullhorn text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.announcements.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Pengumuman</span>
                <?php if(request()->routeIs('siswa.announcements.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-red-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- Akun -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-purple-400 rounded-full"></span>
        Akun
    </div>
    <ul class="px-3 space-y-1 sidebar-items">        <li>
            <a href="<?php echo e(route('siswa.profile.show')); ?>" class="sidebar-item <?php echo e(request()->routeIs('siswa.profile.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('siswa.profile.*') ? 'bg-purple-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-user-circle text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('siswa.profile.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Profil Saya</span>
                <?php if(request()->routeIs('siswa.profile.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event handlers to all sidebar links
    document.querySelectorAll('.sidebar-item').forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Get the URL directly
            const href = this.getAttribute('href');
            if (href && href !== 'javascript:void(0)' && href !== '#') {
                e.preventDefault(); // Stop default event
                console.log('Navigating to:', href); // Debug info
                window.location.href = href; // Force navigation
            }
        });
    });
});

function navigateTo(path) {
    console.log('Direct navigation to:', path);
    window.location.href = path;
    return false;
}
</script><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/siswa/partials/sidebar.blade.php ENDPATH**/ ?>