<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas <?php echo e($classroom->name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 20px;
            margin: 5px 0;
        }
        h2 {
            font-size: 18px;
            margin: 20px 0 10px;
            color: #4338ca;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
        }
        td {
            padding: 8px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA KELAS</h1>
        <h1>SMA NEGERI 1 GIRSIP</h1>
        <p>Tahun Akademik: <?php echo e($classroom->academic_year); ?></p>
    </div>
    
    <div class="info-section">
        <h2>Informasi Kelas</h2>
        <div class="info-item">
            <span class="info-label">Nama Kelas</span>: <?php echo e($classroom->name); ?>

        </div>
        <div class="info-item">
            <span class="info-label">Tingkat</span>: Kelas <?php echo e($classroom->grade_level); ?>

        </div>
        <div class="info-item">
            <span class="info-label">Ruang</span>: <?php echo e($classroom->room_number ?? 'Tidak ditentukan'); ?>

        </div>
        <div class="info-item">
            <span class="info-label">Kapasitas</span>: <?php echo e($classroom->capacity); ?> siswa
        </div>
        <div class="info-item">
            <span class="info-label">Wali Kelas</span>: <?php echo e($classroom->homeroomTeacher->name ?? 'Belum ditetapkan'); ?>

        </div>
    </div>

    <div class="info-section">
        <h2>Daftar Mata Pelajaran</h2>
        <?php if($classroom->subjects->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="20%">Kode</th>
                        <th width="70%">Nama Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $classroom->subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($subject->code); ?></td>
                            <td><?php echo e($subject->name); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada mata pelajaran yang ditambahkan ke kelas ini.</p>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <h2>Daftar Siswa</h2>
        <?php if($classroom->students->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="20%">NIS</th>
                        <th width="40%">Nama Lengkap</th>
                        <th width="30%">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $classroom->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($student->id_number); ?></td>
                            <td><?php echo e($student->name); ?></td>
                            <td><?php echo e($student->email); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada siswa yang terdaftar di kelas ini.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Dicetak pada: <?php echo e(date('d-m-Y H:i:s')); ?></p>
        <p>SMA Negeri 1 Girsip</p>
    </div>
</body>
</html>
<?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\IMPLEMENTASI NEW\sman1-girsip\resources\views/admin/classrooms/export.blade.php ENDPATH**/ ?>