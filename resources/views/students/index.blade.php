@extends('layouts.admin')

@section('title', 'Daftar Siswa')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manajemen Siswa</h3>
                    <p class="text-subtitle text-muted">Kelola data siswa sekolah</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Siswa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Daftar Siswa</h4>
                        <a href="{{ route('students.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Siswa Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible show fade">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>JK</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                    <tr>
                                        <td>{{ $student->nis }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $student->class->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $student->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm me-1">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data siswa</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                "paging": false,
                "info": false,
                "ordering": true,
                "language": {
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada data yang cocok",
                    "infoEmpty": "Tidak ada data tersedia",
                }
            });
        });
    </script>
@endsection
