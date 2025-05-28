@extends('layouts.app')

@section('title', 'Bantuan Penggunaan Sistem')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-10 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Panduan Penggunaan Sistem</h1>
            <p class="text-gray-600">Pelajari cara menggunakan semua fitur di SMAN 1 Digital School Information System</p>
        </div>

        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <div class="border-b border-gray-200">
                <div class="flex flex-wrap -mb-px">
                    <button id="tab-siswa" class="text-blue-600 hover:text-blue-800 inline-flex items-center py-4 px-6 border-b-2 border-blue-500 font-medium text-sm active-tab">
                        <i class="fas fa-user-graduate mr-2"></i> Panduan Siswa
                    </button>
                    <button id="tab-guru" class="text-gray-500 hover:text-gray-700 inline-flex items-center py-4 px-6 border-b-2 border-transparent font-medium text-sm hover:border-gray-300">
                        <i class="fas fa-chalkboard-teacher mr-2"></i> Panduan Guru
                    </button>
                </div>
            </div>

            <!-- Siswa Guide Content -->
            <div id="content-siswa" class="p-6">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Manajemen Tugas untuk Siswa</h2>

                    <div class="mb-6 bg-blue-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-blue-800 mb-2">Melihat Tugas yang Diberikan</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-blue-700">
                            <li>Login ke akun siswa Anda.</li>
                            <li>Pada sidebar, klik menu <strong>"Tugas"</strong>.</li>
                            <li>Anda akan melihat daftar semua tugas yang diberikan oleh guru.</li>
                            <li>Gunakan filter di bagian atas untuk menyaring tugas berdasarkan mata pelajaran atau status.</li>
                        </ol>
                    </div>

                    <div class="mb-6 bg-green-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-green-800 mb-2">Mengerjakan dan Mengumpulkan Tugas</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-green-700">
                            <li>Pada daftar tugas, klik tombol <strong>"Kerjakan"</strong> pada tugas yang ingin dikumpulkan.</li>
                            <li>Lihat detail tugas termasuk deskripsi, deadline, dan file pendukung jika ada.</li>
                            <li>Klik tombol <strong>"Kirim Tugas"</strong> dan unggah file tugas yang telah dikerjakan.</li>
                            <li>Masukkan catatan tambahan jika diperlukan.</li>
                            <li>Klik <strong>"Kirim"</strong> untuk menyelesaikan pengumpulan.</li>
                        </ol>
                    </div>

                    <div class="mb-6 bg-yellow-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-yellow-800 mb-2">Mengedit atau Menghapus Pengumpulan</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-yellow-700">
                            <li>Buka detail tugas yang sudah dikumpulkan.</li>
                            <li>Klik tombol <strong>"Edit"</strong> untuk mengganti file yang telah dikumpulkan.</li>
                            <li>Atau klik tombol <strong>"Hapus"</strong> untuk menghapus pengumpulan (Anda dapat mengumpulkan ulang sebelum deadline).</li>
                            <li>Perhatikan bahwa Anda tidak dapat mengubah atau menghapus pengumpulan setelah deadline atau setelah dinilai.</li>
                        </ol>
                    </div>

                    <div class="bg-purple-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-purple-800 mb-2">Melihat Nilai Tugas</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-purple-700">
                            <li>Nilai akan muncul pada detail tugas setelah guru memberikan penilaian.</li>
                            <li>Anda juga dapat melihat semua nilai tugas di menu <strong>"Nilai"</strong> pada sidebar.</li>
                            <li>Feedback dari guru (jika ada) juga akan ditampilkan bersama dengan nilai.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Guru Guide Content -->
            <div id="content-guru" class="p-6 hidden">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Manajemen Tugas untuk Guru</h2>

                    <div class="mb-6 bg-blue-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-blue-800 mb-2">Membuat Tugas Baru</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-blue-700">
                            <li>Login ke akun guru Anda.</li>
                            <li>Pada sidebar, klik menu <strong>"Tugas"</strong>.</li>
                            <li>Klik tombol <strong>"Buat Tugas Baru"</strong>.</li>
                            <li>Isi semua informasi yang diperlukan:
                                <ul class="list-disc pl-5 mt-2 space-y-1">
                                    <li>Judul dan deskripsi tugas</li>
                                    <li>Mata pelajaran</li>
                                    <li>Deadline pengumpulan</li>
                                    <li>Pilih kelas yang akan diberikan tugas</li>
                                    <li>Unggah file pendukung jika diperlukan</li>
                                </ul>
                            </li>
                            <li>Klik <strong>"Buat Tugas"</strong> untuk menyimpan.</li>
                        </ol>
                    </div>

                    <div class="mb-6 bg-green-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-green-800 mb-2">Mengelola Tugas</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-green-700">
                            <li>Pada daftar tugas, Anda dapat melihat semua tugas yang telah dibuat.</li>
                            <li>Gunakan filter di bagian atas untuk menyaring tugas berdasarkan mata pelajaran, kelas, atau status.</li>
                            <li>Klik tombol <strong>"Lihat"</strong> untuk melihat detail tugas.</li>
                            <li>Klik tombol <strong>"Edit"</strong> untuk mengubah detail tugas.</li>
                            <li>Anda dapat mengaktifkan atau menonaktifkan tugas dengan mengubah status di form edit.</li>
                            <li>Tugas hanya dapat dihapus jika belum ada siswa yang mengumpulkan.</li>
                        </ol>
                    </div>

                    <div class="mb-6 bg-yellow-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-yellow-800 mb-2">Melihat dan Menilai Pengumpulan</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-yellow-700">
                            <li>Pada detail tugas, klik tombol <strong>"Pengumpulan"</strong> atau ikon daftar.</li>
                            <li>Anda akan melihat daftar semua siswa yang telah mengumpulkan tugas.</li>
                            <li>Klik tombol <strong>"Download"</strong> untuk mengunduh file pengumpulan siswa.</li>
                            <li>Klik tombol <strong>"Beri Nilai"</strong> untuk membuka modal penilaian.</li>
                            <li>Masukkan nilai (0-100) dan feedback jika diperlukan.</li>
                            <li>Klik <strong>"Simpan Nilai"</strong> untuk menyimpan penilaian.</li>
                        </ol>
                    </div>

                    <div class="bg-purple-50 rounded-md p-4">
                        <h3 class="text-lg font-medium text-purple-800 mb-2">Statistik dan Laporan</h3>
                        <ol class="list-decimal pl-5 space-y-2 text-purple-700">
                            <li>Pada halaman detail tugas, Anda dapat melihat statistik pengumpulan seperti:
                                <ul class="list-disc pl-5 mt-2 space-y-1">
                                    <li>Jumlah siswa yang sudah mengumpulkan</li>
                                    <li>Jumlah pengumpulan yang sudah dinilai</li>
                                    <li>Persentase pengumpulan dan penilaian</li>
                                    <li>Rata-rata nilai</li>
                                </ul>
                            </li>
                            <li>Pada halaman pengumpulan, gunakan filter untuk mencari siswa tertentu atau melihat status penilaian.</li>
                            <li>Data statistik membantu Anda melacak kemajuan dan kinerja kelas.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Contact Section -->
        <div class="mt-12 bg-white shadow-md rounded-xl p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Butuh Bantuan Lebih?</h2>
            <p class="text-gray-600 mb-6">Jika Anda mengalami masalah atau memiliki pertanyaan yang tidak terjawab dalam panduan ini, silakan hubungi tim dukungan kami.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <h3 class="text-lg font-medium text-blue-800 mb-2">Kontak Administrator</h3>
                    <p class="text-blue-700 mb-3">Silakan hubungi tim administrator sekolah untuk bantuan teknis.</p>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-blue-600 mr-3"></i>
                        <span>admin@sman1-digital.sch.id</span>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <h3 class="text-lg font-medium text-green-800 mb-2">Pusat Bantuan</h3>
                    <p class="text-green-700 mb-3">Kunjungi pusat bantuan untuk melihat FAQ dan tutorial video.</p>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-question-circle mr-2"></i> Kunjungi Pusat Bantuan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabSiswa = document.getElementById('tab-siswa');
        const tabGuru = document.getElementById('tab-guru');
        const contentSiswa = document.getElementById('content-siswa');
        const contentGuru = document.getElementById('content-guru');
        
        tabSiswa.addEventListener('click', function() {
            contentSiswa.classList.remove('hidden');
            contentGuru.classList.add('hidden');
            tabSiswa.classList.add('text-blue-600', 'border-blue-500');
            tabSiswa.classList.remove('text-gray-500', 'border-transparent');
            tabGuru.classList.add('text-gray-500', 'border-transparent');
            tabGuru.classList.remove('text-blue-600', 'border-blue-500');
        });
        
        tabGuru.addEventListener('click', function() {
            contentSiswa.classList.add('hidden');
            contentGuru.classList.remove('hidden');
            tabGuru.classList.add('text-blue-600', 'border-blue-500');
            tabGuru.classList.remove('text-gray-500', 'border-transparent');
            tabSiswa.classList.add('text-gray-500', 'border-transparent');
            tabSiswa.classList.remove('text-blue-600', 'border-blue-500');
        });
    });
</script>
@endpush
@endsection
