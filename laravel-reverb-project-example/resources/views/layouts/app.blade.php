<!DOCTYPE html>
<html lang="vi">

<head>
    {{-- ================= META ================= --}}
    <meta charset="UTF-8">
    <title>@yield('title')</title>

    {{-- ================= VIEWPORT ================= --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ================= VITE CSS ================= --}}
    @vite(['resources/css/app.css'])

    {{-- ================= STYLES ================= --}}
    @stack('styles')
</head>

<body>

    {{-- ================= CONTENT ================= --}}
    <main>
        @yield('content')
    </main>

    {{-- ================= VITE JS ================= --}}
    @vite(['resources/js/app.js'])

    {{-- ================= SCRIPTS ================= --}}
    @stack('scripts')
</body>

</html>
