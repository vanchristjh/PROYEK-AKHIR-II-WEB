@extends('layouts.admin')

@section('title', 'Detail Siswa')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Siswa</h3>
                    <p class="text-subtitle text-muted">Informasi lengkap data siswa</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Siswa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Siswa: {{ $student->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="200">NIS</th>
                                        <td width="30">:</td>
                                        <td>{{ $student->nis }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <td>:</td>
                                        <td>{{ $student->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Kelamin</th>
                                        <td>:</td>
                                        <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kelas</th>
                                        <td>:</td>
                                        <td>{{ $student->class->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lahir</th>
                                        <td>:</td>
                                        <td>{{ date('d F Y', strtotime($student->birth_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>:</td>
                                        <td>{{ $student->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Telepon</th>
                                        <td>:</td>
                                        <td>{{ $student->phone_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>:</td>
                                        <td>{{ $student->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $student->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body py-4 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xl bg-primary">
                                            <span class="avatar-content">{{ substr($student->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ms-3 name">
                                            <h5 class="font-bold">{{ $student->name }}</h5>
                                            <h6 class="text-muted mb-0">{{ $student->nis }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4>Aksi</h4>
                                </div>
                                <div class="card-body">
                                    <div class="buttons">
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-block mb-2">
                                            <i class="bi bi-pencil-square"></i> Edit Data
                                        </a>
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                                <i class="bi bi-trash"></i> Hapus Data
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 d-flex justify-content-start mt-3">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary me-1 mb-1">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
