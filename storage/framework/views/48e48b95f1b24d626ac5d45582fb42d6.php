<!-- Dashboard -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-indigo-400 rounded-full"></span>
        Dashboard
    </div>
    <ul class="sidebar-items">
        <li>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.dashboard') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-indigo-800' : 'bg-indigo-700/50 group-hover:bg-indigo-700'); ?> transition-all duration-200">
                    <i class="fas fa-tachometer-alt text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.dashboard') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Dashboard</span>
                <?php if(request()->routeIs('admin.dashboard')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-indigo-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- User Management -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
        Pengguna
    </div>
    <ul class="sidebar-items">
        <li>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.users.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.users.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-green-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-users text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.users.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Pengguna</span>
                <?php if(request()->routeIs('admin.users.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-green-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- Academic Management -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-purple-400 rounded-full"></span>
        Akademik
    </div>
    <ul class="sidebar-items">
        <li>
            <a href="<?php echo e(route('admin.subjects.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.subjects.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.subjects.*') ? 'bg-purple-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-book text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.subjects.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Mata Pelajaran</span>
                <?php if(request()->routeIs('admin.subjects.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>        <li>
            <a href="<?php echo e(route('admin.classrooms.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.classrooms.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.classrooms.*') ? 'bg-purple-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-school text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.classrooms.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Kelas</span>
                <?php if(request()->routeIs('admin.classrooms.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.schedule.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.schedule.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.schedule.*') ? 'bg-purple-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-calendar-alt text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.schedule.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Jadwal</span>
                <?php if(request()->routeIs('admin.schedule.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- Communication -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-red-400 rounded-full"></span>
        Komunikasi
    </div>
    <ul class="sidebar-items">
        <li class="relative">
            <a href="<?php echo e(route('admin.announcements.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.announcements.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.announcements.*') ? 'bg-red-800' : 'bg-indigo-700/50 group-hover:bg-red-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-bullhorn text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.announcements.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Pengumuman</span>
                <?php if(request()->routeIs('admin.announcements.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-red-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>

<!-- System & Account Settings -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 rounded-full bg-amber-400"></span>
        Akun
    </div>
    
            <a href="<?php echo e(route('admin.profile.show')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.profile.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200'); ?> flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg <?php echo e(request()->routeIs('admin.profile.*') ? 'bg-amber-800' : 'bg-indigo-700/50 group-hover:bg-amber-700/50'); ?> transition-all duration-200">
                    <i class="fas fa-user-circle text-lg w-5 h-5 flex items-center justify-center <?php echo e(request()->routeIs('admin.profile.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white'); ?>"></i>
                </div>
                <span class="ml-3">Profil Saya</span>
                <?php if(request()->routeIs('admin.profile.*')): ?>
                    <span class="absolute inset-y-0 left-0 w-1 bg-amber-400 rounded-tr-md rounded-br-md"></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
</div>
<?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\NEW\PROYEK-AKHIR-II-WEB\resources\views/admin/partials/sidebar.blade.php ENDPATH**/ ?>