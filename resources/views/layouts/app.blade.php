<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Hệ thống Quản lý Lớp học' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('css')
</head>
<body class="bg-gray-100 text-gray-800 font-sans bg-gray-50 flex flex-col min-h-screen">

@if(Auth::check())
    <nav class="bg-indigo-600 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-white tracking-wider hover:text-indigo-200 transition">Class Manager</a>
                    
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="/" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->is('/', 'home') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' }}">Sinh viên</a>
                            <a href="/assignments" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->is('assignments*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' }}">Bài tập</a>
                            <a href="/challenges" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->is('challenges*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' }}">Giải đố</a>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <div class="text-sm">
                        <span class="text-indigo-200 text-sm">Chào,</span> <strong class="text-white">{{ Auth::user()->full_name }}</strong>
                        <span class="text-indigo-300 text-xs ml-1">({{ Auth::user()->role }})</span>
                    </div>
                    <a href="/logout" class="text-sm bg-indigo-700 hover:bg-red-600 text-white px-3 py-1.5 rounded transition">Đăng xuất</a>
                </div>

                <div class="-mr-2 flex md:hidden">
                    <button type="button" id="mobile-menu-btn" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-500 focus:outline-none transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->is('/', 'home') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' }}">Sinh viên</a>
                <a href="/assignments" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->is('assignments*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' }}">Bài tập</a>
                <a href="/challenges" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->is('challenges*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-500 hover:text-white' }}">Giải đố</a>
                @if(Auth::user()->role === 'teacher')
                    <a href="/create-student" class="block px-3 py-2 rounded-md text-base font-medium text-green-300 hover:bg-indigo-500 hover:text-white">+ Thêm Sinh Viên</a>
                @endif
                <a href="/logout" class="block px-3 py-2 rounded-md text-base font-medium text-red-300 hover:bg-indigo-500 hover:text-white">Đăng xuất</a>
            </div>
            <div class="pt-4 pb-3 border-t border-indigo-700">
                <div class="flex items-center px-5">
                    <div class="text-base font-medium text-white">{{ Auth::user()->full_name }}</div>
                    <div class="text-sm font-medium text-indigo-300 ml-3">({{ Auth::user()->role }})</div>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
@endif

<main class="flex-grow w-full p-4 sm:p-6">
    @yield('content')
</main>
    
<div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white p-6 rounded-lg w-full max-w-sm text-center shadow-xl">
        <h3 id="modalTitle" class="text-xl font-bold text-green-600 mb-2">Xác nhận</h3>
        <p id="modalDesc" class="text-sm text-gray-600 mb-6">Nhập mật khẩu để xác nhận.</p>

        <input type="password" id="modal_password" 
               class="w-full px-4 py-2 mb-6 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
               placeholder="Mật khẩu hiện tại...">

        <div class="flex justify-between gap-4">
            <button type="button" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded-md transition cursor-pointer" onclick="closeModal()">Hủy</button>
            <button type="button" id="modalConfirmBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md transition cursor-pointer" onclick="submitAction()">Xác nhận</button>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/script.js') }}"></script>
@stack('scripts')

<script>
    @if(session('toast_error'))
        showToast("{{ session('toast_error') }}", "error");
    @endif
    @if(session('toast_success'))
        showToast("{{ session('toast_success') }}", "success");
    @endif
</script>
</body>
</html>