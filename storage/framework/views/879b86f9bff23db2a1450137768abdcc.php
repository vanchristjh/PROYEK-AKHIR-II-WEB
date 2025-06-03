<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Seluruh Kelas</title>
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
            page-break-after: avoid;
        }
        h3 {
            font-size: 16px;
            color: #555;
            margin: 15px 0 10px;
            page-break-after: avoid;
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
        .classroom-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .page-break {
            page-break-before: always;
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
        <h1>LAPORAN DATA SELURUH KELAS</h1>
        <h1>SMA NEGERI 1 GIRSIP</h1>
        <p>Tanggal Cetak: <?php echo e(date('d-m-Y')); ?></p>
    </div>
    
    <div class="info-section">
        <h2>Ringkasan</h2>
        <table>
            <tr>
                <td width="30%"><strong>Total Kelas</strong></td>
                <td><?php echo e($classrooms->count()); ?> kelas</td>
            </tr>
            <tr>
                <td><strong>Kelas 10</strong></td>
                <td><?php echo e($classrooms->where('grade_level', 10)->count()); ?> kelas</td>
            </tr>
            <tr>
                <td><strong>Kelas 11</strong></td>
                <td><?php echo e($classrooms->where('grade_level', 11)->count()); ?> kelas</td>
            </tr>
            <tr>
                <td><strong>Kelas 12</strong></td>
                <td><?php echo e($classrooms->where('grade_level', 12)->count()); ?> kelas</td>
            </tr>
            <tr>
                <td><strong>Total Siswa</strong></td>
                <td><?php echo e($classrooms->sum(function($classroom) { return $classroom->students->count(); })); ?> siswa</td>
            </tr>
        </table>
    </div>
    
    <h2>Daftar Kelas</h2>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Kelas</th>
                <th width="20%">Tingkat</th>
                <th width="20%">Wali Kelas</th>
                <th width="15%">Jumlah Siswa</th>
                <th width="15%">Kapasitas</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($classroom->name); ?></td>
                    <td>Kelas <?php echo e($classroom->grade_level); ?></td>
                    <td><?php echo e($classroom->homeroomTeacher->name ?? 'Belum ditetapkan'); ?></td>
                    <td><?php echo e($classroom->students->count()); ?></td>
                    <td><?php echo e($classroom->capacity); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="<?php echo e($index > 0 ? 'page-break' : ''); ?>"></div>
        <div class="classroom-section">
            <h2><?php echo e($classroom->name); ?></h2>
            <table>
                <tr>
                    <td width="30%"><strong>Tingkat</strong></td>
                    <td>Kelas <?php echo e($classroom->grade_level); ?></td>
                </tr>
                <tr>
                    <td><strong>Tahun Akademik</strong></td>
                    <td><?php echo e($classroom->academic_year); ?></td>
                </tr>
                <tr>
                    <td><strong>Wali Kelas</strong></td>
                    <td><?php echo e($classroom->homeroomTeacher->name ?? 'Belum ditetapkan'); ?></td>
                </tr>
                <tr>
                    <td><strong>Ruangan</strong></td>
                    <td><?php echo e($classroom->room_number ?? 'Tidak ditentukan'); ?></td>
                </tr>
                <tr>
                    <td><strong>Jumlah Siswa</strong></td>
                    <td><?php echo e($classroom->students->count()); ?> dari <?php echo e($classroom->capacity); ?></td>
                </tr>
            </table>
            
            <h3>Daftar Siswa</h3>
            <?php if($classroom->students->count() > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th width="30%">NIS</th>
                            <th width="60%">Nama Lengkap</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $classroom->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentIndex => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($studentIndex + 1); ?></td>
                                <td><?php echo e($student->id_number); ?></td>
                                <td><?php echo e($student->name); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada siswa yang terdaftar di kelas ini.</p>
            <?php endif; ?>
            
            <h3>Mata Pelajaran</h3>
            <?php if($classroom->subjects->count() > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th width="30%">Kode</th>
                            <th width="60%">Nama Mata Pelajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $classroom->subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subjectIndex => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($subjectIndex + 1); ?></td>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="footer">
        <p>Dicetak pada: <?php echo e(date('d-m-Y H:i:s')); ?></p>
        <p>SMA Negeri 1 Girsip</p>
    </div>
</body>
</html>
<?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\NEW\PROYEK-AKHIR-II-WEB\resources\views/admin/classrooms/export-all.blade.php ENDPATH**/ ?>