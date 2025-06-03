

<?php $__env->startSection('title', 'Daftar Mata Pelajaran'); ?>

<?php $__env->startSection('header', 'Daftar Mata Pelajaran'); ?>

<?php $__env->startSection('navigation'); ?>
    <li>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-users text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengguna</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.subjects.index')); ?>" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-book text-lg w-6"></i>
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
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-book text-9xl"></i>
        </div>
        <div class="relative">
            <h2 class="text-2xl font-bold mb-2">Daftar Mata Pelajaran</h2>
            <p class="text-amber-100 mb-4">Kelola semua mata pelajaran di SMAN 1</p>
            <a href="<?php echo e(route('admin.subjects.create')); ?>" class="inline-flex items-center px-4 py-2 bg-white text-amber-600 rounded-md font-semibold text-sm shadow hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-amber-200 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Mata Pelajaran
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md animate-fade-in-down" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?php echo e(session('success')); ?></span>
                <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md animate-fade-in-down" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo e(session('error')); ?></span>
                <button @click="show = false" class="ml-auto text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Subjects Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <!-- Search and filter -->
            <div class="flex flex-wrap items-center justify-between mb-4">
                <div class="w-full md:w-auto mb-4 md:mb-0">
                    <div class="relative">
                        <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 sm:text-sm" placeholder="Cari mata pelajaran...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data table -->
            <?php if($subjects->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mata Pelajaran</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru Pengajar</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="subjectsTable">
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($subject->code); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($subject->name); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="flex flex-wrap gap-1">
                                            <?php $__empty_1 = true; $__currentLoopData = $subject->teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?php echo e($teacher->name); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <span class="text-gray-400 italic">Belum ada guru pengajar</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="<?php echo e(route('admin.subjects.show', $subject)); ?>" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.subjects.edit', $subject)); ?>" class="text-amber-600 hover:text-amber-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('admin.subjects.destroy', $subject)); ?>" method="POST" class="inline-block">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-book text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada data mata pelajaran</h3>
                    <p class="mt-1 text-gray-500">Silakan tambahkan mata pelajaran baru dengan mengklik tombol di atas</p>
                    <div class="mt-6">
                        <a href="<?php echo e(route('admin.subjects.create')); ?>" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-md font-semibold text-sm shadow hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Mata Pelajaran
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const subjectsTable = document.getElementById('subjectsTable');
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = subjectsTable.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const code = rows[i].getElementsByTagName('td')[0].innerText.toLowerCase();
                const name = rows[i].getElementsByTagName('td')[1].innerText.toLowerCase();
                const teachers = rows[i].getElementsByTagName('td')[2].innerText.toLowerCase();
                
                if (code.includes(searchTerm) || name.includes(searchTerm) || teachers.includes(searchTerm)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\NEW\PROYEK-AKHIR-II-WEB\resources\views/admin/subjects/index.blade.php ENDPATH**/ ?>