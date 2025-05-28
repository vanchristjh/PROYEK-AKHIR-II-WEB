@extends('layouts.app')

@section('title', 'Panduan Pengumpulan Tugas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900">Panduan Pengumpulan Tugas</h1>
                <p class="mt-2 text-gray-600">Berikut adalah panduan lengkap tentang cara mengumpulkan dan mengelola tugas di sistem e-learning SMAN 1.</p>
            </div>
            
            <div class="p-6">
                <div class="prose max-w-none">
                    <h2>Cara Mengumpulkan Tugas</h2>
                    <ol>
                        <li>Masuk ke halaman <strong>Tugas</strong> melalui menu navigasi.</li>
                        <li>Pilih tugas yang ingin dikumpulkan dengan mengklik judulnya.</li>
                        <li>Pada halaman detail tugas, scroll ke bagian bawah dan klik tombol <strong>Choose File</strong> untuk memilih file yang akan diunggah.</li>
                        <li>Setelah memilih file, klik tombol <strong>Kirim Tugas</strong>.</li>
                        <li>Tugas Anda akan berhasil dikumpulkan dan ditandai sebagai "Sudah Dikumpulkan".</li>
                    </ol>
                    
                    <h2 class="mt-8">Format File yang Didukung</h2>
                    <p>Sistem mendukung berbagai format file untuk pengumpulan tugas, antara lain:</p>
                    <ul>
                        <li><strong>Dokumen:</strong> PDF, DOC, DOCX</li>
                        <li><strong>Spreadsheet:</strong> XLS, XLSX</li>
                        <li><strong>Presentasi:</strong> PPT, PPTX</li>
                        <li><strong>Gambar:</strong> JPG, JPEG, PNG</li>
                        <li><strong>Arsip:</strong> ZIP</li>
                    </ul>
                    <p>Ukuran maksimal file yang dapat diunggah adalah <strong>100MB</strong>.</p>
                    
                    <h2 class="mt-8">Mengedit atau Menghapus Pengumpulan</h2>
                    <p>Anda dapat mengedit atau menghapus pengumpulan tugas selama deadline belum berakhir dan tugas belum dinilai oleh guru:</p>
                    <ol>
                        <li>Buka halaman detail tugas yang sudah dikumpulkan.</li>
                        <li>Klik tombol <strong>Edit</strong> untuk mengganti file yang telah diunggah.</li>
                        <li>Jika ingin menghapus pengumpulan, klik tombol <strong>Hapus</strong> dan konfirmasi tindakan tersebut.</li>
                    </ol>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Perhatian!</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Pastikan tugas dikumpulkan sebelum deadline. Sistem akan secara otomatis menutup pengumpulan setelah deadline berakhir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="mt-8">Melihat Nilai dan Feedback</h2>
                    <p>Setelah tugas dinilai oleh guru, Anda dapat melihat nilai dan feedback dengan cara:</p>
                    <ol>
                        <li>Buka halaman detail tugas yang sudah dikumpulkan.</li>
                        <li>Nilai akan ditampilkan di bagian atas halaman.</li>
                        <li>Feedback dari guru (jika ada) akan ditampilkan di bawah informasi pengumpulan.</li>
                    </ol>
                    
                    <h2 class="mt-8">Masalah Umum dan Solusinya</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Masalah</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Solusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">File terlalu besar (melebihi 100MB)</td>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Kompres file atau pisahkan menjadi beberapa bagian lebih kecil</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Format file tidak didukung</td>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Konversi file ke format yang didukung seperti PDF</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Gagal mengunggah file</td>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Periksa koneksi internet dan coba lagi, atau gunakan browser berbeda</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Tidak dapat mengedit/menghapus tugas</td>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">Pastikan deadline belum berakhir dan tugas belum dinilai</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <h2 class="mt-8">Butuh Bantuan Lebih Lanjut?</h2>
                    <p>Jika Anda masih mengalami kesulitan, silakan hubungi:</p>
                    <ul>
                        <li>Guru mata pelajaran terkait</li>
                        <li>Wali kelas</li>
                        <li>Staff IT sekolah melalui email: support@sman1.sch.id</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
