

<?php $__env->startSection('title', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('header', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('navigation'); ?>
    <li>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-users text-lg w-6"></i>
            <span class="ml-3">Pengguna</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('admin.subjects.index')); ?>" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-book text-lg w-6 text-indigo-300"></i>
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
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-medium">Daftar Pengguna</h2>
            <a href="<?php echo e(route('admin.users.create')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all">
                <i class="fas fa-plus mr-2"></i>Tambah Pengguna Baru
            </a>
        </div>

        <!-- Filter and Search Form -->
        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <form action="<?php echo e(route('admin.users.index')); ?>" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-grow min-w-[200px]">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama, username, atau email..." 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <select name="role" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Semua Peran</option>
                        <?php $__currentLoopData = $roles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(strtolower($role->name)); ?>" <?php echo e(request('role') == strtolower($role->name) ? 'selected' : ''); ?>>
                                <?php echo e($role->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <?php if(request()->anyFilled(['search', 'role'])): ?>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php if($user->avatar): ?>
                                            <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                                <?php echo e(substr($user->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>                                        <?php if($user->nip || $user->nisn): ?>
                                            <div class="text-sm text-gray-500"><?php echo e($user->id_number); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($user->username); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($user->email); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo e($user->role_id == 1 ? 'bg-red-100 text-red-800' : 
                                       ($user->role_id == 2 ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800')); ?>">
                                    <?php echo e($user->role->name ?? 'Unknown'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2 justify-end">
                                    <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors" title="Edit Pengguna">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST" class="inline delete-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="delete-btn px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors" title="Hapus Pengguna" 
                                            data-id="<?php echo e($user->id); ?>" data-name="<?php echo e($user->name); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 bg-blue-100 text-blue-400 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 mb-3">Belum ada pengguna yang tersedia</p>
                                    <a href="<?php echo e(route('admin.users.create')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Tambah Pengguna Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if(isset($users) && method_exists($users, 'links')): ?>
            <div class="mt-4">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="text-center mb-6">
                <div class="h-16 w-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-500" id="deleteConfirmationText">Apakah Anda yakin ingin menghapus pengguna ini?</p>
            </div>
            <div class="flex justify-center space-x-4">
                <button type="button" id="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button type="button" id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-notification" class="fixed bottom-4 right-4 z-50 hidden">
        <div class="flex items-center p-4 mb-4 rounded-lg shadow min-w-64" role="alert">
            <div id="toast-icon" class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"></div>
            <div class="ml-3 text-sm font-normal" id="toast-message"></div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" 
                    onclick="document.getElementById('toast-notification').classList.add('hidden')">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');
            
            // Set message
            toastMessage.textContent = message;
            
            // Set toast type (success, error, warning)
            if (type === 'success') {
                toast.querySelector('div').classList.add('bg-green-50', 'text-green-800');
                toastIcon.classList.add('bg-green-100', 'text-green-500');
                toastIcon.innerHTML = '<i class="fas fa-check"></i>';
            } else if (type === 'error') {
                toast.querySelector('div').classList.add('bg-red-50', 'text-red-800');
                toastIcon.classList.add('bg-red-100', 'text-red-500');
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
            } else if (type === 'warning') {
                toast.querySelector('div').classList.add('bg-yellow-50', 'text-yellow-800');
                toastIcon.classList.add('bg-yellow-100', 'text-yellow-500');
                toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            }
            
            // Show toast
            toast.classList.remove('hidden');
            
            // Auto hide toast after 5 seconds
            setTimeout(() => {
                toast.classList.add('hidden');
                // Reset classes for next use
                toast.querySelector('div').className = 'flex items-center p-4 mb-4 rounded-lg shadow min-w-64';
                toastIcon.className = 'inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg';
            }, 5000);
        }

        // Show flash messages using toast if they exist
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(session('success')): ?>
                showToast('<?php echo e(session('success')); ?>', 'success');
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                showToast('<?php echo e(session('error')); ?>', 'error');
            <?php endif; ?>

            // Delete confirmation handling
            const deleteModal = document.getElementById('deleteModal');
            const cancelDelete = document.getElementById('cancelDelete');
            const confirmDelete = document.getElementById('confirmDelete');
            const deleteText = document.getElementById('deleteConfirmationText');
            let currentForm = null;

            // Open modal when delete button is clicked
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.id;
                    const userName = this.dataset.name;
                    currentForm = this.closest('.delete-form');
                    
                    deleteText.textContent = `Apakah Anda yakin ingin menghapus pengguna "${userName}"?`;
                    deleteModal.classList.remove('hidden');
                });
            });

            // Close modal when cancel is clicked
            cancelDelete.addEventListener('click', function() {
                deleteModal.classList.add('hidden');
                currentForm = null;
            });

            // Close modal when clicking outside
            deleteModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    deleteModal.classList.add('hidden');
                    currentForm = null;
                }
            });

            // Submit form when confirm is clicked
            confirmDelete.addEventListener('click', function() {
                if (currentForm) {
                    // Show loading state
                    confirmDelete.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menghapus...';
                    confirmDelete.disabled = true;
                    
                    currentForm.submit();
                }
                deleteModal.classList.add('hidden');
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/admin/users/index.blade.php ENDPATH**/ ?>