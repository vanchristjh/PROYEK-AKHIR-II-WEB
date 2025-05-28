<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SMAN 1 Girsip - Ujian')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Base Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        
        .exam-header {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .exam-footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        /* Prevent leaving the page accidentally */
        html {
            height: 100%;
            overflow: hidden;
        }
        
        body {
            height: 100%;
            overflow: auto;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="exam-header">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-lg font-bold">SMAN 1 Girsip</h1>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-sm text-gray-600">Sistem Ujian Online</span>
            </div>
            <div class="flex items-center">
                <span class="text-sm text-gray-600 mr-2">{{ auth()->user()->name }}</span>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" 
                     alt="User Avatar" class="w-8 h-8 rounded-full">
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <div class="exam-footer">
        <div class="container mx-auto">
            <p>&copy; {{ date('Y') }} SMAN 1 Girsip. Dilarang mencontek atau meninggalkan halaman.</p>
        </div>
    </div>
    
    <script>
        // Prevent leaving the page
        window.addEventListener('beforeunload', function(e) {
            const confirmationMessage = 'Jika Anda meninggalkan halaman, jawaban mungkin tidak tersimpan. Yakin ingin keluar?';
            
            e.preventDefault();
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        });
    </script>
    
    @stack('scripts')
</body>
</html>
